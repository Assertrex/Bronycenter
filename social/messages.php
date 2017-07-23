<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Require Message class for actions with user's messages.
require_once('../system/class/message.php');
$o_message = new Message($o_system, $o_database, $o_user, $o_validate);

// Store required variables.
$isSelected = false;
$userID = intval($_GET['u'] ?? 0);
$conversationID = intval($_GET['c'] ?? 0);
$membersCount = 0;
$messagesCount = 0;

// Check if user has requested conversation.
if (!empty($userID) || !empty($conversationID)) {
    // Check if user's account has verified e-mail address.
    if (!$emailVerified) {
        // Show failed system message if user hasn't verified it's e-mail address.
        $o_system->setMessage(
            'error',
            'You need to verify your e-mail address before you\'ll be able to use private messages!'
        );

        // Redirect into messages page on error.
        header('Location: messages.php');
        die();
    }

    // Display error if both values are set up instead of one.
    if (!empty($userID) && !empty($conversationID)) {
        // Show failed system message if link is invalid.
        $o_system->setMessage(
            'error',
            'Your conversation link is invalid! It can\'t point to both user and conversation at the same time.'
        );

        // Redirect into messages page on error.
        header('Location: messages.php');
        die();
    }

    // Get a conversation ID first if user id value has been specified.
    if (!empty($userID)) {
        $conversationID = $o_message->getConversationID($userID);
    }

    // Get details about conversation.
    if (empty($conversationID)) {
        // Show failed system message if couldn't get an ID of a conversation.
        $o_system->setMessage(
            'error',
            'System couldn\'t get an ID of a conversation.'
        );

        // Redirect into messages page on error.
        header('Location: messages.php');
        die();
    }

    // Try to get details about a conversation.
    $conversation = $o_message->getConversation($conversationID);

    // Check if details about conversation has been fetched.
    if (empty($conversation)) {
        // Show failed system message if details about conversation couldn't be fetched.
        $o_system->setMessage(
            'error',
            'System couldn\'t get details about selected conversation.'
        );

        // Redirect into messages page on error.
        header('Location: messages.php');
        die();
    }

    // Update variables.
    $isSelected = true;
    $conversationID = $conversation['id'];
    $membersCount = $conversation['members_count'];
    $messagesCount = $conversation['messages_count'];

    // Get user details if it's coversation of two members.
    // TODO Allow conversations with more than two members.
    if ($membersCount === 2) {
        // Get details about second user.
        $user = $o_database->read(
            'id, display_name, username, last_online',
            'users',
            'WHERE id = ?',
            [$userID]
        )[0];

        // Get string with last seen message.
        $lastOnlineInterval = $o_system->countDateInterval($user['last_online']);
        if ($lastOnlineInterval < 90) {
            $lastOnline = 'Just now';
        } else {
            $lastOnline = $o_system->getDateIntervalString($lastOnlineInterval);
        }
    }
}

// Send a message.
if (!empty($_POST['submit']) && $_POST['submit'] === 'sendmessage' && $isSelected) {
    // Check if user's account has verified e-mail address.
    if (!$emailVerified) {
        // Show failed system message if user hasn't verified it's e-mail address.
        $o_system->setMessage(
            'error',
            'You need to verify your e-mail address before you\'ll be able to use private messages!'
        );

        // Redirect into messages page on error.
        header('Location: messages.php');
        die();
    }

    // Try to send a message.
    $o_message->send($conversationID, $_POST['message'], $messagesCount);

    // Refresh page after sending a message.
    header('Refresh: 0');
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
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');
    ?>

    <div class="container">
        <?php
        // Show selected conversation messages.
        if ($isSelected) {
        ?>
        <section class="my-5">
            <h3>Your chatting with: <?php echo $user['display_name']; ?> <small>(@<?php echo $user['username']; ?>)</small></h3>
            <p>Last seen: <?php echo $lastOnline; ?></p>
        </section>
        <section class="my-5">
            <?php
            // Get messages from conversation.
            $messages = $o_message->getMessages($conversationID);

            // List messages from user here.
            foreach ($messages as $message) {
            ?>

            <div class="message-row py-3">
                <div class="d-flex align-items-center">
                    <div class="pr-3">
                        <img src="../media/avatars/<?php echo $message['avatar']; ?>/64.jpg" class="rounded" />
                    </div>
                    <div style="width: 100%;">
                        <div class="d-flex justify-content-start mb-2">
                            <?php if (!$message['current_sent']) { ?>
                            <div class="font-weight-bold"><a href="profile.php?u=<?php echo $message['user_id']; ?>"><?php echo $message['display_name']; ?></a></div>
                            <?php } else { ?>
                            <div class="font-weight-bold">You</div>
                            <?php } ?>
                            <div class="ml-auto text-muted"><small style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $message['datetime']; ?> (UTC)"><?php echo $message['send_interval']; ?> <i class="fa fa-clock-o"></i></small></div>
                        </div>
                        <div>
                            <?php echo htmlspecialchars($message['message']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            } // foreach
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
        <?php
        } // if
        // Show list of recent conversations.
        else {
        ?>

        <h1 class="text-center my-5">Messages</h1>
        <section class="my-5">
            <h2 class="pb-4">Last conversations</h2>
            <?php
            // Show last conversations list if user has verified his e-mail address.
            if ($emailVerified) {
                $conversations = $o_message->getConversations();

                // Display each conversation.
                foreach ($conversations as $conversation) {
                    // Display badge for administrators and moderators.
                    switch ($conversation['account_type']) {
                        case '9':
                            $userBadge = '<span class="badge badge-danger">Admin</span>';
                            break;
                        case '8':
                            $userBadge = '<span class="badge badge-info">Mod</span>';
                            break;
                        default:
                            $userBadge = '';
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
                            <div class="pr-2"><?php echo $userBadge; ?><?php echo $conversation['user_online'] ? ' <span class="badge badge-success">Online</span>' : ''; ?></div>
                            <div class="ml-auto text-muted"><small style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $conversation['datetime']; ?> (UTC)"><?php echo $conversation['send_interval']; ?> <i class="fa fa-clock-o"></i></small></div>
                        </div>
                        <div style="color: #424242;">
                            <?php echo $conversation['current_sent'] ? '<small class="text-muted pr-1">You: </small>' : ''; ?>
                            <?php echo htmlspecialchars($conversation['message']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                } // foreach
            } // if
            // Show warning about required e-mail verification if user has not verified it.
            else {
            ?>

            <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to use private messages!</p>

            <?php
            } // else
            ?>
        </section>
        <?php
        } // else
        ?>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>
</body>
</html>
