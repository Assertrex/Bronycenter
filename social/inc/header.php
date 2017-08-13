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

                <li class="nav-item"><a href="index.php" class="nav-link">Feed</a></li>
                <li class="nav-item"><a href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>" class="nav-link">Profile</a></li>
                <li class="nav-item"><a href="members.php" class="nav-link">Members</a></li>
                <li class="nav-item"><a href="messages.php" class="nav-link">Messages</a></li>
                <li class="nav-item"><a href="settings.php" class="nav-link">Settings</a></li>
                <!-- <li class="nav-item"><a href="suggestions.php" class="nav-link">Suggestions</a></li> -->

                <?php
                // Show link to administration panel for moderators and administrators.
                if ($_SESSION['account']['type'] === 9 || $_SESSION['account']['type'] === 8) {
                ?>

                <li class="nav-item"><a href="dashboard.php" class="nav-link">Manage</a></li>

                <?php
                } // if
                ?>

                <li class="nav-item"><a href="../logout.php" class="nav-link">Logout</a></li>

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

            <?php
            } // if
            // Show login form for guests.
            else {
            ?>
            <form method="post" action="../login.php" class="form-inline justify-content-end justify-content-sm-start py-2 py-lg-0">
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
