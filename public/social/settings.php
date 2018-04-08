<?php
$pageSettings = [
    'title' => 'Settings',
    'robots' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require('../../application/partials/init.php');
require('../../application/partials/social/head.php');

// Get details about selected user
$userDetails = $user->generateUserDetails($_SESSION['account']['id'], ['descriptions' => true, 'sensitive' => true]);

// Generate user badges and add them the array with user details
$userDetails = array_merge(
    $userDetails,
    $utilities->generateUserBadges(
        $userDetails,
        'mx-1 badge badge',
        'vertical-align: text-bottom;'
    )
);

// Add two more account standing badges
switch ($userDetails['account_standing']) {
    case '0':
        $userDetails['account_standing_badge'] = '<span class="mx-1 badge badge-success">Good</span>';
        break;
    default:
        $userDetails['account_standing_badge'] = '<span class="mx-1 badge badge-secondary">Unknown</span>';
}

// Separate birthdate into temporary day/month/year values
if (!is_null($userDetails['birthdate'])) {
    $birthdate_temp = explode('-', $userDetails['birthdate']);
    $userDetails['birthdate_temp']['year'] = $birthdate_temp[0];
    $userDetails['birthdate_temp']['month'] = $birthdate_temp[1];
    $userDetails['birthdate_temp']['day'] = $birthdate_temp[2];
} else {
    $userDetails['birthdate_temp'] = null;
}

// Get user's login history
$loginHistory = $session->getHistory();

// Remember translations of read-only state
switch ($_SESSION['account']['reason_readonly']) {
    case 'unverified':
        $translationAccountReadonly = $o_translation->getString('settings', 'accountUnverified');
        break;
    case 'muted':
        $translationAccountReadonly = $o_translation->getString('settings', 'accountMuted');
        break;
    default:
        $translationAccountReadonly = $o_translation->getString('settings', 'accountReadonly');
}
?>

<body id="page-social-settings">
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
            <aside id="aside-list" class="col-12 col-lg-4">
                <section class="fancybox mt-0">
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'accountSettings') ?></h6>

                    <ul class="list-group">
                        <li class="list-group-item active" id="aside-list-credentials"><a href="#credentials" class="d-flex align-items-center"><i class="fa fa-key mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'loginCredentials') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-email"><a href="#email" class="d-flex align-items-center"><i class="fa fa-envelope-o mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'emailSettings') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-standing"><a href="#standing" class="d-flex align-items-center"><i class="fa fa-exclamation-triangle mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'accountStanding') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-login"><a href="#login" class="d-flex align-items-center"><i class="fa fa-clock-o mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'loginHistory') ?></span></a></li>
                    </ul>
                </section>

                <section class="fancybox mt-lg-0">
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'profileSettings') ?></h6>

                    <ul class="list-group">
                        <li class="list-group-item" id="aside-list-basic"><a href="#basic" class="d-flex align-items-center"><i class="fa fa-user-circle-o mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'basicInformation') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-details"><a href="#details" class="d-flex align-items-center"><i class="fa fa-file-text-o mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'tellAboutYourself') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-fandom"><a href="#fandom" class="d-flex align-items-center"><i class="fa fa-users mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'youInFandom') ?></span></a></li>
                        <li class="list-group-item" id="aside-list-creations"><a href="#creations" class="d-flex align-items-center"><i class="fa fa-star-o mr-2" aria-hidden="true"></i> <span><?= $o_translation->getString('settings', 'shareYourCreativity') ?></span></a></li>
                    </ul>
                </section>
            </aside>

            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0 mb-0" id="tabs-content">
                    <?php require('../../application/partials/social/settings/content-credentials.php'); ?>
                    <?php require('../../application/partials/social/settings/content-email.php'); ?>
                    <?php require('../../application/partials/social/settings/content-standing.php'); ?>
                    <?php require('../../application/partials/social/settings/content-login.php'); ?>
                    <?php require('../../application/partials/social/settings/content-basic.php'); ?>
                    <?php require('../../application/partials/social/settings/content-details.php'); ?>
                    <?php require('../../application/partials/social/settings/content-fandom.php'); ?>
                    <?php require('../../application/partials/social/settings/content-creations.php'); ?>
                </section>
            </div>
        </div>
    </div>

    <?php require('../../application/partials/social/footer.php'); ?>
    <?php require('../../application/partials/social/scripts.php'); ?>

    <script src="../resources/scripts/social/settings.js?v=<?= $o_config->getWebsiteCommit(true) ?>"></script>
</body>
</html>
