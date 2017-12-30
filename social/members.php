<?php
// Include system initialization code
require('../system/partials/init.php');

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
require('partials/head.php');
?>

<body>
    <?php
    // Include social header for all pages
    require('../system/partials/header-social.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../system/partials/flash.php');
        ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <section class="fancybox mt-0 mb-0" id="members-list">
                    <h6 class="text-center mb-0">Members list</h6>

                    <div>
                        <?php
                        // Display each member
                        foreach ($members as $member) {
                            // Check if user is currently logged in
                            $isOnline = $user->isOnline(null, $member['last_online']);

                            if ($isOnline) {
                                $userOnline = '<span class="badge badge-success mx-2" style="flex: 1;">Online</span>';
                            } else {
                                $userOnline = '';
                            }

                            // Display user badge
                            switch ($member['account_type']) {
                                case '9':
                                    $userBadge = '<span class="badge badge-danger mx-2" style="flex: 1;">Admin</span>';
                                    break;
                                case '8':
                                    $userBadge = '<span class="badge badge-info mx-2" style="flex: 1;">Mod</span>';
                                    break;
                                default:
                                    $userBadge = '';
                            }

                            // Set user's avatar or get the default one if not existing
                            $avatar = $member['avatar'] ?? 'default';
                        ?>

                        <a class="d-flex align-items-center p-2 px-lg-3" href="profile.php?u=<?php echo $member['id']; ?>">
                            <div style="width: 64px;">
                                <img src="../media/avatars/<?php echo $avatar; ?>/minres.jpg" class="rounded" />
                            </div>
                            <div class="text-center pl-2 pl-lg-3" style="flex: 1; line-height: 1.1;">
                                <div><?php echo htmlspecialchars($member['display_name']); ?></div>
                                <div><small class="text-muted">@<?php echo $member['username']; ?></small></div>
                                <div<?php if (!empty($userOnline) || !empty($userBadge)) { echo ' class="d-flex mt-2"'; } ?>>
                                    <?php echo $userBadge; ?> <?php echo $userOnline; ?>
                                </div>
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
                require('partials/members/aside.php');
                ?>
            </aside>
        </div>
    </div>

    <?php
    // Include social scripts for all pages
    require('partials/scripts.php');
    ?>
</body>
</html>
