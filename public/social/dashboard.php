<?php
use BronyCenter\Post;

$pageSettings = [
    'title' => 'Dashboard',
    'robots' => false,
    'loginRequired' => true,
    'moderatorRequired' => true,
];

require('../../application/partials/init.php');
require('../../application/partials/social/head.php');

$o_post = Post::getInstance();
$dashboardCategory = $_GET['cat'] ?? 'dashboard';
?>

<body id="page-social-dashboard">
    <?php require('../../application/partials/social/header.php'); ?>

    <div class="container">
        <section class="fancybox mt-0">
            <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'navigation') ?></h6>

            <div id="mobile-nav-category" style="font-size: 14px;">
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($dashboardCategory == 'dashboard') ? 'active' : '' ?>" href="?cat=dashboard">
                        <i class="fa fa-tachometer text-primary mr-3" aria-hidden="true"></i> <?= $o_translation->getString('dashboard', 'summary') ?>
                    </a>
                    <a class="nav-link <?= ($dashboardCategory == 'members') ? 'active' : '' ?>" href="?cat=members">
                        <i class="fa fa-users text-primary mr-3" aria-hidden="true"></i> <?= $o_translation->getString('header', 'members') ?>
                    </a>
                    <a class="nav-link <?= ($dashboardCategory == 'posts') ? 'active' : '' ?>" href="?cat=posts">
                        <i class="fa fa-users text-primary mr-3" aria-hidden="true"></i> <?= $o_translation->getString('dashboard', 'posts') ?>
                    </a>
                </nav>
            </div>
        </section>


        <?php
        switch ($dashboardCategory) {
            case 'dashboard':
                require_once('../../application/partials/social/dashboard/dashboard.php');
                break;
            case 'members':
                require_once('../../application/partials/social/dashboard/members.php');
                break;
            case 'posts':
                require_once('../../application/partials/social/dashboard/posts.php');
                break;
        }
        ?>
    </div>

    <?php require('../../application/partials/social/footer.php'); ?>
    <?php require('../../application/partials/modal.php'); ?>
    <?php require('../../application/partials/social/scripts.php'); ?>

    <script src="../resources/scripts/social/dashboard.js?v=<?= $o_config->getWebsiteCommit(true) ?>"></script>
</body>
</html>
