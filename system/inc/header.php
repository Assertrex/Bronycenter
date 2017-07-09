<header>
    <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="index.php">BronyCenter</a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Homepage</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">About</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                    <a href="social/members.php" class="nav-link">Members</a>
                </li>
                <li class="nav-item">
                    <a href="https://github.com/Assertrex/BronyCenter" target="_blank" class="nav-link">Github</a>
                </li>
            </ul>

            <?php
            // Show buttons for logged users.
            if ($isLogged) {
            ?>
            <a href="social/"><button class="btn btn-outline-warning" role="button">Social</button></a>
            <a href="logout.php" class="ml-3"><button class="btn btn-outline-warning" role="button">Logout</button></a>
            <?php
            } // if
            // Show login form for guests.
            else {
            ?>
            <form method="post" action="login.php" class="form-inline">
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
