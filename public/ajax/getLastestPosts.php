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

// Get list of newest posts
$listPosts = $posts->get([
    'fetchMode' => 'getLastest',
    'fetchFromID' => $_GET['id']
]);

// Include partial containing a posts loop
if (!empty(intval($_GET['id']))) {
    echo '<div class="posts-container-from" id="posts-container-from-' . $_GET['id'] . '" style="display: none;">';
    require('../../application/partials/social/index/posts-list-loop.php');
    echo '</div>';
}
