<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Startup;

use Lemoney\iFace\Authentication;
use Monolog\Logger;

/**
 * Class Authenticate
 * @package Lemoney\Startup
 */
abstract class Authenticate extends Logging
{
    /**
     * @var bool if this is an authenticated request
     */
    private $Authorization;

    /**
     * @var string request CSRF token
     */
    private $CSRF_Token;

    /**
     * Authenticate constructor.
     * @param null $ConfigurationFile
     */
    public function __construct($ConfigurationFile = null)
    {
        parent::__construct($ConfigurationFile);
        $this->Authorization = $this->Authenticate();
    }

    /**
     * @return bool Getter for Authentication Status
     */
    public function GetAuthenticationStatus(): bool
    {
        return $this->Authorization;
    }

    /**
     * @return string Getter for CSRF Token
     */
    public function GetCSRFToken(): string
    {
        if (is_null($this->CSRF_Token)) {
            $this->LogMessage("CSRF Token was null", Logger::WARNING);
            $this->CSRF_Token = $this->CSRF_Generate();
            $_SESSION['csrf_token'] = $this->CSRF_Token;
            return $this->CSRF_Token;

        }
        else {
            return $this->CSRF_Token;
        }
    }

    /**
     * @return bool return if it is a valid request
     */
    private function Authenticate(): bool
    {
        // establish a variable to operate on to ensure excluded paths do not get CSRF Token Validation
        $CoreURI = (isset($_SERVER['REQUEST_URI']) && isset(explode("/", $_SERVER['REQUEST_URI'])[1])? $this->Sanitize(explode("/", $_SERVER['REQUEST_URI'])[1]) : null );
        // If Authentication is disabled then return true
        if ($this->conf['Authentication']['Enabled'] === 'off') {
            // if the first URI element is set and it is excluded then CSRF Validation is true
            if (!is_null($CoreURI) && array_key_exists($CoreURI, $this->conf['CSRFExcludePaths'])) {
                $CSRF = true;
            }
            // else Validate the request through CSRF
            else {
                $CSRF = true;
            }
            return (true && $CSRF);
        }
        // else move through the authentication system
        else {
            // establish if a valid authentication type is set
            if (array_key_exists($this->conf['Authentication']['Enabled'], $this->conf['Authentication'])) {
                $class = "Lemoney\\Security\\Authentication\\" . $this->conf['Authentication']['Enabled'];
                // ensure the authentication provider exists
                if (class_exists($class)) {
                    $class = new $class($this->conf['Authentication'][$this->conf['Authentication']['Enabled']]);
                    // if the provider exists and implements the needed interface then attempt authentication
                    if ($class instanceof Authentication) {
                        // if it is an excluded Authentication path then set to true
                        if (array_key_exists($CoreURI, $this->conf['AuthenticationExcludePaths'])) {
                            $Auth = true;
                        }
                        // else establish authentication status
                        else {
                            $Auth = $class->Authenticate();
                        }
                        // if the first URI element is set and it is excluded then CSRF Validation is true
                        if (!is_null($CoreURI) && array_key_exists($CoreURI, $this->conf['CSRFExcludePaths'])) {
                            $CSRF = true;
                        }
                        // else Validate the request through CSRF
                        else {
                            $CSRF = $this->CSRF_Validate();
                        }
                        // Finally set the CSRF Token
                        $_SESSION['csrf_token'] = $this->CSRF_Token;
                        // return true if both variables are true else false
                        return ($Auth && $CSRF);
                    }
                    // return with error since it does not implement the interface correctly
                    else {
                        $this->LogMessage("Authentication Class Does not Implement the Interface", Logger::ALERT);
                        return false;
                    }
                }
                // return with message since the authentication provider class does not exist
                else {
                    $this->LogMessage("Authentication Class Not Found", Logger::ALERT, array("Class" => $class));
                    return false;
                }
            }
            // return logging that an invalid authentication type has been provided
            else {
                $this->LogMessage("Authentication Configuration Mismatch", Logger::ALERT, array("Configuration" => $this->conf['Authentication']));
                return false;
            }
        }
    }

    /**
     * @return bool return if the token is valid
     */
    private function CSRF_Validate(): bool
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['csrf_token'])) {
                    if ($_SESSION['csrf_token'] === $_POST['csrf_token']) {
                        $this->CSRF_Token = $this->CSRF_Generate();
                        return true;
                    } else {
                        $this->CSRF_Token = $this->CSRF_Generate();
                        $this->LogMessage('CSRF Token Mismatch', 300);
                        if (!defined('LPHP_Auth_DenialReason')) {
                            define('LPHP_Auth_DenialReason', 'CSRF Protection Failed to Validate');
                        }
                        return false;
                    }
                } else {
                    $this->CSRF_Token = $this->CSRF_Generate();
                    $this->LogMessage('CSRF Token Not Submitted', 300);
                    if (!defined('LPHP_Auth_DenialReason')) {
                        define('LPHP_Auth_DenialReason', 'CSRF Protection Failed to Validate');
                    }
                    return false;
                }
            } else {
                $this->CSRF_Token = $this->CSRF_Generate();
                return true;
            }
        } else {
            $this->CSRF_Token = $this->CSRF_Generate();
            return true;
        }
    }

    /**
     * @return string return the CSRF token for this request
     */
    private function CSRF_Generate(): string
    {
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        } elseif (isset($_POST['username'])) {
            $username = $this->Sanitize($_POST['username']);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $username = $_SERVER['REMOTE_ADDR'];
        } else {
            $username = base64_encode(random_bytes(4096));
        }
        return base64_encode(hash('sha512', $username . random_bytes(4096)));
    }
}