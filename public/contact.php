<?php
$pageSettings = [
    'title' => 'Contact',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');
?>

<body id="page-contact">
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="contact">
            <h1 class="text-center text-lg-left mb-4">Contact</h1>

            <p>
                As we don't have a contact form yet, you can contact with an
                administrator with methods shown below. Feel free to message me.
            </p>
        </section>

        <section id="methods">
            <ul>
                <li>Messenger: <b><a href="http://m.me/norbert.gotowczyc" target="_blank">Norbert Gotowczyc</a></b></li>
                <li>Snapchat: <b><a href="https://www.snapchat.com/add/assertrex" target="_blank">Assertrex</a></b></li>
                <li>Skype: <b><a href="skype:norbertgti?add">NorbertGTI</a></b></li>
                <li>Discord: <b>NorbertGTI#4048</b></li>
                <li>E-mail: <b><a href="mailto:norbert.gotowczyc@gmail.com">norbert.gotowczyc@gmail.com</a></b></li>
            </ul>
        </section>
    </div>

    <?php require('../application/partials/footer.php'); ?>
    <?php require('../application/partials/scripts.php'); ?>
</body>
</html>
