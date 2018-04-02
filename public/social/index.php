<?php
use BronyCenter\Post;

$pageSettings = [
    'title' => 'Posts',
    'robots' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require('../../application/partials/init.php');
require('../../application/partials/social/head.php');

$posts = Post::getInstance();
?>

<body id="page-social-index">
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

    <script src="../resources/scripts/social/index.js?v=<?= $o_config->getWebsiteCommit() ?>"></script>
</body>
</html>
