<section class="fancybox mb-0" id="posts-list">
    <h6 class="text-center mb-0">Recent posts</h6>

    <p class="mb-0 pt-3 text-center" id="posts-list-checker">
        <button type="button" class="d-inline-block btn btn-sm btn-outline-dark" id="posts-list-checker-unavailable" style="cursor: not-allowed;" disabled>No new posts to show</button>
        <button type="button" class="d-none btn btn-sm btn-outline-primary" id="posts-list-checker-available">Click to show <span id="posts-list-checker-available-counter">0</span> new posts</button>
    </p>

    <div class="px-2 px-lg-3 py-4" id="posts-list-wrapper">
        <?php
        // Get an amount of available posts
        $postsAmount = $posts->getPostsAmount();

        $fetchAmount = 15;
        $fetchOffset = (intval($_GET['p'] ?? 0) ? $_GET['p'] * $fetchAmount - $fetchAmount : 0) ?? 0;

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
