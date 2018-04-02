<?php
// Allow access only for logged users
$loginRequired = true;

// Include system initialization code
require('../../application/partials/init.php');

// Page settings
$pageTitle = 'Achievements';

// Get an array containing user's statistics
$userStatistics = $statistics->get();

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../../application/partials/flash.php');
        ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0" id="statistics">
                    <h6 class="text-center mb-0">Your statistics</h6>

                    <div class="px-2 px-lg-3 py-4">
                        <p>
                            <i class="fa fa-star-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Your points: <span class="font-weight-bold"><?= $userStatistics['user_points']; ?></span>
                        </p>

                        <h6 class="mb-3">Your actions on posts:</h6>
                        <p class="mb-0">
                            <i class="fa fa-pencil-square-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Posts that you have published: <span class="font-weight-bold"><?= $userStatistics['posts_created']; ?></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-trash-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Posts that you have removed: <span class="font-weight-bold"><?= $userStatistics['posts_removed']; ?></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-thumbs-o-up text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Posts that you have liked: <span class="font-weight-bold"><?= $userStatistics['posts_likes_given']; ?></span>
                        </p>
                        <p>
                            <i class="fa fa-comments-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Comments that you have posted: <span class="font-weight-bold"><?= $userStatistics['posts_comments_given']; ?></span>
                        </p>

                        <h6 class="mb-3">Actions of other ponies on your posts:</h6>
                        <p class="mb-0">
                            <i class="fa fa-heart-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Likes that you have received: <span class="font-weight-bold"><?= $userStatistics['posts_likes_received']; ?></span></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-commenting-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Comments that you have received: <span class="font-weight-bold"><?= $userStatistics['posts_comments_received']; ?></span></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-ban text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            Your posts removed by moderators: <span class="font-weight-bold"><?= $userStatistics['posts_removed_mod']; ?></span>
                        </p>
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
                require('../../application/partials/social/achievements/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>
</body>
</html>
