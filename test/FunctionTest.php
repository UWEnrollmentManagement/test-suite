<?php

namespace UWDOEM\TestSuite\Test;

use UWDOEM\TestSuite\WebTestCase;

/**
 * Class FunctionTest
 * @package UWDOEM\TestSuite\Test
 */
class FunctionTest extends WebTestCase
{

    /**
     * Initialize the browser window.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost:8001/');
    }

    /**
     * A test class using WebTestCase shall be able to produce a random
     * string of characters to a desired length.
     *
     * @return void
     */
    public function testRandomChars()
    {
        $numChars = rand(20, 40);

        $chars = $this->randomChars($numChars);
        $this->assertEquals($numChars, strlen($chars));
    }

    /**
     * A test class using WebTestCase shall also have a method for producing
     * random test of an approximate desired length.
     *
     * This text shall be semi-coherent (eg: Markov chain text generator), but
     * this is not enforced by test.
     *
     * @return void
     */
    public function testRandomText()
    {
        $approximateLength = rand(20, 40);

        $chars = $this->randomText($approximateLength);
        $this->assertGreaterThan($approximateLength/2, strlen($chars));
    }

    /**
     * A test class using WebTestCase shall also have a method for producing
     * random string of length greater than two.
     *
     * This text shall be a plausible, natural language name (eg: Orkov), but
     * this is not enforced by test.
     *
     * @return void
     */
    public function testRandomName()
    {
        $name = $this->randomChars(50);
        $this->assertGreaterThan(2, strlen($name));
    }
}
