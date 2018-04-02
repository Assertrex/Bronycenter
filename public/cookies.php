<?php
// Include system initialization code
require('../application/partials/init.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Cookie Use :: <?= $o_config->getWebsiteTitle() ?></title>

    <?php
    // Include stylesheets for all pages
    require('../application/partials/stylesheets.php');
    ?>
</head>
<body>
    <?php
    // Include header for all pages
    require('../application/partials/header.php');
    ?>

    <div class="container">
        <?php
        // Include system messages if any exists
        require('../application/partials/flash.php');
        ?>

        <section id="cookies" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">Cookie Use</h1>

            <p class="mb-4">
                Last update: <b>30.12.2017</b>
            </p>

            <p class="mb-0">
                The website uses cookies. By using the website and agreeing to
                this policy, you consent to our use of cookies in accordance with
                the terms of this policy.
            </p>
        </section>

        <section id="about" class="mb-4">
            <h5 class="mb-4">About cookies</h5>

            <p>
                Cookies are files, often including unique identifiers, that are sent
                by web servers to web browsers, and which may then be sent back to the
                server each time the browser requests a page from the server.
            </p>
            <p>
                Cookies can be used by web servers to identity and track users as they
                navigate different pages on a website, and to identify users returning
                to a website.
            </p>
            <p>
                Cookies may be either "persistent" cookies or "session" cookies. A
                persistent cookie consists of a text file sent by a web server to a
                web browser, which will be stored by the browser and will remain valid
                until its set expiry date (unless deleted by the user before the expiry
                date). A session cookie, on the other hand, will expire at the end of
                the user session, when the web browser is closed.
            </p>
        </section>

        <section id="which" class="mb-4">
            <h5 class="mb-4">Cookies on the website</h5>

            <p>We use both session cookies and persistent cookies on the website.</p>
        </section>

        <section id="usage" class="mb-4">
            <h5 class="mb-4">How we use cookies</h5>

            <p>
                Cookies do not contain any information that personally identifies you,
                but personal information that we store about you may be linked, by us,
                to the information stored in and obtained from cookies. The cookies used
                on the website include those which are strictly necessary cookies for
                access and navigation and cookies that remember your choices
                (functionality cookies).
            </p>
            <p>
                We may use the information we obtain from your use of our cookies
                for the following purposes:
            </p>
            <ul>
                <li>to recognise you when you visit the website</li>
                <li>to personalise the website for you (depending on your settings)</li>
            </ul>
        </section>

        <section id="thirdparty" class="mb-4">
            <h5 class="mb-4">Third party cookies</h5>
            <p>
                Website is not creating any third party cookies, as it's not using
                any advertisment/analytics services to protect your privacy.
            </p>
        </section>

        <section id="blocking" class="mb-4">
            <h5 class="mb-4">Blocking cookies</h5>

            <p>Most browsers allow you to refuse to accept cookies. For example:</p>
            <ul>
                <li>
                    in Internet Explorer you can refuse all cookies by clicking
                    "Tools", "Internet Options", "Privacy", and selecting
                    "Block all cookies" using the sliding selector;
                </li>
                <li>
                    in Firefox you can block all cookies by clicking "Tools",
                    "Options", and un-checking "Accept cookies from sites" in
                    the "Privacy" box.
                </li>
                <li>
                    in Google Chrome you can adjust your cookie permissions by
                    clicking "Options", "Under the hood", Content Settings in the
                    "Privacy" section. Click on the Cookies tab in the Content
                    Settings.
                </li>
                <li>
                    in Safari you can block cookies by clicking “Preferences”,
                    selecting the “Privacy” tab and “Block cookies”.
                </li>
            </ul>
            <p>
                Blocking all cookies will, however, have a negative impact upon
                the usability of many websites. If you block cookies, you may not
                be able to use certain features on the website (log on, access
                content, use search functions).
            </p>
        </section>

        <section id="deleting" class="mb-4">
            <h5 class="mb-4">Deleting cookies</h5>

            <p>You can also delete cookies already stored on your computer:</p>
            <ul>
                <li>
                    in Internet Explorer, you must manually delete cookie files;
                </li>
                <li>
                    in Firefox, you can delete cookies by, first ensuring that
                    cookies are to be deleted when you "clear private data" (this
                    setting can be changed by clicking "Tools", "Options" and
                    "Settings" in the "Private Data" box) and then clicking
                    "Clear private data" in the "Tools" menu.
                </li>
                <li>
                    in Google Chrome you can adjust your cookie permissions by
                    clicking "Options", "Under the hood", Content Settings in the
                    "Privacy" section. Click on the Cookies tab in the Content
                    Settings.
                </li>
                <li>
                    in Safari you can delete cookies by clicking “Preferences”,
                    selecting the “Privacy” tab and “Remove All Website Data”.
                </li>
            </ul>
            <p>
                Obviously, doing this may have a negative impact on the usability
                of many websites.
            </p>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
