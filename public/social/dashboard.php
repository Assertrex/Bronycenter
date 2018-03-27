<?php
// Allow access only for logged moderators
$loginRequired = true;
$moderatorRequired = true;

// Include system initialization code
require('../../application/partials/init.php');

// Use post class for creating and reading user's posts
use BronyCenter\Post;
$o_post = Post::getInstance();

// Page settings
$pageTitle = 'Dashboard :: BronyCenter';

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container">
        <div><?php require_once('../../application/partials/social/dashboard/dashboard.php'); ?></div>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>
</body>
</html>
