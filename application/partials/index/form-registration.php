<?php if ($websiteSettings['enableRegistration']) { ?>
    <form method="post" action="register.php">
        <div class="form-group">
            <label for="register-input-displayname">Display name</label>
            <input type="text" name="display_name" class="form-control" id="register-input-displayname" aria-describedby="displayname-help" placeholder="Example Pony (^_^)" pattern=".{3,32}" title="Field have to be between 3 and 32 characters." autocomplete="off" required />
            <small id="displayname-help" class="form-text">
                Choose how do you want to be seen by everyone. It can be changed later in your account's settings.<br />
                Display name needs to contain from 3 to 32 characters. All characters are allowed.</b>
            </small>
        </div>

        <div class="form-group">
            <label for="register-input-username">Username</label>
            <input type="text" name="username" class="form-control" id="register-input-username" aria-describedby="username-help" placeholder="examplepony2017" pattern=".{3,24}" title="Field have to be between 3 and 24 characters." autocomplete="off" required />
            <small id="username-help" class="form-text">
                Choose your name for login and for the link to your profile. Everyone will be able to see it. This can't be changed later, so choose it carefully.<br />
                Username needs to contain from 3 to 24 characters. Only lowercase alphanumeric characters are allowed (<b>a-z0-9</b>).
            </small>
        </div>

        <div class="form-group">
            <label for="register-input-email">E-mail address</label>
            <input type="email" name="email" class="form-control" id="register-input-email" aria-describedby="email-help" placeholder="epony@poniland.com" pattern=".{5,64}" title="Field have to be between 5 and 64 characters." autocomplete="off" required />
            <small id="email-help" class="form-text">
                We won't share it with anyone. It is needed for many reasons like password recovery or to prevent trolls from making multiaccounts.<br />
                It needs to be a correct e-mail address, because you'll need to click on a link that we'll send to you after registration.
            </small>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-6">
                    <label for="register-input-password">Password</label>
                    <input type="password" name="password" class="form-control" id="register-input-password" aria-describedby="password-help" placeholder="$ecretPass987" pattern=".{6,}" title="Field have to be contain at least 6 characters." autocomplete="off" required />
                </div>
                <div class="col-6">
                    <label for="register-input-passwordrepeat">Repeat password</label>
                    <input type="password" name="passwordrepeat" class="form-control" id="register-input-passwordrepeat" placeholder="$ecretPass987" pattern=".{6,}" title="Field have to be contain at least 6 characters." autocomplete="off" required />
                </div>
            </div>
            <small id="password-help" class="form-text">
                Secure your account with your secret password. Make sure that no one else knows it.<br />
                For extra security it shouldn't be the password that you use on many websites.<br />
                Don't worry, no one should be able to read it as it will be hashed with BCrypt algorithm before storing it in a database.
            </small>
        </div>

        <div class="form-group pt-1 text-center">
            <button type="submit" name="submit" value="register" class="btn btn-primary mb-2" id="register-button-submit">Create account</button>
            <small id="register-button-submit" class="form-text text-muted">
                By signing up, you agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="policy.php" target="_blank">Privacy Policy</a>, including <a href="cookies.php" target="_blank">Cookie Use</a>.
            </small>
        </div>
    </form>
<?php } else { ?>
    <p class="text-danger text-center mb-0"><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> Registration has been temporary turned off.</p>
<?php } ?>
