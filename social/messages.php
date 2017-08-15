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
            'id, display_name, username, last_online, account_type',
            'users',
            'WHERE id = ?',
            [$userID]
        )[0];

        // Get string with last seen message.
        $lastOnlineInterval = $o_system->countDateInterval($user['last_online']);
        if ($lastOnlineInterval < 90) {
            $lastOnline = 'now';
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

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="../resources/css/style.css?v=<?php echo $systemVersion['commit']; ?>" />

    <style type="text/css">
    aside { flex: 0 0 340px; min-height: calc(100vh - 56px); border-right: 1px solid #E0E0E0; }
    aside a { color: #292B2C; }
    aside a:hover { color: #292B2C; text-decoration: none; }
    .conversation-row { line-height: 1.4; }
    .conversation-row.active, .conversation-row:hover { background-color: #EEE; }
    #conversations-displayname { flex: 100%; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
    #conversations-datetime { flex: 0 0 104px; text-align: right; }
    #conversations-message { display: block; width: 242px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
    #conversation-titlebar { height: 65px; border-bottom: 1px solid #E0E0E0; }
    #container-messages { flex: calc(100vh - 178px); overflow-y: auto; }
    .message-row { border-bottom: 1px solid #E0E0E0; }
    .message-row:last-child { border: 0; }

    /* Media query for small devices (<=991px) */
    @media (max-width: 576px) {
        aside { max-width: 186px; }
    }
    </style>
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');
    ?>

    <div class="d-flex">
        <aside class="d-flex flex-column">
            <div style="flex: calc(100vh - 56px); overflow-y: auto;">
                <?php
                // Show last conversations list.
                $conversations = $o_message->getConversations();

                // Display each conversation.
                foreach ($conversations as $conversation) {
                    // Check if conversation is a current conversation.
                    if ($conversation['id'] == ($_GET['u'] ?? 0)) {
                        $isCurrent = true;
                    } else {
                        $isCurrent = false;
                    }

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


                <div class="conversation-row<?php echo $isCurrent ? ' active' : '' ?>">
                    <a href="messages.php?u=<?php echo $conversation['id']; ?>">
                        <div class="d-flex align-items-center px-3 py-2" style="border-bottom: 1px solid #E0E0E0; overflow: hidden;">
                            <div class="pr-3">
                                <img src="../media/avatars/<?php echo $conversation['avatar']; ?>/64.jpg" class="rounded" style="width: 48px; height: 48px;" />
                            </div>
                            <div class="d-flex flex-column" style="flex: 100%">
                                <div class="d-flex" style="margin-bottom: 3px;">
                                    <!-- <span class="pr-2"><?php echo $userBadge; ?><?php echo $conversation['user_online'] ? ' <span class="badge badge-success">Online</span>' : ''; ?></span> -->
                                    <span id="conversations-displayname"><?php echo $conversation['display_name']; ?></span>
                                    <span class="text-muted" id="conversations-datetime"><small style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $conversation['datetime']; ?> (UTC)"><?php echo $conversation['send_interval']; ?> <i class="fa fa-clock-o"></i></small></span>
                                </div>
                                <div>
                                    <small class="text-muted pr-1" id="conversations-message">
                                        <?php echo $conversation['current_sent'] ? 'You: ' : ''; ?>
                                        <?php echo htmlspecialchars($conversation['message']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <?php
                } // foreach
                ?>
            </div>
        </aside>

        <?php
        // Display conversation messages if conversation has been selected.
        if ($isSelected) {
            // Display badge for administrators and moderators.
            switch ($user['account_type']) {
                case '9':
                    $userBadge = '<small class="badge badge-danger mr-1" style="vertical-align: top;">Admin</small>';
                    break;
                case '8':
                    $userBadge = '<small class="badge badge-info mr-1" style="vertical-align: top;">Mod</small>';
                    break;
                default:
                    $userBadge = '';
            }
        ?>

        <div class="d-flex flex-column" style="flex: 100%;">
            <div class="py-2 text-center" id="conversation-titlebar">
                <h6 class="mb-0" style="margin-top: 5px;"><?php echo $user['display_name']; // Badge here ?> <small class="text-muted">(@<?php echo $user['username']; ?>)</small></h6>
                <p class="mb-0" style="margin-top: 2px;"><small class="text-muted">Active <?php echo $lastOnline; ?></small></p>
            </div>

            <div id="container-messages">
                <?php
                // Get messages from conversation.
                $messages = $o_message->getMessages($conversationID);

                // List messages from user here.
                foreach ($messages as $message) {
                ?>

                <div class="message-row py-2">
                    <div class="d-flex mx-3">
                        <div class="pr-3">
                            <img src="../media/avatars/<?php echo $message['avatar']; ?>/64.jpg" class="rounded" style="width: 48px; height: 48px;" />
                        </div>
                        <div style="width: 100%; line-height: 1.3;">
                            <div class="d-flex justify-content-start mb-2">
                                <?php if (!$message['current_sent']) { ?>
                                <div><small class="font-weight-bold"><a href="profile.php?u=<?php echo $message['user_id']; ?>"><?php echo $message['display_name']; ?></a></small></div>
                                <?php } else { ?>
                                <div><small class="font-weight-bold">You</small></div>
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
            </div>

            <div style="border-top: 1px solid #E0E0E0;">
                <form class="d-flex align-items-center" method="post">
                    <div class="form-group mb-0 mr-2" style="position: relative; flex: 100%;">
                        <textarea class="form-control rounded-0" id="message-content" name="message" placeholder="Write a message..." style="border: 0;" maxlength="1000" required autofocus></textarea>
                        <div class="text-center" style="position: absolute; bottom: 2px; right: 16px;">
                            <small class="text-muted"><span id="message-characters-counter">0</span> / 1000</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 mr-2">
                        <button type="submit" name="submit" value="sendmessage" class="btn btn-outline-primary" role="button">Send</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        } // if
        ?>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>

    <script type="text/javascript">
    // Scroll to the newest messages on page load.
    let getValue = new URLSearchParams(window.location.search);
    if (getValue.has('u')) {
        $('#container-messages').scrollTop($('#container-messages')[0].scrollHeight);
    }

    // Count amount of characters used in a message.
    $("#message-content").on("input", function() {
        let amount = $("#message-content").val().length;
        $("#message-characters-counter").text(amount);
    });
    </script>
</body>
</html>
