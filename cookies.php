<?php
// Require system initialization code.
require_once('system/inc/init.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Cookie Use :: BronyCenter</title>

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
            <h1>Cookie Use</h1>
            <p>Last update: <b>17.07.2017</b></p>

            <p>
                The website uses cookies. By using the website and agreeing to
                this policy, you consent to our use of cookies in accordance with
                the terms of this policy.
            </p>

            <h4 class="my-4">About cookies</h4>
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

            <h4 class="my-4">Cookies on the website</h4>
            <p>We use both session cookies and persistent cookies on the website.</p>

            <h4 class="my-4">How we use cookies</h4>
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

            <h4 class="my-4">Third party cookies</h4>
            <p>
                Website is not creating any third party cookies, as it's not using
                any advertisment/analytics services to protect your privacy.
            </p>

            <h4 class="my-4">Blocking cookies</h4>
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

            <h4 class="my-4">Deleting cookies</h4>
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
    // Require HTML of footer for not social pages.
    require_once('system/inc/footer.php');
    ?>
</body>
</html>
