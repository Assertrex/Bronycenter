"use-strict";

$(document).ready(function() {
    let currentProfileTab = 'posts';

    if (window.location.hash) {
        let selectedTab = window.location.hash.substr(1);

        changeProfileTab(selectedTab);
    }

    $('#btn-profile-sendmessage').click((e) => {
        let userID = e.currentTarget.getAttribute('data-userid');
        let userDisplayname = e.currentTarget.getAttribute('data-userdisplayname');

        displayModal(
            'Send a message',
            `
            <p>Write a message that you want to send to: <br /><b>${userDisplayname}</b></p>
            <div class="form-group">
                <label for="input-profile-sendmessage-content">Your message:</label>
                <textarea class="form-control" id="input-profile-sendmessage-content" rows="3" maxlength="1000"></textarea>
                <small class="d-block text-muted text-right mt-1">
                    <span id="input-profile-sendmessage-content-counter">0</span> / 1000
                </small>
            </div>
            `,
            `
            <button type="button" class="btn btn-primary" id="btn-profile-sendmessage-confirm" data-dismiss="modal" data-userid="${userID}">Send</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            `
        );

        addLettersCounter('input-profile-sendmessage-content', 'input-profile-sendmessage-content-counter');

        $('#btn-profile-sendmessage-confirm').click((e) => {
            let messageContent = $('#input-profile-sendmessage-content').val();
            let messageRecipent = e.currentTarget.getAttribute('data-userid');

            $.post('../ajax/doMessageSend.php', { id: messageRecipent, message: messageContent }, function(response) {
                let json;

                try {
                    json = JSON.parse(response);
                } catch (e) {
                    showFlashMessages();
                    return false;
                }

                if (json.status == 'success') {
                    // TODO FILL THIS WITH SOMETHING
                }

                showFlashMessages();
                return true;
            });
        });
    });

    $('#aside-tabs').click((e) => {
        if ($(e.target).hasClass('nav-link')) {
            let selectedTab = e.target.getAttribute('href').substr(1);

            changeProfileTab(selectedTab);
        }
    });

    // Change profile tab and content
    function changeProfileTab(tabName) {
        // Check if tab name is valid
        if (tabName != 'about' &&
            tabName != 'posts' &&
            tabName != 'fandom' &&
            tabName != 'creations') {
            return false;
        }

        // Switch tab if different tab has been selected
        if (tabName == currentProfileTab) {
            return false;
        }

        // Disable current tab and display new one
        $('#aside-' + currentProfileTab).css('display', 'none');
        $('#aside-' + tabName).css('display', 'block');

        // Update current tab on tabs list
        $('#aside-tab-' + currentProfileTab).removeClass('active');
        $('#aside-tab-' + tabName).addClass('active');

        // Store new tab value
        currentProfileTab = tabName;

        return true;
    }
});
