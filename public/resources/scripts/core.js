"use-strict";

$(document).ready(function() {
    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // When user closes a modal, clear it
    $('#mainModal').on('hidden.bs.modal', (e) => {
        $('#mainModal .modal-title').text('');
        $('#mainModal .modal-body').text('');
        $('#mainModal .modal-footer').text('');
    });

    let wasUserLogged = false;
    extendSession();
    setInterval(extendSession, 15000);

    function extendSession() {
        let extendSessionFilePath;

        if (window.location.pathname.split('/').includes('social')) {
            extendSessionFilePath = '../ajax/doSessionExtend.php';
        } else {
            extendSessionFilePath = 'ajax/doSessionExtend.php';
        }

        $.get(extendSessionFilePath, (response) => {
            let result = parseJSON(response);

            if (result == false || result.status != 'success') {
                return false;
            }

            if (wasUserLogged && !result.data.isLoggedIn) {
                window.location.replace('../login.php?func=ajaxlogout');
            }

            wasUserLogged = result.data.isLoggedIn;

            return true;
        });
    }

    // Check current Y position of a window
    let currentPositionY = $(window).scrollTop();

    // Show scroll button if page has been loaded on specified Y position
    let scrollButtonEnabled = false;

    if (currentPositionY > 100) {
        scrollButtonEnabled = true;
        $('#button-scroll-top').show();
    }

    // Listen to the page scroll event
    $(window).scroll((e) => {
        currentPositionY = e.currentTarget.pageYOffset;

        if (currentPositionY > 100) {
            // Stop execution if button is already enabled
            if (scrollButtonEnabled === true) {
                return false;
            }

            scrollButtonEnabled = true;
            $('#button-scroll-top').show();
        } else {
            // Stop execution if button is not enabled yet
            if (scrollButtonEnabled === false) {
                return false;
            }

            scrollButtonEnabled = false;
            $('#button-scroll-top').hide();
        }
    });

    // Listen to the page scroll button
    $('#button-scroll-top').click(() => {
        $('html').animate({ scrollTop: 0 }, 500);
    });

    // Remember the current state of a searchbar
    let searchbarEnabled = false;

    // Add click event listener to the whole document
    $(document).click((e) => {
        let searchbar = $('#navbar-searchbar');
        let searchbarResultbox = $('#navbar-searchbar-result');

        // Close the searchbar if clicked outside of it
        if (searchbarEnabled === true && !searchbar.is(e.target) && !searchbarResultbox.is(e.target))
        {
            searchbarEnabled = false;
            $(searchbarResultbox).removeClass('d-lg-block');
        } else if (searchbarEnabled === false && searchbar.is(e.target) && $(searchbar).val().length > 0) {
            searchbarEnabled = true;
            $(searchbarResultbox).addClass('d-lg-block');
        }
    });

    // Listen to the header's searchbar
    $('#navbar-searchbar').on('input', (e) => {
        let resultContainer = $('#navbar-searchbar-result');
        let currentInput = $(e.currentTarget).val();

        if (currentInput.length > 0) {
            $.get('../ajax/getNavbarSearchboxUsers.php?name=' + currentInput, (result) => {
                // Check if this AJAX call is still required
                if (currentInput != $(e.currentTarget).val()) {
                    return false;
                }

                // Store matching users in a variable
                let matchingUsers = JSON.parse(result);

                // Clear content of a result box
                cleanupSearchboxResults();

                // Show results container
                searchbarEnabled = true;
                resultContainer.addClass('d-lg-block');

                // Check if any matching user has been found
                if (matchingUsers.users.length > 0) {
                    // Display each matching user
                    matchingUsers.users.forEach((user) => {
                        resultContainer.append(`
                            <a class="d-flex align-items-center navbar-searchbar-result-item px-3 py-2" href="profile.php?u=${user.id}">
                                <div class="mr-3"><img src="../media/avatars/${user.avatar}/minres.jpg" class="rounded"></div>
                                <div style="line-height: 1.25;">
                                    <div class="navbar-searchbar-result-item-username">${user.display_name} <small class="ml-1">@${user.username}</small></div>
                                    <div><small><span style="color: rgba(255, 255, 255, .5);">${user.lastSeenTranslation}:</span> ${user.last_online}</small></div>
                                </div>
                            </a>
                        `);
                    });
                }
                // Display information if no results have been found
                else {
                    resultContainer.append(`
                        <div class="d-flex align-items-center px-3 py-2">
                            <span style="color: rgba(255, 255, 255, .5); line-height: 1.25;">No matching users found.</span>
                        </div>
                    `);
                }
            });
        } else {
            // Hide results container
            searchbarEnabled = false;
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
    $.get('../ajax/getFlashMessages.php', (result) => {
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

// Reset a letters counter
function resetLettersCounter(inputID, counterID) {
    $('#' + counterID).text($('#' + inputID).val().length);
}

// Parse a JSON or display an AJAX error
function parseJSON(json) {
    try {
        // Try to parse a JSON
        let result = JSON.parse(json);

        return result;
    } catch (e) {
        // Return a system message about a failed AJAX
        showFlashMessages();

        return false;
    }
}
