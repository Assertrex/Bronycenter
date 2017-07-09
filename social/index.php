<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Create a post if user has submitted a form.
if (!empty($_POST['submit']) && $_POST['submit'] === 'createpost') {
    // Try to create a new post.
    $o_post->create($_SESSION['account']['id'], $_POST['message'], 1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Social :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <style type="text/css">
    .post-row { border-bottom: 1px solid #EEE; }
    .post-row:last-child { border: 0; }
    </style>
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');
    ?>

    <div class="container">
        <section class="my-5">
            <?php
            // Require HTML containing form for post creating.
            require_once('inc/posts-create.php');
            ?>
        </section>

        <section class="mb-5">
            <?php
            // Require HTML containing recent posts.
            require_once('inc/posts-recent.php');
            ?>
        </section>
    </div>

    <?php
    // Require all common JavaScript (like JQuery and Bootstrap).
    require_once('../system/inc/scripts.php');
    ?>
    <script type="text/javascript" src="../resources/js/social.js"></script>
    <script type="text/javascript">
    // First check if document is ready.
    $(document).ready(function() {
        // Call a post like function on button click.
        $(".btn-postlike").click(likePost);

        // Call a post delete function on button click.
        $(".btn-postdelete").click(deletePost);

        // Post like function.
        function likePost(e) {
            // Store details in a variables.
            let postID = e.currentTarget.getAttribute('data-postid');
            let likeStatus = e.currentTarget.getAttribute('data-liked');

            // Make an AJAX call to like post code.
            $.post(
                "ajax/likePost.php",
                { id: postID },
                function(json) {
                    // TODO Display status from JSON here.
                    // TODO Check if action was successful first.

                    // Check if post was already liked and like or unlike it.
                    if (likeStatus === 'false') {
                        e.currentTarget.setAttribute('data-liked', 'true');
                        $(e.currentTarget).toggleClass('btn-outline-primary');
                        $(e.currentTarget).toggleClass('btn-outline-success');
                        $(e.currentTarget).html('<i class="fa fa-thumbs-o-down" aria-hidden="true"></i> Unlike');
                    } else if (likeStatus === 'true') {
                        e.currentTarget.setAttribute('data-liked', 'false');
                        $(e.currentTarget).toggleClass('btn-outline-success');
                        $(e.currentTarget).toggleClass('btn-outline-primary');
                        $(e.currentTarget).html('<i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like');
                    }
                }
            );
        }

        // Post delete function.
        function deletePost(e) {
            // Store details in a variables.
            let postID = e.currentTarget.getAttribute('data-postid');
            let articleElement = e.currentTarget.parentNode.parentNode.parentNode.parentNode;

            // TODO Show a confirmation modal.

            // Make an AJAX call to like post code.
            $.post(
                "ajax/deletePost.php",
                { id: postID },
                function(json) {
                    // TODO Display status from JSON here.
                    // TODO Check if post was successfully removed first.

                    // Hide a deleted post from DOM.
                    $(articleElement).hide();
                }
            );
        }
    });
    </script>
</body>
</html>
