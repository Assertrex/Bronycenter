<?php
$reportedPostsCount = $o_post->countReportedPosts();
$reportedPosts = $o_post->getReportedPosts(5);
$reportedPostsAmount = count($reportedPosts);

for ($i = 0; $i < $reportedPostsAmount; $i++) {
    $reportedPosts[$i]['reported_by'] = $o_post->getUsersThatReportedPost($reportedPosts[$i]['id']);
    $reportedPosts[$i]['reported_by_count'] = count($reportedPosts[$i]['reported_by']);
    $reportedPosts[$i]['reported_by_string'] = makeStringOfReportedBy($reportedPosts[$i]['reported_by'], $reportedPosts[$i]['reported_by_count']);
}

function makeStringOfReportedBy($array, $amount) {
    $o_translation = BronyCenter\Translation::getInstance();
    $o_utilities = BronyCenter\Utilities::getInstance();

    $string = $o_translation->getString('dashboard', 'reportedBy');

    $string .= ': <span class="reported-users-item"><a href="profile.php?u=' . $array[0]['id'] . '" target="_blank">' . $o_utilities->doEscapeString($array[0]['display_name']) . '</a></span>';

    for ($i = 1; $i < $amount; $i++) {
        if ($i + 1 != $amount) {
            $string .= ', <span class="reported-users-item"><a href="profile.php?u=' . $array[$i]['id'] . '" target="_blank">' . $o_utilities->doEscapeString($array[$i]['display_name']) . '</a></span>';
        } else {
            $string .= ' and <span class="reported-users-item"><a href="profile.php?u=' . $array[$i]['id'] . '" target="_blank">' . $o_utilities->doEscapeString($array[$i]['display_name']) . '</a></span>';
        }
    }

    return $string;
}
?>

<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'statistics') ?></h6>

    <div class="row">
        <div class="col-lg" style="font-size: 14px;"></div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold">
                <i class="fa fa-list-ol text-primary mr-2" aria-hidden="true"></i>
                <?= $o_translation->getString('dashboard', 'postsCounters') ?>
            </p>

            <p class="mb-0">
                <?= $o_translation->getString('dashboard', 'existingPosts') ?>:
                <b><?= number_format($o_post->countAvailablePosts(), 0, '.', ' ') ?></b>
            </p>
            <p>
                <?= $o_translation->getString('dashboard', 'postsToReview') ?>:
                <b><?= number_format($reportedPostsCount, 0, '.', ' ') ?></b>
            </p>

            <p class="mb-0">
                <?= $o_translation->getString('dashboard', 'allCreatedPosts') ?>:
                <b><?= number_format($o_post->countCreatedPosts(), 0, '.', ' ') ?></b>
            </p>
            <p class="mb-0">
                <?= $o_translation->getString('dashboard', 'allReportedPosts') ?>
                <b><?= number_format($o_post->countAllReportedPosts(), 0, '.', ' ') ?></b>
            </p>
        </div>

        <div class="col-lg" style="font-size: 14px;"></div>
    </div>
</section>

<section class="fancybox mb-0">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'reportedPosts') ?></h6>

    <div id="reported-posts" style="font-size: 14px;">
        <div class="reported-posts-amount py-2 px-3">
            <small class="d-block text-center text-lg-left">
                <?= $o_translation->getString('dashboard', 'showingAmountReportedPosts', [$reportedPostsAmount, $reportedPostsCount]) ?>
            </small>
        </div>
        <?php foreach($reportedPosts as $reportedPost) { ?>
        <div id="reported-posts-item-<?= $reportedPost['id'] ?>" class="reported-item my-2 mx-1">
            <div class="reported-details py-3 px-3">
                <p class="mb-1">
                    <?= $reportedPost['reported_by_string'] ?>
                </p>
                <p class="mb-0 text-muted">
                    <small>
                        ID: <?= $reportedPost['id'] ?> |
                        Type: <?= $reportedPost['type'] ?> |
                        Status: <?= $reportedPost['status'] ?>
                    </small>
                </p>
                <p class="mb-0">
                    <?= $o_translation->getString('dashboard', 'publishedBy') ?>:
                    <a href="profile.php?u=<?= $reportedPost['user_id'] ?>" target="_blank"><?= $reportedPost['display_name'] ?></a>
                </p>
            </div>
            <div class="reported-post px-2 py-3">
                <p class="text-center mb-0">
                    <?= $utilities->replaceURLsWithLinks($utilities->doEscapeString($reportedPost['content'] ?? '')) ?: '<span class="text-danger">SERVER MESSAGE</span>' ?>
                </p>
            </div>
            <div class="reported-actions d-flex justify-content-between py-3 px-3">
                <button class="btn btn-sm btn-outline-danger btn-action-remove" data-toggle="modal" data-target="#mainModal" data-id="<?= $reportedPost['id'] ?>"><?= $o_translation->getString('dashboard', 'remove') ?></button>
                <button class="btn btn-sm btn-outline-success btn-action-approve" data-toggle="modal" data-target="#mainModal" data-id="<?= $reportedPost['id'] ?>"><?= $o_translation->getString('dashboard', 'approve') ?></button>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
