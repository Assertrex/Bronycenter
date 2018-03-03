<?php

/**
* Used for sending and receiving private messages
*
* @since Release 0.1.0
*/

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
     */
    private static $instance = null;

    /**
     * Place for instance of a database class
     *
     * @since Release 0.1.0
     */
    private $database = null;

    /**
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
     */
    private $flash = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Place for instance of an user class
     *
     * @since Release 0.1.0
     */
    private $user = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->user = User::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Message();
        }

        return self::$instance;
    }

    /**
     * Try to send a message
     *
     * @since Release 0.1.0
     * @var integer $id Recipent of a message
     * @var string $message Content of a message
     * @var string $encryptionKey Encryption key from settings.ini file
     * @return boolean Result of this method
     */
    public function doSend($id, $message, $encryptionKey)
    {
        // Check if any of arguments is empty or invalid
        if (empty(intval($id)) || empty($message)) {
            return false;
        }

        // Cut too long messages
        $message = substr($message, 0, 1000);

        // Encrypt a message (not a good solution, but still better than nothing)
        $method = "AES-256-CBC";
        $key = hash('sha256', $encryptionKey, true);
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($message, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext, $key, true);
        $message = $iv . $hash . $ciphertext;

        // Convert encrypted message into the format that DB can handle
        $message = base64_encode($message);

        // Get a conversation ID
        $conversationID = $this->getConversationID($_SESSION['account']['id'], $id);

        // Store current datetime
        $currentDatetime = $this->utilities->getDatetime();

        // Insert a message to the database
        $messageID = $this->database->create(
            'conversation_id, sender_id, datetime, message',
            'messages_simple_messages',
            '',
            [$conversationID, $_SESSION['account']['id'], $currentDatetime, $message]
        );

        // Get amount of conversation messages
        $conversationMessagesCount = $this->database->read(
            'messages_count',
            'messages_simple_conversations',
            'WHERE id = ?',
            [$conversationID],
            false
        )['messages_count'];

        // Update conversation details
        $conversationUpdated = $this->database->update(
            'last_message_datetime, messages_count',
            'messages_simple_conversations',
            'WHERE id = ?',
            [$currentDatetime, $conversationMessagesCount + 1, $conversationID]
        );

        // Return a message ID or 0 if failed
        return $messageID;
    }

    public function getSelected($messageID, $encryptionKey) {
        // Get a message
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

        // Decode a message from base64
        $message['message'] = base64_decode($message['message']);

        // Decrypt a message
        $method = "AES-256-CBC";
        $iv = substr($message['message'], 0, 16);
        $hash = substr($message['message'], 16, 32);
        $ciphertext = substr($message['message'], 48);
        $key = hash('sha256', $encryptionKey, true);

        // Return decrypted message if decryption is possible
        if (hash_hmac('sha256', $ciphertext, $key, true) == $hash) {
            return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
        } else {
            return false;
        }
    }

    public function getConversationMessages($conversationID, $encryptionKey) {
        // Get messages
        $messages = $this->database->read(
            'conversation_id, sender_id, datetime, message',
            'messages_simple_messages',
            'WHERE conversation_id = ? ORDER BY id DESC LIMIT 50',
            [$conversationID]
        );

        // Check if conversation exists
        if (empty($messages)) {
            $this->flash->error('Selected conversation doesn\'t exist or is empty.');
            return false;
        }

        // Decode and decrypt each message
        for ($i = 0; $i < count($messages); $i++) {
            // Decode messages from base64
            $messages[$i]['message'] = base64_decode($messages[$i]['message']);

            // Decrypt messages
            $method = "AES-256-CBC";
            $iv = substr($messages[$i]['message'], 0, 16);
            $hash = substr($messages[$i]['message'], 16, 32);
            $ciphertext = substr($messages[$i]['message'], 48);
            $key = hash('sha256', $encryptionKey, true);

            // Return decrypted message if decryption is possible
            if (hash_hmac('sha256', $ciphertext, $key, true) == $hash) {
                $messages[$i]['message'] = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
            } else {
                $messages[$i]['message'] = 'Sorry, this message couldn\'t be decrypted!';
            }

            // Add a message interval string
            $messages[$i]['datetime_interval_string'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($messages[$i]['datetime']));

            // Remember that the sender is a current user
            $messages[$i]['sender_current_user'] = $_SESSION['account']['id'] == $messages[$i]['sender_id'];
        }

        return $messages;
    }

    public function getConversationID($senderID, $recipentID) {
        // Get an existing conversation from a database
        $conversation = $this->database->read(
            'id',
            'messages_simple_conversations',
            'WHERE (user_one_id = ? AND user_two_id = ?) OR (user_two_id = ? AND user_one_id = ?)',
            [$senderID, $recipentID, $senderID, $recipentID],
            false
        );

        // Create a new conversation if this is a first message
        if (empty($conversation)) {
            $conversationID = $this->database->create(
                'user_one_id, user_two_id, last_message_datetime',
                'messages_simple_conversations',
                '',
                [$senderID, $recipentID, '0000-00-00 00:00:00']
            );
        } else {
            $conversationID = $conversation['id'];
        }

        return $conversationID;
    }

    public function getConversations($encryptionKey) {
        // Get an existing conversation from a database
        $conversations = $this->database->read(
            'c.id, c.user_one_id, c.user_two_id, c.last_message_datetime, c.messages_count, m.sender_id, m.datetime, m.message, m.seen',
            'messages_simple_conversations c',
            'INNER JOIN (SELECT conversation_id, sender_id, datetime, message, seen FROM messages_simple_messages ORDER BY id DESC) m ON m.conversation_id = c.id WHERE c.user_one_id = ? OR c.user_two_id = ? GROUP BY m.conversation_id ORDER BY c.last_message_datetime DESC',
            [$_SESSION['account']['id'], $_SESSION['account']['id']]
        );

        // Decode and decrypt each message
        for ($i = 0; $i < count($conversations); $i++) {
            // Decode messages from base64
            $conversations[$i]['message'] = base64_decode($conversations[$i]['message']);

            // Decrypt messages
            $method = "AES-256-CBC";
            $iv = substr($conversations[$i]['message'], 0, 16);
            $hash = substr($conversations[$i]['message'], 16, 32);
            $ciphertext = substr($conversations[$i]['message'], 48);
            $key = hash('sha256', $encryptionKey, true);

            // Return decrypted message if decryption is possible
            if (hash_hmac('sha256', $ciphertext, $key, true) == $hash) {
                $conversations[$i]['message'] = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
            } else {
                $conversations[$i]['message'] = 'Sorry, this message couldn\'t be decrypted!';
            }

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
        }

        return $conversations;
    }
}
