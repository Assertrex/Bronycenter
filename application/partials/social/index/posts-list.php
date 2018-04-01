<section class="fancybox mb-0" id="posts-list">
    <h6 class="text-center mb-0"><?= $o_translation->getString('postslist', 'recentPosts') ?></h6>

    <?php
    $currentPage = intval($_GET['p'] ?? 1);
    $currentPage = $currentPage ? $currentPage : 1;

    $postsAmount = $posts->getPostsAmount();
    $fetchAmount = 25;
    $fetchOffset = ($currentPage ? $currentPage * $fetchAmount - $fetchAmount : 0) ?? 0;

    // Display a notification about new posts only on a first page
    if ($currentPage === 1) {
    ?>
    <p class="mb-0 py-2 py-lg-3 text-center" id="posts-list-checker" style="border-bottom: 1px solid #E9ECEF;">
        <button type="button" class="d-inline-block btn btn-sm btn-outline-dark" id="posts-list-checker-unavailable" style="cursor: not-allowed;" disabled><?= $o_translation->getString('postslist', 'noNewPostsToShow') ?></button>
        <button type="button" class="d-none btn btn-sm btn-outline-primary" id="posts-list-checker-available">
            <?= ucfirst($o_translation->getString('postslist', 'clickToShowNewPosts')) ?>
        </button>
    </p>
    <?php
    } else {
    ?>
    <div class="py-2 py-lg-3" style="border-bottom: 1px solid #E9ECEF;">
        <?php include_once(__DIR__ . '/posts-pagination.php'); ?>
    </div>
    <?php
    }
    ?>

    <div class="px-2 px-lg-3 py-4" id="posts-list-wrapper">
        <?php
        // Fetch newest available posts
        $listPosts = $posts->get([
            'fetchMode' => 'getNewest',
            'fetchAmount' => $fetchAmount,
            'fetchOffset' => $fetchOffset
        ]);

        // Include partial containing a posts loop
        require(__DIR__ . '/posts-list-loop.php');
        ?>
    </div>
</section>
