<?php
$pageSettings = [
    'title' => 'Profile',
    'robots' => false,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../../application/partials/init.php');
require('../../application/partials/social/head.php');

if (!empty($_GET['u'])) {
    $profileId = intval($_GET['u']);
} else if (!empty($_SESSION['account']['id'])) {
    $profileId = $_SESSION['account']['id'];
} else {
    $profileId = null;
}

if (empty($profileId)) {
    $flash->info('You\'ve been redirected into members page, because no profile has been selected.');
    $utilities->redirect('members.php');
}

$profileDetails = $user->generateUserDetails($profileId, ['descriptions' => true, 'statistics' => true]);

if (empty($profileDetails)) {
    $flash->info('You\'ve been redirected into members page, because user doesn\'t exist.');
    $utilities->redirect('members.php');
}

$profileDetails = array_merge(
    $profileDetails,
    $utilities->generateUserBadges(
        $profileDetails,
        'badge badge'
    )
);
?>

<body id="page-social-profile">
    <?php require('../../application/partials/social/header.php'); ?>

    <div class="container <?= $profileDetails['is_online'] ?: 'guest'; ?>">
        <?php require('../../application/partials/flash.php'); ?>

        <div class="row">
            <aside class="col-12 col-lg-4">
                <?php require('../../application/partials/social/profile/user-info.php'); ?>
                <?php require('../../application/partials/social/profile/user-details.php'); ?>
                <?php require('../../application/partials/social/profile/user-actions.php'); ?>
            </aside>

            <div class="col-12 col-lg-8">
                <section class="fancybox my-0" id="aside-wrapper">
                    <?php require('../../application/partials/social/profile/navbar.php'); ?>

                    <div id="aside-tabs-content">
                        <?php require('../../application/partials/social/profile/tab-1-content.php'); ?>
                        <?php require('../../application/partials/social/profile/tab-2-content.php'); ?>
                        <?php require('../../application/partials/social/profile/tab-3-content.php'); ?>
                        <?php require('../../application/partials/social/profile/tab-4-content.php'); ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php require('../../application/partials/social/footer.php'); ?>
    <?php require('../../application/partials/social/scripts.php'); ?>

    <script src="../resources/scripts/social/profile.js?v=<?= $o_config->getWebsiteCommit(true) ?>"></script>
</body>
</html>
