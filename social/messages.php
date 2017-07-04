<?php
// Allow access only for logged users
$loginRequired = true;

require_once('../system/inc/init.php');

$isSelected = false;
$userId = intval($_GET['u'] ?? 0);
$conversationId = intval($_GET['c'] ?? 0);
$messagesCount = 0;
$membersCount = 0;

// Check if user has requested conversation
if (!empty($userId) || !empty($conversationId)) {
    $isSelected = true;

    // Display error if user has not verified own e-mail address
    if (!$emailVerified) {
        $o_system->setMessage('error', 'You need to verify your e-mail address before you\'ll be able to see messages.');
        header('Location: messages.php');
        die();
    }

    // Display error if both values are set up
    if (!empty($userId) && !empty($conversationId)) {
        $o_system->setMessage('error', 'Your conversation link is invalid! It can\'t point to both user and conversation at same time.');
        header('Location: messages.php');
        die();
    }

    // Get details about selected conversation
    if (!empty($conversationId)) {
        // TODO Make conversations to work
        $o_system->setMessage('error', 'Conversations with more than two members are not allowed yet!');
        header('Location: messages.php');
        die();

        $conversation = $o_database->read(
            'c.id, c.members_count, c.messages_count',
            'conversations c',
            'INNER JOIN conversations_members cu ON c.id = cu.conversation_id WHERE cu.user_id = ? AND c.id = ?',
            [$_SESSION['account']['id'], $conversationId]
        );

        // Display error if conversation is not existing or if user is not allowed to access it
        if (count($conversation) === 0) {
            $o_system->setMessage('error', 'Conversation doesn\'t exist or you\'re not a member of it.');
            header('Location: messages.php');
            die();
        }

        $messagesCount = $conversation[0]['messages_count'];
        $membersCount = $conversation[0]['members_count'];
    } else if (!empty($userId)) {
        // Display error if user has selected themselfes
        if ($userId === $_SESSION['account']['id']) {
            $o_system->setMessage('error', 'You can\'t start a conversation with yourself.');
            header('Location: messages.php');
            die();
        }

        $conversation = $o_database->read(
            'c.id, c.members_count, c.messages_count',
            'conversations c',
            'INNER JOIN conversations_members cu ON c.id = cu.conversation_id WHERE (cu.user_id = ? OR cu.user_id = ?) AND c.members_count = 2 GROUP BY cu.conversation_id HAVING COUNT(*) = 2',
            [$_SESSION['account']['id'], $userId]
        );

        // Check if conversation already exists
        if (count($conversation) === 0) {
            // Check if user exists
            $userExists = $o_database->read(
                'id',
                'users',
                'WHERE id = ?',
                [$userId]
            );

            // Display an error if user doesn't exist
            if (count($userExists) !== 1) {
                $o_system->setMessage('error', 'Requested user doesn\'t exist.');
                header('Location: messages.php');
                die();
            }

            // Create a conversation
            $conversationId = $o_database->create(
                'members_count',
                'conversations',
                '',
                [2]
            );

            // Insert you into conversation
            $o_database->create(
                'conversation_id, user_id',
                'conversations_members',
                '',
                [$conversationId, $_SESSION['account']['id']]
            );

            // Insert user into conversation
            $o_database->create(
                'conversation_id, user_id',
                'conversations_members',
                '',
                [$conversationId, $userId]
            );

            $conversationId = intval($conversationId);
            $messagesCount = 0;
            $membersCount = 2;
        } else if (count($conversation) > 1) {
            $o_system->setMessage('error', 'More than one conversation with same users has been found. Please, notify JustAnotherBrony about that.');
            header('Location: messages.php');
            die();
        } else {
            $conversationId = intval($conversation[0]['id']);
            $membersCount = intval($conversation[0]['members_count']);
            $messagesCount = $conversation[0]['messages_count'];
        }
    }

    // Get user details if it's coversation of two members
    if ($membersCount === 2) {
        $user = $o_database->read(
            'id, display_name, username, last_online',
            'users',
            'WHERE id = ?',
            [intval($_GET['u'])]
        )[0];

        $lastOnlineInterval = $o_system->countDateInterval($user['last_online']);
        if ($lastOnlineInterval < 90) {
            $lastOnline = 'Just now';
        } else {
            $lastOnline = $o_system->getDateIntervalString($lastOnlineInterval);
        }
    }
}

// Send a message
if (!empty($_POST['submit']) && $_POST['submit'] === 'sendmessage' && $isSelected) {
    // Display error if user has not verified own e-mail address
    if (!$emailVerified) {
        $o_system->setMessage('error', 'You need to verify your e-mail address before you\'ll be able to send messages.');
        header('Location: messages.php');
        die();
    }

    if (empty($_POST['message'])) {
        $o_system->setMessage('error', 'Message can\'t be empty!');
        header('Refresh:0');
        die();
    }

    $o_database->create(
        'conversation_id, user_id, datetime, message',
        'conversations_messages',
        '',
        [$conversationId, $_SESSION['account']['id'], $o_system->getDatetime(), htmlspecialchars($_POST['message'], ENT_QUOTES)]
    );

    $o_database->update(
        'messages_count',
        'conversations',
        'WHERE id = ?',
        [$messagesCount + 1, $conversationId]
    );

    // Refresh after submitting message
    header('Refresh:0');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Messages :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <style type="text/css">
    .conversation-row, .message-row { border-bottom: 1px solid #EEE; }
    .conversation-row:last-child, .message-row:last-child { border: 0; }
    </style>
</head>
<body>
    <?php require_once('inc/header.php'); ?>

    <div class="container">
        <?php if ($isSelected) { ?>
        <section class="my-5">
            <h3>Your chatting with: <?php echo $user['display_name']; ?> <small>(@<?php echo $user['username']; ?>)</small></h3>
            <p>Last seen: <?php echo $lastOnline; ?></p>
        </section>
        <section class="my-5">
            <?php
            // Get messages from user
            $messages = $o_database->read(
                'msg.user_id, usr.display_name, usr.avatar, msg.datetime, msg.message',
                'conversations_messages msg',
                'INNER JOIN users usr ON msg.user_id = usr.id WHERE msg.conversation_id = ? ORDER BY msg.id DESC LIMIT 10',
                [$conversationId]
            );

            // List messages from user here
            foreach (array_reverse($messages) as $message) {
                $sendInterval = $o_system->getDateIntervalString($o_system->countDateInterval($message['datetime']));
                $ownMessage = false;
                if ($message['user_id'] == $_SESSION['account']['id']) { $ownMessage = true; }
                $message['avatar'] = $message['avatar'] ?? 'default';
            ?>

            <div class="message-row py-3">
                <div class="d-flex align-items-center">
                    <div class="pr-3">
                        <img src="../media/avatars/<?php echo $message['avatar']; ?>/64.jpg" class="rounded" />
                    </div>
                    <div style="width: 100%;">
                        <div class="d-flex justify-content-start mb-2">
                            <?php if (!$ownMessage) { ?>
                            <div class="font-weight-bold"><a href="profile.php?u=<?php echo $message['user_id']; ?>"><?php echo $message['display_name']; ?></a></div>
                            <?php } else { ?>
                            <div class="font-weight-bold">You</div>
                            <?php } ?>
                            <div class="ml-auto text-muted"><small style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $message['datetime']; ?> (UTC)"><?php echo $sendInterval; ?> <i class="fa fa-clock-o"></i></small></div>
                        </div>
                        <div>
                            <?php echo $message['message']; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            }
            ?>
        </section>
        <section class="my-5">
            <form method="post">
                <div class="form-group">
                    <textarea class="form-control" name="message" placeholder="Write a message here..." required autofocus></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="sendmessage" class="btn btn-outline-primary" role="button">Send</button>
                </div>
            </form>
        </section>
        <?php } else { ?>
        <h1 class="text-center my-5">Messages</h1>
        <section class="my-5">
            <h2 class="pb-4">Last conversations</h2>
            <?php
            if ($emailVerified) {
                // Get list of conversations (two person)
                // NOTE Bug here (Seems to exist only on Windows)
                $conversations = $o_database->read(
                    'mbr.conversation_id, cvr.members_count, cvr.messages_count, usr.id, usr.display_name, usr.username, usr.last_online, usr.avatar, msg.user_id AS sender_id, msg.datetime, msg.message',
                    'conversations_members mbr',
                    'INNER JOIN conversations cvr ON mbr.conversation_id = cvr.id
                    INNER JOIN (SELECT DISTINCT id, conversation_id, user_id, datetime, message FROM conversations_messages ORDER BY id) msg ON mbr.conversation_id = msg.conversation_id
                    INNER JOIN users usr ON mbr.user_id = usr.id
                    WHERE mbr.conversation_id IN (SELECT conversation_id FROM conversations_members WHERE user_id = ?) AND mbr.user_id != ?
                    GROUP BY mbr.conversation_id ORDER BY msg.id DESC',
                    [$_SESSION['account']['id'], $_SESSION['account']['id']]
                );

                // List all recent conversations
                foreach ($conversations as $conversation) {
                    if ($conversation['messages_count'] != 0) {
                        $sendInterval = $o_system->getDateIntervalString($o_system->countDateInterval($conversation['datetime']));

                        $lastSent = false;
                        if ($conversation['sender_id'] == $_SESSION['account']['id']) { $lastSent = true; }

                        $conversation['avatar'] = $conversation['avatar'] ?? 'default';

                        $isOnline = false;
                        if ($o_system->countDateInterval($conversation['last_online']) < 90) {
                            $isOnline = true;
                        }
            ?>
            <div class="conversation-row py-3">
                <div class="d-flex align-items-center">
                    <div class="pr-3">
                        <img src="../media/avatars/<?php echo $conversation['avatar']; ?>/64.jpg" class="rounded" />
                    </div>
                    <div style="width: 100%;">
                        <div class="d-flex justify-content-start mb-1">
                            <div class="pr-2 font-weight-bold"><a href="messages.php?u=<?php echo $conversation['id']; ?>"><?php echo $conversation['display_name']; ?></a></div>
                            <div class="pr-2"><small class="text-muted">(@<?php echo $conversation['username']; ?>)</small></div>
                            <?php echo $isOnline ? '<div class="pr-2"><span class="badge badge-success">Online</span></div>' : ''; ?>
                            <div class="ml-auto text-muted"><small style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $conversation['datetime']; ?> (UTC)"><?php echo $sendInterval; ?> <i class="fa fa-clock-o"></i></small></div>
                        </div>
                        <div style="color: #424242;">
                            <?php if ($lastSent) { echo '<small class="text-muted pr-1">You: </small>'; } ?>
                            <?php echo $conversation['message']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    }
                }
            } else {
            ?>

            <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to make changes to your account!</p>

            <?php } ?>
        </section>
        <?php } ?>
    </div>

    <?php require_once('../system/inc/scripts.php'); ?>
    <script type="text/javascript" src="../resources/js/social.js"></script>
</body>
</html>
