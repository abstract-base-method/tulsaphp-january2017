<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/5/17
 * Project: 2017.January
 */

namespace Demo\Procedures;


class Register implements \Demo\iFace\Register
{

    public function AttemptRegistration(string $Email, bool $Test = false): bool
    {
        if ($Test) {
            $PDO = new \PDO("sqlite::memory:");
            $Query = "
                CREATE TABLE just_demo
                (
                    id INTEGER PRIMARY KEY NOT NULL,
                    address TEXT NOT NULL
                )
            ";
            $PDO->exec($Query);
            $Query = "
            INSERT INTO
              just_demo
            (address)
            VALUES
            (:address)
            ";
            $Query = $PDO->prepare($Query);
            if ($Query->execute(array(':address' => $Email))) {
                return true;
            } else {
                return false;
            }
        } else {
            $PDO = new \PDO("sqlite:mydb.sqlite");
            $Query = "
            INSERT INTO
              just_demo
            (address) 
            VALUES 
            (:address)
        ";
            $Query = $PDO->prepare($Query);
            if ($Query->execute(array(':address' => $Email))) {
                return true;
            } else {
                return false;
            }
        }
    }
}