<?php
// Require system initialization code.
require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>About :: BronyCenter</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="resources/css/style.css?v=31" />
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');

    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container" id="main_content">
        <section>
            <h1>About</h1>

            <p>
                This website has been created for the fans of the My Little Pony
                show but everyone who wants to make new friends and who can be
                nice and friendly is welcomed. Anyway it should be noted that
                this place is not for everyone. We're trying to make a better
                place, where everyone will be able to get noticed and will be
                able to feel safe here. Trolls and haters are not welcomed here.
                Any acts of harassment will result with a ban.
            </p>

            <p>
                Note that this website is not meant to be a replacement of other
                websites like this. It's different and has stricter rules. It's
                also in the early development stage, which means that a lot
                new features will be added in the future and everything can
                change.
            </p>

            <p>
                BronyCenter has been founded and written from scratch by
                JustAnotherBrony with a support of few friends from a fandom.
                Every new member is a huge motivation for me to not give up.
                Thank you all for being there <i class="fa fa-heart text-danger" aria-hidden="true"></i>.
            </p>

            <p>You can get more details about our hardware and software <a href="server.php">here</a>!</p>

            <p>
                BronyCenter is not owned or affiliated with Hasbro or DHX Media.<br />
                My little Pony™: Friendship is Magic and their characters are owned by: Hasbro Studios® and DHX Media®, And Lauren Faust.<br />
                All rights belong to their respective owners. No copyright infringement intended.<br />
                This website is non-profit which means that it's not making money from anything except optional donations to keep servers up.<br />
                The name "BronyCenter" is rightfully owned by the owner of this website and holds all copyrights for it.<br />
            </p>
        </section>
    </div>

    <?php
    // Require HTML of footer for not social pages.
    require_once('system/inc/footer.php');
    ?>
</body>
</html>
