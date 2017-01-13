<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */

namespace Lemoney;


class KernelTest extends \PHPUnit_Framework_TestCase
{
    public function testKernelInitialization()
    {
        $Kernel = new Kernel();
        $this->assertTrue($Kernel instanceof Kernel);
    }

    public function testConfigurationGet()
    {
        // start kernel
        $Kernel = new Kernel();
        // get config paths
        $OldPath = $Kernel->GetPath('conf|config.json');
        $NewPath = $Kernel->GetPath('conf|config.json.dev');
        // copy the file
        exec("cp $OldPath $NewPath", $OutPut, $Return);
        if ($Return === 0) {
            // assert the operation completed
            $this->assertTrue($Return === 0);
            // reset the kernel
            $Kernel = null;
            // restart the kernel with the new dev config
            $Kernel = new Kernel();
            $Kernel->SetUserConfigurationDirective('Test', 'Var');
            // ensure simple setting
            $this->assertTrue(array_key_exists('Test', $Kernel->GetUserConfiguration()));
            // ensure overwrites work
            $this->assertTrue($Kernel->SetUserConfigurationDirective('Test', 'Var2'));
            $this->assertTrue(array_key_exists('Test', $Kernel->GetUserConfiguration()));
            $this->assertTrue($Kernel->GetUserConfiguration()['Test'] === 'Var2');
            // ensure int
            $this->assertTrue($Kernel->SetUserConfigurationDirective('Test', 2));
            $this->assertTrue(array_key_exists('Test', $Kernel->GetUserConfiguration()));
            $this->assertTrue($Kernel->GetUserConfiguration()['Test'] === 2);
            // ensure array
            $this->assertTrue($Kernel->SetUserConfigurationDirective('Test', array('var', 'foo', 'bar')));
            $this->assertTrue(array_key_exists('Test', $Kernel->GetUserConfiguration()));
            $this->assertTrue($Kernel->GetUserConfiguration()['Test'] === array('var', 'foo', 'bar'));
            // kill this Kernel
            $Kernel = null;
            // remove the dev config
            exec("rm $NewPath", $OutPut, $Return);
            // ensure the delete was successful
            $this->assertTrue($Return === 0);
        }
        else {
            throw new \RuntimeException("Lemoney PHP -- Creating Dev Config Failed!");
        }
    }
}
