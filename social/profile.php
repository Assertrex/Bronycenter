<?php
// Require system initialization code.
require_once('../system/inc/init.php');

// Check if user has been selected or current user is logged in.
if (!empty($_GET['u']) || !empty($_SESSION['account']['id'])) {
    // Check if user has been selected.
    if (!empty($_GET['u'])) {
        // Get details about selected user.
        $id = intval($_GET['u']);
    }

    // Check if user is logged in if noone was selected or previous id has failed.
    if (empty($id) && !empty($_SESSION['account']['id'])) {
        // Get details about currently logged user if noone was selected.
        $id = intval($_SESSION['account']['id']);
    }

    // Get details about selected user.
    $user = $o_user->getDetails($id);

    // Redirect into default page if user has not been found.
    if ($user === false) {
        header('Location: index.php');
    }

    // Name gender types.
    switch ($user['gender']) {
        case 1:
            $user['gender'] = 'Male';
            break;
        case 2:
            $user['gender'] = 'Female';
            break;
        default:
            $user['gender'] = 'Undefined';
    }

    // Name account types.
    switch ($user['account_type']) {
        case 0:
            $user['account_type'] = 'Unverified user';
            break;
        case 1:
            $user['account_type'] = 'Standard member';
            break;
        case 8:
            $user['account_type'] = 'BronyCenter\'s moderator';
            break;
        case 9:
            $user['account_type'] = 'BronyCenter\'s administrator';
            break;
        default:
            $user['account_type'] = 'Unknown user';
    }

    // Format birthdate if available.
    if (!is_null($user['birthdate'])) {
        $current_date = new DateTime();
        $age_interval = new DateTime($user['birthdate']);
        $age_interval = $current_date->diff($age_interval);
        $user['birthdate'] = $age_interval->format('%y years old');
    }

    // Set user's avatar or get the default one if not existing.
    $user['avatar'] = $user['avatar'] ?? 'default';

    // Format activity datetimes.
    $registeredAt = $o_system->getDateIntervalString($o_system->countDateInterval($user['registration_datetime']));
    $lastOnlineAt = $o_system->getDateIntervalString($o_system->countDateInterval($user['last_online']));

    // Check if user is currently logged in.
    $isOnline = $o_user->isOnline(null, $user['last_online']);
} else {
    // Redirect into homepage if no user has been selected by a guest.
    header('Location: ../index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Profile :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');
    ?>

    <div class="container">
        <h1 class="text-center my-3">User profile</h1>

        <section class="my-5">
            <?php echo $isOnline ? '<span class="badge badge-success">Online</span>' : ''; ?>
            <h2 class="mb-3"><?php echo $user['display_name']; ?> (@<?php echo $user['username']; ?>)</h2>

            <p>
                <img src="../media/avatars/<?php echo $user['avatar']; ?>/128.jpg" class="rounded" />
            </p>

            <?php if ($isLogged && $emailVerified && $_SESSION['account']['id'] != $user['id']) { ?>
                <p>Actions: <a href="messages.php?u=<?php echo $user['id']; ?>">Send message</a></p>
            <?php } else if ($isLogged && !$emailVerified && $_SESSION['account']['id'] != $user['id']) { ?>
                <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to interact with other users!</p>
            <?php } ?>

            <p>
                <b>Location:</b> <?php echo ($user['city'] ?? 'Ponyville') . ', ' . ($user['country_code'] ?? 'Equestria'); ?><br />
                <?php if ($user['gender'] != 'Undefined') { ?><b>Gender:</b> <?php echo $user['gender']; ?><br /><?php } ?>
                <?php if (!is_null($user['birthdate'])) { ?><b>Age:</b> <?php echo $user['birthdate'] ?? 'Undefined'; ?><br /><?php } ?>
                <b>Joined:</b> <span title="<?php echo $user['registration_datetime']; ?> (UTC)"><?php echo $registeredAt; ?></span><br />
                <b>Last seen:</b> <span title="<?php echo $user['last_online']; ?> (UTC)"><?php echo $isOnline ? 'Just now ' : $lastOnlineAt; ?></span><br />
                <!-- <b>Timezone:</b> <?php echo $user['timezone']; ?> (offset in minutes)<br /> -->
                <!-- <b>Account standing:</b> <?php echo $user['account_standing']; ?> (0 is not muted/banned)<br /> -->
                <b>Account type:</b> <?php echo $user['account_type']; ?>
            </p>
            <p>
                <b>Description:</b><br />
                <?php echo $user['description'] ?? 'No description'; ?>
            </p>
            <?php
            // Show warning about not verified account if user is logged in with no e-mail.
            if ($isLogged && $user['id'] === $_SESSION['account']['id'] && !$emailVerified) {
            ?>
                <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before other's will be able to see your account!</p>
            <?php
            } // if
            ?>
        </section>
    </div>

    <?php
    // Require all common JavaScript (like JQuery and Bootstrap).
    require_once('../system/inc/scripts.php');
    ?>
    <script type="text/javascript" src="../resources/js/social.js"></script>
</body>
</html>
