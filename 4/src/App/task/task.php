<?php
/**
 * Created by https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace App\task;


use Lemoney\iFace\Router;
use Lemoney\Kernel;
use Lemoney\Services\Middleware\CLI;

class task implements Router
{
    use CLI;

    protected $Kernel;

    public function __construct(Kernel &$kernel)
    {
        $this->Kernel = $kernel;
    }

    /**
     * @return void actual routing
     */
    public function Route()
    {
        if (!is_null($this->Kernel->GetRequest(2))) {
            if ($this->Kernel->GetRequest(2) === 'run') {
                if (!is_null($this->Kernel->GetRequest(3))) {
                    if ($this->Kernel->GetRequest(3) === 'all') {
                        $TaskList = array_diff(scandir($this->Kernel->GetPath("src|App|task|Tasks")), array('..', '.'));
                        foreach ($TaskList as $Task)
                        {
                            $Task = explode(".", $Task);
                            $Task = (array_key_exists(0, $Task)? $Task[0] : "");
                            $class = "App\\task\\Tasks\\" . $this->Kernel->Sanitize($Task);
                            if (class_exists($class)) {
                                $class = new $class($this->Kernel);
                                if ($class instanceof \Lemoney\iFace\Task && 0 < count(array_intersect(array("Lemoney\\Services\\Tools\\CronTools"), class_uses($class)))) {
                                    if ($class->AvailableToRun()) {
                                        if ($class->Execute()) {
                                            $this->PrintSuccess("Task: [" . $class . "] Completed");
                                        }
                                        else {
                                            $this->PrintError("Task: [" . $class . "] Failed To Complete");
                                            exit(1);
                                        }
                                    }
                                    else {
                                        $this->PrintWarning("Task: [" . $class . "] Not Scheduled To Run");
                                        exit(1);
                                    }
                                }
                                else {
                                    $this->PrintError("Task: [" . $class . "] Does Not Implement the required interfaces and traits");
                                    exit(1);
                                }
                            }
                            else {
                                $this->PrintError("Task File Not Found in App/task/Tasks");
                                exit(1);
                            }
                        }
                    }
                    else {
                        $class = "App\\task\\Tasks\\" . $this->Kernel->Sanitize($this->Kernel->GetRequest(3));
                        if (class_exists($class)) {
                            $class = new $class($this->Kernel);
                            if ($class instanceof \Lemoney\iFace\Task && 0 < count(array_intersect(array("Lemoney\\Services\\Tools\\CronTools"), class_uses($class)))) {
                                if ($class->AvailableToRun()) {
                                    if ($class->Execute()) {
                                        $this->PrintSuccess("Task: [" . $class . "] Completed");
                                        exit(0);
                                    }
                                    else {
                                        $this->PrintError("Task: [" . $class . "] Failed To Complete");
                                        exit(1);
                                    }
                                }
                                else {
                                    $this->PrintWarning("Task: [" . $class . "] Not Scheduled To Run");
                                    exit(1);
                                }
                            }
                            else {
                                $this->PrintError("Task: [" . $class . "] Does Not Implement the required interfaces and traits");
                                exit(1);
                            }
                        }
                        else {
                            $this->PrintError("Task File Not Found in App/task/Tasks");
                            exit(1);
                        }
                    }
                }
                else {
                    $this->PrintError("Task Unspecified");
                    exit(1);
                }
            }
            else {
                $this->PrintError("Unrecognized Sub Command");
                exit(1);
            }
        }
        else {
            $this->PrintError("Sub Command Not Issued");
            exit(1);
        }
    }

    /**
     * @return bool if router can route http requests
     */
    public function HTTPAccessible(): bool
    {
        return false;
    }
}