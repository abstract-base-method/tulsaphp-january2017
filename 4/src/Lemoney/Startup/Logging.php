<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Startup;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\ErrorHandler;
use Lemoney\Services\Tools\Email;

/**
 * Class Logging
 * @package Lemoney\Startup
 */
abstract class Logging extends Configuration
{
    /**
     * @var Logger
     */
    protected $AppLog;

    /**
     * Logging constructor.
     * @param null $ConfigurationFile override config for Configuration Class
     */
    public function __construct($ConfigurationFile = null)
    {
        parent::__construct($ConfigurationFile);
        // establish monolog instance
        $this->AppLog = new Logger($this->conf['Application']);
        // setup the error handler
        ErrorHandler::register($this->AppLog);
        // only start registering logging services if logging overall is enabled (By default yes)
        if ($this->conf['Logging']['Enabled']) {
            // if filesystem logging is enabled setup all the log files (by default it is)
            if ($this->conf['Logging']['Filesystem']) {
                $LogDir = $this->GetPath('storage|log|');
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'debug.log', Logger::DEBUG, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'info.log', Logger::INFO, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'notice.log', Logger::NOTICE, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'warning.log', Logger::WARNING, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'error.log', Logger::ERROR, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'alert.log', Logger::ALERT, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'critical.log', Logger::CRITICAL, false));
                $this->AppLog->pushHandler(new StreamHandler($LogDir . 'development.log', Logger::DEBUG));
            }
            // if SNMP is setup then make the handler
            if ($this->conf['Logging']['SNMP']['Enabled']) {
                $this->AppLog->pushHandler(new SyslogUdpHandler($this->conf['Logging']['SNMP']['Host'], $this->conf['Logging']['SNMP']['Port'], LOG_USER, Logger::ERROR));
            }
        }
    }

    /**
     * @param string $Message
     * @param int $Level
     * @param array $Additional
     * @return bool if logging was successful
     */
    public function LogMessage(string $Message, int $Level = Logger::DEBUG, array $Additional = array()): bool
    {
        if ($this->conf['Logging']['Email']['Enabled'] && $Level > Logger::WARNING) {
            $mail = new Email(
                $this->conf['Logging']['Email']['Host'],
                $this->conf['Logging']['Email']['Port'],
                $this->conf['Logging']['Email']['Username'],
                $this->conf['Logging']['Email']['Password'],
                $this->conf['Logging']['Email']['SenderAddress']
            );
            return (
                $this->AppLog->addRecord($Level, $Message, array_merge($this->Connection, $Additional)) &&
                $mail->SendMail(
                    $this->conf['Logging']['Email']['LoggingAddress'],
                    $this->conf['Application'] . ' Logging Service',
                    $this->Sanitize($Message) . "\n\r Additional: \n\r" . json_encode(array_merge($this->Connection, $Additional), JSON_PRETTY_PRINT)
                )
            );
        } else {
            return ($this->AppLog->addRecord($Level, $Message, array_merge($this->Connection, $Additional)));
        }
    }
}