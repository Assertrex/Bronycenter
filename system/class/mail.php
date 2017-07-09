<?php

/**
 * Class used for sending e-mails.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Mail
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     */
    public function __construct($o_system)
    {
        // Store required class object in a property.
        $this->system = $o_system;
    }

    /**
     * Send an e-mail through a PHP.
     *
     * @since 0.1.0
     * @var string $email Destination e-mail address.
     * @var string $subject Title of e-mail.
     * @var string $message Content of e-mail.
     */
	public function sendMail($email, $subject, $message)
	{
		mail($email, $subject, $message);
	}

    /**
     * Send a registration e-mail with link for e-mail verification.
     *
     * @since 0.1.0
     * @var string $email Registered user's e-mail address.
     * @var string $displayName Registered user's display name.
     * @var string $emailHash Hash created for the e-mail.
     * @var string|integer $userID Registered user's ID.
     */
	public function sendRegistrationMail($email, $displayName, $emailHash, $userID)
	{
        // Path to the e-mail verification script.
        $link = $this->system->getSettings('system')['baseurl'] .
                'verifyemail.php?key=' . $emailHash . '&id=' . $userID;

        // Registration mail message subject.
        $subject = 'Hello, ' . $displayName . '! ' .
                   'Verify your e-mail to be able to use all features.';

        // Registration mail message content.
		$message =
'Welcome to the BronyCenter, ' . $displayName . '!

Remember that the website is in early development stage, and many things
will change in the future. You can report a bug or request a feature in
"Suggest a change" page.

Your account is now invisible for others and is in read-only state. Before
you\'ll be able to do anything with it, you need to prove that this e-mail
belongs to you.

Click on the link below to finish e-mail verification:
' $link . '

Why we need your e-mail?

The main reason is to prevent (or at least slow down) muted/banned people
from spamming and harassing people.

It\'s also needed for communication with you (for example to reset your
password or to notify you about something important. Don\'t worry, it won\'t
be shared with anyone else.

Third reason is just to make it easier for you to log in. You can also use it
instead of a username in case that you forget it.

To stop receiving e-mails from us (we rarely send emails anyway), just delete
your account or change in your account settings what you want to receive.';

        // Send a registration mail to the new user.
		$this->sendMail($email, $subject, $message);
	}
}
