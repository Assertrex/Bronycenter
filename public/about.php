<?php
// Include system initialization code
require('../application/partials/init.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>About :: <?= $o_config->getWebsiteTitle() ?></title>

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

        <section id="about" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">About <?= $o_config->getWebsiteTitle() ?></h1>

            <p>
                This website has been created for the fans of the My Little Pony
                show but everyone who wants to make new friends and who can be
                nice and friendly is welcomed. Anyway it should be noted that
                this place is not for everyone. We're trying to make a better
                place, where everyone will be able to get noticed and will be
                able to feel safe here. Trolls and haters are not welcomed here.
                Any acts of harassment will result with a temporary mute or ban.
            </p>

            <p>
                Note that this website is not meant to be a replacement of other
                websites like this. It's different and has stricter rules. It's
                also in the early development stage, which means that a lot
                new features (and bugs) will be added in the future so everything
                can change.
            </p>
        </section>

        <section id="website" class="mb-4">
            <p>
                BronyCenter has been founded and written from scratch by
                JustAnotherBrony with a support of few friends from a fandom.
                Every new member is a huge motivation for me to not give up.
                Thank you all for being there <i class="fa fa-heart text-danger" aria-hidden="true"></i>.
            </p>

            <p>You can get more details about our hardware and software <a href="server.php">here</a>!</p>
        </section>

        <section id="copyright">
            <p>
                BronyCenter is not owned or affiliated with Hasbro or DHX Media.<br />
                My little Pony™: Friendship is Magic and their characters are owned by: Hasbro Studios® and DHX Media®, And Lauren Faust.<br />
                All rights belong to their respective owners. No copyright infringement intended.<br />
                This website is non-profit which means that we're not making money from anything except optional donations to keep servers up.<br />
                The name "BronyCenter" is rightfully owned by the owner of this website and holds all copyrights for it.<br />
            </p>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
