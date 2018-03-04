<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">BronyCenter</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                // Display links for logged users
                if ($loggedIn) {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="social/members.php">Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Assertrex/BronyCenter" target="_blank">Github</a>
                </li>
                <?php
                } // if

                // Display links for guests
                else {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="social/members.php">Members</a>
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
            <div>
                <a class="btn mt-2 mt-lg-0 mr-lg-2" role="button" href="social/">Social</a>
                <a class="btn mt-2 mt-lg-0" role="button" href="logout.php">Logout</a>
            </div>
            <?php
            } // if

            // Display login form for guests
            else {
            ?>
            <form method="post" action="login.php" class="form-inline justify-content-end justify-content-sm-center py-lg-0">
                <input type="text" name="login-username" class="form-control mb-2 mb-sm-0" id="login-username" placeholder="Username or e-mail" autocomplete="username" autofocus required />
                <input type="password" name="login-password" class="form-control mb-2 mb-sm-0 ml-sm-2 mr-lg-2" id="login-password" placeholder="Password" autocomplete="current-password" required />
                <button type="submit" name="login-submit" class="btn mt-sm-2 mt-lg-0" id="login-submit" value="login">Login</button>
            </form>
            <?php
            } // else
            ?>
        </div>
    </nav>
</header>
