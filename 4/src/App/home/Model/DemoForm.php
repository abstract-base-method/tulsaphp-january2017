<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/13/17
 * Project: 2017.January
 */

namespace App\home\Model;


use Lemoney\Kernel;

class DemoForm
{
    /**
     * @var \PDO $DB
     */
    private $DB;

    /**
     * @var Kernel $Kernel
     */
    private $Kernel;

    public function __construct(\PDO $DB, Kernel &$Kernel)
    {
        $this->DB = $DB;
        $this->Kernel = $Kernel;
    }

    public function Post_Request(): bool
    {
        return isset($_POST['email_addr']);
    }

    public function Session_Request(): bool
    {
        return isset($_SESSION['email_addr']);
    }

    public function Unknown_User()
    {
        $this->Kernel->View('demo|form.twig');
    }

    public function Process_Email(): bool
    {
        return true;
    }

}