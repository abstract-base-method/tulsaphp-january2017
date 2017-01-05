<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/2/17
 * Project: 2017.January
 */

namespace Demo\iFace;


interface Register
{
    public function AttemptRegistration(string $Email): bool;
}