<?php
// Include system initialization code
require('../../application/partials/init.php');

// Get a list of active members
$members = $user->getMembersList();

// Page settings
$pageTitle = 'Members :: BronyCenter';
$pageStylesheet = '
#members-list div a { color: #000; text-decoration: none; border-top: 1px solid #E9ECEF; }
#members-list div a:first-child { border-top: 0; }
#members-list div a:hover { background-color: #EEE; }
';

// Include social head content for all pages
require('../../application/partials/social/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../../application/partials/social/header.php');
    ?>

    <div class="container <?= $loggedIn ?: 'guest'; ?>">
        <?php
        // Include system messages if any exists
        require('../../application/partials/flash.php');
        ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0 mb-0" id="members-list">
                    <h6 class="text-center mb-0">Members list</h6>

                    <div>
                        <?php
                        // Display each member
                        foreach ($members as $member) {
                            // Generate additional user details
                            $member = $user->generateUserDetails($member['id']);

                            // Generate user badges and add them the array with user details
                            $member = array_merge(
                                $member,
                                $utilities->generateUserBadges(
                                    $member,
                                    'd-block my-1 badge badge'
                                )
                            );
                        ?>

                        <a class="d-flex align-items-center p-2 px-lg-3" href="profile.php?u=<?= $member['id']; ?>">
                            <div class="mr-2 mr-lg-3" style="width: 64px;">
                                <img src="../media/avatars/<?= $member['avatar']; ?>/minres.jpg" class="rounded" />
                                <div class="d-block d-lg-none">
                                    <?= !empty($member['account_type_badge']) ? '<div>' . $member['account_type_badge'] . '</div>' : ''; ?>
                                    <?= !empty($member['is_online_badge']) ? '<div>' . $member['is_online_badge'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <div class="d-none d-lg-block mr-3" style="width: 66px;">
                                <?= !empty($member['account_type_badge']) ? '<div>' . $member['account_type_badge'] . '</div>' : ''; ?>
                                <?= !empty($member['account_standing_badge']) ? '<div>' . $member['account_standing_badge'] . '</div>' : ''; ?>
                                <?= !empty($member['is_online_badge']) ? '<div>' . $member['is_online_badge'] . '</div>' : ''; ?>
                            </div>
                            <div class="text-center" style="flex: 1; line-height: 1.15;">
                                <div>
                                    <?= $utilities->doEscapeString($member['display_name'], false); ?>
                                </div>
                                <div>
                                    <small class="text-muted">@<?= $member['username']; ?></small>
                                </div>
                                <?php if (!empty($member['short_description'])) { ?>
                                <div class="mt-2">
                                    <small>"<?= $utilities->doEscapeString($member['short_description'], false); ?>"</small>
                                </div>
                                <?php } // if ?>
                            </div>
                        </a>

                        <?php
                        } // foreach
                        ?>
                    </div>
                </section>
            </div>

            <aside class="col-12 col-lg-4">
                <?php
                // Include partial containing an aside panel
                require('../../application/partials/social/members/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('../../application/partials/social/scripts.php');
    ?>
</body>
</html>
