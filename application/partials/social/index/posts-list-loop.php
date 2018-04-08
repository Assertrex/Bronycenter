<?php
// Display each found post
foreach ($listPosts as $post) {
    // Generate additional details about user or get a cached version of it and add to the array
    $post['author'] = $user->generateUserDetails($post['user_id'], []);
    $post['author'] = array_merge($post['author'], $utilities->generateUserBadges($post['author'], 'd-block mt-1 badge badge'));
?>

<article class="d-flex flex-column px-1" id="post-<?= $post['id'] ?>">
    <div class="d-flex">
        <div class="mr-3 post-author-details">
            <img class="rounded mb-1" src="../media/avatars/<?= $post['author']['avatar'] ?>/minres.jpg" alt="<?= $o_translation->getString('postslist', 'userAvatar') ?>">
            <?= $post['author']['account_type_badge'] ?? '' ?>
            <?= $post['author']['account_standing_badge'] ?? '' ?>
            <?= $post['author']['is_online'] ? $post['author']['is_online_badge'] : '' ?>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <div class="mb-2">
                <p class="post-displayname d-flex align-items-center justify-content-between font-weight-bold mb-0">
                    <a href="profile.php?u=<?= $post['author']['id'] ?>" data-toggle="tooltip" data-html="true" title="<?= $post['author']['tooltip'] ?>">
                        <?= $utilities->doEscapeString($post['author']['display_name'], false); ?>
                    </a>
                    <small class="d-none d-md-inline-block text-muted" data-toggle="tooltip" style="cursor: help;" title="<?= $post['datetime'] ?> (UTC)">
                        <?= $post['datetime_interval']; ?>
                    </small>
                </p>
                <p class="d-block d-md-none mb-0">
                    <small class="text-muted" data-toggle="tooltip" style="cursor: help;" title="<?= $post['datetime'] ?> (UTC)">
                        <i class="d-none d-lg-inline fa fa-clock-o pr-1"></i> <?= $post['datetime_interval'] ?>
                    </small>
                </p>
            </div>

            <div class="d-flex align-items-center post-content" style="flex: 1; margin-bottom: .75rem;">
                <div>
                    <?php
                    if ($post['content'] != NULL) {
                        echo $post['was_edited'] ? '<div><small class="font-weight-bold text-dark" style="opacity: .5;">' . $o_translation->getString('postslist', 'postBeenEdited', [$post['edit_count_string']]) . '.</small></div>' : '';
                        echo '<span class="post-content-text">' . $utilities->replaceURLsWithLinks($utilities->doEscapeString($post['content'])) . '</span>';
                    } else {
                    ?>

                    <div>
                        <small class="font-weight-bold text-dark" style="opacity: .5;"><?= $o_translation->getString('postslist', 'postedByServer') ?>.</small>
                    </div>

                    <?php
                        switch ($post['type']) {
                            case '10':
                                echo '<small style="opacity: .6;"><i class="fa fa-user-plus text-dark mr-1" aria-hidden="true"></i></small> ' .
                                     '<span class="post-content-text">' . $o_translation->getString('postslist', 'hasCreatedAnAccount') . '</span>';
                                break;
                            case '11':
                                echo '<small style="opacity: .6;"><i class="fa fa-address-book-o text-dark mr-1" aria-hidden="true"></i></small> ';

                                // Different message depending on user's gender
                                switch ($post['author']['gender']) {
                                    case '1':
                                        echo '<span class="post-content-text">' . $o_translation->getString('postslist', 'hasChangedHisDisplayname') . '</span>';
                                    break;
                                    case '2':
                                        echo '<span class="post-content-text">' . $o_translation->getString('postslist', 'hasChangedHerDisplayname') . '</span>';
                                    break;
                                    default:
                                        echo '<span class="post-content-text">' . $o_translation->getString('postslist', 'hasChangedDisplayname') . '</span>';
                                }
                                break;
                            default:
                                echo '<small style="opacity: .6;"><i class="fa fa-question-circle-o text-dark mr-1" aria-hidden="true"></i></small> ' .
                                     '<span class="post-content-text">' . $o_translation->getString('postslist', 'invalidServerMessage') . '</span>';
                        }
                    }
                    ?>
                </div>
            </div>

            <?php
            if (!$readonlyState) {
            ?>
            <div class="d-flex post-actions" data-postid="<?= $post['id']; ?>">
                <?php if (!$post['current_user_liked']) { ?>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-postlike mr-1" data-postid="<?= $post['id']; ?>" data-hasliked="false">
                <?php } else { ?>
                <button type="button" class="btn btn-outline-primary btn-sm btn-postlike mr-1" data-postid="<?= $post['id']; ?>" data-hasliked="true">
                <?php } ?>
                    <span class="d-inline-block" style="width: 14px; height: 12px; text-align: center;">
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                    </span>
                    <span style="vertical-align: middle;"><?= $o_translation->getString('postslist', 'like') ?></span>
                </button>

                <button type="button" class="btn btn-outline-secondary btn-sm btn-postcommentswitch mr-1" data-active="false">
                    <span class="d-inline-block" style="width: 14px; height: 12px; text-align: center;">
                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                    </span> <?= $o_translation->getString('postslist', 'comment') ?>
                </button>

                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <h6 class="dropdown-header" style="font-size: .813rem;"><?= $o_translation->getString('postslist', 'actions') ?></h6>

                    <?php
                    // Display edit history for posts that have been edited at least once
                    if ($post['was_edited']) {
                    ?>
                    <button class="dropdown-item btn-showedithistorymodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?= $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-history" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;"><?= $o_translation->getString('postslist', 'editHistory') ?></span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>

                    <?php
                    // Display editing tools only for post author
                    if ($post['is_current_user_author'] && $post['type'] == 1) {
                    ?>
                    <button class="dropdown-item btn-postedit" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?= $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-pencil-square-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;"><?= $o_translation->getString('postslist', 'edit') ?></span>
                        </div>
                    </button>

                    <button class="dropdown-item btn-showdeletepostmodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?= $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-trash-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;"><?= $o_translation->getString('postslist', 'delete') ?></span>
                        </div>
                    </button>
                    <?php
                    } else {
                    ?>
                    <button class="dropdown-item btn-showreportmodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?= $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-flag-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;"><?= $o_translation->getString('postslist', 'report') ?></span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>

                    <?php
                    // Display moderate section only for moderators
                    if ($loggedModerator && !$post['is_current_user_author'] && $post['type'] == 1) {
                    ?>
                    <h6 class="dropdown-header" style="font-size: .813rem;"><?= $o_translation->getString('postslist', 'moderate') ?></h6>

                    <button class="dropdown-item btn-showdeletepostmodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?= $post['id']; ?>" data-moderate="true">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-ban" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;"><?= $o_translation->getString('postslist', 'delete') ?></span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>
                </div>
            </div>
            <?php
            } else {
            ?>
            <div class="d-flex post-actions" data-postid="<?= $post['id']; ?>">
                <p class="text-center text-danger mb-0">
                    <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    <small>
                    <?php
                    switch ($_SESSION['account']['reason_readonly']) {
                        case 'unverified':
                            echo $o_translation->getString('postslist', 'accountUnverified');
                            break;
                        case 'muted':
                            echo $o_translation->getString('postslist', 'accountMuted');
                            break;
                        default:
                            echo $o_translation->getString('postslist', 'accountReadonly');
                    }
                    ?>
                    </small>
                </p>
            </div>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="d-flex">
        <div class="mr-3">
            <div class="post-wrapleftunder"></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <!-- Display post likes string if any like exists -->
            <div class="<?= empty($post['string_likes']) ? 'd-none' : 'd-block'; ?> pt-3 post-likes">
                <small class="post-likes-string"><?= $post['string_likes'] ?? ''; ?></small>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="mr-3">
            <div class="post-wrapleftunder"></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <!-- Display post comments if any exists -->
            <div class="pt-2" id="post-comments-wrapper" style="<?= $post['amount_comments'] ? '' : 'display: none;'; ?>">
                <?php
                if ($post['amount_comments'] > 2) {
                    $lastCommentID = $post['array_comments'][0]['id'];
                    $commentsShown = count($post['array_comments']);
                ?>
                <p class="mb-0 text-muted" id="posts-comments-showing-wrapper">
                    <small>
                        <!-- TODO TRANSLATION -->
                        Showing <span class="var-commentsshown"><?= $commentsShown; ?></span> of <span class="var-commentsamount"><?= $post['amount_comments']; ?></span> comments.
                        <a href="#" class="btn-loadmorecomments"
                            data-postID="<?= $post['id']; ?>"
                            data-lastCommentID="<?= $lastCommentID; ?>"
                            data-commentsAmount="<?= $post['amount_comments']; ?>"
                            data-commentsShown="<?= $commentsShown; ?>">
                            <?= $o_translation->getString('postslist', 'viewMore') ?>
                        </a>
                    </small>
                </p>
                <?php
                } // if
                ?>

                <div id="post-comments-container-<?= $post['id']; ?>">
                    <?php
                    // Display each comment.
                    if ($post['amount_comments']) {
                        foreach ($post['array_comments'] as $comment) {
                            // Format comment publish datetime into readable string
                            $comment['datetime_string'] = $utilities->getDateIntervalString($utilities->countDateInterval($comment['datetime']));

                            // Generate additional details about user or get a cached version of it and add to the array
                            $comment['author'] = $user->generateUserDetails($comment['user_id'], []);
                    ?>

                    <div class="d-flex py-2 comment-container" id="comment-<?= $comment['id']; ?>" data-commentid="<?= $comment['id']; ?>">
                        <div class="mr-2">
                            <img src="../media/avatars/<?= $comment['author']['avatar'] ?? 'default'; ?>/minres.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                        </div>
                        <div class="mr-2" style="flex: 1;">
                            <small class="d-block pb-1" style="line-height: 1;">
                                <a href="profile.php?u=<?= $comment['author']['id']; ?>" data-toggle="tooltip" data-html="true" title="<?= $comment['author']['tooltip']; ?>"><?= $utilities->doEscapeString($comment['author']['display_name'], false); ?></a>
                            </small>
                            <small class="d-block pt-1" style="line-height: 1.4; word-break: break-word;">
                                <?= $comment['content'] ?>
                            </small>
                        </div>
                        <div>
                            <small style="color: #BDBDBD; line-height: 1; vertical-align: top; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $comment['datetime']; ?> (UTC)">
                                <?= $comment['datetime_string']; ?>
                            </small>
                        </div>
                    </div>

                    <?php
                        } // foreach
                    } // if
                    ?>
                </div>
            </div>

            <div class="post-comment-input-wrapper" id="post-comment-input-wrapper-<?= $post['id']; ?>" style="display: none;" data-postid="<?= $post['id']; ?>">
                <div class="d-flex pt-3">
                    <div class="pr-2">
                        <img src="../media/avatars/<?= $_SESSION['user']['avatar']; ?>/minres.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                    </div>
                    <div class="pr-2" style="flex: 100%; text-align: right;">
                        <input type="text" class="form-control form-control-sm post-comment-input" id="post-comment-input-<?= $post['id']; ?>" placeholder="<?= $o_translation->getString('postslist', 'writeComment') ?>..." maxlength="500" style="font-size: 12px;" />
                        <small class="d-block text-muted text-right mt-1">
                            <span class="post-comment-input-lettercounter" id="post-comment-input-lettercounter-<?= $post['id']; ?>">0</span> / 500
                        </small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm btn-postcommentsend" role="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <?= $o_translation->getString('postslist', 'send') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<?php
} // foreach
?>
