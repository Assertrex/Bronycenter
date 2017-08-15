<?php
// Require system initialization code.
require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Terms of Use :: BronyCenter</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="resources/css/style.css?v=<?php echo $systemVersion['commit']; ?>" /></head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');

    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container" id="main_content">
        <section>
            <h1>Terms of Use</h1>
            <p>Last update: <b>13.08.2017</b></p>

            <p>
                I don't have time now to think about all of the rules.
                Just be nice for everyone and everything will be good.
            </p>

            <h4 class="my-4">In short</h4>
            <ul>
                <li>BronyCenter is meant to be a safe place for everyone, don't try to change it.</li>
                <li>Respect everyone, don't harras or spam others.</li>
                <li>Don't swear and don't post anything sexually suggestive.</li>
                <li>Use posts and comments only in English language.</li>
                <li>Don't break any laws.</li>
                <li>Don't use any security holes in our software/server. Report
                    them to the administrator as soon as you'll find any.</li>
                <li>You can have only one account. Contact administrator first,
                    if you're thinking about creating a new one.</li>
                <li>You're responsible for everything that you'll do here.</li>
                <li>You have to be at least 13 years old to use it. We can't
                    automatically prevent users from doing forbitten things.</li>
            </ul>
        </section>
    </div>

    <?php
    // Require HTML of footer for not social pages.
    require_once('system/inc/footer.php');
    ?>
</body>
</html>
