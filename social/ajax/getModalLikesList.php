<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Use post class reading post likes
use BronyCenter\Post;
$posts = Post::getInstance();

// Get list of post likes
$likes = $posts->getLikes($_GET['id']);

// Memorize last element of the array
end($likes);
$last = key($likes);

// Display list of users that has liked a post
foreach ($likes as $key => $like) {
    // Generate path to user's avatar
    $avatar = '../media/avatars/' . ($like['avatar'] ?? 'default') . '/minres.jpg';

    // Check if an element is last element in an array
    if ($key === $last) {
        $isLast = true;
    } else {
        $isLast = false;
    }
?>

<a href="profile.php?u=<?php echo $like['id']; ?>" style="text-decoration: none;">
    <div class="d-flex align-items-center py-2 px-3" <?php echo $isLast ? '' : 'style="border-bottom: 1px solid #ECEEEF;"'; ?>>
        <div>
            <img src="../media/avatars/<?php echo $like['avatar']; ?>/minres.jpg" class="rounded" style="width: 48px; height: 48px;" />
        </div>
        <div class="px-3" style="line-height: 1.2;">
            <div><?php echo htmlspecialchars($like['display_name']); ?></div>
            <div><small class="text-muted">@<?php echo $like['username']; ?></small></div>
        </div>
    </div>
</a>

<?php
}
// TODO Return error in JSON if not
