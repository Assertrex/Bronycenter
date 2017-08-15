<?php
// Require system initialization code.
require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Our server :: BronyCenter</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="resources/css/style.css?v=<?php echo $systemVersion['commit']; ?>" /></head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('system/inc/header.php');

    // Require code to display system messages.
    require_once('system/inc/messages.php');
    ?>

    <div class="container" id="main_content">
        <section>
            <h1>Our server and software</h1>
            <p>Last update: <b>13.08.2017</b></p>

            <h4 class="my-4">Details about website:</h4>
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

            <h4 class="my-4">Details about server:</h4>
            <h5>#1 VPS SSD 1 2016v1 (Main)</h5>
            <h6 class="mb-3">
                Our main and only server for now. When we'll get more
                members, this server will be replaced with a VPS SSD 3
                (2-core 2.4GHz, RAM 8GB, SSD 40GB).
            </h6>
            <ul>
                <li><b>Hosted by:</b> <a href="https://www.ovh.com/" target="_blank">OVH</a></li>
                <li><b>Localization:</b> Canada, Beauharnois</li>
                <li><b>Processor:</b> 1 core @2.4GHz (Intel Xeon E5v3)</li>
                <li><b>RAM:</b> 2GB</li>
                <li><b>Storage:</b> 10GB SSD (Local Raid 10)</li>
            </ul>
            <ul>
                <li><b>WebServer:</b> Apache 2.4.27</li>
                <li><b>Server-side language:</b> PHP 7.1.8</li>
                <li><b>Database:</b> MariaDB 10.2.7</li>
            </ul>
        </section>
    </div>

    <?php
    // Require HTML of footer for not social pages.
    require_once('system/inc/footer.php');
    ?>
</body>
</html>
