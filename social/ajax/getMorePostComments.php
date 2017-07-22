<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Try to add a comment to a post.
$comments = $o_post->getPostComments($_GET['id'], $_GET['lastcommentid'], $_GET['amount'] ?? null, $_GET['mode'] ?? null);

// Count fetched comments.
$commentsAmount = count($comments);

// Display each comment.
foreach ($comments as $comment) {
?>

<div class="d-flex align-items-center pt-2 comment-container" id="comment-<?php echo $comment['id']; ?>" data-commentid="<?php echo $comment['id']; ?>" data-fetchamount="<?php echo $commentsAmount; ?>">
    <div>
        <img src="../media/avatars/<?php echo $comment['avatar'] ?? 'default'; ?>/64.jpg" class="rounded" style="display: block; width: 27px; height: 27px;" />
    </div>
    <div class="ml-2" style="line-height: 1.4;">
        <small class="d-block">
            <a href="profile.php?u=<?php echo $comment['user_id']; ?>"><?php echo $comment['display_name']; ?></a>
            <span class="ml-1"><?php echo htmlspecialchars($comment['content'], ENT_QUOTES); ?></span>
        </small>
        <small class="d-inline-block text-muted" style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $comment['datetime']; ?> (UTC)">
            <?php echo $o_system->getDateIntervalString($o_system->countDateInterval($comment['datetime'])); ?>
        </small>
    </div>
</div>

<?php
} // foreach

// TODO Return error in JSON if not.
