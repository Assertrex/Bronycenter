<?php
// Load system messages after executing the code
$deferSystemMessages = true;

require_once('system/inc/init.php');

// Check if login form has been submitted
if (!empty($_POST['submit']) && $_POST['submit'] === 'login') {
    // Try to log in user
    if ($o_user->login()) {
        $o_system->setMessage('warning', 'Note, that BronyCenter is currently in early development stage.<br />Many features will be added/changed in the future. Website design will change as well.');
        header('Location: social/');
        die();
    }

    $o_system->setMessage('error', 'Couldn\'t log in user.');
}

// Store system messages for use inside website
$systemMessages = $o_system->getMessages();
$o_system->clearMessages();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Login :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
</head>
<body>
    <?php require_once('system/inc/header.php'); ?>

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

    <?php require_once('system/inc/footer.php'); ?>
</body>
</html>
