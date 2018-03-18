<?php
// Include system initialization code
require('../application/partials/init.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Terms of Use :: BronyCenter</title>

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

        <section id="terms" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">Terms of Use</h1>

            <p class="mb-4">
                Last update: <b>30.12.2017</b>
            </p>

            <p class="mb-0">
                I don't have time now to think about all of the rules.
                Just be nice for everyone and everything will be good.
            </p>
        </section>

        <section id="about" class="mb-4">
            <h5 class="mb-4">Few rules of which I can think right now.</h5>

            <ul>
                <li>BronyCenter is meant to be a safe place for everyone, don't try to change it.</li>
                <li>Respect everyone, don't harras or spam others.</li>
                <li>Don't swear and don't post anything sexually suggestive.</li>
                <li>Everything public that you post have to be in English, except the private messages.</li>
                <li>Don't break any laws.</li>
                <li>Don't use any security holes in our software/server. Report
                    them to the administrator as soon as you'll find any.</li>
                <li>You're responsible for everything that you'll do here.</li>
                <li>You have to be at least 13 years old to use it. We can't
                    automatically prevent users from doing forbitten things.</li>
            </ul>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
