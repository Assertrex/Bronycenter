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
            <ul class="navbar-nav" style="flex: 1;">
                <?php
                // Display links for logged users
                if ($loggedIn) {
                ?>
                <div class="d-none d-lg-flex justify-content-center" style="flex: 1;">
                    <input class="mx-4 px-3" id="navbar-searchbar" type="text" placeholder="Search for a user..." />
                    <div class="d-none pb-2" id="navbar-searchbar-result"></div>
                </div>

                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>">Profile</a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="members.php">Members</a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="achievements.php">Statistics</a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="settings.php">Settings</a>
                </li>
                <?php
                // Display moderate button only for moderators
                if ($loggedModerator) {
                ?>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="manage.php">Moderate</a>
                </li>
                <?php
                } // if
                ?>
                <li class="nav-item d-lg-none">
                    <a class="d-block nav-link" href="../logout.php">Logout</a>
                </li>
                <?php
                } // if

                // Display links for guests
                else {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="members.php">Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Assertrex/BronyCenter" target="_blank">Github</a>
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
                        <li>
                            <i class="fa fa-user-circle-o" style="font-size: 22px;" aria-hidden="true"></i>
                            <div class="notifications-badge-wrapper">
                                <!-- <span class="badge badge-danger" style="right: -4px;">1</span> -->
                            </div>
                        </li>
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
                                <img class="rounded" id="header-user-avatar" src="../media/avatars/<?php echo $_SESSION['user']['avatar']; ?>/minres.jpg" alt="Your avatar" />
                            </div>
                            <div class="pr-2" id="profile-actions-dropdown-button-username">
                                <?php echo htmlspecialchars($_SESSION['user']['displayname']); ?><br />
                                <small>@<?php echo $_SESSION['account']['username']; ?></small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profile-actions-dropdown-button">
                            <h6 class="dropdown-header">Account</h6>
                            <a class="dropdown-item" href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
                            <a class="dropdown-item" href="achievements.php"><i class="fa fa-bar-chart" aria-hidden="true"></i> Statistics</a>
                            <a class="dropdown-item" href="settings.php"><i class="fa fa-sliders" aria-hidden="true"></i> Settings</a>
                            <a class="dropdown-item" href="../logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>

                            <?php
                            // Display moderate button only for moderators
                            if ($loggedModerator) {
                            ?>
                            <h6 class="dropdown-header">Moderate</h6>
                            <a class="dropdown-item" href="dashboard.php"><i class="fa fa-cogs" aria-hidden="true"></i> Manage</a>
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
            <form method="post" action="../login.php" class="form-inline justify-content-end justify-content-sm-center py-lg-0">
                <input type="text" name="login-username" class="form-control mb-2 mb-sm-0" id="login-username" placeholder="Username or e-mail" required autofocus />
                <input type="password" name="login-password" class="form-control mb-2 mb-sm-0 ml-sm-2 mr-lg-2" id="login-password" placeholder="Password" required />
                <button type="submit" name="login-submit" class="btn mt-sm-2 mt-lg-0" id="login-submit" value="login" role="button">Login</button>
            </form>
            <?php
            } // else
            ?>
        </div>
    </nav>

    <?php
    // Display mobile notifications bar for logged users
    if ($loggedIn) {
    ?>
    <div id="header-mobile-notifications-bar" class="d-block d-lg-none">
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
