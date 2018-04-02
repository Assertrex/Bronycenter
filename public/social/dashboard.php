<?php
// Allow access only for logged moderators
$loginRequired = true;
$moderatorRequired = true;

// Include system initialization code
require('../../application/partials/init.php');

// Use post class for creating and reading user's posts
use BronyCenter\Post;
$o_post = Post::getInstance();

// Set "dashboard" as a default page
$dashboardCategory = $_GET['cat'] ?? 'dashboard';

// Page settings
$pageTitle = 'Dashboard';

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body id="page-dashboard">
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container">
        <section class="fancybox mt-0">
            <h6 class="text-center mb-0">Navigation</h6>

            <div id="mobile-nav-category" style="font-size: 14px;">
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($dashboardCategory == 'dashboard') ? 'active' : '' ?>" href="?cat=dashboard">
                        <i class="fa fa-tachometer text-primary mr-3" aria-hidden="true"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($dashboardCategory == 'members') ? 'active' : '' ?>" href="?cat=members">
                        <i class="fa fa-users text-primary mr-3" aria-hidden="true"></i> Members
                    </a>
                </nav>
            </div>
        </section>


        <?php
        // Display selected page
        switch ($dashboardCategory) {
            case 'dashboard':
                require_once('../../application/partials/social/dashboard/dashboard.php');
                break;
            case 'members':
                require_once('../../application/partials/social/dashboard/members.php');
                break;
        }
        ?>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>
</body>
</html>
