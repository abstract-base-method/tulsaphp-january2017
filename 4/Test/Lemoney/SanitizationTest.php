<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace Lemoney;


use Lemoney\Services\Tools\Sanitization;

class SanitizationTest extends \PHPUnit_Framework_TestCase
{
    use Sanitization;

    public function __construct()
    {
        parent::__construct();
    }

    public function testSaniziation()
    {
        $this->assertTrue(is_string($this->Sanitize("\\Hello")));
    }
}
