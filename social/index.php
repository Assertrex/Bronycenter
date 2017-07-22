<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Create a post if user has submitted a form.
if (!empty($_POST['submit']) && $_POST['submit'] === 'createpost') {
    // Try to create a new post.
    $o_post->create($_SESSION['account']['id'], $_POST['message'], 1);

    header('Location: index.php');
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
    .btn-postshowlikes, .btn-loadmorecomments { color: #0275d8; cursor: pointer; }
    .btn-postshowlikes:hover, .btn-loadmorecomments:hover { color: #014c8c; text-decoration: underline; }
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
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>

    <script type="text/javascript">
    // First check if document is ready.
    $(document).ready(function() {
        // Call a post like function on button click.
        $(".btn-postlike").click(likePost);

        // Call a post switch comment function on button click.
        $(".btn-postcommentswitch").click(switchCommentPost);

        // Call a more comments loader function on button click.
        $(".btn-loadmorecomments").click(loadMoreComments);

        // Call a post delete function on button click.
        $(".btn-postdelete").click(deletePost);

        // Call a post update likes modal function on button click.
        $(".btn-postshowlikes").click(updateLikesModal);

        // Post like function.
        function likePost(e) {
            // Store details in a variables.
            let postID = e.currentTarget.parentNode.getAttribute('data-postid');
            let likeStatus = e.currentTarget.getAttribute('data-liked');
            let likeID = e.currentTarget.getAttribute('data-ownlike-id');

            // Make an AJAX call to like post code.
            $.post(
                "ajax/likePost.php",
                { id: postID },
                function(json) {
                    // TODO Display status from JSON here.
                    // TODO Check if action was successful first.

                    // Check if post was already liked and like or unlike it.
                    if (likeStatus === 'false') {
                        likeStatus = true;
                        e.currentTarget.setAttribute('data-liked', 'true');
                        $(e.currentTarget).toggleClass('btn-outline-primary');
                        $(e.currentTarget).toggleClass('btn-outline-success');
                        $(e.currentTarget).html('<i class="fa fa-thumbs-o-down" aria-hidden="true"></i> Unlike');
                    } else if (likeStatus === 'true') {
                        likeStatus = false;
                        e.currentTarget.setAttribute('data-liked', 'false');
                        $(e.currentTarget).toggleClass('btn-outline-success');
                        $(e.currentTarget).toggleClass('btn-outline-primary');
                        $(e.currentTarget).html('<i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like');
                    }

                    // Update post likes string after successful like/unlike.
                    $.ajax({
                        url: "ajax/getPostLikesString.php",
                        data: { id: postID, ownlike: likeStatus },
                        success: function(response) {
                            if (response.length === 0) {
                                $('#post-like-string-' + postID).html("");
                                $('#post-like-wrapper-' + postID).css("display", "none");
                            } else {
                                $('#post-like-string-' + postID).html(response);
                                $('#post-like-wrapper-' + postID).css("display", "block");

                                // Call a post update likes modal function on new button click.
                                $('#post-like-string-' + postID).find(".btn-postshowlikes").click(updateLikesModal);
                            }
                        }
                    });
                }
            );
        }

        // Switch comment element state function.
        function switchCommentPost(e) {
            // Store details in a variables.
            let switchActive = e.currentTarget.getAttribute('data-active');
            let postID = e.currentTarget.parentNode.getAttribute('data-postid');
            let inputWrapper = $('#post-comment-input-wrapper-' + postID);
            let inputField = inputWrapper.find('.post-comment-input');
            let sendButton = inputWrapper.find('.btn-postcommentsend');

            // Show or hide comment field depending on current status.
            if (switchActive === 'false') {
                switchActive = 'true';
                inputWrapper.show('fast');
                inputField.focus();
                sendButton.click(function(e) {
                    sendCommentPost(postID, inputField);
                });
            } else {
                switchActive = 'false';
                inputWrapper.hide('fast');
                inputField.blur();
                sendButton.unbind('click');
            }

            // Set up new data-active to a comment button.
            e.currentTarget.setAttribute('data-active', switchActive);
        }

        // Post comment function.
        function sendCommentPost(id, inputField) {
            $.post("ajax/commentPost.php", { id: id, content: inputField.val() }, function(response) {
                inputField.val("");

                let postID = id;
                let loadMoreComments = $("#post-" + postID + " .btn-loadmorecomments");
                let newestCommentID = parseInt($("#post-comments-container-" + postID + " .comment-container").last().attr("data-commentid"));
                let commentsContainer = $("#post-comments-container-" + postID);
                let commentsAmount = parseInt($(loadMoreComments).attr("data-commentsAmount"));
                let commentsShown = parseInt($(loadMoreComments).attr("data-commentsShown"));
                let commentsLeft = commentsAmount - commentsShown;

                // Set newest comment ID to 0 if there's no any.
                if (isNaN(newestCommentID)) {
                    newestCommentID = 0;
                }

                // Load more comments.
                $.get("ajax/getMorePostComments.php", { id: postID, lastcommentid: newestCommentID, mode: "send" }, function(response) {
                    // Show own comment.
                    $(commentsContainer).append(response);

                    // Get amount of fetched comments.
                    let fetchedAmount = parseInt($("#post-comments-container-" + postID + " .comment-container").last().attr("data-fetchamount"));

                    // Display container if there was no comments.
                    if (newestCommentID === 0) {
                        $(commentsContainer).parent().show('fast');
                    }

                    // Update comments amount.
                    commentsAmount += fetchedAmount;
                    commentsShown += fetchedAmount;

                    // Update the shown comments amount.
                    if (commentsAmount > 2) {
                        $(loadMoreComments[0].previousElementSibling).text(commentsAmount);
                        $(loadMoreComments[0].previousElementSibling.previousElementSibling).text(commentsShown);
                    }

                    // Update comments details in container data.
                    $(loadMoreComments).attr("data-commentsAmount", commentsAmount);
                    $(loadMoreComments).attr("data-commentsShown", commentsShown);

                    // Listen to new tooltips.
                    $(function () {
                      $('#post-comments-container-' + postID + ' [data-toggle="tooltip"]').tooltip();
                    });
                });
            });
        }

        // Function for getting more comments for a post.
        function loadMoreComments(e) {
            let postID = parseInt(e.currentTarget.getAttribute("data-postID"));
            let lastCommentID = parseInt(e.currentTarget.getAttribute("data-lastCommentID"));
            let commentsAmount = parseInt(e.currentTarget.getAttribute("data-commentsAmount"));
            let commentsShown = parseInt(e.currentTarget.getAttribute("data-commentsShown"));
            let commentsLeft = commentsAmount - commentsShown;

            let commentsContainer = $("#post-comments-container-" + postID);

            // Load more comments.
            $.get("ajax/getMorePostComments.php", { id: postID, lastcommentid: lastCommentID, amount: 10, mode: "more" }, function(response) {
                // Show more comments.
                $(commentsContainer).prepend(response);

                // Update comments amount.
                commentsShown += 10;
                commentsLeft -= 10;

                // Check if there are still any comments.
                if (commentsLeft < 1) {
                    // Add the negative amount of fetched comments if there was less than 10.
                    commentsShown += commentsLeft;

                    // Hide a string showing how many comments have been fetched.
                    $("#post-" + postID + " #posts-comments-showing-wrapper").hide('fast');
                }

                // Update the shown comments amount.
                $(e.currentTarget.previousElementSibling).text(commentsAmount);
                $(e.currentTarget.previousElementSibling.previousElementSibling).text(commentsShown);

                // Update last comment ID.
                lastCommentID = parseInt($("#post-comments-container-" + postID + " div").first()[0].getAttribute("data-commentid"));

                // Update comments details in container data.
                e.currentTarget.setAttribute("data-lastCommentID", lastCommentID);
                e.currentTarget.setAttribute("data-commentsShown", commentsShown);

                // Listen to new tooltips.
                $(function () {
                  $('#post-comments-container-' + postID + ' [data-toggle="tooltip"]').tooltip();
                });
            });
        }

        // Post delete function.
        function deletePost(e) {
            // Store details in a variables.
            let postID = e.currentTarget.parentNode.getAttribute('data-postid');
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

        // Update modal with user's likes list.
        function updateLikesModal(e) {
            // Store details in a variables.
            let postID = e.currentTarget.getAttribute('data-postid');

            // Update modal details after opening.
            $('#mainModal').on('show.bs.modal', function(event) {
                let button = $(event.relatedTarget);

                // Store modal in a variable.
                let modal = $(this);

                // Get list of users that has liked a post.
                $.ajax({
                    url: "ajax/getPostLikesList.php",
                    data: { id: postID },
                    success: function(response) {
                        // Update modal's title and content.
                        modal.find('.modal-title').text('Ponies that like this post.');
                        modal.find('.modal-body').html(response);
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
