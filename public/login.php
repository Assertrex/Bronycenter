<?php
$pageSettings = [
    'title' => 'Login',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');

if (isset($_POST['submit'])) {
    $account = BronyCenter\Account::getInstance();

    if ($account->login()) {
        $flash->info(
            'Please note, that ' . $o_config->getWebsiteTitle() . ' is currently in early development stage.<br />' .
            'Many features will be added/changed in the future. Website design will change as well.'
        );

        if (!empty($_SESSION['data']['pathLoginAccessed'])) {
            $pathLoginAccessed = $_SESSION['data']['pathLoginAccessed'];
            $_SESSION['data']['pathLoginAccessed'] = null;
            $utilities->redirect($pathLoginAccessed);
        }

        $utilities->redirect('social/');
    }

    $flashMessages = $flash->merge($flashMessages);
}

if (!empty($_GET['func']) && $_GET['func'] == 'ajaxlogout') {
    $flash->error($o_translation->getString('errors', 'loggedOut'));
    $flashMessages = $flash->merge($flashMessages);
}
?>

<body id="page-login">
    <?php require_once('../application/partials/header.php'); ?>

    <div class="container">
        <?php require_once('../application/partials/flash.php'); ?>

        <section id="s-title" class="mb-4">
            <h1 class="text-center text-lg-left mb-4"><?= $pageSettings['title'] ?></h1>
        </section>

        <section id="s-login">
            <?php if (!empty($_SESSION['data']['pathLoginAccessed'])) { ?>
            <p class="mb-4">
                <i class="fa fa-sign-in text-primary mr-2" aria-hidden="true"></i>
                <?= $o_translation->getString('common', 'youAreTryingToAccessPage') ?>: "<span class="text-secondary"><?= $_SESSION['data']['pathLoginAccessed'] ?></span>"
            </p>
            <?php } ?>

            <?php if ($websiteSettings['enableLogin']) { ?>
                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="login-input-username"><?= $o_translation->getString('common', 'usernameOrEmail') ?></label>
                        <input type="text" name="username" class="form-control" id="login-input-username" placeholder="<?= $o_translation->getString('common', 'usernameOrEmail') ?>" pattern=".{3,24}" title="Field have to be between 3 and 24 characters." autocomplete="username" required autofocus />
                    </div>

                    <div class="form-group">
                        <label for="login-input-password"><?= $o_translation->getString('common', 'password') ?></label>
                        <input type="password" name="password" class="form-control" id="login-input-password" placeholder="<?= $o_translation->getString('common', 'password') ?>" autocomplete="current-password" required />
                    </div>

                    <div class="form-group pt-1 text-center">
                        <button type="submit" name="submit" value="login" class="btn btn-primary mb-2" id="login-button-submit"><?= $o_translation->getString('common', 'login') ?></button>
                    </div>
                </form>
            <?php } else { ?>
                <p class="text-danger text-center mb-0"><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> <?= $o_translation->getString('common', 'loginTurnedOff') ?>.</p>
            <?php } ?>
        </section>
    </div>

    <?php require('../application/partials/footer.php'); ?>
    <?php require('../application/partials/scripts.php'); ?>

    <script>
    "use-strict";

    $(document).ready(function() {
        <?php if (!empty($_GET['func']) && $_GET['func'] == 'ajaxlogout') { ?>
        window.history.pushState({}, document.title, 'login.php');
        <?php } ?>
    });
    </script>
</body>
</html>
