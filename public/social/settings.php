<?php

// Page settings
$pageTitle = 'Settings :: BronyCenter';
$pageStylesheet = '
aside .fa { width: 15px; }
aside .list-group-item { border-radius: 0 !important; cursor: pointer; }
aside .list-group-item:hover { background-color: #EEE; }
aside .list-group-item a { color: #424242; text-decoration: none; }
aside .list-group-item.active { background-color: #1565C0; border-color: #1565C0; }
aside .list-group-item.active a { color: rgba(255, 255, 255, .8); }
aside .list-group-item span { flex: 1; margin-left: -22px; text-align: center; user-select: none; }
#tabs-content h6 { padding: .5rem 0; background-color: #EEEEEE; color: #616161; border-bottom: 1px solid #BDBDBD; }
#tabs-content .content-block:last-child { margin-bottom: 0 !important; }
#tabs-content .content-title { font-size: 12px; color: #90949C; text-transform: uppercase; border-bottom: 1px solid #DDDFE2; line-height: 26px; }
input, textarea, select { font-size: 12px !important; }
#content-input-avatar { font-size: 12px !important; }
#content-currentavatar { width: 64px; height: 64px; }

@media (min-width: 576px) {
    #tabs-content .content-title { font-size: 13px; }
    input, textarea, select { font-size: 15px !important; }
    .content-block .form-inline input { flex: 1; }
    #content-currentavatar { width: 128px; height: 128px; }
';

// Allow access only for logged users
$loginRequired = true;

// Include system initialization code
require('../../application/partials/init.php');

// Get details about selected user
$userDetails = $user->generateUserDetails($_SESSION['account']['id'], ['descriptions' => true, 'sensitive' => true]);

// Generate user badges and add them the array with user details
$userDetails = array_merge(
    $userDetails,
    $utilities->generateUserBadges(
        $userDetails,
        'mx-1 badge badge',
        'vertical-align: text-bottom;'
    )
);

// Add two more account standing badges
switch ($userDetails['account_standing']) {
    case '0':
        $userDetails['account_standing_badge'] = '<span class="mx-1 badge badge-success">Good</span>';
        break;
    default:
        $userDetails['account_standing_badge'] = '<span class="mx-1 badge badge-secondary">Unknown</span>';
}

// Separate birthdate into temporary day/month/year values
if (!is_null($userDetails['birthdate'])) {
    $birthdate_temp = explode('-', $userDetails['birthdate']);
    $userDetails['birthdate_temp']['year'] = $birthdate_temp[0];
    $userDetails['birthdate_temp']['month'] = $birthdate_temp[1];
    $userDetails['birthdate_temp']['day'] = $birthdate_temp[2];
} else {
    $userDetails['birthdate_temp'] = null;
}

// Get user's login history
$loginHistory = $session->getHistory();

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../../application/partials/flash.php');
        ?>

        <div class="row">
            <aside id="aside-list" class="col-12 col-lg-4">
                <section class="fancybox mt-lg-0">
                    <h6 class="text-center mb-0">Account settings</h6>

                    <ul class="list-group">
                        <li class="list-group-item active" id="aside-list-credentials"><a href="#credentials" class="d-flex align-items-center"><i class="fa fa-key mr-2" aria-hidden="true"></i> <span>Login credentials</span></a></li>
                        <li class="list-group-item" id="aside-list-email"><a href="#email" class="d-flex align-items-center"><i class="fa fa-envelope-o mr-2" aria-hidden="true"></i> <span>E-mail settings</span></a></li>
                        <li class="list-group-item" id="aside-list-standing"><a href="#standing" class="d-flex align-items-center"><i class="fa fa-exclamation-triangle mr-2" aria-hidden="true"></i> <span>Account standing</span></a></li>
                        <li class="list-group-item" id="aside-list-login"><a href="#login" class="d-flex align-items-center"><i class="fa fa-clock-o mr-2" aria-hidden="true"></i> <span>Login history</span></a></li>
                    </ul>
                </section>

                <section class="fancybox mt-lg-0">
                    <h6 class="text-center mb-0">Profile settings</h6>

                    <ul class="list-group">
                        <li class="list-group-item" id="aside-list-basic"><a href="#basic" class="d-flex align-items-center"><i class="fa fa-user-circle-o mr-2" aria-hidden="true"></i> <span>Basic information</span></a></li>
                        <li class="list-group-item" id="aside-list-details"><a href="#details" class="d-flex align-items-center"><i class="fa fa-file-text-o mr-2" aria-hidden="true"></i> <span>Tell about yourself</span></a></li>
                        <li class="list-group-item" id="aside-list-fandom"><a href="#fandom" class="d-flex align-items-center"><i class="fa fa-users mr-2" aria-hidden="true"></i> <span>You in a fandom</span></a></li>
                        <li class="list-group-item" id="aside-list-creations"><a href="#creations" class="d-flex align-items-center"><i class="fa fa-star-o mr-2" aria-hidden="true"></i> <span>Share your creativity</span></a></li>
                    </ul>
                </section>
            </aside>

            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0 mb-0" id="tabs-content">
                    <?php require('../../application/partials/social/settings/content-credentials.php'); ?>
                    <?php require('../../application/partials/social/settings/content-email.php'); ?>
                    <?php require('../../application/partials/social/settings/content-standing.php'); ?>
                    <?php require('../../application/partials/social/settings/content-login.php'); ?>
                    <?php require('../../application/partials/social/settings/content-basic.php'); ?>
                    <?php require('../../application/partials/social/settings/content-details.php'); ?>
                    <?php require('../../application/partials/social/settings/content-fandom.php'); ?>
                    <?php require('../../application/partials/social/settings/content-creations.php'); ?>
                </section>
            </div>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>

    <script type="text/javascript">
    "use-strict";

    // Start when document is ready
    $(document).ready(function() {
        // Default settings tab is set to basic details
        let currentSettingsTab = 'credentials';

        // Change current settings list tab if hash exists and is valid
        if (window.location.hash) {
            let selectedTab = window.location.hash.substr(1);

            changeSettingsTab(selectedTab);
        }

        // Listen for aside settings list clicks
        $("#aside-list").click((e) => {
            let linkNode = e.target;

            // Move to parent if title or icon has been clicked
            if (linkNode.tagName == 'SPAN' || linkNode.tagName == 'I') {
                linkNode = linkNode.parentNode;
            }

            // Move to child link if list item has been clicked
            if (linkNode.tagName == 'LI') {
                linkNode = linkNode.firstElementChild;
            }

            // Check if valid settings tab has been clicked
            if (linkNode.tagName != 'A') {
                return false;
            }

            // Name of selected tab
            let selectedTab = linkNode.getAttribute("href").substr(1);

            // Check if settings tab has been switched correctly
            if (changeSettingsTab(selectedTab)) {
                window.location.hash = selectedTab;

                return true;
            }

            return false;
        });

        // Listen for password change form submit
        $("#content-form-changepassword").submit((e) => {
            // Disable default redirection of form submission
            e.preventDefault();

            // Store input values
            let currentPassword = $("#content-input-oldpassword").val();
            let newPassword = $("#content-input-newpassword").val();
            let newPasswordRepeat = $("#content-input-repeatpassword").val();

            // Check if all fields are filled
            if (currentPassword.length === 0 || newPassword.length === 0 || newPasswordRepeat.length === 0) {
                return false;
            }

            // Request change of a password
            $.post(
                "../ajax/changeSettingsPassword.php",
                { currentpassword: currentPassword, newpassword: newPassword, newpasswordrepeat: newPasswordRepeat },
                () => {
                    $("#content-input-oldpassword").focus();

                    $("#content-input-oldpassword").val('');
                    $("#content-input-newpassword").val('');
                    $("#content-input-repeatpassword").val('');

                    showFlashMessages();
                }
            );
        });

        // Listen for username change form // Not changeable
        addLettersCounter('content-input-username', 'content-counter-username');

        // Listen for email change form // TODO
        addLettersCounter('content-input-email', 'content-counter-email');

        // Listen for display name change form
        addLettersCounter('content-input-displayname', 'content-counter-displayname');

        // Listen for a selected form submit
        $("#content-form-changedisplayname").submit((e) => {
            // Disable default redirection of form submission
            e.preventDefault();

            // Store input value
            let value = $("#content-input-displayname").val();

            // Request change of a user's display name
            $.post(
                "../ajax/changeSettingsDisplayname.php",
                { value: value },
                (result) => {
                    $("#content-input-displayname").focus();
                    $("#content-input-displayname").val(result);
                    showFlashMessages();
                }
            );
        });

        // Listen for gender change form
        listenDetailsForm('gender', 'gender');

        // Listen for birthdate change form
        $("#content-form-changebirthdate").submit((e) => {
            // Disable default redirection of form submission
            e.preventDefault();

            // Store input values
            let valueDay = $("#content-input-birthday").val();
            let valueMonth = $("#content-input-birthmonth").val();
            let valueYear = $("#content-input-birthyear").val();

            // Request change of a user's display name
            $.post(
                "../ajax/changeSettingsBirthdate.php",
                { day: valueDay, month: valueMonth, year: valueYear },
                (result) => {
                    // TODO Update it somehow if it's possible to do it easy
                    showFlashMessages();
                }
            );
        });

        // Listen for avatar change form
        $("#content-form-changeavatar").submit((e) => {
            // Disable default redirection of form submission
            e.preventDefault();

            // Store formdata because file upload via XHR is not supported
            let formData = new FormData();
            formData.append("avatar", document.getElementById("content-input-avatar").files[0]);

            console.log(formData.entries());

            // Request change of a user's avatar
            $.ajax({
                url: "../ajax/changeSettingsAvatar.php",
                method: "POST",
                data: formData,
                cache: false,
                contentType : false,
                processData : false,
                beforeSend: () => {
                    $('#content-form-changeavatar').css('display', 'none');
                    $('#content-form-changeavatar-process').css('display', 'block');
                },
                success: (result) => {
                    if (result.length = 16) {
                        $('#header-user-avatar').attr('src', '../media/avatars/' + result + '/minres.jpg');
                        $('#content-currentavatar').attr('src', '../media/avatars/' + result + '/defres.jpg');
                    }

                    $('#content-form-changeavatar').css('display', 'block');
                    $('#content-form-changeavatar-process').css('display', 'none');

                    showFlashMessages();
                }
            });
        });

        // Listen for city name change form
        addLettersCounter('content-input-city', 'content-counter-city');
        listenDetailsForm('city', 'city');

        // Listen for short description change form
        addLettersCounter('content-input-shortdescription', 'content-counter-shortdescription');
        listenDetailsForm('shortdescription', 'short_description');

        // Listen for full description change form
        listenDetailsForm('fulldescription', 'full_description');
        addLettersCounter('content-input-fulldescription', 'content-counter-fulldescription');

        // Listen for contact methods change form
        listenDetailsForm('contactmethods', 'contact_methods');
        addLettersCounter('content-input-contactmethods', 'content-counter-contactmethods');

        // Listen for favourite music change form
        listenDetailsForm('favouritemusic', 'favourite_music');
        addLettersCounter('content-input-favouritemusic', 'content-counter-favouritemusic');

        // Listen for favourite movies change form
        listenDetailsForm('favouritemovies', 'favourite_movies');
        addLettersCounter('content-input-favouritemovies', 'content-counter-favouritemovies');

        // Listen for favourite games change form
        listenDetailsForm('favouritegames', 'favourite_games');
        addLettersCounter('content-input-favouritegames', 'content-counter-favouritegames');

        // Listen for fandom became a brony change form
        listenDetailsForm('fandombecameabrony', 'fandom_becameabrony');
        addLettersCounter('content-input-fandombecameabrony', 'content-counter-fandombecameabrony');

        // Listen for fandom favourite pony change form
        listenDetailsForm('fandomfavouritepony', 'fandom_favouritepony');
        addLettersCounter('content-input-fandomfavouritepony', 'content-counter-fandomfavouritepony');

        // Listen for fandom favourite episode change form
        listenDetailsForm('fandomfavouriteepisode', 'fandom_favouriteepisode');
        addLettersCounter('content-input-fandomfavouriteepisode', 'content-counter-fandomfavouriteepisode');

        // Listen for creations links change form
        listenDetailsForm('creationslinks', 'creations_links');
        addLettersCounter('content-input-creationslinks', 'content-counter-creationslinks');

        // Listen for details change form submit
        function listenDetailsForm(elementPartID, fieldName) {
            // Listen for a selected form submit
            $("#content-form-change" + elementPartID).submit((e) => {
                // Disable default redirection of form submission
                e.preventDefault();

                // Store input value
                let value = $("#content-input-" + elementPartID).val();

                // Request change of a selected settings field value
                $.post(
                    "../ajax/changeSettingsDetails.php",
                    { field: fieldName, value: value },
                    (result) => {
                        $("#content-input-" + elementPartID).focus();
                        $("#content-input-" + elementPartID).val(result);
                        showFlashMessages();
                    }
                );
            });
        }

        // Change settings tab and content
        function changeSettingsTab(tabName) {
            let settingsTabs = ["credentials", "email", "standing", "login", "basic", "details", "fandom", "creations"];

            // Check if tab name is valid
            if (settingsTabs.indexOf(tabName) == -1) {
                return false;
            }

            // Don't switch tab if same has been selected
            if (tabName == currentSettingsTab) {
                return false;
            }

            // Disable current tab and display new one
            $("#content-" + currentSettingsTab).css("display", "none");
            $("#content-" + tabName).css("display", "block");

            // Update current tab on tabs list
            $("#aside-list-" + currentSettingsTab).removeClass("active");
            $("#aside-list-" + tabName).addClass("active");

            // Store new tab value
            currentSettingsTab = tabName;

            return true;
        }
    });
    </script>
</body>
</html>
