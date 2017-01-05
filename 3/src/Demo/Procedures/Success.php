<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/5/17
 * Project: 2017.January
 */

namespace Demo\Procedures;


class Success implements \Demo\iFace\Success
{

    public function SuccessScreen()
    {
        $path = __FILE__;
        $path = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($path);
        array_pop($path);
        array_pop($path);
        array_pop($path);
        $path = implode(DIRECTORY_SEPARATOR, $path);
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($path.'/View'));
        echo $twig->render('Success.twig');
    }
}