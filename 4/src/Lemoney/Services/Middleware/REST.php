<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Services\Middleware;


trait REST
{

    /**
     * @param int $status HTTP Status Code
     * @return string HTTP Status Code Description
     */
    final function request_status (int $status): string
    {
        $status_codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return (isset($status_codes[$status])) ? $status_codes[$status] : '';
    }

    /**
     * @param int $status HTTP Status Code
     * @param mixed $body Response Body NOTE: Will be json_encode with Angular JSON prefix
     * @param bool $angString include Angular Response String
     * @param string $content_type HTTP Content Type
     * @return void
     */
    public function sendResponse (int $status = 200, $body = "", bool $angString = false, string $content_type = "application/json")
    {
        $status_head = "HTTP/1.1 " . $status . " " . $this->request_status($status);
        header($status_head);
        header("Content-type: " . $content_type);
        /*
         * Since this is an angular application we insert the angular JSON string to protect against JSON attacks
         */
        if ($angString) {
            echo ")]}',\n" . json_encode($body);
        } else {
            echo json_encode($body);
        }
    }
}