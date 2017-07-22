<?php

/**
 * Class used for actions with user's messages.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Message
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * Object of a database class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $database = null;

    /**
     * Object of an user class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $user = null;

    /**
     * Object of a validate class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $validate = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     * @var object $o_database Object of a database class.
     * @var object $o_user Object of an user class.
     * @var object $o_validate Object of a validate class.
     */
    public function __construct($o_system, $o_database, $o_user, $o_validate)
    {
        // Store required classes objects in a properties.
        $this->system = $o_system;
        $this->database = $o_database;
        $this->user = $o_user;
        $this->validate = $o_validate;
    }

    /**
     * Send a message in a specified conversation.
     *
     * @since 0.1.0
     * @var null|integer $conversationID ID of the selected conversation.
     * @var null|string $message Content of the message sent by user.
     * @var null|integer $messagesCount Amount of messages in the conversation.
     * @return boolean Result of this method.
     */
    public function send($conversationID, $message, $messagesCount) {
        // Check if message content is not empty.
        if (empty($message)) {
            // Show a failed system message if message is empty.
            $this->system->setMessage(
                'error',
                'Message can\'t be empty!'
            );

            return false;
        }

        // Store required variables.
        $userID = $_SESSION['account']['id'];
        $message = htmlspecialchars($_POST['message'], ENT_QUOTES);
        $datetime = $this->system->getDatetime();

        // Add message into the conversation in database.
        $messageID = $this->database->create(
            'conversation_id, user_id, datetime, message',
            'conversations_messages',
            '',
            [$conversationID, $userID, $datetime, $message]
        );

        // Check if message has been added successfully.
        if (empty($messageID)) {
            // Show a failed system message if message couldn't be added.
            $this->system->setMessage(
                'error',
                'Message can\'t be empty!'
            );

            return false;
        }

        // Update conversation messages counter in database.
        $this->database->update(
            'messages_count',
            'conversations',
            'WHERE id = ?',
            [$messagesCount + 1, $conversationID]
        );

        return true;
    }

    /**
     * Get messages from a specified conversation.
     *
     * @since 0.1.0
     * @var null|integer $conversationID ID of the selected conversation.
     * @return array Last 25 messages.
     */
    public function getMessages($conversationID) {
        // Get messages from a database.
        $messages = $this->database->read(
            'msg.user_id, usr.display_name, usr.avatar, msg.datetime, msg.message',
            'conversations_messages msg',
            'INNER JOIN users usr ON msg.user_id = usr.id WHERE msg.conversation_id = ? ORDER BY msg.id DESC LIMIT 25',
            [$conversationID]
        );

        // Reverse an array.
        $messages = array_reverse($messages);

        // Set required variables for each message.
        for ($i = 0; $i < count($messages); $i++) {
            // Store a last message send time interval.
            $messages[$i]['send_interval'] = $this->system->getDateIntervalString(
                $this->system->countDateInterval($messages[$i]['datetime'])
            );

            // Store a boolean about if current user has sent a message.
            $messages[$i]['current_sent'] = false;
            if ($messages[$i]['user_id'] == $_SESSION['account']['id']) { $messages[$i]['current_sent'] = true; }

            // Store an avatar path or switch to default if user has not set up.
            $messages[$i]['avatar'] = $messages[$i]['avatar'] ?? 'default';
        }

        // Return array with messages.
        return $messages;
    }

    /**
     * Get details about selected conversation if user is a part of it.
     *
     * @since 0.1.0
     * @var null|integer $conversationID ID of a selected conversation.
     * @return boolean|integer ID of a conversation or false on error.
     */
    public function getConversation($conversationID) {
        // Get details about a conversation if user is a part of it.
        $conversation = $this->database->read(
            'c.id, c.members_count, c.messages_count',
            'conversations c',
            'INNER JOIN conversations_members cu ON c.id = cu.conversation_id ' .
            'WHERE cu.user_id = ? AND c.id = ?',
            [$_SESSION['account']['id'], $conversationID]
        );

        // Check if a conversation has been found and if user is allowed to access it.
        if (count($conversation) === 0) {
            // Show failed system message if a conversation doesn't exist or user is not allowed to access.
            $this->system->setMessage(
                'error',
                'Conversation doesn\'t exist or you\'re not a member of it.'
            );

            return false;
        }

        // Return details of a selected conversation.
        return $conversation[0];
    }

    /**
     * Get an ID of a conversation with selected user.
     *
     * @since 0.1.0
     * @var null|integer $userID ID of a second user from conversation.
     * @return boolean|integer ID of a conversation or false on error.
     */
    public function getConversationID($userID) {
        // Check if user has selected himself.
        if ($userID === $_SESSION['account']['id']) {
            // Show failed system message if user has tried to chat with himself.
            $this->system->setMessage(
                'error',
                'You can\'t start a conversation with yourself.'
            );

            return false;
        }

        // Get a conversation ID.
        $conversationID = $this->database->read(
            'c.id',
            'conversations c',
            'INNER JOIN conversations_members cu ON c.id = cu.conversation_id ' .
            'WHERE (cu.user_id = ? OR cu.user_id = ?) AND c.members_count = 2 ' .
            'GROUP BY cu.conversation_id HAVING COUNT(*) = 2',
            [$_SESSION['account']['id'], $userID]
        );

        // Check if conversation is not existing.
        if (count($conversationID) === 0) {
            // Get an ID of a selected user.
            $userExists = $this->database->read(
                'id',
                'users',
                'WHERE id = ?',
                [$userID]
            );

            // Check if selected user exists.
            if (count($userExists) !== 1) {
                // Show failed system message if selected user is not existing.
                $this->system->setMessage(
                    'error',
                    'Requested user doesn\'t exist.'
                );

                return false;
            }

            // Create a new conversation for 2 members.
            $conversationID = $this->database->create(
                'members_count',
                'conversations',
                '',
                [2]
            );

            // Check if a conversation has been created successfully.
            if (empty($conversationID)) {
                // Show failed system message if new conversation couldn't be created.
                $this->system->setMessage(
                    'error',
                    'Couldn\'t start a new conversation.'
                );

                return false;
            }

            // Insert current user into conversation.
            $this->database->create(
                'conversation_id, user_id',
                'conversations_members',
                '',
                [$conversationID, $_SESSION['account']['id']]
            );

            // Insert user into conversation.
            $this->database->create(
                'conversation_id, user_id',
                'conversations_members',
                '',
                [$conversationID, $userID]
            );
        } // if

        // Check if more than one conversation has been found.
        else if (count($conversationID) > 1) {
            // Show failed system message if more than one conversation has been found.
            $this->system->setMessage(
                'error',
                'More than one conversation has been found. Please, notify website\'s administrator about that.'
            );

            return false;
        } // else if

        // Return ID of existing conversation or of a new conversation.
        return $conversationID[0]['id'] ?? $conversationID;
    }

    public function getConversations() {
        // Get list of conversations.
        $conversations = $this->database->read(
            'mbr.conversation_id, cvr.members_count, cvr.messages_count, usr.id, usr.display_name, usr.username, usr.last_online, usr.avatar, usr.account_type, msg.user_id AS sender_id, msg.datetime, msg.message',
            'conversations_members mbr',
            'INNER JOIN conversations cvr ON mbr.conversation_id = cvr.id
            INNER JOIN (SELECT DISTINCT id, conversation_id, user_id, datetime, message FROM conversations_messages ORDER BY id) msg ON mbr.conversation_id = msg.conversation_id
            INNER JOIN users usr ON mbr.user_id = usr.id
            WHERE mbr.conversation_id IN (SELECT conversation_id FROM conversations_members WHERE user_id = ?) AND mbr.user_id != ?
            GROUP BY mbr.conversation_id ORDER BY msg.id DESC',
            [$_SESSION['account']['id'], $_SESSION['account']['id']]
        );

        // Set required variables for each conversation.
        for ($i = 0; $i < count($conversations); $i++) {
            if ($conversations[$i]['messages_count'] != 0) {
                // Store a last message send time interval.
                $conversations[$i]['send_interval'] = $this->system->getDateIntervalString(
                    $this->system->countDateInterval($conversations[$i]['datetime'])
                );

                $conversations[$i]['user_online'] = $this->user->isOnline(null, $conversations[$i]['last_online']);

                // Store a boolean about if current user has sent last message.
                $conversations[$i]['current_sent'] = false;
                if ($conversations[$i]['sender_id'] == $_SESSION['account']['id']) { $conversations[$i]['current_sent'] = true; }

                // Store an avatar path or switch to default if user has not set up.
                $conversations[$i]['avatar'] = $conversations[$i]['avatar'] ?? 'default';
            }
        }

        return $conversations;
    }
}
