<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Get list of post likes.
$likes = $o_post->getLikes($_GET['id']);

// Display list of users that has liked a post.
foreach ($likes as $like) {
?>

<a href="profile.php?u=<?php echo $like['id']; ?>" style="text-decoration: none;">
    <div class="d-flex align-items-center py-2 px-3" style="border-bottom: 1px solid #ECEEEF;">
        <div>
            <img src="../media/avatars/<?php echo $like['avatar']; ?>/64.jpg" class="rounded" style="width: 48px; height: 48px;" />
        </div>
        <div class="px-3" style="line-height: 1.2;">
            <div><?php echo $like['display_name']; ?></div>
            <div><small class="text-muted">@<?php echo $like['username']; ?></small></div>
        </div>
    </div>
</a>

<?php
}

// TODO Return error in JSON if not.
