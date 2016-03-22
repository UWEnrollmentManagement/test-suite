[![Build Status](https://travis-ci.org/UWEnrollmentManagement/test-suite.svg?branch=master)](https://travis-ci.org/UWEnrollmentManagement/test-suit)
[![Code Climate](https://codeclimate.com/github/UWEnrollmentManagement/test-suite/badges/gpa.svg)](https://codeclimate.com/github/UWEnrollmentManagement/test-suite)
[![Test Coverage](https://codeclimate.com/github/UWEnrollmentManagement/test-suite/badges/coverage.svg)](https://codeclimate.com/github/UWEnrollmentManagement/test-suite/coverage)
[![Latest Stable Version](https://poser.pugx.org/uwdoem/test-suite/v/stable)](https://packagist.org/packages/uwdoem/test-suite)

# TestSuite

PHPUnit web test case class for UWDOEM projects using the [Athens]() web framework.

## Use

This library is published on packagist. To install using Composer, add the `"uwdoem/test-suite": "0.*"` line to your "require-dev" dependencies:

```
{
    "require-dev": {
        ...
        "uwdoem/test-suite": "0.*",
        ...
    }
}
```

## Example

Below is an example test file which makes use of the WebTestCase class:

```
<?php

use UWDOEM\TestSuite\WebTestCase;

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
    
}
```
