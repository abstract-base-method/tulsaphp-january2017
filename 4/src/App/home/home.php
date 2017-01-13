<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace App\home;


use Lemoney\iFace\Router;
use Lemoney\Kernel;
use Lemoney\Services\Middleware\CLI;

class home implements Router
{
    use CLI;

    protected $Kernel;

    /**
     * Router constructor.
     * @param Kernel $Router
     */
    public function __construct(Kernel &$Router)
    {
        $this->Kernel = $Router;
    }

    /**
     * @return void actual routing
     */
    public function Route()
    {
        if ($this->Kernel->GetCLIState()) {
            if (!$this->Kernel->GetUnitTestingState()) {
                $this->PrintSuccess("Welcome to lemoney-php framework. Please let me know what you think!");
            }
        }
        else {
            $this->Kernel->View("Welcome.twig");
        }
    }

    /**
     * @return bool if router can route http requests
     */
    public function HTTPAccessible(): bool
    {
        return true;
    }
}