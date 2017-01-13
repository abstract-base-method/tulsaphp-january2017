<?php
/**
 * @package lemoney-php
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 */

namespace Lemoney\Services\Tools;


use Lemoney\Kernel;

trait CronTools
{
    /**
     * @var array $AvailableToRun if run then true else false
     */
    private $AvailableToRun = array();

    /**
     * @var Kernel $Kernel
     */
    protected $Kernel;

    /**
     * CronTools constructor.
     * @param Kernel $Kernel
     */
    public function __construct(Kernel &$Kernel)
    {
        $this->Kernel = $Kernel;
    }

    /**
     * @return bool check if available to run
     */
    public function AvailableToRun(): bool
    {
        $FinalState = false;
        foreach ($this->AvailableToRun as $Rule)
        {
            if ($Rule) {
                $FinalState = true;
                continue;
            }
            else {
                $FinalState = false;
                break;
            }
        }
        return $FinalState;
    }

    /**
     * Only Run if in development
     * @return CronTools trigger only if testing
     */
    public function TestOnly()
    {
        if (file_exists($this->Kernel->GetPath("conf|config.json.dev"))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * Only Run on Week Days
     * @return $this
     */
    public function IsWeekDay()
    {
        $date = date('N');
        if ($date < 6) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * Only Run On Weekends
     * @return CronTools instance
     */
    public function IsWeekEnd()
    {
        $date = date('N');
        if ($date > 5) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * Always Run CronTools
     * @return $this
     */
    public function AlwaysRun()
    {
        $this->AvailableToRun[] = true;
        return $this;
    }

    /**
     * @param int $Hour checks hour to be
     * @return $this
     */
    public function IsHour(int $Hour)
    {
        if ($Hour === intval(date('H'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * @param int $Minute checks to see if it is minute to run
     * @return $this
     */
    public function IsMinute(int $Minute)
    {
        if ($Minute === intval(date('i'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * @param int $Second checks if second is correct
     * @return $this
     */
    public function IsSecond(int $Second)
    {
        if ($Second === intval(date('s'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * @param int $Day checks if specific day
     * @return $this
     */
    public function IsDay(int $Day)
    {
        if ($Day === intval(date('d'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * @param int $Month checks Month
     * @return $this
     */
    public function IsMonth(int $Month)
    {
        if ($Month === intval(date('m'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }

    /**
     * @param int $Year checks if year
     * @return $this
     */
    public function IsYear(int $Year)
    {
        if ($Year === intval(date('Y'))) {
            $this->AvailableToRun[] = true;
        }
        else {
            $this->AvailableToRun[] = false;
        }
        return $this;
    }
}