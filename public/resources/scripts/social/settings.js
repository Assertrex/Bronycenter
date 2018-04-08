"use-strict";

$(document).ready(function() {
    let currentSettingsTab = 'credentials';

    if (window.location.hash) {
        let selectedTab = window.location.hash.substr(1);
        changeSettingsTab(selectedTab);
    }

    function changeSettingsTab(tabName) {
        let settingsTabs = ['credentials', 'email', 'standing', 'login', 'basic', 'details', 'fandom', 'creations'];

        if (settingsTabs.indexOf(tabName) == -1) {
            return false;
        }

        if (tabName == currentSettingsTab) {
            return false;
        }

        $('#content-' + currentSettingsTab).css('display', 'none');
        $('#content-' + tabName).css('display', 'block');

        $('#aside-list-' + currentSettingsTab).removeClass('active');
        $('#aside-list-' + tabName).addClass('active');

        currentSettingsTab = tabName;

        return true;
    }

    $('#aside-list').click((e) => {
        let linkNode = e.target;

        if (linkNode.tagName == 'SPAN' || linkNode.tagName == 'I') {
            linkNode = linkNode.parentNode;
        }

        if (linkNode.tagName == 'LI') {
            linkNode = linkNode.firstElementChild;
        }

        if (linkNode.tagName != 'A') {
            return false;
        }

        let selectedTab = linkNode.getAttribute('href').substr(1);

        if (changeSettingsTab(selectedTab)) {
            window.location.hash = selectedTab;
            return true;
        }

        return false;
    });

    $('#content-form-changepassword').submit((event) => {
        event.preventDefault();

        $.post(
            '../ajax/changeSettingsPassword.php',
            {
                currentpassword: $('#content-input-oldpassword').val(),
                newpassword: $('#content-input-newpassword').val(),
                newpasswordrepeat: $('#content-input-repeatpassword').val()
            },
            (response) => {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    showFlashMessages();
                    return false;
                }

                $('#content-input-oldpassword').val('');
                $('#content-input-newpassword').val('');
                $('#content-input-repeatpassword').val('');

                $('#content-input-oldpassword').focus();

                showFlashMessages();
                return true;
            }
        );
    });

    $('#content-form-changedisplayname').submit((event) => {
        event.preventDefault();

        $.post(
            '../ajax/changeSettingsDisplayname.php',
            {
                value: $('#content-input-displayname').val()
            },
            (response) => {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    showFlashMessages();
                    return false;
                }

                $('#profile-actions-dropdown-displayname').text(result.data.displayname);

                $('#content-input-displayname').focus();
                $('#content-input-displayname').val(result.data.displayname);

                showFlashMessages();
                return true;
            }
        );
    });

    $('#content-form-changebirthdate').submit((event) => {
        event.preventDefault();

        $.post(
            '../ajax/changeSettingsBirthdate.php',
            {
                day: $('#content-input-birthday').val(),
                month: $('#content-input-birthmonth').val(),
                year: $('#content-input-birthyear').val()
            },
            (response) => {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    showFlashMessages();
                    return false;
                }

                showFlashMessages();
                return true;
            }
        );
    });

    $('#content-form-changeavatar').submit((event) => {
        event.preventDefault();

        let formData = new FormData();
        formData.append('avatar', document.getElementById('content-input-avatar').files[0]);

        $.ajax({
            url: '../ajax/changeSettingsAvatar.php',
            method: 'POST',
            data: formData,
            cache: false,
            contentType : false,
            processData : false,
            beforeSend: () => {
                $('#content-form-changeavatar').css('display', 'none');
                $('#content-form-changeavatar-process').css('display', 'block');
            },
            success: (response) => {
                let result = parseJSON(response);

                if (result == false || result.status != 'success') {
                    showFlashMessages();
                    return false;
                }

                if (result.data.avatar.length == 16) {
                    $('#header-user-avatar').attr('src', '../media/avatars/' + result.data.avatar + '/minres.jpg');
                    $('#content-currentavatar').attr('src', '../media/avatars/' + result.data.avatar + '/defres.jpg');
                }

                $('#content-form-changeavatar').css('display', 'block');
                $('#content-form-changeavatar-process').css('display', 'none');

                showFlashMessages();
                return true;
            }
        });
    });

    function listenDetailsForm(elementPartID, fieldName) {
        $('#content-form-change' + elementPartID).submit((event) => {
            event.preventDefault();

            let value = $('#content-input-' + elementPartID).val();

            $.post(
                '../ajax/changeSettingsDetails.php',
                {
                    field: fieldName,
                    value: value
                },
                (response) => {
                    let result = parseJSON(response);

                    if (result == false || result.status != 'success') {
                        showFlashMessages();
                        return false;
                    }

                    $('#content-input-' + elementPartID).focus();
                    $('#content-input-' + elementPartID).val(value);

                    showFlashMessages();
                    return true;
                }
            );
        });
    }

    listenDetailsForm('gender', 'gender');
    listenDetailsForm('city', 'city');
    listenDetailsForm('shortdescription', 'short_description');
    listenDetailsForm('fulldescription', 'full_description');
    listenDetailsForm('contactmethods', 'contact_methods');
    listenDetailsForm('favouritemusic', 'favourite_music');
    listenDetailsForm('favouritemovies', 'favourite_movies');
    listenDetailsForm('favouritegames', 'favourite_games');
    listenDetailsForm('fandombecameabrony', 'fandom_becameabrony');
    listenDetailsForm('fandomfavouritepony', 'fandom_favouritepony');
    listenDetailsForm('fandomfavouriteepisode', 'fandom_favouriteepisode');
    listenDetailsForm('creationslinks', 'creations_links');

    addLettersCounter('content-input-username', 'content-counter-username');
    addLettersCounter('content-input-email', 'content-counter-email');
    addLettersCounter('content-input-displayname', 'content-counter-displayname');
    addLettersCounter('content-input-city', 'content-counter-city');
    addLettersCounter('content-input-shortdescription', 'content-counter-shortdescription');
    addLettersCounter('content-input-fulldescription', 'content-counter-fulldescription');
    addLettersCounter('content-input-contactmethods', 'content-counter-contactmethods');
    addLettersCounter('content-input-favouritemusic', 'content-counter-favouritemusic');
    addLettersCounter('content-input-favouritemovies', 'content-counter-favouritemovies');
    addLettersCounter('content-input-favouritegames', 'content-counter-favouritegames');
    addLettersCounter('content-input-fandombecameabrony', 'content-counter-fandombecameabrony');
    addLettersCounter('content-input-fandomfavouritepony', 'content-counter-fandomfavouritepony');
    addLettersCounter('content-input-fandomfavouriteepisode', 'content-counter-fandomfavouriteepisode');
    addLettersCounter('content-input-creationslinks', 'content-counter-creationslinks');
});
