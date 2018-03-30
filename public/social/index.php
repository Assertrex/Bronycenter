<?php
// Allow access only for logged users
$loginRequired = true;

// Include system initialization code
require('../../application/partials/init.php');

// Use post class for creating and reading user's posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Page settings
$pageTitle = 'Posts :: BronyCenter';
$pageStylesheet = '
.posts-container-from { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(189, 189, 189, .5); }
.post-content-text { word-break: break-word; }
.comment-container { border-bottom: 1px solid #EEEEEE; }
.comment-container:last-child { padding-bottom: 0 !important; border-bottom: 0; }
';

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
                <?php
                // Include partial containing a post creator
                require('../../application/partials/social/index/post-creator.php');

                // Include partial containing a posts list
                require('../../application/partials/social/index/posts-list.php');

                // Include partial containing a posts pagination bar
                echo '<div class="my-3">';
                    require('../../application/partials/social/index/posts-pagination.php');
                echo '</div>';
                ?>
            </div>

            <aside class="col-12 col-lg-4">
                <?php
                // Include partial containing an aside panel
                require('../../application/partials/social/index/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>

    <script src="../resources/scripts/social/index.js?v=<?php echo $websiteVersion['commit']; ?>"></script>
</body>
</html>
