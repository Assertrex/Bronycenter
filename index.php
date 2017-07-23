<?php
// Require system initialization code.
require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Welcome to BronyCenter! Social network designed to share love and tolerance.</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');
    ?>

    <div class="jumbotron bg-faded mb-0">
        <div class="container">
            <h1 class="display-4">Welcome to the BronyCenter</h1>
            <p class="lead">
                A social network website created for those who love My Little Pony
                and/or want to make new friends.
            </p>
        </div>
    </div>

    <?php
    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container">
        <section class="my-5">
            <div class="row">
                <div class="col-lg">
                    <h3 class="pb-3 text-left">Progress</h3>

                    <ul class="list-group" style="list-style: none;">
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Posts comments</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Profile posts</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Administration panel</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Suggestions page</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Notifications</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Better messages</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Chatrooms</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Actions (hugs etc.)</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Photo posts</li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <h3 class="pb-3 text-center">What is this place?</h3>

                    <p class="text-center">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus id urna ut massa lobortis sagittis sed et neque.
                        Praesent at velit vel orci faucibus tempor. Morbi eget justo at sapien tincidunt efficitur vitae porta nunc.
                        Vestibulum ex neque, egestas vel sem vel, lacinia tempor magna. Mauris maximus urna et nunc pretium porttitor.
                    </p>
                </div>

                <div class="col-lg">
                    <h3 class="pb-3 text-right">Statistics</h3>

                    <ul class="list-group text-right" style="list-style: none;">
                        <li>Server status: <b class="text-success">Open</b></li>
                        <li class="text-muted">Last update: <b>23.07.2017</b></li>
                        <li class="text-muted">Suggestions: <b>0</b> (of <b>0</b>)</li>
                        <br />
                        <li>Online now: <b><?php echo $o_user->getOnlineUsersCount(); ?></b></li>
                        <li>Accounts created: <b><?php echo $o_user->getUsersCount(); ?></b></li>
                        <li>Posts published: <b><?php echo $o_post->getPostsCount(); ?></b></li>
                        <li class="text-muted">Photos uploaded: <b>0</b></li>
                        <li class="text-muted">Sent hugs: <b>0</b></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="text-center">Join now!</h2>

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
