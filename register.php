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

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="resources/css/style.css?v=<?php echo $systemVersion['commit']; ?>" /></head></head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');

    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container">
        <section id="scn-register">
            <h1>Register</h1>

            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="register-input-displayname">Display name</label>
                    <input type="text" name="displayname" class="form-control" id="register-input-displayname" aria-describedby="displayname-help" placeholder="Example Pony" required />
                    <small id="displayname-help" class="form-text text-muted">
                        Choose how do you want to be seen by everyone. It can be changed later in your account's settings.<br />
                        Display name needs to contain from 3 to 24 characters. Allowed characters: <b>a-zA-Z0-9 ,_()</b>
                    </small>
                </div>

                <div class="form-group">
                    <label for="register-input-username">Username</label>
                    <input type="text" name="username" class="form-control" id="register-input-username" aria-describedby="username-help" placeholder="examplepony2017" required />
                    <small id="username-help" class="form-text text-muted">
                        Choose your name for login and for the link to your profile. Everyone will be able to see it. This can't be changed later.<br />
                        Username needs to contain from 3 to 20 characters. Only lowercase alphanumeric characters are allowed (<b>a-z0-9</b>).
                    </small>
                </div>

                <div class="form-group">
                    <label for="register-input-email">E-mail address</label>
                    <input type="text" name="email" class="form-control" id="register-input-email" aria-describedby="email-help" placeholder="epony@poniland.com" required />
                    <small id="email-help" class="form-text text-muted">
                        We won't share it with anyone. It is needed for many reasons like password recovery or to prevent trolls from making multiaccounts.<br />
                        It needs to be a correct e-mail address, because you'll need to click on a link that we'll send to you after registration.
                    </small>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6">
                            <label for="register-input-password">Password</label>
                            <input type="password" name="password" class="form-control" id="register-input-password" aria-describedby="password-help" placeholder="$ecretPass987" required />
                        </div>
                        <div class="col-6">
                            <label for="register-input-passwordrepeat">Repeat password</label>
                            <input type="password" name="passwordrepeat" class="form-control" id="register-input-passwordrepeat" placeholder="$ecretPass987" required />
                        </div>
                    </div>
                    <small id="password-help" class="form-text text-muted">
                        Secure your account with your secret password. Make sure that no one else knows it.<br />
                        For extra security it shouldn't be the password that you use on many websites.<br />
                        Don't worry, no one should be able to read it as it will be hashed with BCrypt algorithm before storing in the database.
                    </small>
                </div>

                <div class="form-group pt-1">
                    <button type="submit" name="submit" value="register" class="btn btn-primary" id="register-button-submit" role="button">Create account</button>
                    <small id="register-button-submit" class="form-text text-muted">
                        By signing up, you agree to the <a href="terms.php">Terms of Service</a> and <a href="policy.php">Privacy Policy</a>, including <a href="cookies.php">Cookie Use</a>.
                    </small>
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
