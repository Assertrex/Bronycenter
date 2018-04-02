<?php
$pageSettings = [
    'title' => 'Our server',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');
?>

<body id="page-server">
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="details" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">Our server and software</h1>

            <p class="mb-0">Last update: <b>30.12.2017</b></p>
        </section>

        <section id="website">
            <h5 class="mb-4">Details about website:</h5>

            <ul>
                <li>Written from scratch with clean PHP.</li>
                <li>No third-party frameworks or libraries for PHP.
                    <ul>
                        <li>At least for now. I'm too stupid to learn <a href="https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller" target="_blank">MVC</a>.</li>
                    </ul>
                </li>
                <li>It's using Bootstrap 4 framework (responsive CSS) and JQuery 3 library (JavaScript).</li>
                <li>You can check out source-code on <a href="https://github.com/Assertrex/BronyCenter" target="_blank">GitHub</a>.</li>
            </ul>
        </section>

        <section id="server">
            <h5 class="my-4">Details about server:</h5>

            <h6>#1: VPS SSD 1 2016v1 (Main)</h6>
            <p>
                Our main and the only server for now. When we'll get more
                members, this server will be upgraded to have better parameters.
            </p>

            <ul>
                <li><b>Hosted by:</b> <a href="https://www.ovh.com/" target="_blank">OVH</a></li>
                <li><b>Localization:</b> Canada, Beauharnois</li>
                <li><b>Processor:</b> 1 core @2.4GHz (Intel Xeon E5v3)</li>
                <li><b>RAM:</b> 2GB</li>
                <li><b>Storage:</b> 10GB SSD (Local Raid 10)</li>
            </ul>
            <ul>
                <li><b>Web server:</b> Nginx 1.12.1</li>
                <li><b>Server-side language:</b> PHP 7.1.12</li>
                <li><b>Database:</b> MariaDB 10.2.11</li>
            </ul>
        </server>
    </div>

    <?php require('../application/partials/footer.php'); ?>
    <?php require('../application/partials/scripts.php'); ?>
</body>
</html>
