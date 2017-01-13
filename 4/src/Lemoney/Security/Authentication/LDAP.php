<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Security\Authentication;


use Lemoney\iFace\Authentication;
use Lemoney\Services\Tools\Sanitization;

class LDAP implements Authentication
{
    use Sanitization;

    private $conf;

    /**
     * Authentication constructor.
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        $this->conf = $conf;
    }

    /**
     * @return bool if user is authentic and should set $this->CSRF_Token
     */
    function Authenticate(): bool
    {
        if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
            $username = $_SESSION['username'];
            $password = $_SESSION['password'];
        } elseif (isset($_POST['username']) && isset($_POST['password'])) {
            $username = strtolower($this->Sanitize($_POST['username']));
            $password = $this->Sanitize($_POST['password']);
        } else {
            return false;
        }
        $ldapConnect = ldap_connect($this->conf['Server']);
        if ($ldapConnect) {
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConnect, LDAP_OPT_REFERRALS, 0);
            $bind = ldap_bind($ldapConnect, $this->conf['DN'] . "\\" . $username, $password);
            if ($bind) {
                $result = ldap_search($ldapConnect, $this->conf['Tree'], str_replace('%Username%', $username, $this->conf['ADSearchString']));
                if (ldap_count_entries($ldapConnect, $result) === 1) {
                    $attrs = ldap_get_attributes($ldapConnect, ldap_first_entry($ldapConnect, $result));
                    $fullname = $attrs['displayName'];
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    $_SESSION['fullname'] = $fullname[0];
                    return true;
                } else {
                    trigger_error("lemoney-php -- LDAP Entries Not One -- Check configuration");
                    if (!defined('LPHP_Auth_DenialReason')) {
                        define('LPHP_Auth_DenialReason', 'LDAP Tree search not 1');
                    }
                    return false;
                }
            } else {
                trigger_error("lemoney-php -- LDAP Login Failed -- Check configuration");
                if (!defined('LPHP_Auth_DenialReason')) {
                    define('LPHP_Auth_DenialReason', 'Username/Password Authentication Failure');
                }
                return false;
            }
        } else {
            trigger_error("lemoney-php -- Connection to LDAP Server Failed -- Check configuration");
            if (!defined('LPHP_Auth_DenialReason')) {
                define('LPHP_Auth_DenialReason', 'Connection to server failed');
            }
            return false;
        }
    }
}