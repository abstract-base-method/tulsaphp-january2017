<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace Lemoney;


use Lemoney\Services\Tools\Filesystem;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    use Filesystem;

    public function __construct()
    {
        parent::__construct();
    }

    public function testFilesystem()
    {
        $this->assertTrue(is_string($this->GetPath()));
        $this->assertTrue(is_dir($this->GetPath()));
    }
}
