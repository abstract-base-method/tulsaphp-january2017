<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\iFace;


/**
 * Interface Authentication
 * @package Lemoney\iFace
 */
interface Authentication
{
    /**
     * Authentication constructor.
     * @param array $conf
     */
    function __construct(array $conf);

    /**
     * @return bool if user is authentic and should set $this->CSRF_Token also should set LPHP_Auth_DenialReason if you want an error to show on the login page
     */
    function Authenticate(): bool;
}