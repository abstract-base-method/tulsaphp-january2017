<?php
/**
 * Created By: https://www.github.com/lemoney
 * Project: lemoney-php
 * License: Apache 2.0
 */
declare(strict_types = 1);


namespace Lemoney\Services\Tools;

/**
 * Class Email
 * @package Lemoney\Services
 */
class Email
{
    use Sanitization;

    /**
     * @var \PHPMailer $mail for sending mail
     */
    private $mail;

    /**
     * @var string
     */
    private $Host;

    /**
     * @var string
     */
    private $Port;

    /**
     * @var string
     */
    private $Username;

    /**
     * @var string
     */
    private $Password;

    /**
     * @var string
     */
    private $SendFrom;

    /**
     * Email constructor.
     * @param string $Host
     * @param string $Port
     * @param string $Username
     * @param string $Password
     * @param string $SendFrom
     */
    public function __construct(string $Host, string $Port, string $Username, string $Password, string $SendFrom)
    {
        $this->mail = new \PHPMailer();
        $this->Host = $Host;
        $this->Port = $Port;
        $this->Username = $Username;
        $this->Password = $Password;
        $this->SendFrom = $SendFrom;
    }

    /**
     * @param string $Address
     * @param string $Subject
     * @param string $Message
     * @param array $CC
     * @return bool if email send correctly
     */
    public function SendMail(string $Address, string $Subject, string $Message, array $CC = array()): bool
    {
        $this->mail->isSMTP();
        $this->mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
        $this->mail->Host = $this->Host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->Username;
        $this->mail->Password = $this->Password;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = $this->Port;
        $this->mail->setFrom($this->SendFrom);
        $this->mail->addAddress($Address);
        // Add CC accounts
        if (!empty($CC)) {
            foreach ($CC as $address) {
                if (!empty($address)) {
                    $this->mail->addCC($this->Sanitize($address));
                }
            }
        }
        $this->mail->isHTML(true);
        $this->mail->Subject = $this->Sanitize($Subject);
        $this->mail->Body = strip_tags($Message, '<h1><h2><h3><h4><h5><h6><p><strong><i>');
        return ($this->mail->send());
    }
}