<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">BronyCenter</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav my-1 my-lg-0 mr-auto">
                <?php
                // Display links for logged users
                if ($loggedIn) {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fa fa-home d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fa fa-info-circle d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php"><i class="fa fa-envelope-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="social/members.php"><i class="fa fa-address-book-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Assertrex/BronyCenter" target="_blank"><i class="fa fa-github d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Github</a>
                </li>
                <?php
                } // if

                // Display links for guests
                else {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fa fa-home d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Homepage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fa fa-info-circle d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php"><i class="fa fa-envelope-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="social/members.php"><i class="fa fa-address-book-o d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Members</a>
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
            <div class="d-none d-lg-block">
                <a class="btn mt-2 mt-lg-0 mr-lg-2" role="button" href="social/">Social</a>
                <a class="btn mt-2 mt-lg-0" role="button" href="logout.php">Logout</a>
            </div>

            <ul class="d-flex d-lg-none navbar-nav mt-2">
                <li class="nav-item">
                    <a class="nav-link" href="social/"><i class="fa fa-users d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Social</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fa fa-sign-out d-inline-block d-lg-none mr-2" aria-hidden="true" style="width: 18px;"></i> Logout</a>
                </li>
            </ul>
            <?php
            } // if

            // Display login form for guests
            else {
            ?>
                <?php if ($websiteSettings['enableLogin']) { ?>
                    <form method="post" action="login.php" class="form-inline justify-content-end justify-content-sm-center py-lg-0">
                        <input type="text" name="username" class="form-control mb-2 mb-sm-0" id="login-username" placeholder="Username or e-mail" autocomplete="username" autofocus required />
                        <input type="password" name="password" class="form-control mb-2 mb-sm-0 ml-sm-2 mr-lg-2" id="login-password" placeholder="Password" autocomplete="current-password" required />
                        <button type="submit" name="submit" class="btn mt-sm-2 mt-lg-0" id="login-submit" value="login">Login</button>
                    </form>
                <?php } else { ?>
                    <p class="text-light text-center mb-0"><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> Login has been temporary turned off.</p>
                <?php } ?>
            <?php
            } // else
            ?>
        </div>
    </nav>
</header>
