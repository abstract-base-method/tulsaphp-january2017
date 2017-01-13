<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);
/*
 * Valid Selections:
 *   1: Filesystem - Use local session handling (stored in storage/session)
 *   2: Faker - Used for testing routes requiring authentication
 */
define('Lemoney_Session_Handler', 'Filesystem');
if (!file_exists(LEMONEY_INSTALL_PATH . "/storage/session/keyfile")) {
    file_put_contents(LEMONEY_INSTALL_PATH . "/storage/session/keyfile", base64_encode(openssl_random_pseudo_bytes(4096)));
}

/*
 * SESSION HANDLER HANDLER
 * This is where session handling begins
 */
// if the declared session handler isn't built in then use lemoney-php
if (defined('Lemoney_Session_Handler')) {
    if (Lemoney_Session_Handler !== 'Filesystem' && file_exists(LEMONEY_INSTALL_PATH . "/storage/session/sessions.json")) {
        unlink(LEMONEY_INSTALL_PATH . "/storage/session/sessions.json");
    }
    $class = "Lemoney\\Security\\Session\\" . Lemoney_Session_Handler;
    if (class_exists($class)) {
        $class = new $class;
        if ($class instanceof SessionHandlerInterface && $class instanceof Lemoney\iFace\SessionHandler)
            session_set_save_handler($class, true);
    } else {
        throw new RuntimeException('lemoney-php -- Unrecognized Session Handler -- Check config/session.php');
    }
} else {
    throw new RuntimeException('lemoney-php -- Session Handler Unset -- Check config/session.php');
}