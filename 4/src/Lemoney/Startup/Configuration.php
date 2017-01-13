<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Startup;

use Lemoney\Services\Tools\Filesystem;
use Lemoney\Services\Tools\Sanitization;

/**
 * Class Configuration
 * @package Lemoney\Startup
 */
abstract class Configuration
{
    use Filesystem;
    use Sanitization;

    /**
     * @var array $conf configuration array
     */
    protected $conf;

    /**
     * @var array $Connection connection information array
     */
    protected $Connection;

    /**
     * @var string $ConfigurationFile configuration file path
     */
    private $ConfigurationFile;

    /**
     * @var bool $DevEnvironment if true then configuration file loaded environment set to development
     */
    protected $Environment;

    /**
     * Configuration constructor.
     * @param string|null $ConfigurationFile if you want to override the default configuration file then provide a path
     * @throws \Exception if file cannot be read or found
     */
    public function __construct(string $ConfigurationFile = null)
    {
        if (is_null($ConfigurationFile)) {
            $Dev = $this->GetPath("conf|config.json.dev");
            $Prod = $this->GetPath("conf|config.json");
            if (file_exists($Dev) && is_readable($Dev)) {
                $this->ConfigurationFile = $Dev;
                $this->conf = json_decode(file_get_contents($Dev), true);
            } elseif (file_exists($Prod) && is_readable($Prod)) {
                $this->ConfigurationFile = $Prod;
                $this->conf = json_decode(file_get_contents($Prod), true);
            } else {
                throw new \RuntimeException("lemoney-php -- Configuration File Unreadable -- There is no available configuration file");
            }
        }
        else {
            if (file_exists($ConfigurationFile) && is_readable($ConfigurationFile)) {
                $this->ConfigurationFile = $ConfigurationFile;
                $this->conf = json_decode(file_get_contents($ConfigurationFile), true);
            }
            else {
                throw new \RuntimeException("lemoney-php -- Configuration File Unreadable -- The override config file cannot be read [$ConfigurationFile]");
            }
        }
        if (
            array_key_exists('Environment', $this->conf) &&
            in_array($this->conf['Environment'], $this->conf['AvailableEnvironment'])
        ) {
            $this->Environment = $this->conf['Environment'];
        }
        else {
            $this->Environment = 'undefined';
        }
        $this->Connection = $this->ConnectionInfo();
    }

    /**
     * @throws \Exception if file cannot be written to or found
     */
    public function __destruct()
    {
        if (file_exists($this->ConfigurationFile) && is_readable($this->ConfigurationFile)) {
            file_put_contents($this->ConfigurationFile, json_encode($this->conf, JSON_PRETTY_PRINT));
        } else {
            throw new \Exception('lemoney-php -- Configuration Cannot Be Written To -- The configuration files are not available');
        }
    }

    /**
     * @return array returns connection info
     */
    private function ConnectionInfo(): array
    {
        if (isset($_SESSION['username'])) {
            $username = $this->Sanitize($_SESSION['username']);
        } elseif (isset($_POST['username'])) {
            $username = $this->Sanitize($_POST['username']);
        } elseif (php_sapi_name() === 'cli') {
            $username = get_current_user();
        } else {
            $username = 'UNKNOWN';
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $address = $_SERVER['REMOTE_ADDR'];
        } else {
            $address = 'localhost';
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $request = $_SERVER['REQUEST_URI'];
        } else {
            $request = 'localhost';
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        } else {
            $method = 'localhost';
        }
        return array(
            'user' => $username,
            'address' => $address,
            'request' => $request,
            'method' => $method,
            'environment' => $this->Environment
        );
    }

}