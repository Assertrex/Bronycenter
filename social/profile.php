<?php
// Include system initialization code
require('../system/partials/init.php');

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
        'mx-1 badge badge'
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
require('partials/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../system/partials/header-social.php');
    ?>

    <div class="container <?php echo $profileDetails['is_online'] ?: 'guest'; ?>">
        <?php
        // Include system messages if any exists
        require('../system/partials/flash.php');
        ?>

        <div class="row">
            <aside class="col-12 col-lg-4">
                <section class="fancybox text-center p-4 mt-0">
                    <img src="../media/avatars/<?php echo $profileDetails['avatar']; ?>/defres.jpg" class="rounded mb-3">

                    <?php if (!empty($profileDetails['recent_displaynames_divs'])) { ?>
                    <h5 class="mb-0" style="cursor: help;" data-toggle="tooltip" data-html="true" title="<div class='my-1'>Previous display name:</div><div class='mb-2' style='color: #BDBDBD;'><?php echo $utilities->doEscapeString($profileDetails['recent_displaynames_divs']); ?></div>"><?php echo $utilities->doEscapeString($profileDetails['display_name']); ?></h5>
                    <?php } else { ?>
                    <h5 class="mb-0"><?php echo $utilities->doEscapeString($profileDetails['display_name']); ?></h5>
                    <?php } ?>

                    <p class="mb-0 text-muted" style="margin-top: -2px;"><small>@<?php echo $profileDetails['username']; ?></small></p>
                    <p class="mb-0 mt-2">
                        <?php echo $profileDetails['is_online_badge']; ?>
                        <?php echo $profileDetails['account_type_badge'] ?? ''; ?>
                        <?php echo $profileDetails['account_standing_badge'] ?? ''; ?>
                    </p>

                    <?php if (!empty($profileDetails['short_description'])) { ?>
                    <p class="mt-3 mb-0" style="font-size: 90%; line-height: 1.4;">
                        <?php echo $utilities->doEscapeString($profileDetails['short_description']); ?>
                    </p>
                    <?php } // if ?>
                </section>

                <section class="fancybox">
                    <h6 class="text-center mb-0">About</h6>

                    <div class="px-4 my-3">
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Location">
                                <i class="fa fa-map-marker text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo $profileDetails['city'] ? $utilities->doEscapeString($profileDetails['city']) . ', ' : ''; ?><?php echo $profileDetails['country_name']; ?>
                        </p>
                        <?php if (!empty($profileDetails['gender'])) { ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Gender">
                                <i class="fa fa-transgender text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo $profileDetails['gender_name']; ?>
                        </p>
                        <?php } // if ?>
                        <?php if (!empty($profileDetails['birthdate'])) { ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Age">
                                <i class="fa fa-user-o text-primary" aria-hidden="true"></i>
                            </span>
                            <?php echo $profileDetails['birthdate_years']; ?>
                        </p>
                        <?php } // if ?>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Account created">
                                <i class="fa fa-address-book-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $profileDetails['registration_datetime']; ?> (UTC)"><?php echo $profileDetails['registration_interval']; ?></span>
                        </p>
                        <p class="mb-0">
                            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="Last seen">
                                <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
                            </span>
                            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?php echo $profileDetails['last_online']; ?> (UTC)"><?php echo $profileDetails['is_online'] ? 'Just now ' : $profileDetails['last_online_interval']; ?></span>
                        </p>
                    </div>
                </section>

                <section class="d-none fancybox">
                    <h6 class="text-center mb-0">Quick actions</h6>

                    <div class="px-4 my-3">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-block">Send a message</button>
                    </div>
                </section>
            </aside>

            <div class="col-12 col-lg-8">
                <section class="fancybox my-0" id="aside-wrapper">
                    <?php if ($profileDetails['filled_about'] || $profileDetails['filled_fandom'] || $profileDetails['filled_creations']) { ?>
                    <ul class="nav nav-tabs" id="aside-tabs">
                        <?php if ($profileDetails['filled_about']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-about" href="#about">About</a>
                        </li>
                        <?php } // if ?>
                        <li class="nav-item">
                            <a class="nav-link active" id="aside-tab-posts" href="#posts">Posts</a>
                        </li>
                        <?php if ($profileDetails['filled_fandom']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-fandom" href="#fandom">Fandom</a>
                        </li>
                        <?php } // if ?>
                        <?php if ($profileDetails['filled_creations']) { ?>
                        <li class="nav-item">
                            <a class="nav-link" id="aside-tab-creations" href="#creations">Creations</a>
                        </li>
                        <?php } // if ?>
                    </ul>
                    <?php } // if ?>

                    <div id="aside-tabs-content">
                        <?php if ($profileDetails['filled_about']) { ?>
                        <div id="aside-about" style="display: none;">
                            <h6 class="text-center mb-0">About</h6>

                            <div class="p-3">
                                <?php if (!empty($profileDetails['full_description'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Something about me</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['full_description']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['contact_methods'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">You can find me there</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['contact_methods']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_music'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Favourite music</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['favourite_music']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_movies'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Favourite movies</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['favourite_movies']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['favourite_games'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Favourite games</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['favourite_games']); ?></div>
                                </div>
                                <?php } // if ?>
                            </div>
                        </div>
                        <?php } // if ?>

                        <div id="aside-posts">
                            <h6 class="text-center mb-0">Posts</h6>

                            <div class="p-3">
                                <p class="mb-0 text-info text-center">
                                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                    User's posts will appear there in future update.
                                </p>
                            </div>
                        </div>

                        <?php if ($profileDetails['filled_fandom']) { ?>
                        <div id="aside-fandom" style="display: none;">
                            <h6 class="text-center mb-0">Fandom</h6>

                            <div class="p-3">
                                <?php if (!empty($profileDetails['fandom_becameabrony'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">When I've became a brony/pegasister</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['fandom_becameabrony']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['fandom_favouritepony'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Favourite pony</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['fandom_favouritepony']); ?></div>
                                </div>
                                <?php } // if ?>

                                <?php if (!empty($profileDetails['fandom_favouriteepisode'])) { ?>
                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Favourite episode</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['fandom_favouriteepisode']); ?></div>
                                </div>
                                <?php } // if ?>
                            </div>
                        </div>
                        <?php } // if ?>

                        <?php if ($profileDetails['filled_creations']) { ?>
                        <div id="aside-creations" style="display: none;">
                            <h6 class="text-center mb-0">Creations</h6>

                            <div class="p-3">
                                <div class="aside-content-blocks mb-3 text-info text-center">
                                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                    Social networks integration will be available in future update.
                                </div>

                                <div class="aside-content-blocks mb-3">
                                    <p class="aside-content-titles mb-2">Look what I've made</p>
                                    <div><?php echo $utilities->doEscapeString($profileDetails['creations_links']); ?></div>
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
    require('partials/scripts.php');
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
