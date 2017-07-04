<?php
$displayJumbotron = true;

require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Homepage :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
</head>
<body>
    <?php require_once('system/inc/header.php'); ?>

    <div class="container mb-5">
        <section class="mb-5">
            <div class="row">
                <div class="col-lg">
                    <h3 class="pb-3 text-left">Progress</h3>

                    <ul class="list-group" style="list-style: none;">
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Login and registration</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> User authorization</li>
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Posts publishing</li>
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Posts liking</li>
                        <li class="ml-3" style="padding-left: 1px;"><b style="display: none; color: #4CAF50;">&#x2713;</b> Posts commenting</li>
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Profile pages</li>
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Private messages</li>
                        <li class="text-muted"><b style="color: #4CAF50;">&#x2713;</b> Account settings</li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <h3 class="pb-3 text-center">More About Us</h3>

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
                        <li class="text-muted">Last update: <b>03.07.2017</b></li>
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
                    <input type="text" name="displayname" class="form-control" id="register-input-displayname" placeholder="Display name" required />
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
                    <button type="submit" name="submit" value="register" class="btn btn-primary" id="register-button-submit" role="button">Create account</button>
                </div>
            </form>
        </section>
    </div>

    <?php require_once('system/inc/footer.php'); ?>
</body>
</html>
