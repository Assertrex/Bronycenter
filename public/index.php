<?php
$pageSettings = [
    'title' => 'Social network designed to share love and tolerance',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');
?>

<body>
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="jumbotron text-light mb-0">
        <div class="container">
            <h1 class="display-4 mb-3">Welcome to the <?= $o_config->getWebsiteTitle() ?></h1>

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

        <section id="description" class="mb-4">
            <p class="text-center">
                Website is currently at early development stage and it's not ready for open registration.<br />
                If you'd like to join and to test things out, please <a href="contact.php">contact</a> the administrator.<br />
                We'll be happy to see you there!
            </p>
        </section>

        <section id="registration" class="mb-4">
            <?php
            // Include partial containing a post creator
            require('../application/partials/index/form-registration.php');
            ?>
        </section>

        <section id="website" class="text-center mb-0">
            <p class="mb-0">Version: <?= $o_config->getWebsiteVersion() ?> (<?= $o_config->getWebsiteDate() ?>)</p>
            <p class="mb-0"><?= $o_config->getWebsiteTitle() ?> &copy; 2017 - 2018</p>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
