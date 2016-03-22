<?php

namespace UWDOEM\TestSuite;

use Markov\Markov;

/**
 * Class WebTestCase
 *
 * @package UWDOEM\TestSuite
 */
class WebTestCase extends \PHPUnit_Extensions_Selenium2TestCase
{

    /**
     * Fill all of the form elements on the page.
     *
     * @param string[] $desiredValues An array of name => submissionValues to use in lieu of random data.
     * @return string[] An array of submissionValues provided to the form.
     */
    protected function fillForm(array $desiredValues = [])
    {
        /** @var string[] $submittedValues */
        $submittedValues = [];

        /** @var array $selectInputs */
        $selectInputs = $this->elements($this->using('css selector')->value('form select'));

        /** @var array $nonSelectInputs */
        $nonSelectInputs = $this->elements($this->using('css selector')->value('input:not([type=submit]), textarea'));

        foreach ($selectInputs as $selectInput) {
            $selectInput->click();

            $enabledOptions = $selectInput->elements($this->using('css selector')->value('option:enabled'));

            $chosenOption = $enabledOptions[array_rand($enabledOptions)];

            $chosenOption->click();

            $submittedValues[] = $chosenOption->attribute('innerHTML');
        }


        foreach ($nonSelectInputs as $nonSelectInput) {
            $name = $nonSelectInput->attribute('name');
            $name = strtok($name, '+');
            $name = strrev(strtok(strrev($name), '-'));

            if ($nonSelectInput->displayed() === true) {
                $nonSelectInput->click();

                $submission = "";

                if ($name !== "" && array_key_exists($name, $desiredValues) === true) {
                    $submission = $desiredValues[$name];
                } elseif ($nonSelectInput->attribute('type') === 'textarea') {
                    $submission = $this->randomText(rand(100, 300));
                    if ($submission === "") {
                        $submission .= rand(100, 999999999);
                    }
                    $submission = trim(preg_replace('/\s+/', ' ', $submission));
                } elseif ($nonSelectInput->attribute('type') === 'text') {
                    $submission = $this->randomChars(5);
                }

                if ($submission !== "") {
                    $this->keys($submission);
                    $submittedValues[] = $submission;
                }
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
}
