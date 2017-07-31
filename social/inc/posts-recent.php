<h2 class="text-center mb-4">Recent posts</h2>

<?php
// Get list of recent posts.
$posts = $o_post->getRecent();

// Display each post.
foreach ($posts as $post) {
    // Make a named interval of when a post has been published.
    $publishInterval = $o_system->getDateIntervalString($o_system->countDateInterval($post['datetime']));

    // Display badge for administrators and moderators.
    switch ($post['account_type']) {
        case '9':
            $userBadge = '<span class="d-block badge badge-danger mt-2">Admin</span>';
            break;
        case '8':
            $userBadge = '<span class="d-block badge badge-info mt-2">Mod</span>';
            break;
        default:
            $userBadge = '';
    }

    // Set user's avatar or get the default one if not existing.
    $post['avatar'] = $post['avatar'] ?? 'default';

    // Check if user is currently logged in.
    $isOnline = $o_user->isOnline(null, $post['last_online']);

    // Get list of post likes.
    $likes = $o_post->getLikes($post['id']);

    // Get string about users that has liked a post.
    $likesString = $o_post->getLikesString($post['id'], $likes, $post['ownlike']);

    // Remember amount of post comments.
    $commentsAmount = intval($post['comment_count']);

    // Get array of post comments (limit to 2 newest) if any exists.
    if ($commentsAmount > 0) {
        $comments = $o_post->getPostComments($post['id'], null, 2);
    }
?>

<article class="post-row py-4" id="post-<?php echo $post['id']; ?>">
    <div class="d-flex">
        <div class="d-flex flex-column pr-3">
            <div><img src="../media/avatars/<?php echo $post['avatar']; ?>/64.jpg" class="rounded" /></div>
            <div><?php echo $userBadge; ?></div>
        </div>
        <div class="d-flex flex-column" style="flex: 100%;">
            <div style="margin-top: -5px;">
                <?php if ($post['type'] == 1) { // Standard post ?>

                <div>
                    <div class="font-weight-bold mb-1"><a href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a></div>
                    <div><?php echo htmlspecialchars($post['content']); ?></div>
                </div>

                <?php } else if ($post['type'] == 10) { // Join post ?>

                <div>
                    <div class="mb-1"><a class="font-weight-bold" href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a> has joined BronyCenter.</div>
                    <div>Welcome our new member!</div>
                </div>

                <?php } else if ($post['type'] == 11) { // Changed username post ?>

                <div>
                    <a class="font-weight-bold" href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a> has changed display name.
                </div>

                <?php } ?>
            </div>

            <div class="pt-3" data-postid="<?php echo $post['id']; ?>">
                <?php
                // Display available actions for logged user with verified e-mail.
                if ($emailVerified) {
                ?>
                    <?php if (!$post['ownlike']) { ?>
                    <button type="button" class="btn btn-outline-primary btn-sm btn-postlike" role="button" data-liked="false" data-ownlike-id="false">
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like
                    </button>
                    <?php } else { ?>
                    <button type="button" class="btn btn-outline-success btn-sm btn-postlike" role="button" data-liked="true" data-ownlike-id="<?php echo $post['ownlike_id']; ?>">
                        <i class="fa fa-thumbs-o-down" aria-hidden="true"></i> Unlike
                    </button>
                    <?php } ?>
                    <button type="button" class="btn btn-outline-primary btn-sm btn-postcommentswitch" role="button" data-active="false"><i class="fa fa-comment-o" aria-hidden="true"></i> Comment</button>
                    <?php if ($post['type'] == 1) { ?>
                    <div class="d-inline-block dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            More
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item disabled" type="button" role="button">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true" style="width: 20px; vertical-align: middle;"></i>
                                <span style="vertical-align: middle;">Report</span>
                            </button>

                            <?php if ($post['user_id'] == $_SESSION['account']['id'] && $post['type'] == 1) { ?>
                                <button class="dropdown-item btn-deletepost" type="button" role="button" style="color: #F44336;" data-postid="<?php echo $post['id']; ?>">
                                    <i class="fa fa-trash-o" aria-hidden="true" style="width: 20px; vertical-align: middle;"></i>
                                    <span style="vertical-align: middle;">Delete</span>
                                </button>
                            <?php } else if ($_SESSION['account']['type'] == 8 || $_SESSION['account']['type'] == 9) { ?>
                                <button class="dropdown-item btn-moddeletepost" type="button" role="button" style="color: #F44336;" data-postid="<?php echo $post['id']; ?>">
                                    <i class="fa fa-ban" aria-hidden="true" style="width: 20px; vertical-align: middle;"></i>
                                    <span style="vertical-align: middle;">Delete as Mod</span>
                                </button>
                            <?php }?>
                        </div>
                    </div>
                    <?php } ?>
                <?php
                } // if
                // Show warning about required e-mail verification if user has not verified it.
                else {
                ?>
                    <p class="text-danger"><small><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to do any actions with posts!</small></p>
                <?php
                } // else
                ?>
            </div>

            <div class="pt-3" id="post-like-wrapper-<?php echo $post['id']; ?>" style="display: <?php echo $post['any_likes'] ? 'block' : 'none'; ?>">
                <small>
                    <i class="fa fa-thumbs-o-up text-muted mr-1" aria-hidden="true"></i>
                    <span id="post-like-string-<?php echo $post['id']; ?>"><?php echo $likesString ?? ''; ?></span>
                </small>
            </div>

            <div class="pt-2" id="post-comments-wrapper" style="<?php echo $commentsAmount ? '' : 'display: none;'; ?>">
                <?php
                if ($commentsAmount > 2) {
                    $lastCommentID = $comments[0]['id'];
                    $commentsShown = count($comments);
                ?>
                <p class="mb-0 text-muted" id="posts-comments-showing-wrapper">
                    <small>
                        Showing <span class="var-commentsshown"><?php echo $commentsShown; ?></span> of <span class="var-commentsamount"><?php echo $commentsAmount; ?></span> comments.
                        <span class="btn-loadmorecomments"
                              data-postID="<?php echo $post['id']; ?>"
                              data-lastCommentID="<?php echo $lastCommentID; ?>"
                              data-commentsAmount="<?php echo $commentsAmount; ?>"
                              data-commentsShown="<?php echo $commentsShown; ?>">
                              View more</span>
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

                    <div class="d-flex align-items-center pt-2 comment-container" id="comment-<?php echo $comment['id']; ?>" data-commentid="<?php echo $comment['id']; ?>">
                        <div>
                            <img src="../media/avatars/<?php echo $comment['avatar'] ?? 'default'; ?>/64.jpg" class="rounded" style="display: block; width: 26px; height: 26px;" />
                        </div>
                        <div class="ml-2" style="line-height: 1.4;">
                            <small class="d-block">
                                <a href="profile.php?u=<?php echo $comment['user_id']; ?>"><?php echo $comment['display_name']; ?></a>
                                <span class="ml-1"><?php echo htmlspecialchars($comment['content']); ?></span>
                            </small>
                            <small class="d-inline-block text-muted" style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $comment['datetime']; ?> (UTC)">
                                <?php echo $o_system->getDateIntervalString($o_system->countDateInterval($comment['datetime'])); ?>
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
                        <img src="../media/avatars/<?php echo $_SESSION['user']['avatar']; ?>/64.jpg" class="rounded" style="display: block; width: 27px; height: 27px;" />
                    </div>
                    <div class="pr-2" style="flex: 100%;">
                        <input type="text" class="form-control form-control-sm post-comment-input" placeholder="Write a comment..." maxlength="250" />
                        <small class="text-muted"><span>0</span> / 250</small>
                    </div>
                    <div style="flex: 100%;">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-postcommentsend" role="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Send</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="pl-3" style="flex: 150px; text-align: right;">
            <small class="text-muted" style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $post['datetime']; ?> (UTC)">
                <?php echo $publishInterval; ?> <i class="fa fa-clock-o"></i>
            </small>
            <!-- <span class="text-muted pl-2" style="cursor: pointer;">
                <i class="fa fa-ellipsis-v" aria-hidden="true" style="vertical-align: middle;"></i>
            </span> -->
            <div style="padding-top: 1px;"><?php echo $isOnline ? '<span class="badge badge-success">Online</span>' : ''; ?></div>
        </div>
    </div>
</article>

<?php
}
?>
