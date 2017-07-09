<?php
// Require system initialization code.
require_once('system/inc/init.php');

// Check if login form has been submitted.
if (!empty($_POST['submit']) && $_POST['submit'] === 'login') {
    // Try to log in user.
    if ($o_user->login()) {
        // Show warning system message to inform new user about early development.
        $o_system->setMessage(
            'warning',
            'Note, that BronyCenter is currently in early development stage.<br />Many features will be added/changed in the future. Website design will change as well.'
        );

        // Redirect user into directory with social pages.
        header('Location: social/');
        die();
    }

    // Show failed system message when user couldn't be logged in.
    $o_system->setMessage(
        'error',
        'Couldn\'t log in user.'
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Login :: BronyCenter</title>

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
            <h1 class="mb-3 text-center">Login</h1>

            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="login-input-username">Username or e-mail</label>
                    <input type="text" name="username" class="form-control" id="login-input-username" placeholder="Username or e-mail" required autofocus />
                </div>

                <div class="form-group">
                    <label for="login-input-password">Password</label>
                    <input type="password" name="password" class="form-control" id="login-input-password" placeholder="Password" required />
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" value="login" class="btn btn-primary" id="login-button-submit" role="button">Log In</button>
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
