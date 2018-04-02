<?php

/**
* Class used for sending e-mails
*
* @since Release 0.1.0
*/

namespace BronyCenter;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../lib/PHPMailer/PHPMailer.php');
require_once(__DIR__ . '/../lib/PHPMailer/SMTP.php');
require_once(__DIR__ . '/../lib/PHPMailer/Exception.php');

class Mail
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of a config class
     *
     * @since Release 0.1.0
     */
    private $o_config = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->o_config = Config::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false) {
        if (!self::$instance || $reset === true) {
            self::$instance = new Mail();
        }

        return self::$instance;
    }

    /**
     * Send an e-mail
     *
     * @since Release 0.1.0
     * @var string $email E-mail address of a recipent
     * @var string $subject Title of a message
     * @var string $message Content of a message
     * @return boolean Result of an e-mail sending
     */
    public function send($email, $subject, $message) {
        // Get website's settings
        $mailSettings = $this->o_config->getSettings('mail');

        // Create a PHPMailer instance
        $mail = new PHPMailer;

        // Set e-mail details
        $mail->setFrom($mailSettings['fromAddress'], $mailSettings['fromName']);
        $mail->addAddress($email, $email);
        $mail->Subject  = $subject;
        $mail->Body     = $message;

        // Log in to external SMTP server
        if ($mailSettings['useSMTP']) {
            $mail->isSMTP();

            // Use self-signed certificate on local environment
            if ($mailSettings['fakeSSL']) {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

            $mail->SMTPDebug = $mailSettings['enableDebug'] ? 3 : 0;
            $mail->Host = $mailSettings['hostname'];
            $mail->Port = $mailSettings['port'];
            $mail->SMTPSecure = $mailSettings['encryption'];
            $mail->SMTPAuth = $mailSettings['authentication'];
            $mail->Username = $mailSettings['username'];
            $mail->Password = $mailSettings['password'];
        }

        // Send an e-mail
        if (!$mail->send()) {
            echo 'Message was not sent.<br />';
            echo 'Mailer error: ' . $mail->ErrorInfo;
        }
    }

    /**
     * Send an registration e-mail
     *
     * @since Release 0.1.0
     * @var string $email E-mail address of a recipent
     * @var integer $accountID ID of a created user
     * @var string $displayName Display name of a created user
     * @var string $emailToken Token generated for use with an activation link
     * @return boolean Result of an e-mail sending
     */
    public function sendRegistration($email, $accountID, $displayName, $emailToken) {
        $verifyEmailURL = $_SERVER['REQUEST_SCHEME'] . '://' .
                          $_SERVER['HTTP_HOST'] .
                          str_replace('register.php', '', $_SERVER['REQUEST_URI']) .
                          'managemail.php?id=' . $accountID .
                          '&token=' . $emailToken .
                          '&func=verify';

        $this->send(
            $email,
            'Welcome to the BronyCenter!',
"Hello, $displayName!

It looks like you've just created an account on BronyCenter.
We're happy that you've decided to join us.

Before you'll be ready to do anything with it, you have to enable your account
first. To do that, you just have to click a link below to verify that you're an
owner of this e-mail address.

$verifyEmailURL

Why? We need your e-mail address to:
- prevent trolls from creating multiaccounts,
- send you important notifications about website and your account,
- allow you easily reset a forgotten password

We promise that we won't spam you with random e-mails and we won't give your
e-mail address to anyone else.

If you haven't created this account, then someone else could have done this.
In this case just skip this e-mail. We won't send you anything more until you
verify it."
        );
    }
}
