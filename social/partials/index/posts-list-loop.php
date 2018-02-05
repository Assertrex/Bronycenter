<?php
// Display each found post
foreach ($listPosts as $post) {
    // Generate additional details about user or get a cached version of it and add to the array
    $post['author'] = $user->generateUserDetails($post['user_id']);
    $post['author'] = array_merge($post['author'], $utilities->generateUserBadges($post['author'], 'd-block mt-1 badge badge'));
?>

<article class="d-flex flex-column px-1" id="post-<?php echo $post['id']; ?>">
    <div class="d-flex">
        <div class="mr-3 post-author-details">
            <img class="rounded mb-1" src="../media/avatars/<?php echo $post['author']['avatar']; ?>/minres.jpg" alt="User's avatar">
            <?php echo $post['author']['is_online'] ? $post['author']['is_online_badge'] : ''; ?>
            <?php echo $post['author']['account_type_badge'] ?? ''; ?>
            <?php echo $post['author']['account_standing_badge'] ?? ''; ?>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <div class="mb-2">
                <p class="post-displayname d-flex align-items-center justify-content-between font-weight-bold mb-0">
                    <a href="profile.php?u=<?php echo $post['author']['id']; ?>" data-toggle="tooltip" data-html="true" title="<?php echo $post['author']['tooltip']; ?>">
                        <?php echo $post['author']['display_name']; ?>
                    </a>
                    <small class="d-none d-md-inline-block text-muted" data-toggle="tooltip" style="cursor: help;" title="<?php echo $post['datetime'] . ' (UTC)'; ?>">
                        <?php echo $post['datetime_interval']; ?>
                    </small>
                </p>
                <p class="d-block d-md-none mb-0">
                    <small class="text-muted" data-toggle="tooltip" style="cursor: help;" title="<?php echo $post['datetime'] . ' (UTC)'; ?>">
                        <i class="d-none d-lg-inline fa fa-clock-o pr-1"></i> <?php echo $post['datetime_interval']; ?>
                    </small>
                </p>
            </div>

            <div class="d-flex align-items-center post-content" style="flex: 1; margin-bottom: .75rem;">
                <div>
                    <?php
                    if ($post['content'] != NULL) {
                        echo $post['was_edited'] ? '<div><small class="font-weight-bold text-dark" style="opacity: .5;">Post has been edited' . $post['edit_count_string'] . '.</small></div>' : '';
                        echo '<span class="post-content-text">' . $utilities->doEscapeString($post['content']) . '</span>';
                    } else {
                    ?>

                    <div>
                        <small class="font-weight-bold text-dark" style="opacity: .5;">Posted by a server</small>
                    </div>

                    <?php
                        switch ($post['type']) {
                            case '10':
                                echo '<small style="opacity: .6;"><i class="fa fa-user-plus text-dark mr-1" aria-hidden="true"></i></small> ' .
                                     '<span class="post-content-text">Has created a new account. Welcome our new member!</span>';
                                break;
                            case '11':
                                echo '<small style="opacity: .6;"><i class="fa fa-address-book-o text-dark mr-1" aria-hidden="true"></i></small> ';

                                // Different message depending on user's gender
                                switch ($post['author']['gender']) {
                                    case '1':
                                        echo '<span class="post-content-text">Has changed his display name.</span>';
                                    break;
                                    case '2':
                                        echo '<span class="post-content-text">Has changed her display name.</span>';
                                    break;
                                    default:
                                        echo '<span class="post-content-text">Has changed his/her display name.</span>';
                                }
                                break;
                            default:
                                echo '<small style="opacity: .6;"><i class="fa fa-question-circle-o text-dark mr-1" aria-hidden="true"></i></small> ' .
                                     '<span class="post-content-text">Server message of an invalid type!</span>';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="d-flex post-actions" data-postid="<?php echo $post['id']; ?>">
                <?php
                // Display not active like button if user has not liked a post
                if (!$post['current_user_liked']) {
                ?>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-postlike mr-1" data-postid="<?php echo $post['id']; ?>" data-hasliked="false">
                    <span class="d-inline-block" style="width: 14px; height: 12px; text-align: center;">
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                    </span>
                    <span style="vertical-align: middle;">Like</span>
                </button>
                <?php
                // Display active like button if user has already liked a post
                } else {
                ?>
                <button type="button" class="btn btn-outline-primary btn-sm btn-postlike mr-1" data-postid="<?php echo $post['id']; ?>" data-hasliked="true">
                    <span class="d-inline-block" style="width: 14px; height: 12px; text-align: center;">
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                    </span>
                    <span style="vertical-align: middle;">Like</span>
                </button>
                <?php
                }
                ?>

                <button type="button" class="btn btn-outline-secondary btn-sm btn-postcommentswitch mr-1" data-active="false">
                    <span class="d-inline-block" style="width: 14px; height: 12px; text-align: center;">
                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                    </span> Comment
                </button>

                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <h6 class="dropdown-header" style="font-size: .813rem;">Actions</h6>

                    <?php
                    // Display edit history for posts that have been edited at least once
                    if ($post['was_edited']) {
                    ?>
                    <button class="dropdown-item btn-showedithistorymodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?php echo $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-history" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Edit history</span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>

                    <?php
                    // Display editing tools only for post author
                    if ($post['is_current_user_author'] && $post['type'] == 1) {
                    ?>
                    <button class="dropdown-item btn-postedit" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?php echo $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-pencil-square-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Edit</span>
                        </div>
                    </button>

                    <button class="dropdown-item btn-deletepost" type="button" role="button" data-postid="<?php echo $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-trash-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Delete</span>
                        </div>
                    </button>
                    <?php
                    } else {
                    ?>
                    <button class="dropdown-item btn-showreportmodal" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?php echo $post['id']; ?>">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-flag-o" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Report</span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>

                    <?php
                    // Display moderate section only for moderators
                    if ($loggedModerator && !$post['is_current_user_author'] && $post['type'] == 1) {
                    ?>
                    <h6 class="dropdown-header" style="font-size: .813rem;">Moderate</h6>

                    <button class="dropdown-item btn-deletepost" type="button" role="button" data-postid="<?php echo $post['id']; ?>" data-moderate="true">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-ban" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Delete</span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="mr-3">
            <div class="post-wrapleftunder"></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <!-- Display post likes string if any like exists -->
            <div class="<?php echo empty($post['string_likes']) ? 'd-none' : 'd-block'; ?> pt-3 post-likes">
                <small class="post-likes-string"><?php echo $post['string_likes'] ?? ''; ?></small>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="mr-3">
            <div class="post-wrapleftunder"></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <!-- Display post comments if any exists -->
            <div class="pt-2" id="post-comments-wrapper" style="<?php echo $post['amount_comments'] ? '' : 'display: none;'; ?>">
                <?php
                if ($post['amount_comments'] > 2) {
                    $lastCommentID = $post['array_comments'][0]['id'];
                    $commentsShown = count($post['array_comments']);
                ?>
                <p class="mb-0 text-muted" id="posts-comments-showing-wrapper">
                    <small>
                        Showing <span class="var-commentsshown"><?php echo $commentsShown; ?></span> of <span class="var-commentsamount"><?php echo $post['amount_comments']; ?></span> comments.
                        <a href="#" class="btn-loadmorecomments"
                              data-postID="<?php echo $post['id']; ?>"
                              data-lastCommentID="<?php echo $lastCommentID; ?>"
                              data-commentsAmount="<?php echo $post['amount_comments']; ?>"
                              data-commentsShown="<?php echo $commentsShown; ?>">
                              View more</a>
                    </small>
                </p>
                <?php
                } // if
                ?>

                <div id="post-comments-container-<?php echo $post['id']; ?>">
                    <?php
                    // Display each comment.
                    if ($post['amount_comments']) {
                        foreach ($post['array_comments'] as $comment) {
                            // Format comment publish datetime into readable string
                            $comment['datetime_string'] = $utilities->getDateIntervalString($utilities->countDateInterval($comment['datetime']));

                            // Generate additional details about user or get a cached version of it and add to the array
                            $comment['author'] = $user->generateUserDetails($comment['user_id']);
                    ?>

                    <div class="d-flex py-2 comment-container" id="comment-<?php echo $comment['id']; ?>" data-commentid="<?php echo $comment['id']; ?>">
                        <div class="mr-2">
                            <img src="../media/avatars/<?php echo $comment['author']['avatar'] ?? 'default'; ?>/minres.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                        </div>
                        <div class="mr-2" style="flex: 1;">
                            <small class="d-block pb-1" style="line-height: 1;">
                                <a href="profile.php?u=<?php echo $comment['author']['id']; ?>" data-toggle="tooltip" data-html="true" title="<?php echo $comment['author']['tooltip']; ?>"><?php echo $utilities->doEscapeString($comment['author']['display_name']); ?></a>
                            </small>
                            <small class="d-block pt-1" style="line-height: 1.4; word-break: break-word;">
                                <?php echo $utilities->doEscapeString($comment['content']); ?>
                            </small>
                        </div>
                        <div>
                            <small style="color: #BDBDBD; line-height: 1; vertical-align: top; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $comment['datetime']; ?> (UTC)">
                                <?php echo $comment['datetime_string']; ?>
                            </small>
                        </div>
                    </div>

                    <?php
                        } // foreach
                    } // if
                    ?>
                </div>
            </div>

            <div id="post-comment-input-wrapper-<?php echo $post['id']; ?>" style="display: none;">
                <div class="d-flex pt-3">
                    <div class="pr-2">
                        <img src="../media/avatars/<?php echo $_SESSION['user']['avatar']; ?>/minres.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                    </div>
                    <div class="pr-2" style="flex: 100%; text-align: right;">
                        <input type="text" class="form-control form-control-sm post-comment-input" placeholder="Write a comment..." maxlength="500" style="font-size: 12px;" />
                        <small class="text-muted"><span class="text-danger">Letter counter not available yet!</span></small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm btn-postcommentsend" role="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<?php
} // foreach
?>
