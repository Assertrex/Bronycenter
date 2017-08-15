<?php
// Check if current user is a moderator.
$isModerator = false;
if (!empty($_SESSION['account']['type']) && ($_SESSION['account']['type'] === 8 || $_SESSION['account']['type'] === 9)) {
    $isModerator = true;
}

// Get current avatar of user or show the default one.
$avatarName = $_SESSION['user']['avatar'] ?? 'default';
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="<?php echo $isLogged ? 'index.php' : '../index.php'; ?>">BronyCenter</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                // Show navbar links for logged users.
                if ($isLogged) {
                ?>

                <li class="nav-item"><a class="nav-link d-block d-lg-none" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="members.php">Members</a></li>
                <li class="nav-item"><a class="nav-link" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link d-block d-lg-none" href="settings.php">Settings</a></li>
                <?php if ($isModerator) { ?>
                <li class="nav-item"><a class="nav-link d-block d-lg-none" href="dashboard.php">Manage</a></li>
                <?php } ?>
                <li class="nav-item"><a class="nav-link d-block d-lg-none" href="../logout.php">Logout</a></li>

                <?php
                } // if

                // Show navbar links for guests.
                else {
                ?>

                <li class="nav-item"><a href="../index.php" class="nav-link">Homepage</a></li>
                <li class="nav-item"><a href="../about.php" class="nav-link">About</a></li>
                <li class="nav-item"><a href="../contact.php" class="nav-link">Contact</a></li>
                <li class="nav-item"><a href="members.php" class="nav-link">Members</a></li>
                <li class="nav-item"><a href="https://github.com/Assertrex/BronyCenter" target="_blank" class="nav-link">Github</a></li>

                <?php
                } // else
                ?>
            </ul>

            <?php
            // Show (nothing yet) for logged users.
            if ($isLogged) {
            ?>

            <!-- TODO Show notifications here for logged users. -->
            <ul class="navbar-nav align-items-center d-none d-lg-flex">
                <li class="nav-item">
                    <a class="nav-link py-1 px-3 disabled" style="cursor: not-allowed;">
                        <i class="fa fa-user-circle-o" style="font-size: 22px;" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="nav-item" style="margin-bottom: 1px;">
                    <a class="nav-link py-1 px-3 disabled" style="cursor: not-allowed;">
                        <i class="fa fa-envelope-o" style="font-size: 25px;" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1 px-3 disabled" style="cursor: not-allowed;">
                        <i class="fa fa-globe" style="font-size: 25px;" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="nav-item ml-2">
                    <div class="dropdown" id="profile-actions-dropdown">
                        <a href="#" class="nav-link d-flex align-items-center py-1 dropdown-toggle" role="button" id="profile-actions-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="pr-2">
                                <img class="rounded" src="../media/avatars/<?php echo $avatarName; ?>/64.jpg" alt="Your avatar" />
                            </div>
                            <div class="pr-2" id="profile-actions-dropdown-button-username">
                                <?php echo $_SESSION['user']['displayName']; ?><br />
                                <small>@<?php echo $_SESSION['user']['username']; ?></small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profile-actions-dropdown-button">
                            <h6 class="dropdown-header">Account</h6>
                            <a class="dropdown-item" href="profile.php"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
                            <a class="dropdown-item" href="settings.php"><i class="fa fa-sliders" aria-hidden="true"></i> Settings</a>
                            <a class="dropdown-item" href="../logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                            <?php if ($isModerator) { ?>
                            <h6 class="dropdown-header">Website</h6>
                            <a class="dropdown-item" href="dashboard.php"><i class="fa fa-cogs" aria-hidden="true"></i> Manage</a>
                            <?php } ?>
                        </div>
                    </div>
                </li>
            </ul>

            <?php
            } // if
            // Show login form for guests.
            else {
            ?>
            <form method="post" action="../login.php" class="form-inline justify-content-end justify-content-sm-center py-lg-0">
                <input type="text" name="username" class="form-control mb-2 mb-sm-0" id="login-input-username" placeholder="Username or e-mail" required autofocus />
                <input type="password" name="password" class="form-control mb-2 mb-sm-0 mx-sm-2" id="login-input-password" placeholder="Password" required />
                <button type="submit" name="submit" class="btn" id="login-button-submit" value="login" role="button">Login</button>
            </form>
            <?php
            } // else
            ?>
        </div>
    </nav>
</header>
