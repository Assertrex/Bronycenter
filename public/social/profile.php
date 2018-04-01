<?php
// Include system initialization code
require('../../application/partials/init.php');

// Get an ID of a selected profile
if (!empty($_GET['u'])) {
    $profileId = intval($_GET['u']);
} else if (!empty($_SESSION['account']['id'])) {
    $profileId = $_SESSION['account']['id'];
} else {
    $profileId = null;
}

// Redirect guest if no profile has been choosen
if (empty($profileId)) {
    $flash->info('You\'ve been redirected into members page, because no profile has been selected.');
    $utilities->redirect('members.php');
}

// Get details about selected user
$profileDetails = $user->generateUserDetails($profileId, ['descriptions' => true]);

// Redirect if user has not been found
if (empty($profileDetails)) {
    $flash->info('You\'ve been redirected into members page, because user doesn\'t exist.');
    $utilities->redirect('members.php');
}

// Generate user badges and add them the array with user details
$profileDetails = array_merge(
    $profileDetails,
    $utilities->generateUserBadges(
        $profileDetails,
        'badge badge'
    )
);

// Page settings
$pageTitle = 'Profile :: BronyCenter';
$pageStylesheet = '
#aside-tabs { background: #EEE; justify-content: center; }
#aside-tabs .nav-item { flex: 1; text-align: center; }
#aside-tabs .nav-item:first-child .nav-link.active { border-left: 0; }
#aside-tabs .nav-item:last-child .nav-link.active { border-right: 0; }
#aside-tabs .nav-link { border-top: 0; border-radius: 0; }
#aside-tabs .nav-link.active { background-color: #EEE; color: #616161; border-bottom: 1px solid #EEE; }
#aside-tabs-content h6 { padding: .5rem 0; background-color: #EEEEEE; color: #616161; border-bottom: 1px solid #BDBDBD; }
#aside-tabs-content .aside-content-titles { font-size: 13px; color: #90949c; text-transform: uppercase; border-bottom: 1px solid #dddfe2; line-height: 26px; }
#aside-tabs-content .aside-content-blocks:last-child { margin-bottom: 0 !important; }
';

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container <?= $profileDetails['is_online'] ?: 'guest'; ?>">
        <?php
        // Include system messages if any exists
        require('../../application/partials/flash.php');
        ?>

        <div class="row">
            <aside class="col-12 col-lg-4">
                <section class="fancybox text-center p-4 mt-0">
                    <img src="../media/avatars/<?= $profileDetails['avatar']; ?>/defres.jpg" class="rounded mb-3">

                    <?php if (!empty($profileDetails['recent_displaynames_divs'])) { ?>
                    <h5 class="mb-0" style="cursor: help;" data-toggle="tooltip" data-html="true" title="<div class='my-1'>Previous display name:</div><div class='mb-2' style='color: #BDBDBD;'><?= $utilities->doEscapeString($profileDetails['recent_displaynames_divs'], false); ?></div>"><?= $utilities->doEscapeString($profileDetails['display_name'], false); ?></h5>
                    <?php } else { ?>
                    <h5 class="mb-0"><?= $utilities->doEscapeString($profileDetails['display_name'], false); ?></h5>
                    <?php } ?>

                    <p class="mb-0 text-muted" style="margin-top: -2px;"><small>@<?= $profileDetails['username']; ?></small></p>
                    <p class="mb-0 mt-2">
                        <?= $profileDetails['account_type_badge'] ?? ''; ?>
                        <?= $profileDetails['account_standing_badge'] ?? ''; ?>
                        <?= $profileDetails['is_online_badge']; ?>
                    </p>

                    <?php if (!empty($profileDetails['short_description'])) { ?>
                    <p class="mt-3 mb-0" style="font-size: 90%; line-height: 1.4;">
                        <?= $utilities->doEscapeString($profileDetails['short_description'], false); ?>
                    </p>
                    <?php } // if ?>
                </section>

                <section class="fancybox">
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'aboutMe') ?></h6>

                    <div class="px-4 my-3">
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'location') ?>">
                                <i class="fa fa-map-marker text-primary" aria-hidden="true"></i>
                            </span>
                            <?= $profileDetails['city'] ? $utilities->doEscapeString($profileDetails['city'], false) . ', ' : ''; ?><?= $profileDetails['country_name'] ?? '<span class="text-danger">Unknown country</span>'; ?>
                        </p>
                        <?php if (!empty($profileDetails['gender'])) { ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'gender') ?>">
                                <i class="fa fa-transgender text-primary" aria-hidden="true"></i>
                            </span>
                            <?= $profileDetails['gender_name']; ?>
                        </p>
                        <?php } // if ?>
                        <?php if (!empty($profileDetails['birthdate'])) { ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'age') ?>">
                                <i class="fa fa-user-o text-primary" aria-hidden="true"></i>
                            </span>
                            <?= $profileDetails['birthdate_years']; ?>
                        </p>
                        <?php } // if ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'accountCreated') ?>">
                                <i class="fa fa-address-book-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $profileDetails['registration_datetime']; ?> (UTC)"><?= $profileDetails['registration_interval']; ?></span>
                        </p>
                        <?php if (!empty($profileDetails['last_online'])) { ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'lastSeen') ?>">
                                <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $profileDetails['last_online']; ?> (UTC)">
                                <?= $profileDetails['is_online'] ? $o_translation->getString('dates', 'justNow') : $profileDetails['last_online_interval']; ?>
                            </span>
                        </p>
                        <?php } // if ?>
                    </div>
                </section>

                <?php if ($loggedIn && !$readonlyState) { ?>
                <section class="fancybox">
                    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'quickActions') ?></h6>

                    <div class="px-4 my-3">
                        <?php if ($profileDetails['id'] == $_SESSION['account']['id']) { ?>
                        <button type="button" role="button" class="btn btn-outline-primary btn-sm btn-block" style="cursor: not-allowed" disabled>
                            <?= $o_translation->getString('profile', 'sendMessage') ?>
                        </button>
                        <?php } else { ?>
                        <button type="button" role="button" data-toggle="modal" data-target="#mainModal" id="btn-profile-sendmessage" class="btn btn-outline-primary btn-sm btn-block" data-userid="<?= $profileDetails['id']; ?>"  data-userdisplayname="<?= $utilities->doEscapeString($profileDetails['display_name']); ?>">
                            <?= $o_translation->getString('profile', 'sendMessage') ?>
                        </button>
                        <?php } // if ?>
                    </div>
                </section>
                <?php } // if ?>
            </aside>

            <div class="col-12 col-lg-8">
                <section class="fancybox my-0" id="aside-wrapper">
                    <?php if ($profileDetails['filled_about'] || $profileDetails['filled_fandom'] || $profileDetails['filled_creations']) { ?>
                    <ul class="nav nav-tabs" id="aside-tabs">
                        <?php if ($profileDetails['filled_about']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-about" href="#about"><?= $o_translation->getString('profile', 'about') ?></a>
                        </li>
                        <?php } // if ?>
                        <li class="nav-item">
                            <a class="nav-link active" id="aside-tab-posts" href="#posts"><?= $o_translation->getString('profile', 'posts') ?></a>
                        </li>
                        <?php if ($profileDetails['filled_fandom']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-fandom" href="#fandom"><?= $o_translation->getString('profile', 'fandom') ?></a>
                        </li>
                        <?php } // if ?>
                        <?php if ($profileDetails['filled_creations']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-creations" href="#creations"><?= $o_translation->getString('profile', 'creations') ?></a>
                        </li>
                        <?php } // if ?>
                    </ul>
                    <?php } // if ?>

                    <div id="aside-tabs-content">
                        <?php if ($profileDetails['filled_about']) { ?>
                        <div id="aside-about" style="display: none;">
                            <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'about') ?></h6>

                            <div class="p-3">
                                <?php if (!empty($profileDetails['full_description'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_1') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['full_description']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['contact_methods'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_2') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['contact_methods']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_music'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_3') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['favourite_music']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_movies'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_4') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['favourite_movies']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_games'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_5') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['favourite_games']); ?></div>
                                </div>
                                <?php } // if ?>
                            </div>
                        </div>
                        <?php } // if ?>

                        <div id="aside-posts">
                            <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'posts') ?></h6>

                            <div class="p-3">
                                <p class="mb-0 text-info text-center">
                                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                    <?= $o_translation->getString('profile', 'postsInFutureUpdate') ?>.
                                </p>
                            </div>
                        </div>

                        <?php if ($profileDetails['filled_fandom']) { ?>
                        <div id="aside-fandom" style="display: none;">
                            <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'fandom') ?></h6>

                            <div class="p-3">
                                <?php if (!empty($profileDetails['fandom_becameabrony'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_1') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['fandom_becameabrony']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['fandom_favouritepony'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_2') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['fandom_favouritepony']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['fandom_favouriteepisode'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_3') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['fandom_favouriteepisode']); ?></div>
                                </div>
                                <?php } // if ?>
                            </div>
                        </div>
                        <?php } // if ?>

                        <?php if ($profileDetails['filled_creations']) { ?>
                        <div id="aside-creations" style="display: none;">
                            <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'creations') ?></h6>

                            <div class="p-3">
                                <div class="aside-content-blocks mb-3 text-info text-center">
                                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                    <?= $o_translation->getString('profile', 'creationsInFutureUpdate') ?>.
                                </div>

                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_3_custom_field_1') ?></p>
                                    <div><?= $utilities->doEscapeString($profileDetails['creations_links']); ?></div>
                                </div>

                            </div>
                        </div>
                        <?php } // if ?>
                    </div>
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
        // Default profile tab is set to post
        let currentProfileTab = 'posts';

        // Change current tab if hash exists and is valid
        if (window.location.hash) {
            let selectedTab = window.location.hash.substr(1);

            changeProfileTab(selectedTab);
        }

        // Listen to a send message button
        $('#btn-profile-sendmessage').click((e) => {
            let userID = e.currentTarget.getAttribute('data-userid');
            let userDisplayname = e.currentTarget.getAttribute('data-userdisplayname');

            // Display a modal for writing a message
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

            // Listen to the form input and update it's letters counter
            addLettersCounter('input-profile-sendmessage-content', 'input-profile-sendmessage-content-counter');

            // Listen to a send button
            $('#btn-profile-sendmessage-confirm').click((e) => {
                let messageContent = $('#input-profile-sendmessage-content').val();
                let messageRecipent = e.currentTarget.getAttribute('data-userid');

                // Send a new message
                $.post("../ajax/doMessageSend.php", { id: messageRecipent, message: messageContent }, function(response) {
                    let json;

                    // Try to parse a JSON
                    try {
                        json = JSON.parse(response);
                    } catch (e) {
                        // Return a failed system message
                        showFlashMessages();
                        return false;
                    }

                    // Modify post content if it has been modified successfully
                    if (json.status == 'success') {
                        // TODO FILL THIS WITH SOMETHING
                    }

                    // Return a successful system message
                    showFlashMessages();
                    return true;
                });
            });
        });

        // Listen for profile tab clicks
        $("#aside-tabs").click((e) => {
            // Check if tab link has been clicked
            if ($(e.target).hasClass('nav-link')) {
                let selectedTab = e.target.getAttribute("href").substr(1);

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
            $("#aside-" + currentProfileTab).css("display", "none");
            $("#aside-" + tabName).css("display", "block");

            // Update current tab on tabs list
            $("#aside-tab-" + currentProfileTab).removeClass("active");
            $("#aside-tab-" + tabName).addClass("active");

            // Store new tab value
            currentProfileTab = tabName;

            return true;
        }
    });
    </script>
</body>
</html>
