<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Services\Tools;


/**
 * Class Filesystem
 * @package Lemoney\Services
 */
trait Filesystem
{
    /**
     * @param string|null $Path
     * @return string either the base path of the application OR the full path of the $Path replacing | with DIRECTORY_SEPARATOR
     */
    public function GetPath(string $Path = null): string
    {
        $Base = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        array_pop($Base);
        array_pop($Base);
        array_pop($Base);
        array_pop($Base);
        $Base = implode(DIRECTORY_SEPARATOR, $Base) . DIRECTORY_SEPARATOR;
        if (is_null($Path)) {
            return $Base;
        } else {
            return $Base . str_replace("|", DIRECTORY_SEPARATOR, $Path);
        }
    }
}