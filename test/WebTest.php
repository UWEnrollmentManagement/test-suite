<?php

namespace UWDOEM\TestSuite\Test;

use UWDOEM\TestSuite\WebTestCase;

/**
 * Class WebTest
 * @package UWDOEM\TestSuite\Test
 */
class WebTest extends WebTestCase
{

    /**
     * Initialize the browser window
     *
     * @return void
     */
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost:8001/');
    }

    /**
     * A test class using WebTestCase shall be able to visit a page and retrieve
     * the title.
     *
     * @return void
     */
    public function testTitle()
    {
        $this->url('/form.php');
        $this->assertEquals('Form', $this->title());
    }

    /**
     * WebTestCase shall provide a method for filtering displayed elements from
     * undisplayed elements.
     *
     * @return void
     */
    public function testDisplayedFilter()
    {
        $this->url('/form.php');
        $this->assertEquals('Form', $this->title());

        $hiddenInput = $this->element($this->using('css selector')->value('input[name="hidden-input"]'));
        $displayedInputs = $this->displayed($this->elements($this->using('css selector')->value('input')));

        $this->assertNotEmpty($displayedInputs);

        foreach ($displayedInputs as $input) {
            $this->assertNotEquals($hiddenInput->attribute('name'), $input->attribute('name'));
        }
    }

    /**
     * A test class using WebTestCase shall be able to fill a form with random
     * data, and submit.
     *
     * @return void
     */
    public function testFormFill()
    {
        $this->url('/form.php');
        $this->assertEquals('Form', $this->title());

        $values = $this->fillForm();

        $this->element($this->using('css selector')->value('input[type=submit]'))->click();

        $body = $this->element($this->using('css selector')->value('body'))->attribute('innerHTML');

        foreach ($values as $value) {
            $this->assertContains($value, $body);
        }
    }

    /**
     * A test class using WebTestCase shall be able to fill a form with a
     * mix of random and predesignated data, and submit.
     *
     * @return void
     */
    public function testFormFillWithPreset()
    {
        $this->url('/form.php');
        $this->assertEquals('Form', $this->title());

        $lastName = str_shuffle("abcdefgh");

        $desiredValues = ["lastname" => $lastName];

        $values = $this->fillForm($desiredValues);

        $this->assertContains($lastName, $values);

        $this->element($this->using('css selector')->value('input[type=submit]'))->click();

        $body = $this->element($this->using('css selector')->value('body'))->attribute('innerHTML');

        foreach ($values as $value) {
            $this->assertContains($value, $body);
        }
    }
}
