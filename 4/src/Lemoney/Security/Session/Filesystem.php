<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Security\Session;


use Lemoney\iFace\SessionHandler;

/**
 * Class Filesystem
 * @package Lemoney\Security\Session
 */
class Filesystem implements \SessionHandlerInterface, SessionHandler
{
    use \Lemoney\Services\Tools\Filesystem;

    private $Sessions;

    /**
     * Filesystem constructor. for session handling
     */
    public function __construct()
    {
        $sessionFile = $this->GetPath('storage|session|sessions.json');
        if (file_exists($sessionFile)) {
            $this->Sessions = json_decode(file_get_contents($sessionFile), true);
        } else {
            $this->Sessions = array();
        }
    }

    /**
     * finish out request with session storage
     */
    public function __destruct()
    {
        file_put_contents($this->GetPath('storage|session|sessions.json'), json_encode($this->Sessions, JSON_PRETTY_PRINT));
    }

    /**
     * Read session data
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     * @param string $session_id The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function read($session_id)
    {
        if (isset($this->Sessions[$session_id])) {
            return $this->Decrypt($this->Sessions[$session_id]['data'], $session_id);
        } else {
            return "";
        }
    }

    /**
     * @param string $data
     * @param string $iv
     * @param string $Method
     * @return string should return decrypted cipher text from Encrypt
     */
    function Decrypt(string $data, string $iv, $Method = 'AES-256-CBC'): string
    {
        $iv = substr($iv, 0, 16);
        $dec = openssl_decrypt($data, $Method, file_get_contents($this->GetPath("storage|session|keyfile")), 0, $iv);
        if (is_string($dec)) {
            return $dec;
        } else {
            throw new \RuntimeException('lemoney-php Session Handler -- Decryption Failed -- ' . __METHOD__);
        }
    }

    /**
     * Close the session
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function close()
    {
        file_put_contents($this->GetPath('storage|session|sessions.json'), json_encode($this->Sessions, JSON_PRETTY_PRINT));
        return true;
    }

    /**
     * Destroy a session
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($session_id)
    {
        $found = false;
        foreach ($this->Sessions as $key => $value) {
            if ($key === $session_id) {
                unset($this->Sessions[$key]);
                $found = true;
                break;
            } else {
                continue;
            }
        }
        return $found;
    }

    /**
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $maxlifetime <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function gc($maxlifetime)
    {
        foreach ($this->Sessions as $key => $value) {
            if ($value['max'] === time() - $maxlifetime) {
                unset($this->Sessions[$key]);
            }
        }
        return true;
    }

    /**
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * Write session data
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function write($session_id, $session_data)
    {
        $this->Sessions[$session_id]['max'] = time();
        $this->Sessions[$session_id]['data'] = $this->Encrypt($session_data, $session_id);
        return true;
    }

    /**
     * @param string $data
     * @param string $iv
     * @param string $Method
     * @return string should return cipher text
     */
    function Encrypt(string $data, string $iv, string $Method = 'AES-256-CBC'): string
    {
        $iv = substr($iv, 0, 16);
        $enc = openssl_encrypt($data, $Method, file_get_contents($this->GetPath("storage|session|keyfile")), 0, $iv);
        if (is_string($enc)) {
            return $enc;
        } else {
            throw new \RuntimeException('lemoney-php Session Handler -- Encryption Failed -- ' . __METHOD__);
        }
    }
}
