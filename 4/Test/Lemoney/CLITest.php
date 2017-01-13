<?php
/**
 * @package lemoney-php
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 */

namespace Lemoney;

class CLITest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testTask()
    {
        // Test that command line tool runs the default task module
        exec("lemoney task run all", $Output, $ReturnStatus);
        $this->assertTrue(($ReturnStatus === 0? true : false));
    }

    public function testFalseTask()
    {
        // Test that command line tool throws exit code one for unrecognized commands
        exec("lemoney test", $Output, $ReturnStatus);
        $this->assertTrue(($ReturnStatus === 1? true : false));
    }
}
