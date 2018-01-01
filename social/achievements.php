<?php
// Allow access only for logged users
$loginRequired = true;

// Include system initialization code
require('../system/partials/init.php');

// Page settings
$pageTitle = 'Achievements :: BronyCenter';

// Get an array containing user's statistics
$userStatistics = $statistics->get();

// Include social head content for all pages
require('partials/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../system/partials/header-social.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../system/partials/flash.php');
        ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0" id="statistics">
                    <h6 class="text-center mb-0">Your statistics</h6>

                    <div class="px-2 px-lg-3 py-4">
                        <div class="text-info text-center mb-3">
                            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                            Statistics may not be correct and will be re-counted in future updates.
                        </div>

                        <p>Your points: <span class="font-weight-bold"><?php echo $userStatistics['user_points']; ?></span></p>

                        <p class="mb-0">You've published <span class="font-weight-bold"><?php echo $userStatistics['posts_created']; ?></span> posts.</p>
                        <p class="mb-0">You've liked <span class="font-weight-bold"><?php echo $userStatistics['posts_likes_given']; ?></span> posts.</p>
                        <p class="mb-0">You've commented <span class="font-weight-bold"><?php echo $userStatistics['posts_comments_given']; ?></span> posts.</p>
                        <p>You've deleted <span class="font-weight-bold"><?php echo $userStatistics['posts_deleted']; ?></span> of your posts.</p>

                        <p class="mb-0">Your posts have been liked <span class="font-weight-bold"><?php echo $userStatistics['posts_likes_received']; ?></span> times.</p>
                        <p class="mb-0">Your posts have been commented <span class="font-weight-bold"><?php echo $userStatistics['posts_comments_received']; ?></span> times.</p>
                    </div>
                </section>

                <section class="fancybox mt-0 mb-0" id="achievements">
                    <h6 class="text-center mb-0">Your achievements</h6>

                    <div class="px-2 px-lg-3 py-4">
                        <div class="text-info text-center">
                            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                            Achievements will be available in future updates.
                        </div>
                    </div>
                </section>
            </div>

            <aside class="col-12 col-lg-4">
                <?php
                // Include partial containing an aside panel
                require('partials/achievements/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('partials/scripts.php');
    ?>
</body>
</html>
