<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/2/17
 * Project: 2017.January
 */

namespace Demo;


use Demo\iFace\Error;
use Demo\iFace\Register;
use Demo\iFace\Registration;
use Demo\iFace\Success;

class Demo
{
    /**
     * @var array of objects to use
     */
    private $Actions = [
        'Registration' => '\\Demo\\Procedures\\Registration',
        'Register' => '\\Demo\\Procedures\\Register',
        'Success' => '\\Demo\\Procedures\\Success',
        'Error' => '\\Demo\\Procedures\\Error'
    ];

    /**
     * @var Registration $Registration to show registration screen
     */
    private $Registration;

    /**
     * @var Register $Register to do registration
     */
    private $Register;

    /**
     * @var Success $Success to show success screen
     */
    private $Success;

    /**
     * @var Error $Error to show Error Screen
     */
    private $Error;

    /**
     * Demo constructor.
     */
    public function __construct()
    {
        if ($this->GenerateObjects()) {
            $this->StartProgram();
        } else {
            echo "Generator Error!";
            exit;
        }
    }

    private function StartProgram()
    {
        if (isset($_POST['email_addr'])) {
            try {
                if ($this->Register->AttemptRegistration($_POST['email_addr'])) {
                    $this->Success->SuccessScreen();
                } else {
                    $this->Error->ErrorScreen("Email System Entry Failure!");
                }
            } catch (\PDOException $PDOException) {
                $this->Error->ErrorScreen($PDOException->getMessage());
            }
        }
        else {
            $this->Registration->RegistrationScreen();
        }
    }

    /**
     * Runs Constructor
     */
    public static function TheDemo()
    {
        new Demo();
    }

    /**
     * Generator for Objects for use
     * @return bool
     */
    private function GenerateObjects(): bool
    {
        if (
            isset($this->Actions['Error']) &&
            class_exists($this->Actions['Error'])
        ) {
            $this->Error = new $this->Actions['Error'];
            if (!$this->Error instanceof Error) {
                return false;
            }
        }
        else {
            return false;
        }
        if (
            isset($this->Actions['Registration']) &&
            class_exists($this->Actions['Registration'])
        ) {
               $this->Registration = new $this->Actions['Registration'];
               if (!$this->Registration instanceof Registration) {
                   return false;
               }
        }
        else {
            return false;
        }
        if (
            isset($this->Actions['Register']) &&
            class_exists($this->Actions['Register'])
        ) {
            $this->Register = new $this->Actions['Register'];
            if (!$this->Register instanceof Register) {
                return false;
            }
        }
        else {
            return false;
        }
        if (
            isset($this->Actions['Success']) &&
            class_exists($this->Actions['Success'])
        ) {
            $this->Success = new $this->Actions['Success'];
            if (!$this->Success instanceof Success) {
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }
}