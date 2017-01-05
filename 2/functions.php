<?php
/**
 * Created by James E. Bell Jr
 * Date: 1/1/17
 * Project: 2017.January
 */

// Main Logic Located Here
function StartProgram()
{
    // Include Head so all HTML Works
    include "html/head.html";
    // begin the logical switching
    if (isset($_SESSION['email_addr'])) {
        Registered();
    }
    elseif (isset($_POST['email_addr'])) {
        Register($_POST['email_addr']);
    }
    else {
        Registration();
    }
}

// A user has already registered
function Registered()
{
    include "html/registered.html";
}

function Register($EmailAddress)
{
    try {
        $PDO = new PDO("sqlite:mydb.sqlite");
        $Query = "
            INSERT INTO
              just_demo
            (address) 
            VALUES 
            (:address)
        ";
        $Query = $PDO->prepare($Query);
        if ($Query->execute(array(':address' => $EmailAddress))) {
            include "html/success.html";
        }
    } catch (PDOException $PDOException) {
        $Error = $PDOException->getMessage();
        include "html/error.html";
    }
}

function Registration()
{
    include "html/registration.html";
}

