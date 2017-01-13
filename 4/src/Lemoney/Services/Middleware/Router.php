<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Services\Middleware;


use Lemoney\Kernel;
use App;

/**
 * Class Router
 * @package App
 */
class Router implements \Lemoney\iFace\Router
{
    use REST;
    use CLI;

    /**
     * @var Kernel
     */
    protected $Kernel;

    /**
     * Router constructor.
     * @param Kernel $Kernel
     * @param bool $OverrideRouter if testing for example disable automatic routing
     */
    public function __construct(Kernel &$Kernel, bool $OverrideRouter = false)
    {
        $this->Kernel = $Kernel;
        if ($OverrideRouter === false) {
            $this->Route();
        }
    }

    /**
     * @Purpose To Serve Entry Points to application
     */
    public static function EntryPoint()
    {
        $Kernel = new Kernel();
        new Router($Kernel);
    }

    /**
     *  Highest level Router for application stack
     */
    public function Route()
    {
        if ($this->Kernel->GetCLIState()) {
            $this->cli();
        }
        else {
            $this->http();
        }
    }

    /**
     * @return bool if router can route http requests
     */
    public function HTTPAccessible(): bool
    {
        return true;
    }

    /*
     * Handles Ensuring Routers can route HTTP Requests
     */
    private function http()
    {
        if (!is_null($this->Kernel->GetRequest(1)) && $this->Kernel->GetRequest(1) === 'logout') {
            $_SESSION = array();
            session_destroy();
            header("Location: /");
        }
        elseif ($this->Kernel->GetAuthenticationStatus()) {
            if (!is_null($this->Kernel->GetRequest(1))) {
                $class = "App\\" . $this->Kernel->GetRequest(1) . "\\" . $this->Kernel->GetRequest(1);
                if (class_exists($class)) {
                    $class = new $class($this->Kernel);
                    if ($class instanceof \Lemoney\iFace\Router) {
                        if ($class->HTTPAccessible()) {
                            $class->Route();
                        }
                        else {
                            $class = new App\home\home($this->Kernel);
                            $class->Route();
                        }
                    } else {
                        $class = new App\home\home($this->Kernel);
                        $class->Route();
                    }
                } else {
                    $class = new App\home\home($this->Kernel);
                    $class->Route();
                }
            } else {
                $class = new App\home\home($this->Kernel);
                $class->Route();
            }
        } else {
            if (!is_null($this->Kernel->GetRequest(1)) && $this->Kernel->GetRequest(1) === 'api') {
                $this->sendResponse(403, "Unauthorized request. Please transmit credentials and try again");
            } else {
                if (defined('LPHP_Auth_DenialReason')) {
                    $this->Kernel->View("Login.twig", array("LoginError" => LPHP_Auth_DenialReason));
                } else {
                    $this->Kernel->View("Login.twig");
                }
            }
        }
    }

    /**
     * Handles Ensuring Routers can route CLI Requests
     */
    private function cli()
    {
        if (!is_null($this->Kernel->GetRequest(1)) && $this->Kernel->GetRequest(1) === 'logout') {
            $_SESSION = array();
            session_destroy();
            header("Location: /");
        }
        elseif ($this->Kernel->GetAuthenticationStatus()) {
            if (!is_null($this->Kernel->GetRequest(1))) {
                $class = "App\\" . $this->Kernel->GetRequest(1) . "\\" . $this->Kernel->GetRequest(1);
                if (class_exists($class)) {
                    $class = new $class($this->Kernel);
                    if ($class instanceof \Lemoney\iFace\Router && 0 < count(array_intersect(array("Lemoney\\Services\\Middleware\\CLI"), class_uses($class)))) {
                        $class->Route();
                    } else {
                        $this->PrintError("Module Does Not Implement CLI Toolkit Please Try Again");
                        exit(1);
                    }
                } else {
                    $this->PrintError("Module Not Found. Please Check Your Documentation and Try Again");
                    exit(1);
                }
            } else {
                $this->PrintError("Command Provided Please Try Again");
                exit(1);
            }
        } else {
            $this->PrintError("Unauthorized Request Please Try Again");
            exit(1);
        }
    }
}