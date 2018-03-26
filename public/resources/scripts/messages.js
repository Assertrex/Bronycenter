"use-strict";

/**
 * Reload a conversations list
 *
 * @returns {boolean} - Result of this function
**/

function doConversationsListReload() {
    $.get('../ajax/getConversationsList.php', (response) => {
        // Make place for a fetched and parsed JSON
        let result = parseJSON(response);

        // Stop executing if AJAX response couldn't be parsed or there was an error
        if (result == false || result.status != 'success') {
            return false;
        }

        // Remove everything from conversations container
        while ($('#list-conversations')[0].firstChild) {
            $('#list-conversations')[0].removeChild($('#list-conversations')[0].firstChild);
        }

        // Store all conversations details in a global variable
        conversationsDetails = result.conversations;

        // Format and display each conversation as a list item
        result.conversations.forEach((conversation, index) => {
            // Prepare variables to store optional HTML code
            let conversationSeenIconString = '';
            let conversationSenderIconString = '';
            let conversationOnlineIconString = '';

            // Display an "eye icon", if last message sent by a current user has been seen by a recipient
            if (conversation.seen && conversation.sender_current_user) {
                conversationSeenIconString = '<small class="text-primary" style="position: absolute; bottom: 0; right: 8px;"><i class="fa fa-eye" aria-hidden="true"></i></small>';
            }

            // Display an "user icon", if last message has been sent by a current user
            if (conversation.sender_current_user) {
                conversationSenderIconString = '<i class="fa fa-user-circle text-primary mr-1" aria-hidden="true"></i> ';
            }

            // Display a green "circle icon" if user is already online
            if (conversation.user_details.is_online) {
                conversationOnlineIconString = '<small class="text-success" style="vertical-align: text-top;"><i class="fa fa-circle mr-1" aria-hidden="true"></i></small>';
            }

            // Create a place for a conversation helpers
            conversationsHelpers[conversation.id] = { inputValue: '', elementMessageLast: null };

            // Display an conversation item on top of a list
            $('#list-conversations').append(`
                <div id="list-conversations-item-${conversation.id}" class="list-conversations-item d-flex align-items-center" data-conversationid="${conversation.id}" data-userid="${conversation.user_details.id}" data-index="${index}">
                    <div>
                        <img src="../media/avatars/${conversation.user_details.avatar}/minres.jpg" class="rounded my-2 ml-2" style="width: 50px; height: 50px;" />
                    </div>

                    <div class="ml-2 pl-1" style="position: relative; flex: 1; line-height: 1.6; overflow: hidden;">
                        <div style="width: 170px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            ${conversationOnlineIconString}
                            ${conversation.user_details.display_name}
                        </div>
                        <small class="text-muted" style="position: absolute; top: 2px; right: 8px;">${conversation.datetime_interval_string}</small>
                        <small class="d-block text-muted" style="width: 240px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            ${conversationSenderIconString}
                            ${conversation.message}
                        </small>
                        ${conversationSeenIconString}
                    </div>
                </div>
            `);
        });

        // Switch to a newest conversation, if any exists
        if (conversationsDetails.length != 0) {
            doConversationSwitch($('#list-conversations').children()[0]);
        }

        return true;
    });
}

/**
 * Reload details of a current conversation
 *
 * @param {integer} index - Index from a conversations list
 * @returns {boolean} - Result of this function
**/

function doConversationDetailsReload(index) {
    // Remove everything from conversations details container
    // TODO: Replace elements content instead
    while ($('#messages-info-user')[0].firstChild) {
        $('#messages-info-user')[0].removeChild($('#messages-info-user')[0].firstChild);
    }

    // Display conversation details on a page
    // TODO: Replace elements content instead
    $('#messages-info-user').append(`
        <div class="mr-2">
            <img src="../media/avatars/${conversationsDetails[index].user_details.avatar}/minres.jpg" class="rounded" style="width: 46px; height: 46px;" />
        </div>

        <div class="d-flex flex-column" style="flex: 1;">
            <h6 id="messages-info-displayname" class="d-inline-block mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${conversationsDetails[index].user_details.display_name}</h6>
            <p id="messages-info-activeinterval" class="mb-0 text-muted"><small>Active: ${conversationsDetails[index].user_details.last_online_interval}</small></p>
        </div>
    `);

    return true;
}

/**
 * Change a current conversation
 *
 * @param {object} elementConversationLink - Clicked HTML element on a conversations list
 * @returns {boolean} - Result of this function
**/

function doConversationSwitch(elementConversationLink) {
    let conversationID = elementConversationLink.getAttribute('data-conversationid');
    let conversationUserID = elementConversationLink.getAttribute('data-userid');
    let conversationListIndex = elementConversationLink.getAttribute('data-index');
    let elementConversationTextarea = $('#message-creator-textarea');

    // Stop executing if the same conversation has been selected
    if (conversationsCurrent['id'] == conversationID) {
        return false;
    }

    // Do some cleanup, if conversation is switching (not loading a first one)
    if (conversationsCurrent['id'] != null) {
        // Change a conversations list item from active to an inactive
        $('#list-conversations-item-' + conversationsCurrent['id']).removeClass('active');

        // Store a textarea value before switching a conversation
        conversationsHelpers[conversationsCurrent['id']]['inputValue'] = elementConversationTextarea.val();

        // Check if conversation already contains a saved message
        elementConversationTextarea.val(conversationsHelpers[conversationID]['inputValue']);

        // Reset a letters counter in a messages creator textarea
        resetLettersCounter('message-creator-textarea', 'message-creator-lettercounter');
    }

    // Update current conversation values
    conversationsCurrent['id'] = conversationID;
    conversationsCurrent['user_id'] = conversationUserID;
    conversationsCurrent['list_index'] = conversationListIndex;

    // Change an URL to a current conversation
    // TODO: Don't do this with a hash, use query string instead
    window.location.hash = conversationID;

    // Mark a selected conversation as active
    $(elementConversationLink).addClass('active');

    // Open messages tab and hide conversations tab on a mobile
    $('#aside').toggleClass('d-none');
    $('#main').toggleClass('d-none');
    $('#aside').toggleClass('d-flex');
    $('#main').toggleClass('d-flex');

    // Focus on a conversation input
    $('#message-creator-textarea').focus();

    // Update details about a conversation
    doConversationDetailsReload(conversationListIndex);

    // Update messages list
    doMessagesReload(conversationID);

    return true;
}

/**
 * Reload all messages in a conversation
 *
 * @param {integer} conversationID - ID of a current conversation
 * @returns {boolean} - Result of this function
**/

function doMessagesReload(conversationID) {
    // Set a limit for a messages to fetch
    let messagesLimit = 10;

    // Store AJAX query string
    let queryString = '?conversation_id=' + conversationID + '&messages_limit=' + messagesLimit;

    // Make an AJAX request to GET conversations messages
    $.get('../ajax/getConversationMessages.php' + queryString, (response) => {
        // Make place for a fetched and parsed JSON
        let result = parseJSON(response);

        // Make a place for a date of a last messages block
        let lastBlockDate = '';

        // Stop executing if AJAX response couldn't be parsed or there was an error
        if (result == false || result.status != 'success') {
            return false;
        }

        // Empty messages container
        while ($('#messages-show')[0].firstChild) {
            $('#messages-show')[0].removeChild($('#messages-show')[0].firstChild);
        }

        // Reverse an array elements order
        result.messages = result.messages.reverse();

        // Format and display each message
        result.messages.forEach((element) => {
            // Make a place for a message author class
            let messageAuthorClassString = '';

            // Make a place for a messages block datetime string
            let messagesBlockDatetimeString = '';

            // Add an author class to a message, if current user is an message author
            if (element.sender_current_user) {
                messageAuthorClassString = 'message-item-author';
            }

            // Compare message datetime with a last messages block datetime
            if (lastBlockDate != null) {
                // Compare both messages timestamps
                let messagesDelay = new Date(element.datetime).getTime() / 1000 - lastBlockDate;

                // If there was an one hour delay between two messages, then display a datetime string
                if (messagesDelay > 3600) {
                    lastBlockDate = new Date(element.datetime).getTime() / 1000;

                    messagesBlockDatetimeString = '<div class="text-center my-3"><small>' + element.datetime + ' (UTC)</small></div>';
                }
            } else {
                // Store and display a messages block datetime, if this is a first fetched message
                lastBlockDate = new Date(element.datetime).getTime() / 1000;
                messagesBlockDatetimeString = '<div class="text-center my-3"><small>' + element.datetime + ' (UTC)</small></div>';
            }

            // Display a message (with a messages block datetime string if existing)
            $('#messages-show').append(`
                ${messagesBlockDatetimeString}
                <div class="message-item-wrapper ${messageAuthorClassString} my-1">
                    <div class="message-item px-2 py-1">
                        ${element.message}
                    </div>
                </div>
            `);
        });

        // Scroll to a bottom of a page (newest message) after displaying all messages
        $('#messages-show').scrollTop($('#messages-show')[0].scrollHeight);

        // Remember last message element
        conversationsHelpers[conversationsCurrent['id']]['elementMessageLast'] = $('#messages-show .message-item').last()[0];

        return true;
    });
}

/**
 * Send a message
 *
 * @returns {boolean} - Result of this function
**/

function doMessageSend() {
    let elementTextarea = $('#message-creator-textarea');
    let messageContent = elementTextarea.val();

    // Don't send a message, if user input value is empty
    if (messageContent.length == 0) {
        return false;
    }

    // Make an AJAX call to send a message
    $.post(
        '../ajax/doMessageSend.php',
        {
            'id': conversationsCurrent['user_id'],
            'message': messageContent
        },
        (response) => {
            // Make place for a fetched and parsed JSON
            let result = parseJSON(response);

            // Stop executing if AJAX response couldn't be parsed or there was an error
            if (result == false || result.status != 'success') {
                return false;
            }

            // Reload messages container
            doMessagesReload(conversationsCurrent['id']);

            // Clear a textarea and reset it's height after sending a message
            elementTextarea.val('');
            elementTextarea.css('height', initialTextareaHeight + 'px');

            // Clear a currently saved message
            conversationsHelpers[conversationsCurrent['id']]['inputValue'] = '';

            return true;
        }
    );

    // Focus on a conversation input
    $('#message-creator-textarea').focus();

    return true;
}
