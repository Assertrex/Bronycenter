<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Change selected setting when user has submitted a form.
if (!empty($_POST['submit'])) {
    switch($_POST['submit']) {
        case 'changedisplayname':
            $o_user->changeDisplayname($_SESSION['account']['id'], $_POST['displayname']);
            break;
        case 'changepassword':
            $o_user->changePassword($_SESSION['account']['id'], $_POST['passwordold'], $_POST['passwordnew'], $_POST['passwordrepeat']);
            break;
        case 'changebirthdate':
            $o_user->changeBirthdate($_SESSION['account']['id'], $_POST['day'], $_POST['month'], $_POST['year']);
            break;
        case 'changegender':
            $o_user->changeGender($_SESSION['account']['id'], $_POST['gender']);
            break;
        case 'changecity':
            $o_user->changeCity($_SESSION['account']['id'], $_POST['city']);
            break;
        case 'changedescription':
            $o_user->changeDescription($_SESSION['account']['id'], $_POST['description']);
            break;
        case 'changeavatar':
            // Require Image class for image manipulation.
            require_once('../system/class/image.php');

            // Try to change user's avatar.
            $o_user->changeAvatar($_SESSION['account']['id'], $_FILES['avatar']);
            break;
    }
}

// Get details about current user.
$user = $o_user->getDetails($_SESSION['account']['id']);

// Store birthdate as day, month and year variables if set up.
if (!is_null($user['birthdate'])) {
    $birthdate_temp = explode('-', $user['birthdate']);
    $user['birthdate_temp']['year'] = $birthdate_temp[0];
    $user['birthdate_temp']['month'] = $birthdate_temp[1];
    $user['birthdate_temp']['day'] = $birthdate_temp[2];
} else {
    $user['birthdate_temp']['year'] = '';
    $user['birthdate_temp']['month'] = '';
    $user['birthdate_temp']['day'] = '';
}

// Get current avatar of user or show the default one.
$avatarName = $_SESSION['user']['avatar'] ?? 'default';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Settings :: BronyCenter</title>

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
        <h1 class="text-center my-3">Settings</h1>

        <?php
        // Show forms for changing settings if user has verified his e-mail address.
        if ($emailVerified) {
        ?>
        <section class="my-5">
            <h5>Change display name</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <input type="text" name="displayname" value="<?php echo $user['display_name']; ?>" placeholder="Your display name" class="form-control" />
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changedisplayname" class="btn btn-outline-primary" role="button">Change display name</button>
                </div>
            </form>

            <h5>Change e-mail address</h5>
            <p>Not available yet...</p>

            <h5>Change password</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <input type="password" name="passwordold" placeholder="Old password" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="password" name="passwordnew" placeholder="New password" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="password" name="passwordrepeat" placeholder="Repeat password" class="form-control" required />
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changepassword" class="btn btn-outline-primary" role="button">Change password</button>
                </div>
            </form>

            <h5>Change country</h5>
            <p>Not available yet...</p>

            <h5>Change timezone</h5>
            <p>Not available yet...</p>

            <h5>Change birthdate</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <input type="number" name="day" placeholder="DD" value="<?php echo $user['birthdate_temp']['day']; ?>" class="form-control" min="1" max="31" style="display: inline-block; width: 72px;" />
                    <input type="number" name="month" placeholder="MM" value="<?php echo $user['birthdate_temp']['month']; ?>" class="form-control" min="1" max="12" style="display: inline-block; width: 72px;" />
                    <input type="number" name="year" placeholder="YYYY" value="<?php echo $user['birthdate_temp']['year']; ?>" class="form-control" min="1900" max="<?php echo date('Y'); ?>" style="display: inline-block; width: 86px;" />
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changebirthdate" class="btn btn-outline-primary" role="button">Change birthdate</button>
                </div>
            </form>

            <h5>Change gender</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <select name="gender" class="form-control">
                        <option value="0" <?php if (empty($user['gender'])) { echo 'selected'; } ?>>Undefined</option>
                        <option value="1" <?php if ($user['gender'] == 1) { echo 'selected'; } ?>>Male</option>
                        <option value="2" <?php if ($user['gender'] == 2) { echo 'selected'; } ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changegender" class="btn btn-outline-primary" role="button">Change gender</button>
                </div>
            </form>

            <h5>Change city</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <input type="text" name="city" value="<?php echo $user['city']; ?>" placeholder="Your city" class="form-control" />
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changecity" class="btn btn-outline-primary" role="button">Change city</button>
                </div>
            </form>

            <h5>Change description</h5>
            <form method="post" action="settings.php">
                <div class="form-group">
                    <textarea name="description" placeholder="Your new description" class="form-control"><?php echo $user['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changedescription" class="btn btn-outline-primary" role="button">Change description</button>
                </div>
            </form>

            <h5>Change avatar</h5>
            <form method="post" action="settings.php" enctype="multipart/form-data">
                <div class="form-group">
                    <img src="../media/avatars/<?php echo $avatarName; ?>/128.jpg" class="rounded" />
                </div>
                <div class="form-group">
                    <input type="file" name="avatar" class="form-control-file" aria-describedby="avatarhelp" />
                    <small id="avatarhelp" class="form-text text-muted">Avatar needs to be in 1:1 resolution (recommended is 256x256). Only .jpg and .png are allowed. They will be converted into JPEG image.</small>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" value="changeavatar" class="btn btn-outline-primary" role="button">Change avatar</button>
                </div>
            </form>
        </section>
        <?php
        } // if
        // Show warning about required e-mail verification if user has not verified it.
        else {
        ?>
        <section class="my-5">
            <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to make changes to your account!</p>
        </section>
        <?php
        } // else
        ?>
    </div>

    <?php
    // Require all common JavaScript (like JQuery and Bootstrap).
    require_once('../system/inc/scripts.php');
    ?>
    <script type="text/javascript" src="../resources/js/social.js"></script>
</body>
</html>
