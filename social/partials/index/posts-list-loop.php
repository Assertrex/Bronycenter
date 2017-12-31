<?php
// Display each found post
foreach ($listPosts as $post) {
    // Check if author is currently logged in
    $post['userOnline'] = $user->isOnline(null, $post['last_online']);
    $post['userOnlineBadge'] = $post['userOnline'] ? '<span class="d-block badge badge-success mt-1">Online</span>' : '';

    // Remember amount of post comments
    $commentsAmount = intval($post['comment_count']);

    // Remember if post has been edited
    $wasEdited = false;

    if ($post['edit_count'] != 0) {
        $wasEdited = true;

        if ($post['edit_count'] > 1) {
            $editCounterString = ' ' . $post['edit_count'] . ' times';
        } else {
            $editCounterString = '';
        }
    }

    // Get array of post comments (limit to 2 newest) if any exists
    if ($commentsAmount > 0) {
        $comments = $posts->getComments($post['id'], null, 2);
    }

    // Check if post contains any likes
    if ($post['hasLikes']) {
        // Get list of a post likes
        $likes = $posts->getLikes($post['id']);

        // Get string about users that has liked a post
        $likesString = $posts->getLikesString($post['id'], $likes, $post['hasLiked']);
    }

    // Name gender types
    switch ($post['gender']) {
        case 1:
            $post['gender'] = 'Male';
            break;
        case 2:
            $post['gender'] = 'Female';
            break;
        default:
            $post['gender'] = 'Unknown gender';
    }

    // Format birthdate if available
    if (!is_null($post['birthdate'])) {
        $current_date = new DateTime();
        $age_interval = new DateTime($post['birthdate']);
        $age_interval = $current_date->diff($age_interval);
        $post['birthdate'] = $age_interval->format('%y years old');
    } else {
        $post['birthdate'] = 'Unknown age';
    }

    // Get a full name of user's country
    $post['country_code'] = $utilities->getCountryName($post['country_code']);

    // TODO ESCAPE STRINGS
    $usernameTooltip = '
    <div style=\'padding: .5rem .25rem; line-height: 1.2;\'>
        <div>' . htmlspecialchars($post['display_name']) . '</div>
        <div><small class=\'text-muted\'>@' . htmlspecialchars($post['username']) . '</small></div>

        <div style=\'padding-top: 8px; text-align: left;\'>
            <div style=\'margin-bottom: 1px;\'>
                <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                    <i class=\'fa fa-transgender text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                </span>
                <small>' . $post['gender'] . '</small>
            </div>
            <div style=\'margin-bottom: 1px;\'>
                <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                    <i class=\'fa fa-user-o text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                </span>
                <small>' . $post['birthdate'] . '</small>
            </div>
            <div>
                <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                    <i class=\'fa fa-map-marker text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                </span>
                <small>' . $post['country_code'] . '</small>
            </div>
        </div>
    </div>
    ';
?>

<article class="d-flex flex-column px-1" id="post-<?php echo $post['id']; ?>">
    <div class="d-flex">
        <div class="mr-3 post-author-details">
            <img class="rounded" src="<?php echo $post['avatar']; ?>" alt="User's avatar">
            <div><?php echo $post['userBadge']; ?></div>
            <div><?php echo $post['userOnlineBadge']; ?></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <div class="mb-2">
                <p class="post-displayname d-flex align-items-center justify-content-between font-weight-bold mb-0">
                    <a href="profile.php?u=<?php echo $post['user_id']; ?>" data-toggle="tooltip" data-html="true" title="<?php echo $usernameTooltip; ?>">
                        <?php echo htmlspecialchars($post['display_name']); ?>
                    </a>
                    <small class="d-none d-md-inline-block text-muted" data-toggle="tooltip" style="cursor: help;" title="<?php echo $post['datetime'] . ' (UTC)'; ?>">
                        <?php echo $post['datetimeInterval']; ?>
                    </small>
                </p>
                <p class="d-block d-md-none mb-0">
                    <small class="text-muted" data-toggle="tooltip" style="cursor: help;" title="<?php echo $post['datetime'] . ' (UTC)'; ?>">
                        <i class="d-none d-lg-inline fa fa-clock-o pr-1"></i> <?php echo $post['datetimeInterval']; ?>
                    </small>
                </p>
            </div>

            <div class="d-flex align-items-center post-content" style="flex: 1; margin-bottom: .75rem;">
                <div>
                    <?php
                    if ($post['content'] != NULL) {
                        echo $wasEdited ? '<div><small class="font-weight-bold text-dark" style="opacity: .5;">Post has been edited' . $editCounterString . '.</small></div>' : '';
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
                                echo '<small style="opacity: .6;"><i class="fa fa-address-book-o text-dark mr-1" aria-hidden="true"></i></small> ' .
                                     '<span class="post-content-text">Has changed his/her display name.</span>';
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
                if (!$post['hasLiked']) {
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
                    if ($wasEdited) {
                    ?>
                    <button class="dropdown-item btn-postedithistory" type="button" role="button" data-toggle="modal" data-target="#mainModal" data-postid="<?php echo $post['id']; ?>" disabled>
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
                    if ($post['ownPost'] && $post['type'] == 1) {
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
                    <button class="dropdown-item disabled" type="button" role="button" disabled>
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-exclamation-triangle" style="margin-right: 12px;" aria-hidden="true"></i>
                            <span style="flex: 1;">Report</span>
                        </div>
                    </button>
                    <?php
                    } // if
                    ?>

                    <?php
                    // Display moderate section only for moderators
                    if ($loggedModerator && !$post['ownPost'] && $post['type'] == 1) {
                    ?>
                    <h6 class="dropdown-header" style="font-size: .813rem;">Moderate</h6>

                    <button class="dropdown-item btn-deletepost" type="button" role="button" data-postid="<?php echo $post['id']; ?>" data-moderate="true">
                        <div class="d-flex align-items-center" style="font-size: .875em;">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                            <span class="text-right" style="flex: 1;">Delete</span>
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
            <div class="<?php echo empty($likesString) ? 'd-none' : 'd-block'; ?> pt-3 post-likes">
                <small class="post-likes-string"><?php echo $likesString ?? ''; ?></small>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="mr-3">
            <div class="post-wrapleftunder"></div>
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <!-- Display post comments if any exists -->
            <div class="pt-2" id="post-comments-wrapper" style="<?php echo $commentsAmount ? '' : 'display: none;'; ?>">
                <?php
                if ($commentsAmount > 2) {
                    $lastCommentID = $comments[0]['id'];
                    $commentsShown = count($comments);
                ?>
                <p class="mb-0 text-muted" id="posts-comments-showing-wrapper">
                    <small>
                        Showing <span class="var-commentsshown"><?php echo $commentsShown; ?></span> of <span class="var-commentsamount"><?php echo $commentsAmount; ?></span> comments.
                        <a href="#" class="btn-loadmorecomments"
                              data-postID="<?php echo $post['id']; ?>"
                              data-lastCommentID="<?php echo $lastCommentID; ?>"
                              data-commentsAmount="<?php echo $commentsAmount; ?>"
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
                    if ($commentsAmount) {
                        foreach ($comments as $comment) {
                    ?>

                    <div class="d-flex py-2 comment-container" id="comment-<?php echo $comment['id']; ?>" data-commentid="<?php echo $comment['id']; ?>">
                        <div class="mr-2">
                            <img src="../media/avatars/<?php echo $comment['avatar'] ?? 'default'; ?>/minres.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                        </div>
                        <div class="mr-2" style="flex: 1;">
                            <small class="d-block pb-1" style="line-height: 1;">
                                <a href="profile.php?u=<?php echo $comment['user_id']; ?>"><?php echo htmlspecialchars($comment['display_name']); ?></a>
                            </small>
                            <small class="d-block pt-1" style="line-height: 1.4; word-break: break-word;">
                                <?php echo $utilities->doEscapeString($comment['content']); ?>
                            </small>
                        </div>
                        <div>
                            <small style="color: #BDBDBD; line-height: 1; vertical-align: top; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $comment['datetime']; ?> (UTC)">
                                <?php echo $utilities->getDateIntervalString($utilities->countDateInterval($comment['datetime'])); ?>
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
    // Delete likes variables to prevent displaying likes from previous post
    unset($likes);
    unset($likesString);
} // foreach
?>