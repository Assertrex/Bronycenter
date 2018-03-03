<?php
// Include system initialization code
require('../application/partials/init.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Welcome to BronyCenter! Social network designed to share love and tolerance.</title>

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

    <div class="jumbotron text-light mb-0">
        <div class="container">
            <h1 class="display-4 mb-3">Welcome to the BronyCenter</h1>

            <p class="lead">
                Social network created for those who love My Little Pony
                and/or want to make new friends.
            </p>
        </div>
    </div>

    <div class="container" style="padding-top: 1rem;">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="description">
            <p class="text-center">
                Website is currently at early development stage and it's not ready for open registration.<br />
                If you'd like to join and to test things out, please <a href="contact.php">contact</a> the administrator.<br />
                We'll be happy to see you there!
            </p>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
