<?php
// Include system initialization code
require('../application/partials/init.php');

// Check if login form has been submitted
if (isset($_POST['submit'])) {
    // Create an instance of an account class
    $account = BronyCenter\Account::getInstance();

    // Check if user has been correctly logged in
    if ($account->login()) {
        // Display flash message notification about website
        $flash->info(
            'Please note, that BronyCenter is currently in early development stage.<br />' .
            'Many features will be added/changed in the future. Website design will change as well.'
        );

        // Redirect user into the social part of a website
        $utilities->redirect('social/');
    }

    // Get new system flash messages
    $flashMessages = $flash->merge($flashMessages);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Login :: <?= $o_config->getWebsiteTitle() ?></title>

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
            <h1 class="text-center text-lg-left mb-4">Login</h1>
        </section>

        <section id="s-login">
            <?php if ($websiteSettings['enableLogin']) { ?>
                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="login-input-username">Username</label>
                        <input type="text" name="username" class="form-control" id="login-input-username" placeholder="examplepony2017" pattern=".{3,24}" title="Field have to be between 3 and 24 characters." autocomplete="username" required autofocus />
                    </div>

                    <div class="form-group">
                        <label for="login-input-password">Password</label>
                        <input type="password" name="password" class="form-control" id="login-input-password" placeholder="$ecretPass987" autocomplete="current-password" required />
                    </div>

                    <div class="form-group pt-1 text-center">
                        <button type="submit" name="submit" value="login" class="btn btn-primary mb-2" id="login-button-submit">Login</button>
                    </div>
                </form>
            <?php } else { ?>
                <p class="text-danger text-center mb-0"><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> Login has been temporary turned off.</p>
            <?php } ?>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
