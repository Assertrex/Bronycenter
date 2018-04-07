"use-strict";

$(document).ready(function() {
    bindListeners();

    function bindListeners() {
        addReportedPostsRemoveButtonListener();
        addReportedPostsApproveButtonListener();
    }

    function addReportedPostsRemoveButtonListener() {
        $('#reported-posts .reported-item .btn-action-remove').click((event) => {
            let postID = event.currentTarget.getAttribute('data-id');

            removePost(postID);
        });
    }

    function addReportedPostsApproveButtonListener() {
        $('#reported-posts .reported-item .btn-action-approve').click((event) => {
            let postID = event.currentTarget.getAttribute('data-id');

            approvePost(postID);
        });
    }

    function removePost(postID) {
        displayModal(
            'Removing a post',
            `
            Are you sure that you want to remove this post?
            `,
            '<button type="button" id="btn-action-remove-confirm" class="btn btn-danger" data-dismiss="modal">Remove</button>' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>'
        );

        $('#mainModal #btn-action-remove-confirm').click((event) => {
            $.post('../ajax/doPostDelete.php', { id: postID, reason: '' }, function(response) {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    return false;
                }

                $('#reported-posts-item-' + postID).hide('slow', function() {
                    $(this).remove();
                });

                return true;
            });
        });
    }

    function approvePost(postID) {
        displayModal(
            'Approving a post',
            `
            Are you sure that you want to keep this post? It won't be reported anymore.
            `,
            '<button type="button" id="btn-action-approve-confirm" class="btn btn-success" data-dismiss="modal">Approve</button>' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>'
        );

        $('#mainModal #btn-action-approve-confirm').click((event) => {
            $.post('../ajax/doPostApprove.php', { id: postID }, function(response) {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    return false;
                }

                $('#reported-posts-item-' + postID).hide('slow', function() {
                    $(this).remove();
                });

                return true;
            });
        });
    }
});
