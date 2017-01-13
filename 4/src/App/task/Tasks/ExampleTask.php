<?php
/**
 * Created by https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace App\task\Tasks;


use Lemoney;
use Lemoney\Kernel;
use Monolog\Logger;

class ExampleTask implements Lemoney\iFace\Task
{
    use Lemoney\Services\Tools\CronTools;

    protected $Kernel;

    public function __construct(Kernel &$Kernel)
    {
        $this->Kernel = $Kernel;
        $this->AlwaysRun();
    }

    public function __toString()
    {
        return "Example Task Printing To Log";
    }

    /**
     * @return bool if task executed then true else false
     */
    public function Execute(): bool
    {
        /**
         * Execution Logic runs here returning a status boolean
         */
        $file = $this->Kernel->GetPath("storage|log|development.log");
        if (file_exists($file)) {
            $OriginalLines = count(file($file));
        }
        else {
            $OriginalLines = 0;
        }
        for ($i = 1; $i < 11; $i++)
        {
            $this->Kernel->LogMessage("Test Message $i", Logger::DEBUG);
        }
        $file = $this->Kernel->GetPath("storage|log|development.log");
        $NewLines = count(file($file));
        return ($NewLines === $OriginalLines + 10? true : false );
    }
}