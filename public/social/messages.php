<?php
// Include system initialization code
require('../../application/partials/init.php');

// Page settings
$pageTitle = 'Messages :: BronyCenter';
$pageStylesheet = '';

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body class="d-flex flex-column vh-100">
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div id="page-messages" class="container-fullscreen d-flex">
        <div id="messages-mobile-wrapper">
            <aside id="aside" class="d-flex d-lg-flex flex-column">
                <div id="messages-mobile-aside-wrapper">
                    <h5 class="text-center py-3 my-0" style="height: 58px; border-bottom: 1px solid #BDBDBD;">Conversations list</h5>

                    <div id="list-conversations"></div>

                    <div class="text-info text-center py-2 px-2" style="font-size: .875rem;">
                        <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                        This is a temporiary messaging system. It can contain bugs and it will be rewritten in the future.
                    </div>
                </div>
            </aside>

            <main id="main" class="d-none d-lg-flex flex-column">
                <div id="messages-info" class="d-flex align-items-center text-center" style="height: 58px;">
                    <div class="d-block d-lg-none">
                        <i id="messages-info-action-conversations" class="fa fa-arrow-circle-o-left text-primary mx-3" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>

                    <div id="messages-info-user" class="d-flex align-items-center justify-content-center ml-lg-3" style="flex: 1;"></div>

                    <div>
                        <i class="fa fa-info-circle text-primary mx-3" style="font-size: 2rem;" aria-hidden="true"></i>
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
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>

    <script src="../resources/scripts/messages.js?v=<?php echo $websiteVersion['commit']; ?>"></script>

    <script>
    "use-strict";

    // Store default resizable textarea height
    let initialTextareaHeight;

    // Store all conversations details
    let conversationsDetails = [];

    // Store all conversations helpers
    var conversationsHelpers = [];

    // Store current conversation details
    let conversationsCurrent = {
        id: null,
        user_id: null,
        list_index: null
    };

    // Start when document is ready
    $(document).ready(function() {
        // Load list of existing conversations
        doConversationsListReload();

        // Listen to a conversation list items clicks
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
                doConversationSwitch(linkNode);
            }
        });

        // Add a letters counter to messages creator textarea
        addLettersCounter('message-creator-textarea', 'message-creator-lettercounter');

        // Handle resizable textarea height in a message creator
        // FIXME: This doesn't work anymore, after a commit: bd8794d2a34ffa81775cb91bcb2e245a2b474478
        $('#message-creator-textarea').each(function () {
            initialTextareaHeight = this.scrollHeight;
            $(this).css('height', initialTextareaHeight + 'px');
        }).on('input', function () {
            $(this).css('height', initialTextareaHeight);
            $(this).css('height', this.scrollHeight + 'px');
        });

        // Try to send a message after send button click
        $('#message-creator-send-button').click(doMessageSend);

        // Try to send a message after an ENTER key press (without holding a SHIFT key)
        $('#message-creator-textarea').keydown(function(event){
            if ((event.which == 13) && !event.shiftKey) {
                $('#message-creator-send-button').click();
                return false;
            }
        });

        // Handle messages tab closing on a mobile version
        $('#messages-info-action-conversations').click(() => {
            $('#aside').toggleClass('d-none');
            $('#main').toggleClass('d-none');
            $('#aside').toggleClass('d-flex');
            $('#main').toggleClass('d-flex');
        });
    });
    </script>
</body>
</html>
