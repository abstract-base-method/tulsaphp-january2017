<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace Lemoney\Services\Middleware;


use Lemoney\Kernel;
use Monolog\Logger;

/**
 * Class CLI
 * @package Lemoney\Services
 *
 * Provides implementation of things for command line tasks,
 * makes implementing CLI tools in apps easier
 */
trait CLI
{
    protected $Kernel;

    /**
     * CLI constructor.
     * @param Kernel $kernel
     */
    function __construct(Kernel &$kernel)
    {
        $this->Kernel = $kernel;
    }

    /**
     * @param string $Message to Print in Red
     */
    public function PrintError(string $Message)
    {
        $this->Kernel->LogMessage("ERROR IN CONSOLE: " . $this->Kernel->Sanitize($Message), Logger::ERROR);
        echo "\n\033[0;31m" . $this->Kernel->Sanitize($Message) . "\033[0m\n\n";
    }

    /**
     * @param string $Message to Print in Yellow
     */
    public function PrintWarning(string $Message)
    {
        $this->Kernel->LogMessage("WARNING IN CONSOLE: " . $this->Kernel->Sanitize($Message), Logger::WARNING);
        echo "\n\033[1;33m" . $this->Kernel->Sanitize($Message) . "\033[0m\n\n";
    }

    /**
     * @param string $Message To Print in Green
     */
    public function PrintSuccess(string $Message)
    {
        $this->Kernel->LogMessage("SUCCESS IN CONSOLE: " . $this->Kernel->Sanitize($Message), Logger::INFO);
        echo "\n\033[0;32m" . $this->Kernel->Sanitize($Message) . "\033[0m\n\n";
    }

    /**
     * @param string $Message To Print
     * @param string $Color Color To Print In
     */
    public function PrintWithColor(string $Message, $Color = "0m")
    {
        echo "\n\033[" . $this->Kernel->Sanitize($Color) . $this->Kernel->Sanitize($Message) ."\033[0m\n\n";
    }

    /**
     * @param string $Message to print without special coloring
     */
    public function Print(string $Message)
    {
        echo "\n" . $this->Kernel->Sanitize($Message) . "\n\n";
    }

    /**
     * @param string $Prompt to ask user for input
     * @return string user response [SANITIZED]
     */
    public function UserInput(string $Prompt): string
    {
        fwrite(STDOUT, "\n" . $this->Kernel->Sanitize($Prompt . "\n"));
        return trim($this->Kernel->Sanitize(fgets(STDIN)));
    }

    /**
     * @param string $RootLevelTag root tag to set in XML response
     * @param array $InputArray input array to transmit
     */
    public function PrintXML(string $RootLevelTag, array $InputArray)
    {
        $xml = new \SimpleXMLElement("<" . $this->Kernel->Sanitize($RootLevelTag) . "/>");
        array_walk_recursive($InputArray, array($xml, 'addChild'));
        echo "\n" . $xml->asXML() . "\n\n";
    }

    /**
     * @param mixed $Input input to print as JSON
     * @throws \Exception throws if unknown data type
     */
    public function PrintJSON($Input)
    {
        if (is_array($Input)) {
            $Input = $Input;
        }
        elseif (is_numeric($Input)) {
            $Input = $this->Kernel->Sanitize(strval($Input));
        }
        else {
            if (!is_string($Input)) {
                throw new \Exception("lemoney-php -- Invalid Data Type -- " . __CLASS__ . "::" . __METHOD__);
            } else {
                $Input = $this->Kernel->Sanitize($Input);
            }
        }
        echo "\n" . json_encode($Input, JSON_PRETTY_PRINT) . "\n\n";
    }
}