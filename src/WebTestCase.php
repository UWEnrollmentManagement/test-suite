<?php

namespace UWDOEM\TestSuite;

use PHPUnit_Extensions_Selenium2TestCase_WebDriverException;

use PHPUnit_Extensions_Selenium2TestCase_Keys as Keys;
use PHPUnit_Extensions_Selenium2TestCase_Element as Element;

use Markov\Markov;

/**
 * Class WebTestCase
 *
 * @package UWDOEM\TestSuite
 */
class WebTestCase extends \PHPUnit_Extensions_Selenium2TestCase
{

    /**
     * Given an array of desired values and a field name, retrieve the desired value
     * for that input, or return null.
     *
     * @param string   $inputName
     * @param string[] $desiredValues
     * @return null
     */
    protected static function getDesiredValue($inputName, array $desiredValues)
    {
        $inputName = strtok($inputName, '+');

        foreach ($desiredValues as $key => $value) {
            if (strpos($inputName, $key) === 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Filter out any elements which are not displayed.
     *
     * @param array $elements
     * @return array
     */
    protected static function displayed(array $elements)
    {
        return array_filter(
            $elements,
            function ($element) {
                return $element->displayed();
            }
        );
    }

    /**
     * Close the current Athens "multi-panel" panel.
     *
     * @return void
     */
    protected function closePanel()
    {
        $this->moveto($this->byCssSelector('nav.breadcrumbs li:first-child a'));
        $this->click();
        sleep(2);
    }

    /**
     * @param Element $selectElement
     * @param string  $labelValue
     * @return boolean Whether or not an option was found with the indicated label value.
     */
    protected function selectOption(Element $selectElement, $labelValue)
    {
        $selectElement->click();

        $enabledOptions = $selectElement->elements($this->using('css selector')->value('option:enabled'));

        foreach ($enabledOptions as $option) {
            if (trim($option->attribute('innerHTML')) === trim($labelValue)) {
                $option->click();
                return true;
            }
        }

        return false;
    }

    /**
     * Fill all of the form elements on the page.
     *
     * @param string[] $desiredValues An array of name => submissionValues to use in lieu of random data.
     * @param string   $formSelector
     * @return string[] An array of submissionValues provided to the form.
     */
    protected function fillForm(array $desiredValues = [], $formSelector = 'form')
    {
        /** @var string[] $submittedValues */
        $submittedValues = [];

        /** @var array $selectInputs */
        $selectInputs = $this->elements($this->using('css selector')->value("$formSelector select"));

        /** @var array $radioInputs */
        $radioInputs = $this->elements($this->using('css selector')->value("$formSelector input[type=radio]"));

        /** @var array $fileInputs */
        $fileInputs = $this->elements($this->using('css selector')->value("$formSelector input[type=file]"));

        /** @var array $otherInputs */
        $otherInputs = $this->elements(
            $this->using('css selector')->value(
                "$formSelector input:not([type=submit]):not([type=file]):not([type=radio]), " .
                    "$formSelector textarea, $formSelector .note-editing-area"
            )
        );

        foreach ($selectInputs as $selectInput) {
            $desiredValue = static::getDesiredValue($selectInput->attribute('name'), $desiredValues);

            $selectInput->click();

            $enabledOptions = $selectInput->elements($this->using('css selector')->value('option:enabled'));

            $chosenOption = $enabledOptions[array_rand($enabledOptions)];
            if ($desiredValue !== null) {
                foreach ($enabledOptions as $enabledOption) {
                    if (trim($enabledOption->attribute('innerHTML')) === trim($desiredValue)) {
                        $chosenOption = $enabledOption;
                    }
                }
            }

            $chosenOption->click();

            $submittedValues[] = $chosenOption->attribute('innerHTML');
        }

        foreach (static::displayed($radioInputs) as $input) {
            $desiredValue = static::getDesiredValue($input->attribute('name'), $desiredValues);

            $label = $this->byCssSelector('label[data-value-for="' . $input->attribute('value') . '"]')
                ->attribute('innerHTML');
            if ($desiredValue === null || $desiredValue === trim($label)) {
                $input->click();
                $submittedValues[] = trim($label);
            }
        }

        foreach (static::displayed($fileInputs) as $input) {
            // Figure out something to do.
        }

        foreach (static::displayed($otherInputs) as $input) {
            $desiredValue = static::getDesiredValue($input->attribute('name'), $desiredValues);

            $input->click();

            if ($desiredValue !== null) {
                $submission = $desiredValue;
            } elseif ($input->attribute('type') === 'textarea') {
                $submission = $this->randomText(rand(100, 300));
            } else {
                $submission = $this->randomChars(5);
            }

            if ($submission !== "") {
                /* Select All */
                $this->ctrlA();

                /* Key the Data */
                $this->keys($submission);

                $submittedValues[] = $submission;
            }
        }
        return $submittedValues;
    }

    /**
     * Pauses execution of the script while the spinner is visible (indicating loading data)
     *
     * @param integer $maxWait The maximum amount of time, in seconds, to wait for the spinner to clear.
     * @return void
     */
    protected function waitForSpinner($maxWait = 20)
    {
        $waitIncrement = 1;
        $loadingGif = $this->element($this->using('id')->value('mask-screen'));
        sleep(2);

        for ($waited=0; $waited<=$maxWait; $waited+=$waitIncrement) {
            if ($loadingGif->displayed() === false) {
                break;
            }
            sleep(1);
        }
        sleep(4);
    }

    /**
     * Key in a Ctrl-A
     *
     * @return void
     */
    protected function ctrlA()
    {
        $this->keys(Keys::CONTROL);
        $this->keys('a');
        $this->keys(Keys::CONTROL);
    }

    /**
     * @param integer $numChars
     * @return string A string of random characters.
     */
    protected function randomChars($numChars)
    {
        return substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", (int)$numChars/10 + 1)), 0, $numChars);
    }

    /**
     * @return string A nameish looking string
     */
    protected function randomName()
    {

        $vowels = ["a", "e", "i", "o", "u", "ow", "ou", "ae", "ie", "igh", "oi", "oo", "ea", "ee"];
        $consonants = [
            "b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "y", "z",
            "sch", "ng", "ch", "gh", "ph", "wh", "th", "bl", "cl", "fl", "gl", "pl", "br", "cr", "dr", "fr", "gr",
            "str", "spr", "sl",
        ];

        $numChoices = rand(4, 6);

        $name = "";
        for ($i=rand(0, 1); $i<$numChoices; $i++) {
            $name .= $i % 2 !== 0 ? $vowels[array_rand($vowels)]: $consonants[array_rand($consonants)];
        }

        return ucfirst($name);
    }

    /**
     * @param integer $approximateLength
     * @return string
     */
    protected function randomText($approximateLength = 500)
    {

        /** @var integer $markovAssociativityLength */
        $markovAssociativityLength = 4;

        /** @var string $text */
        $text = file_get_contents(__DIR__ . "/markov/text/kant.txt");

        /** @var array $markov_table */
        $markov_table = Markov::generate_markov_table($text, $markovAssociativityLength);

        /** @var string $markov */
        $markov = Markov::generate_markov_text($approximateLength, $markov_table, $markovAssociativityLength);

        return preg_replace('/[^,;. a-zA-Z0-9_-]|[,;. ]$/s', '', $markov);
    }

    /**
     * Predicate which reports whether an element with the given CSS selector exists.
     *
     * @param string $selector
     * @return boolean
     */
    protected function elementExistsByCss($selector)
    {
        echo "($selector)";
        try {
            $this->byCssSelector($selector);
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            return false;
        }

        return true;
    }
}
