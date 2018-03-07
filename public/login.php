<?php
// Include system initialization code
require('../application/partials/init.php');

if (!empty($_POST['login-submit'])) {
    // Get stored flash messages from session
    $account = BronyCenter\Account::getInstance();
    $loginStatus = $account->login([
        'username' => $_POST['login-username'],
        'password' => $_POST['login-password']
    ]);

    if ($loginStatus) {
        // Display flash message notification about website
        $flash->info(
            'Please note, that BronyCenter is currently in early development stage.<br />' .
            'Many features will be added/changed in the future. Website design will change as well.'
        );

        // Redirect user into the social part of a website
        header('Location: social/');
        die();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Login :: BronyCenter</title>

    <?php
    // Include stylesheets for all pages
    require('../application/partials/stylesheets.php');
    ?>
</head>
<body>
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="s-title" class="mb-4">
            <h1 class="text-center">Login</h1>
        </section>

        <section id="s-login">
            <?php
            // Include partial containing a post creator
            // require('../application/partials/index/form-registration.php');
            ?>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
