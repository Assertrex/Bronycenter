<?php
// Require system initialization code.
require_once('../system/inc/init.php');

// Get details about last online members.
$members = $o_database->read(
    'id, display_name, username, last_online, avatar',
    'users',
    'WHERE account_type != 0 ORDER BY last_online DESC LIMIT 25',
    []
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Members :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <style type="text/css">
    .member-col:hover { text-decoration: none; }
    .member-col .member-col-wrap { width: 265px; border: 1px solid #E0E0E0; }
    .member-col:hover .member-col-wrap { border-color: #9E9E9E; }
    .member-col .member-col-wrap img { width: 96px; border-right: 1px solid #E0E0E0; }
    .member-col:hover .member-col-wrap img { border-color: #9E9E9E; }
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
        <h1 class="text-center">Members</h1>

        <section class="my-5">
            <div class="d-flex flex-row flex-wrap justify-content-center">
                <?php
                // Display each member.
                foreach ($members as $member) {
                    // Set user's avatar or get the default one if not existing.
                    $avatar = $member['avatar'] ?? 'default';

                    // Check if user is currently logged in.
                    $isOnline = $o_user->isOnline(null, $member['last_online']);
                ?>
                <a href="profile.php?u=<?php echo $member['id']; ?>" class="member-col my-2 mx-1">
                    <div class="d-flex align-items-center rounded member-col-wrap">
                        <div>
                            <img src="../media/avatars/<?php echo $avatar; ?>/128.jpg" class="rounded-left" />
                        </div>
                        <div class="d-flex flex-column justify-content-center text-center px-3" style="width: 100%; line-height: 1.2;">
                            <div style="color: #000;"><?php echo $member['display_name']; ?></div>
                            <div class="text-muted"><small>@<?php echo $member['username']; ?></small></div>
                            <div style="padding-top: 3px;"><?php echo $isOnline ? '<span class="badge badge-success">Online</span>' : ''; ?></div>
                        </div>
                    </div>
                </a>
                <?php
                }
                ?>
            </div>
        </section>
    </div>

    <?php
    // Require all common JavaScript (like JQuery and Bootstrap).
    require_once('../system/inc/scripts.php');
    ?>
    <script type="text/javascript" src="../resources/js/social.js"></script>
</body>
</html>
