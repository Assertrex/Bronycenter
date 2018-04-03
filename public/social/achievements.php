<?php
$pageSettings = [
    'title' => 'Achievements',
    'robots' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require('../../application/partials/init.php');
require('../../application/partials/social/head.php');

$userStatistics = $statistics->get();
?>

<body id="page-social-achievements">
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
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'yourStatistics') ?></h6>

                    <div class="px-2 px-lg-3 py-4">
                        <p>
                            <i class="fa fa-star-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'yourPoints') ?>: <span class="font-weight-bold"><?= $userStatistics['user_points']; ?></span>
                        </p>

                        <h6 class="mb-3"><?= $o_translation->getString('achievements', 'yourActionsOnPosts') ?>:</h6>
                        <p class="mb-0">
                            <i class="fa fa-pencil-square-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'postsThatYouHavePublished') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_created']; ?></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-trash-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'postsThatYouHaveRemoved') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_removed']; ?></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-thumbs-o-up text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'postsThatYouHaveLiked') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_likes_given']; ?></span>
                        </p>
                        <p>
                            <i class="fa fa-comments-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'commentsThatYouHavePosted') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_comments_given']; ?></span>
                        </p>

                        <h6 class="mb-3"><?= $o_translation->getString('achievements', 'othersActionsOnYourPosts') ?>:</h6>
                        <p class="mb-0">
                            <i class="fa fa-heart-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'likesThatYouHaveReceived') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_likes_received']; ?></span></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-commenting-o text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'commentsThatYouHaveReceived') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_comments_received']; ?></span></span>
                        </p>
                        <p class="mb-0">
                            <i class="fa fa-ban text-primary text-center mr-2" style="width: 15px;" aria-hidden="true"></i>
                            <?= $o_translation->getString('achievements', 'yourPostsRemovedByModerators') ?>: <span class="font-weight-bold"><?= $userStatistics['posts_removed_mod']; ?></span>
                        </p>
                    </div>
                </section>

                <section class="fancybox mt-0 mb-0" id="achievements">
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'yourAchievements') ?></h6>

                    <div class="px-2 px-lg-3 py-4">
                        <div class="text-info text-center">
                            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                            <?= $o_translation->getString('common', 'featureNotAvailable') ?>
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
