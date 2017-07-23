<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Check if user is authorized to enter.
if ($_SESSION['account']['type'] !== 9 && $_SESSION['account']['type'] !== 8) {
    // Redirect user into index.php if user is not logged in.
    header('Location: index.php');
    die();
}

// Get list of newest members.
$members = $o_database->read(
    'id, display_name, username, registration_datetime, login_datetime, last_online, avatar, account_type, account_standing',
    'users',
    '',
    []
);

// Get list of muted/banned members.
$bannedMembers = $o_database->read(
    'id, display_name, username, registration_datetime, login_datetime, last_online, avatar, account_type, account_standing',
    'users',
    'WHERE account_standing != 0',
    []
);

// Get list of removed posts.
$removedPosts = $o_database->read(
    'p.id, p.user_id, p.datetime, p.like_count, p.comment_count, p.type, p.status, p.delete_id, p.delete_datetime, p.delete_reason, up.display_name, up.username, up.account_type, ud.display_name AS delete_displayname',
    'posts p',
    'INNER JOIN users up ON up.id = p.user_id INNER JOIN users ud ON ud.id = p.delete_id WHERE p.status != 0 ORDER BY p.delete_datetime DESC LIMIT 25',
    []
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Manage :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <style type="text/css">

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
        <section class="my-5">
            <h1 class="text-center my-5">Manage</h1>
        </section>

        <section class="my-5">
            <h2 class="pb-4">Members</h2>

            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>Display name</th>
                        <th>Username</th>
                        <th>Member since</th>
                        <th>Last login</th>
                        <th>Last seen</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($members as $member) {
                        // Display badge for administrators and moderators.
                        switch ($member['account_type']) {
                            case '0':
                                $userBadge = '<span class="d-block badge badge-primary">Unverified</span>';
                                break;
                            case '9':
                                $userBadge = '<span class="d-block badge badge-danger">Admin</span>';
                                break;
                            case '8':
                                $userBadge = '<span class="d-block badge badge-info">Mod</span>';
                                break;
                            default:
                                $userBadge = '';
                        }
                    ?>

                    <tr>
                        <th scope="row"><?php echo $member['id']; ?></th>
                        <td class="pr-2" style="vertical-align: middle;"><?php echo $userBadge; ?></td>
                        <td><?php echo $member['display_name']; ?></td>
                        <td>@<?php echo $member['username']; ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['registration_datetime'])); ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['login_datetime'])); ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['last_online'])); ?></td>
                        <td></td>
                    </tr>

                    <?php
                    } // foreach
                    ?>
                </tbody>
            </table>
        </section>

        <section class="my-5">
            <h2 class="pb-4">Removed posts</h2>

            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>Display name</th>
                        <th>Deleted by</th>
                        <th>Post published</th>
                        <th>Deleted at</th>
                        <th>Delete reason</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($removedPosts as $post) {
                        // Display badge for administrators and moderators.
                        switch ($post['account_type']) {
                            case '0':
                                $userBadge = '<span class="d-block badge badge-primary">Unverified</span>';
                                break;
                            case '9':
                                $userBadge = '<span class="d-block badge badge-danger">Admin</span>';
                                break;
                            case '8':
                                $userBadge = '<span class="d-block badge badge-info">Mod</span>';
                                break;
                            default:
                                $userBadge = '';
                        }
                    ?>

                    <tr>
                        <th scope="row"><?php echo $post['id']; ?></th>
                        <td class="pr-2" style="vertical-align: middle;"><?php echo $userBadge; ?></td>
                        <td><?php echo $post['display_name']; ?></td>
                        <td><?php echo $post['delete_displayname']; ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($post['datetime'])); ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($post['delete_datetime'])); ?></td>
                        <td><?php echo $post['delete_reason'] ?? '<small class="text-muted">No reason</small>'; ?></td>
                        <td><?php echo $post['like_count']; ?></td>
                        <td><?php echo $post['comment_count']; ?></td>
                        <td></td>
                    </tr>

                    <?php
                    } // foreach
                    ?>
                </tbody>
            </table>
        </section>

        <section class="my-5">
            <h2 class="pb-4">Muted &amp; banned members</h2>

            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>Display name</th>
                        <th>Username</th>
                        <th>Member since</th>
                        <th>Last login</th>
                        <th>Last seen</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($bannedMembers as $member) {
                        // Display badge for administrators and moderators.
                        switch ($member['account_type']) {
                            case '0':
                                $userBadge = '<span class="d-block badge badge-primary">Unverified</span>';
                                break;
                            case '9':
                                $userBadge = '<span class="d-block badge badge-danger">Admin</span>';
                                break;
                            case '8':
                                $userBadge = '<span class="d-block badge badge-info">Mod</span>';
                                break;
                            default:
                                $userBadge = '';
                        }
                    ?>

                    <tr>
                        <th scope="row"><?php echo $member['id']; ?></th>
                        <td class="pr-2" style="vertical-align: middle;"><?php echo $userBadge; ?></td>
                        <td><?php echo $member['display_name']; ?></td>
                        <td>@<?php echo $member['username']; ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['registration_datetime'])); ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['login_datetime'])); ?></td>
                        <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['last_online'])); ?></td>
                        <td></td>
                    </tr>

                    <?php
                    } // foreach
                    ?>
                </tbody>
            </table>
        </section>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>

    <script type="text/javascript">
    // First check if document is ready.
    $(document).ready(function() {

    });
    </script>
</body>
</html>
