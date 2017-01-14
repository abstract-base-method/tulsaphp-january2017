<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace App\home;


use App\home\Model\DemoForm;
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
            $Model = new DemoForm($this->Kernel->DatabaseString('Primary'), $this->Kernel);
            if ($Model->Post_Request()) {
                if ($Model->Process_Email()) {
                    $this->Kernel->View('demo|success.twig');
                }
                else {
                    $this->Kernel->View('demo|error.twig', ['Error' => 'Error In The System']);
                }
            }
            elseif ($Model->Session_Request()) {
                $this->Kernel->View('demo|registered.twig');
            }
            else {
                $Model->Unknown_User();
            }
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