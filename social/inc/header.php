<header>
    <nav class="navbar navbar-toggleable-sm navbar-inverse bg-inverse">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php if ($isLogged) { ?>
        <a class="navbar-brand" href="index.php">BronyCenter</a>
        <?php } else { ?>
        <a class="navbar-brand" href="../index.php">BronyCenter</a>
        <?php } ?>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php if ($isLogged) { ?>
                <li class="nav-item"><a href="index.php" class="nav-link">Feed</a></li>
                <li class="nav-item"><a href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>" class="nav-link">Profile</a></li>
                <li class="nav-item"><a href="members.php" class="nav-link">Members</a></li>
                <li class="nav-item"><a href="messages.php" class="nav-link">Messages</a></li>
                <li class="nav-item"><a href="settings.php" class="nav-link">Settings</a></li>
                <!-- <li class="nav-item"><a href="suggestions.php" class="nav-link">Suggestions</a></li> -->
                <li class="nav-item"><a href="../logout.php" class="nav-link">Logout</a></li>
                <?php } else { ?>
                <li class="nav-item"><a href="../index.php" class="nav-link">Homepage</a></li>
                <li class="nav-item"><a href="../about.php" class="nav-link">About</a></li>
                <li class="nav-item"><a href="../contact.php" class="nav-link">Contact</a></li>
                <li class="nav-item"><a href="members.php" class="nav-link">Members</a></li>
                <?php } ?>
            </ul>

            <?php if (!$isLogged) { ?>
            <form method="post" action="../login.php" class="form-inline">
                <input type="text" name="username" class="form-control" id="login-input-username" placeholder="Username or e-mail" required autofocus />
                <input type="password" name="password" class="form-control mx-2" id="login-input-password" placeholder="Password" required />
                <button type="submit" name="submit" class="btn btn-outline-warning" id="login-button-submit" value="login" role="button">Log In</button>
            </form>
            <?php } ?>
        </div>
    </nav>
</header>

<div class="container my-5">
    <?php
    // Store system messages and remove them from session
    $systemMessages = $o_system->getMessages();
    $o_system->clearMessages();

    foreach ($systemMessages as $message) {
    ?>
    <div class="alert <?php echo $message['alert-class']; ?> alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" role="button">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong><?php echo $message['alert-title']; ?></strong> <?php echo $message['message']; ?>
    </div>
    <?php
    }
    ?>
</div>
