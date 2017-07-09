<header>
    <nav class="navbar navbar-toggleable-sm navbar-inverse bg-inverse">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php
        // Set homepage path for logged users.
        if ($isLogged) {
        ?>
        <a class="navbar-brand" href="index.php">BronyCenter</a>
        <?php
        } // if
        // Set homepage path for guests.
        else {
        ?>
        <a class="navbar-brand" href="../index.php">BronyCenter</a>
        <?php
        } // else
        ?>

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
            <form method="post" action="../login.php" class="form-inline">
                <input type="text" name="username" class="form-control" id="login-input-username" placeholder="Username or e-mail" required autofocus />
                <input type="password" name="password" class="form-control mx-2" id="login-input-password" placeholder="Password" required />
                <button type="submit" name="submit" class="btn btn-outline-warning" id="login-button-submit" value="login" role="button">Log In</button>
            </form>
            <?php
            } // else
            ?>
        </div>
    </nav>
</header>
