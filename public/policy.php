<?php
$pageSettings = [
    'title' => 'Privacy Policy',
    'robots' => true,
    'loginRequired' => false,
    'moderatorRequired' => false,
];

require('../application/partials/init.php');
require('../application/partials/head.php');
?>

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

        <section id="policy" class="mb-4">
            <h1 class="text-center text-lg-left mb-4">Privacy Policy</h1>

            <p class="mb-4">
                Last update: <b>30.12.2017</b> (outdated)
            </p>

            <p>
                We care about your privacy and anonymity. You can learn here
                about what informations we store about you and what you can do
                with it. We're planning to work further on a security in the future.
                It's a lot of learning and work and it's not really needed yet
                with an amount of users that we have.
            </p>
            <p class="mb-0">
                Remember, that <b>we're not responsible</b> for any sensitive details,
                that you will share with us or send in private messages, in case of us
                being hacked. We're in an early development stage and even
                the biggest ones (Dropbox, Adobe, MySpace) have been hacked in
                the past. We promise to do everything that we can do to prevent it,
                but in case of being hacked, we'll notify you about that as soon
                as we'll find that out. Check out below to get more details about
                what informations, how and why we store about you.
            </p>
        </section>

        <section id="collect" class="mb-4">
            <h5 class="mb-4">Informations that we collect about you.</h5>

            <p>
                We don't use any kind of software that will spy on you
                (e.g. Google Analytics, Google AdWords, Facebook Like buttons).
                First informations that we receive are logged by the Apache
                webserver software. By signing up, you allow us to store more
                details about you, including informations that you've provided
                in input forms. Your connection session is stored in a browser's
                cookie. It's used only for recognizing that your account belongs
                to you. On each page refresh or every 60 seconds while staying
                idle your session is extended and datetime about when you were
                last seen is updated.
            </p>
            <p>
                Google can show your profile in it's search engine. It should
                be hidden by default, but you can enable it in your account's
                settings. We can't remove it from Google once your profile
                is added by you, or by our mistake. It usually changes after
                few days.
            <ul>
                <li class="mb-3">
                    <b>Automatically by a webserver:</b>
                    <ul>
                        <li>IP address</li>
                        <li>Browser's user agent</li>
                        <li>Name of a requested page</li>
                        <li>Date and time of a request</li>
                    </ul>
                </li>
                <li class="mb-3">
                    <b>Details about you after registering:</b>
                    <ul>
                        <li>Display name</li>
                        <li>Username</li>
                        <li>E-mail address</li>
                        <li>Password</li>
                        <li>IP address</li>
                        <li>Date and time of a request</li>
                        <li>Your country based on your IP address</li>
                        <li>Your timezone based on your IP address</li>
                    </ul>
                </li>
                <li class="mb-3">
                    <b>Details about you after successful login:</b>
                    <ul>
                        <li>ID of your account</li>
                        <li>IP address</li>
                        <li>Date and time of a request</li>
                        <li>Browser's user agent</li>
                    </ul>
                </li>
                <li>
                    <b>Date and time when you were last seen logged is updated automatically.</b>
                </li>
                <li class="mb-3">
                    <b>Your private messages are stored in a database with a weak encryption, but not as a plain text.</b>
                </li>
                <li class="mb-3">
                    <b>Optional details about you that you can share:</b>
                    <ul>
                        <li>Your avatar</li>
                        <li>Your birthdate</li>
                        <li>Your gender</li>
                        <li>Your city</li>
                        <li>Your description</li>
                    </ul>
                </li>
            </ul>
        </section>

        <section id="public" class="mb-4">
            <h5 class="mb-4">Which details about you can be seen.</h5>

            <p>
                <b>Everyone (even not logged) can see your:</b> display name, username,
                registraton datetime, last online datetime, country, timezone, age,
                avatar, account type, gender, city, description, your posts, likes and comments.
            </p>
            <p>
                <b>Members can't see anything more yet.</b>
            </p>
            <p>
                <b>Moderators can't see anything more yet.</b>
            </p>
            <p>
                <b>Administrator can see everything.</b>
            </p>
        </section>

        <section id="store" class="mb-4">
            <h5 class="mb-4">Details about informations that we store.</h5>

            <p>
                <b>IP Address</b> is a numerical label that is assigned to all devices
                in your network. It allows us to check if your account has been
                hacked. From an IP address we can also get a name of a country
                that you're actually at and your timezone. Everyone will be able
                to see where are you from on your profile page. I don't think
                that this will reduce your anonymity much.
            </p>
            <p>
                <b>Browser's user agent</b> is a line of text used for telling
                a website informations about your browser and operating system.
                We'll be able to know what device (if mobile), browser and
                operating system do you use. We use it mostly for statistics to
                know which software is most popular. It's also useful to check
                when someones account will get hacked. That's why it's stored on
                each successful login.
            </p>
            <p>
                <b>Display name</b> is public everywhere where your profile appears.
                It can be easily changed to different free name but everyone
                will see that you've changed it on posts page and your profile.
            </p>
            <p>
                <b>Username</b> is mostly used for login. It can appear in few
                places like on your profile, or on members page. In the future
                it will be also used to make better profile links. It can't be
                changed, but if you really need to, and you have good argument
                for that, then contact with the website's administrator.
            </p>
            <p>
                <b>E-mail address</b> is needed to prevent banned users from
                making multiaccounts. It needs to be verified, by clicking a link
                in a mail after registration, to make your account appear for
                others. It's also used for login, password recovery and to send
                you e-mails about your account's security. We won't ever spam
                you with useless mails and it won't be shared with anyone else.
            </p>
            <p>
                <b>Password</b> is needed to secure your account. No one else
                should know it and you shouldn't tell it anyone. Also, you
                shouldn't use same password on multiple websites. If a website
                will get hacked and your password will not be secured enough,
                hackers can get it and try to log in with your login/e-mail and
                password on other websites.<br />
                Your password should be secure in our database, as we're using
                encrypted connection (<span class="text-success">https://</span>)
                and we're storing it after hashing with a BCrypt algorithm ($13),
                so it's impossible (or nearly imposible) to decrypt and read your
                password.
            </p>
            <p>
                <b>Private messages</b> are stored in the database in a plain
                text, so you shouldn't send any sensitive details by them.
                If we'll get hacked, hackers can get all of them. It shouldn't
                happen, but as I care about your security, I have to tell you,
                that's possible.
            </p>
        </section>

        <section id="rights" class="mb-4">
            <h5 class="mb-4">Individuals' rights</h5>

            <p>
                We don't know if we need to write this privacy policy, as we're
                not an organisation or a company, but it's better to give you
                as much as we can. Below we've decribed your rights based on a
                <a href="https://ico.org.uk/for-organisations/data-protection-reform/overview-of-the-gdpr/individuals-rights/" target="_blank">GDPR</a>.
                It's a new EU regulation about data protection that will come live
                at May 2018.
            </p>

            <ul>
                <li class="mb-3">
                    <b>The right to be informed.</b>
                    <ul class="mt-2">
                        <li>
                            On this page you can get details about what
                            informations do we store about you. If you have
                            any questions, contact with an administrator by a
                            private message after logging in here, or with one
                            of contact options listed on contact page.
                        </li>
                    </ul>
                </li>
                <li class="mb-3">
                    <b>The right of access.</b>
                    <ul class="mt-2">
                        <li>
                            We're not prepared yet to give you automatically
                            all the informations that we've stored about you.
                            If you really want to see them, contact with an
                            administrator by a private message after logging in
                            here, or with one of contact options listed on
                            contact page.
                        </li>
                    </ul>
                </li>
                <li class="mb-3">
                    <b>The right to rectification.</b>
                    <ul class="mt-2">
                        <li>
                            If you need to update your details, check out a
                            settings page. If something that you need to change
                            is not available here, contact with an
                            administrator by a private message after logging in
                            here, or with one of contact options listed on
                            contact page.
                        </li>
                    </ul>
                </li>
                <li class="mb-3">
                    <b>The right to erasure.</b>
                    <ul class="mt-2">
                        <li>
                            If you want to be completly removed from our database,
                            contact with an administrator by a private message
                            after logging in here, or with one of contact options
                            listed on contact page. Note, that we don't have the
                            ability to modify or remove what web search/archive
                            engines has stored about you.
                        </li>
                    </ul>
                </li>
                <li>
                    <b>The right to object.</b>
                    <ul class="mt-2">
                        <li>
                            We don't share your details with anyone, so we're
                            clear here.
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
    </div>

    <?php
    // Include scripts for all pages
    require('../application/partials/scripts.php');
    ?>
</body>
</html>
