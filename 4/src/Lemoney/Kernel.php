<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney;

use Lemoney\Startup\Authenticate;
use Monolog\Logger;

/**
 * Class Kernel
 * @package Lemoney
 */
class Kernel extends Authenticate
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var array data passed to view
     */
    protected $ViewData;

    /**
     * @var array|null
     */
    protected $request;

    /**
     * @var bool if CLI Script then true else false
     */
    protected $IsCLI;

    /**
     * @var bool $UnitTesting if true application SHOULD act as if it is operating in a headless testing environment
     */
    protected $UnitTesting;

    /**
     * Kernel constructor.
     * @param null $ConfigurationFile
     * @param bool $UnitTesting set Unit Testing Flag for application test handling
     */
    public function __construct($ConfigurationFile = null, bool $UnitTesting = false)
    {
        global $argv;
        parent::__construct($ConfigurationFile);
        $this->UnitTesting = $UnitTesting;
        if (php_sapi_name() === 'cli') {
            $this->IsCLI = true;
            $this->request = $argv;
            $this->twig = null;
        }
        else {
            $this->IsCLI = false;
            $this->request = (isset($_SERVER['REQUEST_URI']) ? explode("/", strtolower($this->Sanitize($_SERVER['REQUEST_URI']))) : null);
            $this->twig = $this->GenerateTwigEnvironment();
            $this->ViewData = array(
                'csrf_token' => $this->GetCSRFToken(),
                'Application' => $this->conf['Application'],
                'SupportLink' => $this->conf['SupportLink']
            );
        }
    }

    /**
     * @return bool returns boolean if true then running in a Unit Testing like environment
     */
    public function GetUnitTestingState(): bool
    {
        return $this->UnitTesting;
    }

    /**
     * @param int $Position URI Position example.com/1/2/3/4/5/etc...
     * @return array|string|null $this->request
     */
    public function GetRequest(int $Position = null)
    {
        if (!is_null($Position)) {
            if (array_key_exists($Position, $this->request)) {
                return $this->request[$Position];
            } else {
                return null;
            }
        } else {
            return $this->request;
        }
    }

    /**
     * @return bool $this->IsCLI
     */
    public function GetCLIState(): bool
    {
        return $this->IsCLI;
    }

    /**
     * @return string returns environment string
     */
    public function GetEnvironmentType(): string
    {
        if ($this->Environment === 'undefined') {
            $this->LogMessage('Undefined Application Environment', Logger::CRITICAL);
        }
        return $this->Environment;
    }

    /**
     * @param string $FileName
     * @param array $ViewData
     */
    public function View(string $FileName, array $ViewData = array())
    {
        if (!is_null($this->twig)) {
            if (file_exists($this->GetPath('storage|view|' . $FileName))) {
                $this->LogMessage('View Loaded: ' . $this->Sanitize($FileName));
                echo $this->twig->render(implode(DIRECTORY_SEPARATOR, explode("|", $this->Sanitize($FileName))), array_merge($this->ViewData, $ViewData));
            }
            else {
                $this->LogMessage('View Not Found: ' . $this->Sanitize($FileName), Logger::ERROR);
                echo $this->twig->render('Error.twig', array_merge($this->ViewData, array('ErrorTitle' => 'Page Not Found', 'ErrorMessage' => 'The requested view is not located on this server')));
            }
        }
        else {
            $this->LogMessage("Twig property was undefined. Was Kernel::View called from CLI?", Logger::WARNING);
            $this->twig = $this->GenerateTwigEnvironment();
            if (file_exists($this->GetPath('storage|view|' . $FileName))) {
                $this->LogMessage('View Loaded: ' . $this->Sanitize($FileName));
                echo $this->twig->render(implode(DIRECTORY_SEPARATOR, explode("|", $this->Sanitize($FileName))), array_merge($this->ViewData, $ViewData));
            }
            else {
                $this->LogMessage('View Not Found: ' . $this->Sanitize($FileName), Logger::ERROR);
                echo $this->twig->render('Error.twig', array_merge($this->ViewData, array('ErrorTitle' => 'Page Not Found', 'ErrorMessage' => 'The requested view is not located on this server')));
            }
        }
    }

    /**
     * @return array returns array stored in User in the loaded config document
     */
    public function GetUserConfiguration(): array
    {
        if (array_key_exists('User', $this->conf)) {
            return $this->conf['User'];
        } else {
            $this->LogMessage('User Configuration Directive Not Found', Logger::WARNING);
            return array();
        }
    }

    /**
     * @param string $Key new array key
     * @param string|int|array $Value value to set to
     * @return bool return if set was successful
     */
    public function SetUserConfigurationDirective(string $Key, $Value): bool
    {
        if (is_string($Value)) {
            $FinalValue = $this->Sanitize($Value);
        }
        elseif (is_numeric($Value)) {
            $FinalValue = intval($Value);
        }
        elseif (is_array($Value)) {
            $FinalValue = $Value;
        }
        else {
            $this->LogMessage("Invalid Configuration Value", Logger::ERROR);
            return false;
        }
        $this->conf['User'][$this->Sanitize($Key)] = $FinalValue;
        return true;
    }

    /**
     * @param string $ConnectionName connection key found in config.json
     * @return \PDO returns the PDO object requested
     * @throws \RuntimeException if the connection is not found or databases are not enabled then this throws an error
     */
    public function DatabaseString(string $ConnectionName): \PDO
    {
        if ($this->conf['Database']['Enabled']) {
            if (array_key_exists($ConnectionName, $this->conf['Database']['Servers'])) {
                if (is_array($this->conf['Database']['Servers'][$ConnectionName])) {
                    if (
                        array_key_exists(0, $this->conf['Database']['Servers'][$ConnectionName]) &&
                        array_key_exists(1, $this->conf['Database']['Servers'][$ConnectionName]) &&
                        array_key_exists(2, $this->conf['Database']['Servers'][$ConnectionName])
                    )
                    {
                        return new \PDO($this->conf['Database']['Servers'][$ConnectionName][0], $this->conf['Database']['Servers'][$ConnectionName][1], $this->conf['Database']['Servers'][$ConnectionName][2]);
                    }
                    else {
                        throw new \RuntimeException("lemoney-php -- Connection String configuration not recognized -- The Connection String Requested [$ConnectionName] is not configured in the configuration file properly");
                    }
                }
                else {
                    return new \PDO($this->conf['Database']['Servers'][$ConnectionName]);
                }
            }
            else {
                throw new \RuntimeException("lemoney-php -- Connection String is not Found -- The Connection String Requested [$ConnectionName] is not configured in the configuration file");
            }
        }
        else {
            throw new \RuntimeException("lemoney-php -- Connections are not Enabled -- Databases are not configured in the configuration file");
        }
    }

    /**
     * @param string $Environment environment to set to
     * @param bool $ReloadTwig reload twig to enable new environment settings
     * @return bool if successful then true else false
     */
    public function SetEnvironment(string $Environment, bool $ReloadTwig = true): bool
    {
        if (in_array($this->Sanitize($Environment), $this->conf['AvailableEnvironment'])) {
            $this->LogMessage("Configured Environment Changed During Request", Logger::INFO);
            $this->Environment = $this->Sanitize($Environment);
            // since caching and debug are determined by environment twig is reloaded by default
            // so to enable debug
            if ($ReloadTwig) {
                $this->twig = $this->GenerateTwigEnvironment();
            }
            return true;
        }
        else {
            $this->LogMessage("Invalid Environment Attempted to be set", Logger::ERROR);
            return false;
        }
    }

    /**
     * returns Twig instance for use in View()
     * Reason: I found as we added caching and debug options separating logic was helpful
     * @return \Twig_Environment
     */
    private function GenerateTwigEnvironment(): \Twig_Environment
    {
        if ($this->GetEnvironmentType() === 'development') {

            return new \Twig_Environment(new \Twig_Loader_Filesystem([
                $this->GetPath('src|Lemoney|Services|Tools|InternalView|'),
                $this->GetPath('storage|view|')
            ]), [
                'cache' => $this->GetPath('storage|cache|template|'),
                'auto_reload' => true,
                'debug' => true
            ]);
        }
        else {
            return new \Twig_Environment(new \Twig_Loader_Filesystem([
                $this->GetPath('src|Lemoney|Services|Tools|InternalView|'),
                $this->GetPath('storage|view|')
            ]), [
                'cache' => $this->GetPath('storage|cache|template|')
            ]);
        }
    }
}