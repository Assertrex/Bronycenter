<?php

/**
 * Used for sending and receiving private messages
 *
 * @since Release 0.1.0
**/

namespace BronyCenter;

use DateTime;
use BronyCenter\Database;
use BronyCenter\Flash;
use BronyCenter\Utilities;

class Message
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
    **/
    private static $instance = null;

    /**
     * Place for instance of a database class
     *
     * @since Release 0.1.0
    **/
    private $database = null;

    /**
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
    **/
    private $flash = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
    **/
    private $utilities = null;

    /**
     * Place for instance of a user class
     *
     * @since Release 0.1.0
    **/
    private $user = null;

    /**
     * Place for instance of a validator class
     *
     * @since Release 0.1.0
    **/
    private $validator = null;

    /**
     * Place for a message encryption key
     *
     * @since Release 0.1.0
    **/
    private $encryptionKey = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
    **/
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->user = User::getInstance();
        $this->validator = Validator::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
    **/
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Message();
        }

        return self::$instance;
    }

    /**
     * Set an encryption key
     *
     * @since Release 0.1.0
     * @var string $key - Encryption key from a settings.ini file
     * @return string - Encrypted message
    **/
    public function setEncryptionKey($key)
    {
        // Check if encryption key is valid
        if (strlen($key) != 32 || !ctype_alnum($key)) {
            die(
                '<h1>Software configuration error!</h1>' .
                '<p>Messages encryption key is invalid.<br />' .
                'Update it\'s value in a <i>settings.ini</i> file.<br />' .
                'It needs to be a <b>32 characters</b> long <b>alphanumeric</b> string.</p>'
            );
        }

        // Set a new encryption key
        $this->encryptionKey = $key;
    }

    /**
     * Encrypt a message
     *
     * @since Release 0.1.0
     * @var string $message - Content of a message
     * @return string - Encrypted message
    **/
    private function doMessageEncrypt($message)
    {
        // Encrypt a message (not a good solution, but still better than nothing)
        $method = 'AES-256-CBC';
        $key = hash('sha256', $this->encryptionKey, true);
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($message, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext, $key, true);
        $message = $iv . $hash . $ciphertext;

        // Convert encrypted message into the format that DB can handle
        return base64_encode($message);
    }

    /**
     * Decrypt a message
     *
     * @since Release 0.1.0
     * @var string $encryptedMessage - Message in an encrypted version
     * @return string - Decrypted message
    **/
    private function doMessageDecrypt($encryptedMessage)
    {
        // Decode a message from base64
        $encryptedMessage = base64_decode($encryptedMessage);

        // Decrypt a message
        $method = 'AES-256-CBC';
        $iv = substr($encryptedMessage, 0, 16);
        $hash = substr($encryptedMessage, 16, 32);
        $ciphertext = substr($encryptedMessage, 48);
        $key = hash('sha256', $this->encryptionKey, true);

        // Return decrypted message, if decryption is possible
        if (hash_hmac('sha256', $ciphertext, $key, true) == $hash) {
            $message = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
            $message = $this->utilities->doEscapeString($message, false);
        } else {
            $message = 'Sorry, this message couldn\'t be decrypted!';
        }

        return $message;
    }

    /**
     * Get detailed array about all conversations
     *
     * @since Release 0.1.0
     * @return array|boolean - Details about fetched message
    **/
    public function getConversationsArray()
    {
        // Get all existing conversations with a current user
        $conversations = $this->database->read(
            'c.id, c.user_one_id, c.user_two_id, c.last_message_datetime, c.messages_count, m.sender_id, m.datetime, m.message, m.seen',
            'messages_simple_conversations c',
            'INNER JOIN (SELECT DISTINCT conversation_id, sender_id, datetime, message, seen FROM messages_simple_messages ORDER BY id DESC) m ON m.conversation_id = c.id WHERE c.user_one_id = ? OR c.user_two_id = ? GROUP BY m.conversation_id ORDER BY c.last_message_datetime DESC',
            [$_SESSION['account']['id'], $_SESSION['account']['id']]
        );

        // Format details about each message
        for ($i = 0; $i < count($conversations); $i++) {
            // Decrypt a message
            $conversations[$i]['message'] = $this->doMessageDecrypt($conversations[$i]['message']);

            // Add a message interval string
            $conversations[$i]['datetime_interval_string'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($conversations[$i]['datetime']));

            // Remember that last message was sent by a current user
            $conversations[$i]['sender_current_user'] = $_SESSION['account']['id'] == $conversations[$i]['sender_id'];

            // Check if a first person is a conversation creator
            $conversations[$i]['person_id'] = $conversations[$i]['user_one_id'];

            if ($conversations[$i]['person_id'] == $_SESSION['account']['id']) {
                $conversations[$i]['person_id'] = $conversations[$i]['user_two_id'];
            }

            // Get details about person in this conversation
            $conversations[$i]['user_details'] = $this->user->generateUserDetails($conversations[$i]['person_id']);

            // Escape HTML characters
            $conversations[$i]['user_details']['display_name'] = $this->utilities->doEscapeString($conversations[$i]['user_details']['display_name'], false);
        }

        return $conversations;
    }

    /**
     * Get an ID of a conversation between two selected users
     *
     * @since Release 0.1.0
     * @var integer $senderID - ID of a user that sends a message
     * @var integer $recipentID - ID of a user that receives a message
     * @return integer|boolean - ID of a conversation
    **/
    public function getConversationID($senderID, $recipentID)
    {
        $senderID = intval($senderID);
        $recipentID = intval($recipentID);

        // Check if both users ID's are a valid integers
        if (empty($senderID) || empty($recipentID)) {
            return false;
        }

        // Get an ID of existing conversation from a database
        $conversation = $this->database->read(
            'id',
            'messages_simple_conversations',
            'WHERE (user_one_id = ? AND user_two_id = ?) OR (user_two_id = ? AND user_one_id = ?)',
            [$senderID, $recipentID, $senderID, $recipentID],
            false
        );

        // Create a new conversation, if this is a first message
        if (empty($conversation)) {
            $conversationID = $this->database->create(
                'user_one_id, user_two_id',
                'messages_simple_conversations',
                '',
                [$senderID, $recipentID]
            );
        } else {
            $conversationID = $conversation['id'];
        }

        return $conversationID;
    }

    /**
     * Get messages for a selected conversation
     *
     * @since Release 0.1.0
     * @var integer $conversationID - ID of a selected conversation
     * @var integer $messagesLimit - Amount of messages to fetch
     * @var integer|null $messagesLastID - ID of a last fetched message
     * @return array|boolean - Details about fetched message
    **/
    public function getMessages($conversationID, $messagesLimit, $messagesLastID = null)
    {
        $conversationID = intval($conversationID);

        // Check if conversation ID is a valid integers
        if (empty($conversationID)) {
            return false;
        }

        // Fetch messages from a database
        $messages = $this->database->read(
            'conversation_id, sender_id, datetime, message',
            'messages_simple_messages',
            'WHERE conversation_id = ? ORDER BY id DESC LIMIT ?',
            [$conversationID, $messagesLimit]
        );

        // Check if conversation exists
        if (empty($messages)) {
            $this->flash->error('Selected conversation doesn\'t exist or is empty.');
            return false;
        }

        // Decode and decrypt each message
        for ($i = 0; $i < count($messages); $i++) {
            // Decrypt a message
            $messages[$i]['message'] = $this->doMessageDecrypt($messages[$i]['message']);

            // Remember that the sender is a current user
            $messages[$i]['sender_current_user'] = $_SESSION['account']['id'] == $messages[$i]['sender_id'];
        }

        return $messages;
    }

    /**
     * Get a selected message
     *
     * @since Release 0.1.0
     * @var integer $messageID - ID of a selected message
     * @return array|boolean - Details about fetched message
    **/
    public function getMessageSelected($messageID)
    {
        if (empty(intval($messageID))) {
            return false;
        }

        // Fetch a message details
        $message = $this->database->read(
            'conversation_id, sender_id, datetime, message',
            'messages_simple_messages',
            'WHERE id = ?',
            [$messageID],
            false
        );

        // Check if message exists
        if (empty($message)) {
            $this->flash->error('Selected message does not exist.');
            return false;
        }

        // Decrypt a message
        $message['message'] = $this->doMessageDecrypt($message['message']);

        return $message;
    }

    /**
     * Try to send a message
     *
     * @since Release 0.1.0
     * @var integer $id Recipent of a message
     * @var string $message Content of a message
     * @return integer|boolean ID of a created message
    **/
    public function doMessageSend($id, $message)
    {
        // Check if recipent ID is valid
        if (empty(intval($id))) {
            return false;
        }

        // Cut too long messages
        $message = substr($message, 0, 1000);

        // Check if a private message content is valid
        if (!$this->validator->isPrivateMessageValid($message)) {
            return false;
        }

        // Encrypt a message
        $message = $this->doMessageEncrypt($message);

        // Get a conversation ID
        $conversationID = $this->getConversationID($_SESSION['account']['id'], intval($id));

        // Store current datetime
        $currentDatetime = $this->utilities->getDatetime();

        // Insert a message to a database
        $messageID = $this->database->create(
            'conversation_id, sender_id, datetime, message',
            'messages_simple_messages',
            '',
            [$conversationID, $_SESSION['account']['id'], $currentDatetime, $message]
        );

        // Check if message has been successfully added
        if (empty($messageID)) {
            return false;
        }

        // Get amount of conversation messages
        $conversationMessagesCount = $this->database->read(
            'messages_count',
            'messages_simple_conversations',
            'WHERE id = ?',
            [$conversationID],
            false
        )['messages_count'];

        // Update conversation details
        $this->database->update(
            'last_message_datetime, messages_count',
            'messages_simple_conversations',
            'WHERE id = ?',
            [$currentDatetime, $conversationMessagesCount++, $conversationID]
        );

        // Return a message ID or 0 if failed
        return $messageID;
    }
}
