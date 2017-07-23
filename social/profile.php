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
            $user['gender'] = 'Unknown';
    }

    // Display badge for administrators and moderators.
    switch ($user['account_type']) {
        case '0':
            $userBadge = '<span class="badge badge-primary">Unverified</span>';
            break;
        case '9':
            $userBadge = '<span class="badge badge-danger">Admin</span>';
            break;
        case '8':
            $userBadge = '<span class="badge badge-info">Mod</span>';
            break;
        default:
            $userBadge = '';
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
    <style type="text/css">
    #aside-details { flex: 0 0 25%; }
    #aside-posts { flex: 0 0 75%; }

    @media (max-width: 1200px) {
        #aside-details { flex: 0 0 29%; }
        #aside-posts { flex: 0 0 71%; }
    }

    @media (max-width: 992px) {
        #aside-details { flex: 0 0 35%; }
        #aside-posts { flex: 0 0 65%; }
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

    <div class="container">
        <section class="d-flex flex-column flex-md-row my-4">
            <div class="py-2 pr-0 pr-md-3" id="aside-details">
                <div class="rounded p-3 mb-3 text-center" style="border: 1px solid #E0E0E0;">
                    <img src="../media/avatars/<?php echo $user['avatar']; ?>/128.jpg" class="rounded mb-3" style="border: 2px solid #E0E0E0;" />

                    <h5 class="mb-0"><?php echo $user['display_name']; ?></h5>
                    <p class="mb-0 text-muted" style="margin-top: -2px;"><small>@<?php echo $user['username']; ?></small></p>
                    <p><?php echo $userBadge; ?> <?php echo $isOnline ? '<span class="badge badge-success">Online</span>' : '<span class="badge badge-danger">Offline</span>'; ?></p>

                    <p class="mb-0" style="line-height: 1.2;"><small><?php echo htmlspecialchars($user['description'] ?? 'No description'); ?></small></p>
                </div>

                <div class="rounded mb-3" style="border: 1px solid #E0E0E0;">
                    <h6 class="p-2 mb-0 text-center" style="background-color: #F4F4F4; border-bottom: 1px solid #E0E0E0;">About</h6>

                    <div class="p-3">
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Location">
                                <i class="fa fa-map-marker text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo htmlspecialchars(($user['city'] ? $user['city'] . ', ' : '') . ($user['country_code'] ?? 'Unknown')); ?>
                        </p>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Gender">
                                <i class="fa fa-transgender text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo $user['gender']; ?>
                        </p>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Age">
                                <i class="fa fa-user-o text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo $user['birthdate'] ?? 'Unknown'; ?>
                        </p>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Last seen">
                                <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;"  data-toggle="tooltip" data-placement="top" title="<?php echo $user['last_online']; ?> (UTC)"><?php echo $isOnline ? 'Just now ' : $lastOnlineAt; ?></span>
                        </p>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Account created">
                                <i class="fa fa-address-book-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;"  data-toggle="tooltip" data-placement="top" title="<?php echo $user['registration_datetime']; ?> (UTC)"><?php echo $registeredAt; ?></span>
                        </p>
                    </div>
                </div>

                <div class="rounded" style="border: 1px solid #E0E0E0;">
                    <h6 class="p-2 mb-0 text-center" style="background-color: #F4F4F4; border-bottom: 1px solid #E0E0E0;">Actions</h6>

                    <div class="p-3 text-center">
                        <?php
                        // Show disabled buttons if user is not allowed to use it.
                        if (!$isLogged || $user['id'] === $_SESSION['account']['id'] || !$emailVerified) {
                        ?>
                        <p class="mb-0"><button class="btn btn-outline-primary btn-sm" role="button" disabled>Send message</button></p>
                        <?php
                        } // if
                        else {
                        ?>
                        <p class="mb-0"><a href="messages.php?u=<?php echo $user['id']; ?>"><button class="btn btn-outline-primary btn-sm" role="button">Send message</button></a></p>
                        <?php
                        } // else
                        ?>
                    </div>
                </div>
            </div>

            <div class="py-2" id="aside-posts">
                <div class="rounded p-3 mb-3 text-center" style="border: 1px solid #E0E0E0;">
                    <p class="mb-0 text-info">
                        <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                        User's posts and posting to a user's profile will be available there soon (somewhere in the future).
                    </p>

                    <?php
                    // Show warning about not verified account if user is logged in with no e-mail.
                    if ($isLogged && $user['id'] === $_SESSION['account']['id'] && !$emailVerified) {
                    ?>
                    <p class="text-danger mt-3 mb-0">
                        <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                        You need to verify your e-mail address before other's will be able to see your account!
                    </p>
                    <?php
                    } // if
                    ?>
                </div>
            </div>
        </section>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>
</body>
</html>
