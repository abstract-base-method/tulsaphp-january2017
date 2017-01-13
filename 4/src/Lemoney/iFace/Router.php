<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\iFace;


use Lemoney\Kernel;

/**
 * Interface Router
 * @package Lemoney\iFace
 */
interface Router
{
    /**
     * Router constructor.
     * @param Kernel $Router
     */
    public function __construct(Kernel &$Router);

    /**
     * @return bool if router can route http requests
     */
    public function HTTPAccessible(): bool;

    /**
     * @return void actual routing
     */
    public function Route();
}