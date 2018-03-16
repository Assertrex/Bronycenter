<?php
// Include system initialization code
require('../../application/partials/init.php');

// Page settings
$pageTitle = 'Messages :: BronyCenter';
$pageStylesheet = '
body { height: 100vh; }
.container-full { flex: 1; }
#aside { width: 340px; border-right: 1px solid #BDBDBD; }
#aside #list-conversations { flex: 1; }
#aside #list-conversations .list-conversations-item { border-bottom: 1px solid #BDBDBD; cursor: pointer; }
#aside #list-conversations .list-conversations-item.active { background-color: #E0E0E0; }
#aside #list-conversations .list-conversations-item:hover { background-color: #E0E0E0; }
#main { flex: 1; }
#main #messages-info { padding-top: .563rem !important; padding-bottom: .563rem !important; line-height: 1.3; border-bottom: 1px solid #BDBDBD; }
#main #messages-info #messages-info-displayname {  }
#main #messages-show { flex: 1; overflow-y: auto; }
#main #messages-show small:first-child { margin-top: 0 !important; }
#main #messages-show .message-item-wrapper { }
#main #messages-show .message-item { display: inline-block; max-width: 70%; background-color: #F1F0F0; text-align: left; border: 1px solid #BDBDBD; border-radius: 4px; }
#main #messages-show .message-item-author { text-align: right; }
#main #messages-show .message-item-author .message-item { background-color: #BBDEFB; border-color: #2196F3; }
#main #message-creator #message-creator-textarea-wrapper { position: relative; flex: 1; border-top: 1px solid #BDBDBD; }
#main #message-creator #message-creator-textarea { height: 48px; max-height: 168px; padding-top: .75rem !important; padding-bottom: .75rem !important; background: none; border: 0; overflow: hidden; overflow-y: auto; resize: none; box-shadow: none; }
#main #message-creator #message-creator-lettercounter-wrapper { position: absolute; bottom: 4px; right: 20px; }
#main #message-creator #message-creator-send-button { font-size: 1.125rem; }
';
// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body class="d-flex flex-column">
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container-full d-flex">
        <aside id="aside" class="d-flex flex-column">
            <h5 class="text-center py-3 my-0" style="height: 58px; border-bottom: 1px solid #BDBDBD;">Conversations list</h5>

            <div id="list-conversations"></div>

            <div class="text-info text-center py-2 px-2" style="font-size: .875rem;">
                <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                This is a temporiary messaging system. It will be rewritten in a future.
            </div>
        </aside>

        <main id="main" class="d-flex flex-column">
            <div id="messages-info" class="d-flex align-items-center text-center" style="height: 58px;">
                <div id="messages-info-user" class="d-flex align-items-center" style="flex: 1;"></div>

                <div>
                    <i class="fa fa-info-circle text-primary mr-3" style="font-size: 2rem;" aria-hidden="true"></i>
                </div>
            </div>

            <div id="messages-show" class="py-3 px-4">

            </div>

            <div id="message-creator">
                <div id="message-creator-wrapper" class="d-flex align-items-stretch">
                    <div id="message-creator-textarea-wrapper" class="form-group mb-0">
                        <textarea type="text" id="message-creator-textarea" class="form-control" placeholder="Write a message..." maxlength="1000"></textarea>

                        <small id="message-creator-lettercounter-wrapper" class="text-muted">
                            <span id="message-creator-lettercounter">0</span> / 1000
                        </small>
                    </div>
                    <button type="button" id="message-creator-send-button" class="btn btn-primary px-4 rounded-0">
                        <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>

    <script type="text/javascript">
    "use-strict";

    // Start when document is ready
    $(document).ready(function() {
        // Store details of all conversations
        let conversationsList = [];

        // Store current conversation details
        let currentConversationID;
        let currentConversationUserID;
        let currentConversationUserDisplayname;
        let currentConversationUserActive;

        // Store default resizable textarea height
        let initialTextareaHeight;

        // Load conversations list
        conversationsReload();

        // Handle resizable textarea height in a message creator
        // FIXME: Try to stop moving textarea after executing this code on page reload
        $('#message-creator-textarea').each(function () {
            initialTextareaHeight = this.scrollHeight;
            $(this).css('height', initialTextareaHeight + "px");
        }).on('input', function () {
            $(this).css('height', initialTextareaHeight);
            $(this).css('height', this.scrollHeight + "px");

            // Count letters in a textarea
            $('#message-creator-lettercounter').text(this.value.length);
        });

        // Listen to the conversation list items clicks
        $('#list-conversations').click((e) => {
            let linkNode = e.target;

            // I have no idea xdd
            if (!$(linkNode).hasClass('list-conversations-item')) {
                linkNode = linkNode.parentNode;
            }

            if (!$(linkNode).hasClass('list-conversations-item')) {
                linkNode = linkNode.parentNode;
            }

            if ($(linkNode).hasClass('list-conversations-item')) {
                switchConversation(linkNode);
            }
        });

        // Try to send a message after send button click
        $('#message-creator-send-button').click(messageSend);

        // Try to send a message after ENTER key press
        $('#message-creator-textarea').keydown(function(event){
            if ((event.which == 13) && !event.shiftKey) {
                $('#message-creator-send-button').click();
                return false;
            }
        });

        // Change a current conversation
        function switchConversation(conversationLinkNode) {
            let conversationID = conversationLinkNode.getAttribute('data-conversationid');
            let conversationListIndex = conversationLinkNode.getAttribute('data-index');

            if (currentConversationID != conversationID) {
                $('#list-conversations-item-' + currentConversationID).removeClass('active');

                currentConversationID = conversationID;
                currentConversationUserID = conversationLinkNode.getAttribute('data-userid');

                $(conversationLinkNode).addClass('active');

                window.location.hash = currentConversationID;
                messagesReload(currentConversationID, currentConversationUserID);
                conversationDetailsReload(conversationListIndex);
            }
        }

        // Get messages
        function messagesReload(conversationID, userID) {
            $.get('../ajax/getConversationMessages.php?id=' + conversationID, (response) => {
                let json;

                // Try to parse a JSON
                try {
                    json = JSON.parse(response);
                } catch (e) {
                    // Return a failed system message
                    showFlashMessages();
                    return false;
                }

                if (json.status == 'success') {
                    // Remove everything from messages container
                    while ($('#messages-show')[0].firstChild) {
                        $('#messages-show')[0].removeChild($('#messages-show')[0].firstChild);
                    }

                    let lastBlockDate;

                    // Reverse the array elements order
                    json.messages = json.messages.reverse();

                    // Display every message
                    json.messages.forEach((element) => {
                        let messageAuthorClass = '';
                        let messageDateIntervalString = '';

                        if (element.sender_current_user) {
                            messageAuthorClass = 'message-item-author';
                        }

                        if (lastBlockDate != null) {
                            let date1_ms = new Date(element.datetime).getTime() / 1000;
                            let date2_ms = new Date(lastBlockDate).getTime() / 1000;
                            let dates_interval = date1_ms - date2_ms;

                            if (dates_interval > 3600) {
                                lastBlockDate = element.datetime;
                                messageDateIntervalString = '<small class="d-block text-center my-2">' + element.datetime_interval_string + '</small>';
                            }
                        } else {
                            lastBlockDate = element.datetime;
                            messageDateIntervalString = '<small class="d-block text-center my-2">' + element.datetime_interval_string + '</small>';
                        }

                        $('#messages-show').append(`
                            ${messageDateIntervalString}
                            <div class="message-item-wrapper ${messageAuthorClass} my-1">
                                <div class="message-item px-2 py-1">
                                    ${element.message}
                                </div>
                            </div>
                        `);
                    });
                }

                $('#messages-show').scrollTop($('#messages-show')[0].scrollHeight);
            });
        }

        function conversationsReload() {
            $.get('../ajax/getConversationsList.php', (response) => {
                let json;
                let conversationsListLastAmount = 0;

                // Try to parse a JSON
                try {
                    json = JSON.parse(response);
                } catch (e) {
                    // Return a failed system message
                    showFlashMessages();
                    return false;
                }

                if (json.status != 'success') {
                    return false;
                }

                conversationsList = json.conversations;

                // Remove everything from conversations container
                while ($('#list-conversations')[0].firstChild) {
                    $('#list-conversations')[0].removeChild($('#list-conversations')[0].firstChild);
                    conversationsListLastAmount++;
                }

                json.conversations.forEach((conversation, index) => {
                    let conversationSeenIconString = '';
                    let conversationSenderIconString = '';
                    let conversationOnlineIconString = '';

                    if (conversation.seen && conversation.sender_current_user) {
                        conversationSeenIconString = '<small class="text-primary" style="position: absolute; bottom: 0; right: 8px;"><i class="fa fa-eye" aria-hidden="true"></i></small>';
                    }

                    if (conversation.sender_current_user) {
                        conversationSenderIconString = '<i class="fa fa-user-circle text-primary mr-1" aria-hidden="true"></i> ';
                    }

                    if (conversation.user_details.is_online) {
                        conversationOnlineIconString = '<small class="text-success" style="vertical-align: text-top;"><i class="fa fa-circle mr-1" aria-hidden="true"></i></small>';
                    }

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

                // Get children of a conversation list div
                let conversationListChildren = $('#list-conversations').children();

                // Select newest conversation if messages page has been opened for a first time
                if (conversationsListLastAmount == 0 && conversationListChildren.length != 0) {
                    switchConversation(conversationListChildren[0]);
                }
            });
        }

        function conversationDetailsReload(index) {
            // Remove everything from conversations details container
            while ($('#messages-info-user')[0].firstChild) {
                $('#messages-info-user')[0].removeChild($('#messages-info-user')[0].firstChild);
            }

            $('#messages-info-user').append(`
                <div>
                    <img src="../media/avatars/${conversationsList[index].user_details.avatar}/minres.jpg" class="rounded ml-3" style="width: 46px; height: 46px;" />
                </div>

                <div style="flex: 1;">
                    <h6 id="messages-info-displayname" class="mb-0">${conversationsList[index].user_details.display_name}</h6>
                    <p id="messages-info-activeinterval" class="mb-0 text-muted"><small>Active: ${conversationsList[index].user_details.last_online_interval}</small></p>
                </div>
            `);
        }

        function messageSend() {
            let elementTextarea = $('#message-creator-textarea');
            let messageContent = elementTextarea.val();

            // Don't send a message if input is empty
            if (messageContent.length == 0) {
                return false;
            }

            // Try to send a message
            $.ajax({
                'url': '../ajax/doMessageSend.php',
                'method': 'POST',
                'data': {
                    'id': currentConversationUserID,
                    'message': messageContent
                },
                'success': function(result) {
                    let json;

                    // Try to parse a JSON
                    try {
                        json = JSON.parse(result);
                    } catch (e) {
                        // Return a failed system message
                        showFlashMessages();
                        return false;
                    }

                    // Reload messages if message has been sent successfully
                    if (json.status == 'success') {
                        messagesReload(currentConversationID, currentConversationUserID);
                    }

                    return true;
                }
            });

            // Clear a textarea and reset it's height after sending a message
            elementTextarea.val('');
            elementTextarea.css('height', initialTextareaHeight + "px");
        }
    });
    </script>
</body>
</html>
