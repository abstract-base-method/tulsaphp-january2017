<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */


class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testRouter()
    {
        global $argv;
        $argv[1] = 'home';
        $Kernel = new \Lemoney\Kernel(null, true);
        $Router = new \Lemoney\Services\Middleware\Router($Kernel);
        $this->assertTrue($Router instanceof \Lemoney\iFace\Router);
    }
}
