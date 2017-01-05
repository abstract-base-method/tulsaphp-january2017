<!DOCTYPE html>
<html>
<head>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <title>Version 1</title>
</head>

<?php
/**
 * Created by James E. Bell Jr
 * Date: 12/29/16
 * Project: 2016.January
 */

/**
 * All logic located on index page along with all HTML
 */

// If Session is set
if (isset($_SESSION['email_addr'])) {
    echo "<body>
            <div class='row'>
                <div class='col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-sm-4 col-md-4 col-lg-4'>
                    <div class='panel panel-warning'>
                        <div class='panel-heading'>
                            You Already Registered
                        </div>
                        <div class='panel-body text-center'>
                            <h3>More? You want More Spam??</h3>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    ";
}
// if an email address has been posted
elseif (isset($_POST['email_addr'])) {
    // try to write to the DB
    try {
        $DB = new PDO("sqlite:mydb.sqlite");
        $Query = "
        INSERT INTO
          just_demo
        (
          address
        )
        VALUES 
        (
          :demo
        )
    ";
        $Query = $DB->prepare($Query);
        if ($Query->execute(array(':demo' => $_POST['email_addr']))) {
            $_SESSION['email_addr'] = $_POST['email_addr'];
            echo "<body>
            <div class='row'>
                <div class='col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-sm-4 col-md-4 col-lg-4'>
                    <div class='panel panel-success'>
                        <div class='panel-heading'>
                            Thanks
                        </div>
                        <div class='panel-body text-center'>
                            <p>SPAM AWAY BOYS!</p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    ";
        }
        else {
            echo "<body>
            <div class='row'>
                <div class='col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-sm-4 col-md-4 col-lg-4'>
                    <div class='panel panel-danger'>
                        <div class='panel-heading'>
                            something went wrong!
                        </div>
                        <div class='panel-body text-center'>
                            <p>Error</p>
                            <h4>" . json_encode($Query->errorInfo(), JSON_PRETTY_PRINT) . "</h4>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    ";
        }
    }
    // Catch the Error And Output it
    catch (PDOException $pdo) {
        echo "<body>
            <div class='row'>
                <div class='col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-sm-4 col-md-4 col-lg-4'>
                    <div class='panel panel-danger'>
                        <div class='panel-heading'>
                            something went wrong!
                        </div>
                        <div class='panel-body text-center'>
                            <p>Error</p>
                            <h4>" . json_encode($pdo->getMessage(), JSON_PRETTY_PRINT) . "</h4>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    ";
    }
}
// display the form if nothing has happened yet
else {
    echo "<body>
            <div class='row'>
                <div class='col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-sm-4 col-md-4 col-lg-4'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading'>
                            A Simple Little Thing
                        </div>
                        <div class='panel-body text-center'>
                            <p>Enter your email below to get email</p>
                            <form action='index.php' method='post' name='email_form'>
                                <div class=\"input-group\">
                                    <span class=\"input-group-addon\" id=\"basic-addon1\">@</span>
                                    <input type=\"text\" class=\"form-control\" placeholder=\"Email Address\" name='email_addr' aria-describedby=\"basic-addon1\">
                                </div> 
                                <input type='submit' class='btn btn-primary' value='Send Me More Spam'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    ";
}