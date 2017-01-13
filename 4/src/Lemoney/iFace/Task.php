<?php
/**
 * @package lemoney-php
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 */

namespace Lemoney\iFace;

use Lemoney\Kernel;

interface Task
{
    /**
     * Task constructor.
     * @param Kernel $Kernel ensure kernel is passed in
     */
    public function __construct(Kernel &$Kernel);

    /**
     * @return bool if task executed then true else false
     */
    public function Execute(): bool;

    /*
     * Ensure string representation is present
     */
    public function __toString();
}