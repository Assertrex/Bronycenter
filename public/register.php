<?php
$pageSettings = [
    'title' => 'Registration',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');

// Check if register form has been submitted
if (!empty($_POST['submit']) && $_POST['submit'] === 'register') {
    // Require GeoIP class to get details about new user's IP address
    require_once('../application/core/geoip.php');

    // Require Mail class to send a verification email to a new user
    require_once('../application/core/mail.php');

    // Create an instance of an account class for managing accounts
    $account = BronyCenter\Account::getInstance();

    // Try to register a new user
    if ($account->doRegister()) {
        // Redirect new user into login page
        $utilities->redirect('login.php');
    } else {
        // Get new system flash messages
        $flashMessages = $flash->merge($flashMessages);
    }
}
?>

<body id="page-register">
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="s-title" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">Registration</h1>
        </section>

        <section id="s-registration">
            <?php
            // Include partial containing a post creator
            require('../application/partials/index/form-registration.php');
            ?>
        </section>
    </div>

    <?php require('../application/partials/footer.php'); ?>
    <?php require('../application/partials/scripts.php'); ?>
</body>
</html>
