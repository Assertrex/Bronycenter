<h2 class="text-center mb-4">Recent posts</h2>

<?php
$posts = $o_post->getRecent();

foreach ($posts as $post) {
    $publishInterval = $o_system->getDateIntervalString($o_system->countDateInterval($post['datetime']));

    $post['avatar'] = $post['avatar'] ?? 'default';

    $isOnline = false;
    if ($o_system->countDateInterval($post['last_online']) < 90) {
        $isOnline = true;
    }
?>

<article class="post-row py-4">
    <div class="d-flex">
        <div class="pr-3">
            <img src="../media/avatars/<?php echo $post['avatar']; ?>/64.jpg" class="rounded" />
        </div>
        <div class="d-flex flex-column">
            <div style="margin-top: -5px;">
                <?php if ($post['type'] == 1) { ?>

                <div>
                    <div class="font-weight-bold mb-1"><a href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a></div>
                    <div><?php echo $post['content']; ?></div>
                </div>

                <?php } else if ($post['type'] == 10) { ?>

                <div>
                    <div class="mb-1"><a class="font-weight-bold" href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a> has joined BronyCenter.</div>
                    <div>Welcome our new member!</div>
                </div>

                <?php } else if ($post['type'] == 11) { ?>

                <div>
                    <a class="font-weight-bold" href="profile.php?u=<?php echo $post['user_id']; ?>"><?php echo $post['display_name']; ?></a> has changed display name.
                </div>

                <?php } ?>
            </div>

            <div class="pt-3">
                <?php if ($emailVerified) { ?>
                    <?php if (is_null($post['like_id'])) { ?>
                    <button type="button" class="btn btn-outline-primary btn-sm btn-postlike" role="button" data-postid="<?php echo $post['id']; ?>" data-liked="false"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like</button>
                    <?php } else { ?>
                    <button type="button" class="btn btn-outline-success btn-sm btn-postlike" role="button" data-postid="<?php echo $post['id']; ?>" data-liked="true"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> Unlike</button>
                    <?php } ?>
                    <button type="button" class="btn btn-outline-primary btn-sm disabled" role="button"><i class="fa fa-comment-o" aria-hidden="true"></i> Comment</button>
                    <button type="button" class="btn btn-outline-primary btn-sm disabled" role="button"><i class="fa fa-retweet" aria-hidden="true"></i> Share</button>
                    <button type="button" class="btn btn-outline-danger btn-sm disabled" role="button"><i class="fa fa-flag-o" aria-hidden="true"></i> Report</button>
                    <?php if ($post['user_id'] == $_SESSION['account']['id'] && $post['type'] == 1) { ?>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-postdelete" role="button" data-postid="<?php echo $post['id']; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-danger"><small><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to do any actions with posts!</small></p>
                <?php } ?>
            </div>
        </div>
        <div class="ml-auto pl-3">
            <small class="text-muted" style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $post['datetime']; ?> (UTC)"><?php echo $publishInterval; ?> <i class="fa fa-clock-o"></i></small>
            <div style="padding-top: 1px; text-align: right;"><?php echo $isOnline ? '<span class="badge badge-success">Online</span>' : ''; ?></div>
        </div>
    </div>
</article>

<?php
}
?>
