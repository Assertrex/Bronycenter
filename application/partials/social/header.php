<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <?php
        // Display links for logged users
        if ($loggedIn) {
        ?>
        <a class="navbar-brand" href="index.php">BronyCenter</a>
        <?php
        } // if
        else {
        ?>
        <a class="navbar-brand" href="../index.php">BronyCenter</a>
        <?php
        } // if
        ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mt-1 mt-lg-0" style="flex: 1;">
                <?php
                // Display links for logged users
                if ($loggedIn) {
                ?>
                <div class="d-none d-lg-flex justify-content-center" style="flex: 1;">
                    <input class="mx-4 px-3" id="navbar-searchbar" type="text" placeholder="<?= $o_translation->getString('common', 'searchForUser') ?>..." />
                    <div class="d-none pb-2" id="navbar-searchbar-result"></div>
                </div>

                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="profile.php?u=<?= $_SESSION['account']['id']; ?>"><i class="fa fa-user-o mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'profile') ?></a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="members.php"><i class="fa fa-address-book-o mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'members') ?></a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="messages.php"><i class="fa fa-envelope-o mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'messages') ?></a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="achievements.php"><i class="fa fa-bar-chart mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'statistics') ?></a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="settings.php"><i class="fa fa-sliders mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'settings') ?></a>
                </li>
                <?php
                // Display moderate button only for moderators
                if ($loggedModerator) {
                ?>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="dashboard.php"><i class="fa fa-cogs mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'manage') ?></a>
                </li>
                <?php
                } // if
                ?>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="../logout.php"><i class="fa fa-sign-out mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'logout') ?></a>
                </li>
                <?php
                } // if

                // Display links for guests
                else {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php"><i class="fa fa-home d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'homepage') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../about.php"><i class="fa fa-info-circle d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'about') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../contact.php"><i class="fa fa-envelope-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'contact') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="members.php"><i class="fa fa-address-book-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> <?= $o_translation->getString('header', 'members') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Assertrex/BronyCenter" target="_blank"><i class="fa fa-github d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Github</a>
                </li>
                <?php
                } // else
                ?>
            </ul>

            <?php
            // Display user panel for logged users
            if ($loggedIn) {
            ?>
            <ul class="navbar-nav align-items-center d-none d-lg-flex">
                <div id="header-notifications-bar">
                    <ul>
                        <a href="members.php">
                            <li>
                                <i class="fa fa-user-circle-o" style="font-size: 22px;" aria-hidden="true"></i>
                                <div class="notifications-badge-wrapper">
                                    <!-- <span class="badge badge-danger" style="right: -4px;">1</span> -->
                                </div>
                            </li>
                        </a>
                        <a href="messages.php">
                            <li>
                                <i class="fa fa-comments-o" style="margin-top: -2px; font-size: 25px;" aria-hidden="true"></i>
                                <div class="notifications-badge-wrapper">
                                    <!-- <span class="badge badge-danger" style="right: -4px;">1</span> -->
                                </div>
                            </li>
                        </a>
                        <li>
                            <i class="fa fa-bell-o" style="font-size: 22px;" aria-hidden="true"></i>
                            <div class="notifications-badge-wrapper">
                                <!-- <span class="badge badge-danger" style="right: -2px;">1</span> -->
                            </div>
                        </li>
                    </ul>
                </div>

                <li class="nav-item ml-3">
                    <div class="dropdown" id="profile-actions-dropdown">
                        <a href="#" class="nav-link d-flex align-items-center py-1 dropdown-toggle" role="button" id="profile-actions-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="pr-2" id="profile-actions-dropdown-avatar">
                                <img class="rounded" id="header-user-avatar" src="../media/avatars/<?= $_SESSION['user']['avatar']; ?>/minres.jpg" alt="<?= $o_translation->getString('common', 'yourAvatar') ?>" />
                            </div>
                            <div class="pr-2" id="profile-actions-dropdown-button-username">
                                <?= $utilities->doEscapeString($_SESSION['user']['displayname'], false); ?><br />
                                <small>@<?= $_SESSION['account']['username']; ?></small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profile-actions-dropdown-button">
                            <h6 class="dropdown-header"><?= $o_translation->getString('header', 'account') ?></h6>
                            <a class="dropdown-item" href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>"><i class="fa fa-user" aria-hidden="true"></i> <?= $o_translation->getString('header', 'profile') ?></a>
                            <a class="dropdown-item" href="achievements.php"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?= $o_translation->getString('header', 'statistics') ?></a>
                            <a class="dropdown-item" href="settings.php"><i class="fa fa-sliders" aria-hidden="true"></i> <?= $o_translation->getString('header', 'settings') ?></a>
                            <a class="dropdown-item" href="../logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> <?= $o_translation->getString('header', 'logout') ?></a>

                            <?php
                            // Display moderate button only for moderators
                            if ($loggedModerator) {
                            ?>
                            <h6 class="dropdown-header"><?= $o_translation->getString('header', 'moderate') ?></h6>
                            <a class="dropdown-item" href="dashboard.php"><i class="fa fa-cogs" aria-hidden="true"></i> <?= $o_translation->getString('header', 'manage') ?></a>
                            <?php
                            } // if
                            ?>
                        </div>
                    </div>
                </li>
            </ul>
            <?php
            } // if

            // Display login form for guests
            else {
            ?>
                <?php if ($websiteSettings['enableLogin']) { ?>
                    <form method="post" action="../login.php" class="form-inline justify-content-end justify-content-sm-center py-lg-0">
                        <input type="text" name="username" class="form-control mb-2 mb-sm-0" id="login-username" placeholder="<?= $o_translation->getString('common', 'usernameOrEmail') ?>" autocomplete="username" autofocus required />
                        <input type="password" name="password" class="form-control mb-2 mb-sm-0 ml-sm-2 mr-lg-2" id="login-password" placeholder="<?= $o_translation->getString('common', 'password') ?>" autocomplete="current-password" required />
                        <button type="submit" name="submit" class="btn mt-sm-2 mt-lg-0" id="login-submit" value="login"><?= $o_translation->getString('common', 'login') ?></button>
                    </form>
                <?php } else { ?>
                    <p class="text-light text-center mb-0"><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> <?= $o_translation->getString('common', 'loginTurnedOff') ?>.</p>
                <?php } ?>
            <?php
            } // else
            ?>
        </div>
    </nav>

    <?php
    // Display mobile notifications bar for logged users
    if ($loggedIn) {
    ?>
    <div id="header-mobile-notifications-bar" class="d-none d-lg-none"> <!-- .d-block -->
        <ul>
            <li>
                <i class="fa fa-user-circle-o" style="font-size: 22px;" aria-hidden="true"></i>
                <div class="notifications-badge-wrapper">
                    <!-- <span class="badge badge-danger">1</span> -->
                </div>
            </li>
            <li>
                <i class="fa fa-comments-o" style="margin-top: -2px; font-size: 25px;" aria-hidden="true"></i>
                <div class="notifications-badge-wrapper">
                    <!-- <span class="badge badge-danger">1</span> -->
                </div>
            </li>
            <li>
                <i class="fa fa-bell-o" style="font-size: 22px;" aria-hidden="true"></i>
                <div class="notifications-badge-wrapper">
                    <!-- <span class="badge badge-danger">1</span> -->
                </div>
            </li>
        </ul>
    </div>
    <?php
    } // if
    ?>
</header>
