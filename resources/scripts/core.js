"use-strict";

// Start when document is ready
$(document).ready(function() {
    // Enable Bootstrap's tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Clear modal's content after closing it
    $('#mainModal').on('hidden.bs.modal', (e) => {
        $('#mainModal .modal-title').text('');
        $('#mainModal .modal-body').text('');
        $('#mainModal .modal-footer').text('');
    });

    // Extend account session every X seconds
    setInterval(() => {
        // TODO Handle a false return
        $.get("../system/ajax/doExtendSession.php");
    }, 30000);

    // Listen to the header's searchbar
    $('#navbar-searchbar').on('input', (e) => {
        let resultContainer = $('#navbar-searchbar-result');
        let currentInput = $(e.currentTarget).val();

        if (currentInput.length > 0) {
            $.get("ajax/getNavbarSearchboxUsers.php?name=" + currentInput, (result) => {
                // Check if this AJAX call is still required
                if (currentInput != $(e.currentTarget).val()) {
                    return false;
                }

                // Store matching users in a variable
                let matchingUsers = JSON.parse(result);

                // Clear content of a result box
                cleanupSearchboxResults();

                // Check if any matching user has been found
                if (matchingUsers.users.length > 0) {
                    // Show results container
                    resultContainer.addClass('d-lg-block');

                    // Display each matching user
                    matchingUsers.users.forEach((user) => {
                        resultContainer.append(`
                            <a class="d-flex align-items-center navbar-searchbar-result-item px-3 py-2" href="profile.php?u=${user.id}">
                                <div class="mr-3"><img src="../media/avatars/${user.avatar}/minres.jpg" class="rounded"></div>
                                <div style="line-height: 1.25;">
                                    <div class="navbar-searchbar-result-item-username">${user.display_name} <small class="ml-1">@${user.username}</small></div>
                                    <div><small><span style="color: rgba(255, 255, 255, .5);">Last seen:</span> ${user.last_online}</small></div>
                                </div>
                            </a>
                        `);
                    });
                } else {
                    // Hide results container
                    resultContainer.removeClass('d-lg-block');
                }
            });
        } else {
            // Hide results container
            resultContainer.removeClass('d-lg-block');
        }
    });
});

function cleanupSearchboxResults() {
    let resultContainer = $('#navbar-searchbar-result');

    // Remove everything from results container
    while (resultContainer[0].firstChild) {
        resultContainer[0].removeChild(resultContainer[0].firstChild);
    }
}

// Fill up a modal
function displayModal(title, content, footer) {
    $('#mainModal .modal-title').text(title);
    $('#mainModal .modal-body').html(content);
    $('#mainModal .modal-footer').html(footer);
}

// Show available flash messages
function showFlashMessages() {
    $.get("../system/ajax/getFlashMessages.php", (result) => {
        $('#flash-messages').prepend(result);
    });
}

// Add a input listener on a selected input or textarea field
function addLettersCounter(inputID, counterID) {
    $('#' + inputID).each(function () {
        // Count letters in an input or textarea field on start
        $('#' + counterID).text(this.value.length);
    }).on('input', function () {
        // Count letters in an input or textarea field on input
        $('#' + counterID).text(this.value.length);
    });
}
