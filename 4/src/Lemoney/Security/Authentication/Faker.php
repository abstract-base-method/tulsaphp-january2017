<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Security\Authentication;


use Lemoney\iFace\Authentication;
use Lemoney\Services\Tools\Sanitization;

class Faker implements Authentication
{
    use Sanitization;

    private $conf;

    /**
     * Authentication constructor.
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        $this->conf = $conf;
    }

    /**
     * @return bool if user is authentic and should set $this->CSRF_Token also should set LPHP_Auth_DenialReason if you want an error to show on the login page
     */
    function Authenticate(): bool
    {
        if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
            $username = $_SESSION['username'];
            $password = $_SESSION['password'];
        } elseif (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $this->Sanitize($_POST['username']);
            $password = $this->Sanitize($_POST['password']);
        } else {
            return false;
        }
        if ($username === $this->conf['Username'] && $password === $this->conf['Password']) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            return true;
        } else {
            if (!defined('LPHP_Auth_DenialReason')) {
                define('LPHP_Auth_DenialReason', 'Username/Password Failure');
            }
            return false;
        }
    }
}