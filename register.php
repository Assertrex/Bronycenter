<?php
// Require system initialization code.
require_once('system/inc/init.php');

// Check if register form has been submitted.
if (!empty($_POST['submit']) && $_POST['submit'] === 'register') {
    // Require GeoIP class to get details about visitor's IP address.
    require_once('system/class/geoip.php');

    // Require Mail class to send a verification email to new user.
    require_once('system/class/mail.php');

    // Try to register new user.
    if ($o_user->register()) {
        // Redirect new user into login page.
        header('Location: login.php');
        die();
    }

    // Show failed system message when new user couldn't be created.
    $o_system->setMessage(
        'error',
        'Couldn\'t create an account.'
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Register :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');

    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container mb-5">
        <section class="mb-5">
            <h1 class="mb-3 text-center">Register</h1>

            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="register-input-displayname">Display name</label>
                    <input type="text" name="displayname" class="form-control" id="register-input-displayname" placeholder="Display name" required autofocus />
                </div>

                <div class="form-group">
                    <label for="register-input-username">Username</label>
                    <input type="text" name="username" class="form-control" id="register-input-username" placeholder="Username" required />
                </div>

                <div class="form-group">
                    <label for="register-input-email">E-mail address</label>
                    <input type="text" name="email" class="form-control" id="register-input-email" placeholder="E-mail address" required />
                </div>

                <div class="form-group">
                    <label for="register-input-password">Password</label>
                    <input type="password" name="password" class="form-control" id="register-input-password" placeholder="Password" required />
                </div>

                <div class="form-group">
                    <label for="register-input-passwordrepeat">Repeat password</label>
                    <input type="password" name="passwordrepeat" class="form-control" id="register-input-passwordrepeat" placeholder="Repeat password" required />
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" value="register" class="btn btn-primary" id="register-button-submit" role="button">Join</button>
                </div>
            </form>
        </section>
    </div>

    <?php
    // Require HTML of footer for not social pages.
    require_once('system/inc/footer.php');
    ?>
</body>
</html>
