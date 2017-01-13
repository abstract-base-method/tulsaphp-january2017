<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\iFace;


/**
 * Interface SessionHandler
 * @package Lemoney\iFace
 */
interface SessionHandler
{
    /**
     * @param string $data
     * @param string $iv
     * @param string $Method
     * @return string should return cipher text
     */
    function Encrypt(string $data, string $iv, string $Method = 'AES-256-CBC'): string;

    /**
     * @param string $data
     * @param string $iv
     * @param string $Method
     * @return string should return decrypted cipher text from Encrypt
     */
    function Decrypt(string $data, string $iv, $Method = 'AES-256-CBC'): string;
}