<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Try to add a comment to a post
$comments = $posts->getComments($_GET['id'], $_GET['lastcommentid'], $_GET['amount'] ?? null, $_GET['mode'] ?? null);

// Count fetched comments
$commentsAmount = count($comments);

// Display each comment
foreach ($comments as $comment) {
    // Format comment publish datetime into readable string
    $comment['datetime_string'] = $utilities->getDateIntervalString($utilities->countDateInterval($comment['datetime']));

    // Generate additional details about user or get a cached version of it and add to the array
    $comment['author'] = $user->generateUserDetails($comment['user_id']);
?>

<div class="d-flex py-2 comment-container" id="comment-<?php echo $comment['id']; ?>" data-commentid="<?php echo $comment['id']; ?>" data-fetchamount="<?php echo $commentsAmount; ?>">
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

// TODO Return error in JSON if not
