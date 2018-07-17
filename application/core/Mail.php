<?php

namespace BronyCenter\Core;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer();
        $this->mail->CharSet = 'UTF-8';

        $this->mail->setFrom($_ENV['MAIL_ADDRESS'], $_ENV['MAIL_NAME']);

        if ($_ENV['MAIL_SMTP'] == "true") {
            $this->mail->isSMTP();

            if ($_ENV['MAIL_FAKESSL'] == "true") {
                $this->mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

            $this->mail->SMTPDebug = ($_ENV['MAIL_DEBUG'] == "true") ? 3 : 0;
            $this->mail->Host = $_ENV['MAIL_HOSTNAME'];
            $this->mail->Port = $_ENV['MAIL_PORT'];
            $this->mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $this->mail->SMTPAuth = $_ENV['MAIL_AUTHENTICATION'];
            $this->mail->Username = $_ENV['MAIL_USERNAME'];
            $this->mail->Password = $_ENV['MAIL_PASSWORD'];
        }
    }

    public function sendAsTemplate($email, $template, $arguments)
    {
        $message = file_get_contents('../application/mail/base.html');
        $subject = '';

        switch ($template) {
            case 'verification':
                $subject = 'Verify your e-mail address to use BronyCenter!';
                $display_name = $arguments['display_name'];
                $verification_link = $arguments['verification_link'];

                $content = file_get_contents('../application/mail/verification.html');
                $message = str_replace('<!-- #{Content} -->', $content, $message);

                $message = str_replace(
                    ['<!-- #{Subject} -->', '<!-- #{DisplayName} -->' , '<!-- #{VerificationLink} -->'],
                    [$subject, $display_name, $verification_link],
                    $message
                );

                break;
        }

        $this->mail->isHTML(true);

        $this->send($email, $subject, $message);
    }

    public function send($email, $subject, $message)
    {
        $this->mail->addAddress($email, $email);
        $this->mail->Subject  = $subject;
        $this->mail->Body     = $message;

        if (!$this->mail->send()) {
            // TODO: Display error or something...
        }
    }
}
