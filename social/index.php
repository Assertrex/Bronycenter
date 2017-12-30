<?php
// Allow access only for logged users
$loginRequired = true;

// Include system initialization code
require('../system/partials/init.php');

// Use post class for creating and reading user's posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Page settings
$pageTitle = 'Posts :: BronyCenter';
$pageStylesheet = '
.posts-container-from { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(189, 189, 189, .5); }
.post-content-text { word-break: break-word; }
.comment-container { border-bottom: 1px solid #EEEEEE; }
.comment-container:last-child { padding-bottom: 0 !important; border-bottom: 0; }
';

// Include social head content for all pages
require('partials/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../system/partials/header-social.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../system/partials/flash.php');
        ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <?php
                // Include partial containing a post creator
                require('partials/index/post-creator.php');

                // Include partial containing a posts list
                require('partials/index/posts-list.php');

                // Include partial containing a posts pagination bar
                require('partials/index/posts-pagination.php');
                ?>
            </div>

            <aside class="col-12 col-lg-4">
                <?php
                // Include partial containing an aside panel
                require('partials/index/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('partials/scripts.php');
    ?>

    <script type="text/javascript">
    // Enable the strict JavaScript mode
    "use-strict";

    // Check if new posts are available
    setInterval(
        () => {
            // Get ID of last fetched post
            let lastPostID = $('#posts-list-wrapper').children(':first');

            // Check if lastest fetched post exists into additional container
            if (lastPostID[0].className == 'posts-container-from') {
                lastPostID = lastPostID.children(':first').attr('id').substr(5);
            } else {
                lastPostID = lastPostID.attr('id').substr(5);
            }

            // Get amount of new posts
            $.get('ajax/doGetLastestPostsCounter.php?id=' + lastPostID, function(response) {
                let json;

                // Try to parse a JSON
                try {
                    json = JSON.parse(response);
                } catch (e) {
                    return false;
                }

                // Modify post content if it has been modified successfully
                if (json.status == 'success') {
                    // Update new posts counter value
                    $('#posts-list-checker-available-counter').text(json.amount);

                    // Update counter text if any new post exists
                    if (json.amount > 0) {
                        // Hide notification about available posts
                        $('#posts-list-checker-available').addClass('d-inline-block');
                        $('#posts-list-checker-available').removeClass('d-none');

                        // Show default notifications about no new posts
                        $('#posts-list-checker-unavailable').addClass('d-none');
                        $('#posts-list-checker-unavailable').removeClass('d-inline-block');
                    }
                }
            });
        }, 15000
    );

    // Store default resizable textarea height
    let initialTextareaValue;
    let textareaState = false;

    // Handle resizable textarea height in a post creator
    // FIXME: Try to stop moving textarea after executing this code on page reload
    $('#post-creator-textarea').each(function () {
        initialTextareaValue = this.scrollHeight;
        $(this).css('height', initialTextareaValue + "px");
    }).on('input', function () {
        $(this).css('height', initialTextareaValue);
        $(this).css('height', this.scrollHeight + "px");

        // Count letters in a textarea
        $('#post-creator-lettercounter').text(this.value.length);

        // Don't allow to submit if content is too short
        if (this.value.length >= 3 && textareaState === false) {
            textareaState = true;
            $('#post-creator-submit').prop('disabled', false);
            $('#post-creator-submit').css('cursor', 'pointer');
        } else if (this.value.length < 3 && textareaState === true) {
            textareaState = false;
            $('#post-creator-submit').prop('disabled', true);
            $('#post-creator-submit').css('cursor', 'not-allowed');
        }
    });

    // Handle the post create action
    $('#post-creator-submit').click(function() {
        if ($('#post-creator-textarea').val().length < 3) {
            return false;
        }

        $.ajax({
            'url': 'ajax/doPostCreate.php',
            'method': 'POST',
            'data': {
                'content': $('#post-creator-textarea').val(),
                'type': 1
            },
            'success': function(result) {
                // Reset content of a textarea after publishing new post
                $('#post-creator-textarea').val('');

                // Reset letters counter of a textarea
                $('#post-creator-lettercounter').text(0);

                // Return textarea to it's initial height
                $('#post-creator-textarea').css('height', initialTextareaValue + 'px');

                // Display new posts including the created one
                displayLastestPosts();

                return true;
            }
        });
    });

    // Fetch new posts
    $('#posts-list-checker-available').click(displayLastestPosts);

    // Bind listeners to a default posts container
    bindListeners('#posts-list');

    function displayLastestPosts() {
        // Get ID of last fetched post
        let lastPostID = $('#posts-list-wrapper').children(':first');

        // Check if lastest fetched post exists into additional container
        if (lastPostID[0].className == 'posts-container-from') {
            lastPostID = lastPostID.children(':first').attr('id').substr(5);
        } else {
            lastPostID = lastPostID.attr('id').substr(5);
        }

        $.get('ajax/doGetLastestPosts.php?id=' + lastPostID, (result) => {
            $('#posts-list-wrapper').prepend(result);
            $('#posts-container-from-' + lastPostID).slideDown('fast');

            // Bind default listeners to the new posts
            bindListeners('#posts-container-from-' + lastPostID);

            // Hide notification about available posts
            $('#posts-list-checker-available').addClass('d-none');
            $('#posts-list-checker-available').removeClass('d-inline-block');

            // Show default notifications about no new posts
            $('#posts-list-checker-unavailable').addClass('d-inline-block');
            $('#posts-list-checker-unavailable').removeClass('d-none');
        });
    }

    // Listen to a post like button click
    function listenToPostsLikeButton(containerName) {
        // Add click listeners to selected container
        $(containerName + ' .btn-postlike').click(function() {
            let button = $(this);
            let postID = $(button).data('postid');

            $.ajax({
                'url': 'ajax/doPostLike.php?id=' + postID,
                'method': 'GET',
                'success': function() {
                    let hasLiked = $(button).attr('data-hasliked');

                    // Update a like status
                    if (hasLiked === 'false') {
                        hasLiked = 'true';
                        $(button).attr('data-hasliked', hasLiked);
                        $(button).removeClass('btn-outline-secondary');
                        $(button).addClass('btn-outline-primary');
                    } else {
                        hasLiked = 'false';
                        $(button).attr('data-hasliked', hasLiked);
                        $(button).removeClass('btn-outline-primary');
                        $(button).addClass('btn-outline-secondary');
                    }

                    // Update a post likes string
                    updatePostLikeString(postID, hasLiked);
                }
            });
        });

        // Function for getting and updating post likes string
        function updatePostLikeString(postID, hasLiked) {
            $.ajax({
                'url': 'ajax/getPostLikesString.php?id=' + postID + '&hasliked=' + hasLiked,
                'method': 'GET',
                'success': function(result) {
                    if (result != '') {
                        $('#post-' + postID + ' .post-likes').removeClass('d-none');
                        $('#post-' + postID + ' .post-likes').addClass('d-block');
                    } else {
                        $('#post-' + postID + ' .post-likes').removeClass('d-block');
                        $('#post-' + postID + ' .post-likes').addClass('d-none');
                    }

                    $('#post-' + postID + ' .post-likes-string').html(result);
                }
            });
        }
    }

    // Listen to a post likes show modal button
    function listenToPostsLikesShowModalButton(containerName) {
        $(containerName + ' .btn-showlikesmodal').click(() => {
            // Request server for a post likes list
            $.ajax({
                'url': 'ajax/getModalLikesList.php?id=' + $('.btn-showlikesmodal').data('postid'),
                'method': 'GET',
                'success': (result) => {
                    displayModal(
                        'Users that liked this post',
                        result,
                        '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>'
                    );
                }
            });
        });
    }

    // Listen to a post like button click
    function listenToPostsCommentButton(containerName) {
        // Handle the action for displaying an comment input form
        $(containerName + " .btn-postcommentswitch").click((e) => {
            // Store details in a variables
            let switchActive = e.currentTarget.getAttribute('data-active');
            let postID = e.currentTarget.parentNode.getAttribute('data-postid');
            let inputWrapper = $('#post-comment-input-wrapper-' + postID);
            let inputField = inputWrapper.find('.post-comment-input');
            let sendButton = inputWrapper.find('.btn-postcommentsend');

            // Show or hide comment field depending on current status
            if (switchActive === 'false') {
                switchActive = 'true';
                $(e.currentTarget).toggleClass('btn-outline-secondary btn-outline-primary');
                inputWrapper.show('fast');
                inputField.focus();
                sendButton.click(function(e) {
                    sendCommentPost(postID, inputField);
                });
            } else {
                switchActive = 'false';
                $(e.currentTarget).toggleClass('btn-outline-primary btn-outline-secondary');
                inputWrapper.hide('fast');
                inputField.blur();
                sendButton.unbind('click');
            }

            // Set up new data-active to a comment button
            e.currentTarget.setAttribute('data-active', switchActive);
        });
    }

    // Listen to a post show more comments button click
    function listenToPostsMoreCommentsButton(containerName) {
        $(containerName + " .btn-loadmorecomments").click((e) => {
            e.preventDefault();

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
        });
    }

    // Listen to a post edit button click
    function listenToPostsEditButton(containerName) {
        $(containerName + " .btn-postedit").click((e) => {
            let editContentID = e.currentTarget.getAttribute('data-postid');
            let editTextareaContent = $('#post-' + editContentID + ' .post-content-text').text();

            displayModal(
                'Post editor',
                `<p><small>You can now edit your post, but your post edit history will be available for everypony.</small></p>
                 <textarea class="std-textarea textarea-posteditcontent" style="height: 126px; background-color: #EEEEEE; padding: .5rem 1rem; border-radius: 8px;">${editTextareaContent}</textarea>`,
                `<button type="button" class="btn btn-primary btn-posteditconfirm" data-dismiss="modal" data-postid="${editContentID}">Apply</button>
                 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>`
            );

            // Listen for an apply button click
            $('#mainModal .btn-posteditconfirm').click((e) => {
                let editedTextareaContent = $('#mainModal .textarea-posteditcontent').val();

                // Edit post content only if it has been changed
                if (editTextareaContent != editedTextareaContent) {
                    // Send a new post content
                    $.post("ajax/doPostEdit.php", { id: editContentID, content: editedTextareaContent }, function(response) {
                        let json;

                        // Try to parse a JSON
                        try {
                            json = JSON.parse(response);
                        } catch (e) {
                            showFlashMessages();
                            return false;
                        }

                        // Modify post content if it has been modified successfully
                        if (json.status == 'success') {
                            $('#post-' + json.post + ' .post-content-text').text(json.content);

                            // Display newlines in a post without the need of page refreshing
                            $('#post-' + json.post + ' .post-content-text').css('white-space', 'pre-line');
                        }

                        showFlashMessages();
                        return true;
                    });
                }
            });
        });
    }

    // Listen to a post delete button click
    function listenToPostsDeleteButton(containerName) {
        $(containerName + ' .btn-deletepost').click(function() {
            let button = $(this);
            let postID = $(button).data('postid');
            let userModerator = $(button).data('moderate');

            // Display reason form if moderator tries to remove a post
            // TODO Display a fancy modal instead of native JS function
            if (userModerator) {
                let reason;

                if (reason = prompt('Why do you think that this post needs to be removed? (This will be shown to post author and moderators)')) {
                    $.ajax({
                        'url': 'ajax/doPostDelete.php?id=' + postID + '&reason=' + reason,
                        'method': 'GET',
                        'success': function(result) {
                            $('#post-' + postID).hide('slow', function() {
                                let containerParent = $('#post-' + postID).parent();

                                // Remove the whole container if it is the last fetched post
                                if (containerParent[0].className == 'posts-container-from' &&
                                    containerParent.children().length == 1) {
                                    $(this).remove();
                                    containerParent.remove();
                                } else {
                                    $(this).remove();
                                }

                                showFlashMessages();
                            });
                        }
                    });
                }
            } else {
                if (confirm('Are you sure that you want to remove your post?')) {
                    $.ajax({
                        'url': 'ajax/doPostDelete.php?id=' + postID,
                        'method': 'GET',
                        'success': function(result) {
                            $('#post-' + postID).hide('slow', function() {
                                let containerParent = $('#post-' + postID).parent();

                                // Remove the whole container if it is the last fetched post
                                if (containerParent[0].className == 'posts-container-from' &&
                                    containerParent.children().length == 1) {
                                    $(this).remove();
                                    containerParent.remove();
                                } else {
                                    $(this).remove();
                                }

                                showFlashMessages();
                            });
                        }
                    });
                }
            }
        });
    }

    // Function used to bind all listeners to a selected posts container
    function bindListeners(containerName) {
        listenToPostsLikeButton(containerName);
        listenToPostsLikesShowModalButton(containerName);
        listenToPostsCommentButton(containerName);
        listenToPostsMoreCommentsButton(containerName);
        listenToPostsEditButton(containerName);
        listenToPostsDeleteButton(containerName);

        // Add tooltips to the new container
        if (containerName != '#posts-list') {
            $(function () {
                $(containerName + ' [data-toggle="tooltip"]').tooltip();
            });
        }
    }

    // Post comment function
    function sendCommentPost(id, inputField) {
        $.post("ajax/doPostComment.php", { id: id, content: inputField.val() }, function(response) {
            inputField.val("");

            let postID = id;
            let loadMoreComments = $("#post-" + postID + " .btn-loadmorecomments");
            let newestCommentID = parseInt($("#post-comments-container-" + postID + " .comment-container").last().attr("data-commentid"));
            let commentsContainer = $("#post-comments-container-" + postID);
            let commentsAmount = parseInt($(loadMoreComments).attr("data-commentsAmount"));
            let commentsShown = parseInt($(loadMoreComments).attr("data-commentsShown"));
            let commentsLeft = commentsAmount - commentsShown;

            // Set newest comment ID to 0 if there's no any
            if (isNaN(newestCommentID)) {
                newestCommentID = 0;
            }

            // Load more comments.
            $.get("ajax/getMorePostComments.php", { id: postID, lastcommentid: newestCommentID, mode: "send" }, function(response) {
                // Show own comment
                $(commentsContainer).append(response);

                // Get amount of fetched comments
                let fetchedAmount = parseInt($("#post-comments-container-" + postID + " .comment-container").last().attr("data-fetchamount"));

                // Display container if there was no comments
                if (newestCommentID === 0) {
                    $(commentsContainer).parent().show('fast');
                }

                // Update comments amount
                commentsAmount += fetchedAmount;
                commentsShown += fetchedAmount;

                // Update the shown comments amount
                if (commentsAmount > 2) {
                    $(loadMoreComments[0].previousElementSibling).text(commentsAmount);
                    $(loadMoreComments[0].previousElementSibling.previousElementSibling).text(commentsShown);
                }

                // Update comments details in container data
                $(loadMoreComments).attr("data-commentsAmount", commentsAmount);
                $(loadMoreComments).attr("data-commentsShown", commentsShown);

                // Listen to the new tooltips
                $(function () {
                  $('#post-comments-container-' + postID + ' [data-toggle="tooltip"]').tooltip();
                });
            });
        });
    }
    </script>
</body>
</html>
