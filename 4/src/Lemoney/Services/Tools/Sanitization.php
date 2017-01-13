<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Services\Tools;


/**
 * Class Sanitization
 * @package Lemoney\Services
 */
trait Sanitization
{
    /**
     * @param string $String
     * @return string cleaned string from $String
     */
    public function Sanitize(string $String): string
    {
        // remove any slashes
        $String = stripslashes($String);
        $String = stripslashes($String);
        $String = str_replace("'", "|", $String);
        // finally return the string back
        return strip_tags(htmlspecialchars($String));
    }
}