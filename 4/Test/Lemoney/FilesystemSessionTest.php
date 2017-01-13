<?php
/**
 * Created by https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace Lemoney\Security\Session;


class FilesystemSessionTest extends \PHPUnit_Framework_TestCase
{
    private $Handler;

    public function __construct()
    {
        parent::__construct();
        $this->Handler = new Filesystem();
    }

    public function testEncryptString()
    {
        $CheckText = "Hello, World";
        $StartingText = "Hello, World";
        $StartingText = $this->Handler->Encrypt($StartingText, 'abcdefghijklmnop');
        $StartingText = $this->Handler->Decrypt($StartingText, 'abcdefghijklmnop');
        $this->assertTrue($CheckText === $StartingText);
    }

    public function testEncryptArray()
    {
        $CheckText = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $StartingText = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $StartingText = $this->Handler->Encrypt($StartingText, 'abcdefghijklmnop');
        $StartingText = $this->Handler->Decrypt($StartingText, 'abcdefghijklmnop');
        $this->assertTrue($CheckText === $StartingText);
    }

    public function testEncryptDuplicate()
    {
        $CheckText1 = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $CheckText2 = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $this->assertTrue($CheckText1 === $CheckText2);
        $StartingText1 = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $StartingText2 = serialize(array(
            'test' => 'output',
            7 => 'dog',
            'blue',
            'sky'
        ));
        $this->assertTrue($StartingText1 === $StartingText2);
        $this->assertTrue($CheckText1 === $StartingText1);
        $this->assertTrue($CheckText2 === $StartingText2);
        $StartingText1 = $this->Handler->Encrypt($StartingText1, 'abcdefghijklmnop');
        $StartingText2 = $this->Handler->Encrypt($StartingText2, 'abcdefghijklmnop');
        $this->assertTrue($StartingText1 === $StartingText2);
        $StartingText1 = $this->Handler->Decrypt($StartingText1, 'abcdefghijklmnop');
        $StartingText2 = $this->Handler->Decrypt($StartingText2, 'abcdefghijklmnop');
        $this->assertTrue($StartingText1 === $StartingText2);
        $this->assertTrue($CheckText1 === $StartingText1);
        $this->assertTrue($CheckText2 === $StartingText2);
    }
}
