<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Change selected setting when user has submitted a form.
if (!empty($_POST['submit'])) {
    switch($_POST['submit']) {
        case 'changedisplayname':
            $o_user->changeDisplayname($_SESSION['account']['id'], $_POST['displayname']);
            break;
        case 'changepassword':
            $o_user->changePassword($_SESSION['account']['id'], $_POST['passwordold'], $_POST['passwordnew'], $_POST['passwordrepeat']);
            break;
        case 'changebirthdate':
            $o_user->changeBirthdate($_SESSION['account']['id'], $_POST['day'], $_POST['month'], $_POST['year']);
            break;
        case 'changegender':
            $o_user->changeGender($_SESSION['account']['id'], $_POST['gender']);
            break;
        case 'changecity':
            $o_user->changeCity($_SESSION['account']['id'], $_POST['city']);
            break;
        case 'changeshortdescription':
            $o_user->changeProfileDescription($_SESSION['account']['id'], $_POST['shortdescription'], 'shortdescription');
            break;
        case 'changeinterestsdescription':
            $o_user->changeProfileDescription($_SESSION['account']['id'], $_POST['interestsdescription'], 'interestsdescription');
            break;
        case 'changefulldescription':
            $o_user->changeProfileDescription($_SESSION['account']['id'], $_POST['fulldescription'], 'fulldescription');
            break;
        case 'changebronyintervaldescription':
            $o_user->changeProfileDescription($_SESSION['account']['id'], $_POST['bronyintervaldescription'], 'bronyintervaldescription');
            break;
        case 'changefavponydescription':
            $o_user->changeProfileDescription($_SESSION['account']['id'], $_POST['favponydescription'], 'favponydescription');
            break;
        case 'changeavatar':
            // Require Image class for image manipulation.
            require_once('../system/class/image.php');

            // Try to change user's avatar.
            $o_user->changeAvatar($_SESSION['account']['id'], $_FILES['avatar']);
            break;
    }
}

// Get details about current user.
$user = $o_user->getDetails($_SESSION['account']['id']);

// Get current user descriptions.
$tmp_user_descriptions = $o_user->getDescriptions($_SESSION['account']['id']);

// Merge both arrays.
$user = array_merge($user, $tmp_user_descriptions);

// Unset temporiary array with user descriptions.
unset($tmp_user_descriptions);

// Store birthdate as day, month and year variables if set up.
if (!is_null($user['birthdate'])) {
    $birthdate_temp = explode('-', $user['birthdate']);
    $user['birthdate_temp']['year'] = $birthdate_temp[0];
    $user['birthdate_temp']['month'] = $birthdate_temp[1];
    $user['birthdate_temp']['day'] = $birthdate_temp[2];
} else {
    $user['birthdate_temp']['year'] = '';
    $user['birthdate_temp']['month'] = '';
    $user['birthdate_temp']['day'] = '';
}

// Get current avatar of user or show the default one.
$avatarName = $_SESSION['user']['avatar'] ?? 'default';

// Store country name.
$user['country_name'] = $o_user->getCountryName($user['country_code']) ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Settings :: BronyCenter</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha256-rr9hHBQ43H7HSOmmNkxzQGazS/Khx+L8ZRHteEY1tQ4=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <style type="text/css">
    @media (max-width: 767px) {
        #birthdateGrid { text-align: left !important; }
    }
    </style>
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');
    ?>

    <div class="container">
        <h1 class="text-center my-5">Settings</h1>

        <?php
        // Show forms for changing settings if user has verified his e-mail address.
        if ($emailVerified) {
        ?>
        <section class="my-5">
            <div id="accordion" role="tablist" aria-multiselectable="true">
                <!-- Change account settings card -->
                <div class="card">
                    <div class="card-header" role="tab" id="headingAccountSettings">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseAccountSettings" aria-expanded="false" aria-controls="collapseAccountSettings">
                                Account settings
                            </a>
                        </h5>
                    </div>

                    <div id="collapseAccountSettings" class="collapse" role="tabpanel" aria-labelledby="headingAccountSettings">
                        <div class="card-block">
                            <form method="post" action="settings.php" class="pb-2">
                                <h6 class="pb-2">Username</h6>
                                <div class="form-group">
                                    <input type="text" name="username" value="<?php echo $user['username']; ?>" placeholder="Your username" class="form-control" aria-describedby="usernameHelp" disabled />
                                    <small id="usernameHelp" class="form-text text-muted">You can't change your username. If you really need, please contact the administrator.</small>
                                </div>
                            </form>

                            <form method="post" action="settings.php" class="pb-2">
                                <h6 class="pb-2">E-mail address</h6>
                                <div class="form-group">
                                    <input type="text" name="email" value="<?php echo $user['email']; ?>" placeholder="Your email" class="form-control" aria-describedby="emailHelp" disabled />
                                    <small id="emailHelp" class="form-text text-muted">Change of e-mail address is not available yet.</small>
                                </div>
                            </form>

                            <h6 class="pb-2">Password</h6>
                            <form method="post" action="settings.php">
                                <div class="form-group row mb-0">
                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <input type="password" name="passwordold" placeholder="Old password" class="form-control" required />
                                    </div>
                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <input type="password" name="passwordnew" placeholder="New password" class="form-control" required />
                                    </div>
                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <input type="password" name="passwordrepeat" placeholder="Repeat password" class="form-control" required />
                                    </div>
                                    <div class="col-lg-3 mb-0">
                                        <button type="submit" name="submit" value="changepassword" class="btn btn-outline-primary" role="button">Change password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change profile settings card -->
                <div class="card">
                    <div class="card-header" role="tab" id="headingProfileSettings">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseProfileSettings" aria-expanded="false" aria-controls="collapseProfileSettings">
                                Profile settings
                            </a>
                        </h5>
                    </div>

                    <div id="collapseProfileSettings" class="collapse" role="tabpanel" aria-labelledby="headingProfileSettings">
                        <div class="card-block">
                            <h6 class="pb-2">Display name</h6>
                            <form method="post" action="settings.php" class="pb-2">
                                <div class="form-group row">
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <input type="text" name="displayname" value="<?php echo $user['display_name']; ?>" placeholder="Your display name" class="form-control" />
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" name="submit" value="changedisplayname" class="btn btn-outline-primary d-inline-block" role="button">Change display name</button>
                                    </div>
                                </div>
                            </form>

                            <h6 class="pb-2">Gender</h6>
                            <form method="post" action="settings.php" class="pb-2">
                                <div class="form-group row">
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <select name="gender" class="form-control d-inline-block">
                                            <option value="0" <?php if (empty($user['gender'])) { echo 'selected'; } ?>>Undefined</option>
                                            <option value="1" <?php if ($user['gender'] == 1) { echo 'selected'; } ?>>Male</option>
                                            <option value="2" <?php if ($user['gender'] == 2) { echo 'selected'; } ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" name="submit" value="changegender" class="btn btn-outline-primary" role="button">Change gender</button>
                                    </div>
                                </div>
                            </form>

                            <h6 class="pb-2">Birthdate</h6>
                            <form method="post" action="settings.php" class="pb-2">
                                <div class="form-group row mb-md-0">
                                    <div class="col-md-7 mb-sm-3 mb-sm-0 text-right" id="birthdateGrid">
                                        <input type="number" name="day" placeholder="DD" value="<?php echo $user['birthdate_temp']['day']; ?>" class="form-control d-inline-block mr-1" min="1" max="31" style="width: 72px;" />
                                        <input type="number" name="month" placeholder="MM" value="<?php echo $user['birthdate_temp']['month']; ?>" class="form-control d-inline-block mr-1" min="1" max="12" style="width: 72px;" />
                                        <input type="number" name="year" placeholder="YYYY" value="<?php echo $user['birthdate_temp']['year']; ?>" class="form-control d-inline-block mr-1 mb-3 mb-sm-0" min="1900" max="<?php echo date('Y'); ?>" style="width: 86px;" />
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" name="submit" value="changebirthdate" class="btn btn-outline-primary d-inline-block" role="button">Change birthdate</button>
                                    </div>
                                </div>
                            </form>

                            <h6 class="pb-2">Profile avatar</h6>
                            <form method="post" action="settings.php" enctype="multipart/form-data">
                                <div class="d-flex flex-column flex-md-row align-items-center">
                                    <img src="../media/avatars/<?php echo $avatarName; ?>/128.jpg" class="rounded mr-3 mb-3 mb-md-0" />
                                    <div class="d-inline-block">
                                        <input type="file" name="avatar" class="form-control-file" aria-describedby="avatarhelp" />
                                        <small id="avatarhelp" class="form-text text-muted mb-3 mb-md-2">Avatar needs to be in 1:1 resolution (recommended is 256x256). Only .jpg and .png are allowed. They will be converted into JPEG image.</small>
                                        <button type="submit" name="submit" value="changeavatar" class="btn btn-outline-primary" role="button">Change avatar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change profile details card -->
                <div class="card">
                    <div class="card-header" role="tab" id="headingProfileDetails">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseProfileDetails" aria-expanded="false" aria-controls="collapseProfileDetails">
                                Profile description
                            </a>
                        </h5>
                    </div>

                    <div id="collapseProfileDetails" class="collapse" role="tabpanel" aria-labelledby="headingProfileDetails">
                        <div class="card-block">
                            <form method="post" action="settings.php" class="pb-3">
                                <h6 class="pb-2">Write a short description about you.</h6>
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <input type="text" id="shortdescription-value" class="form-control" name="shortdescription" placeholder="e.g.: I am a cute little pony that loves to hug everypony!" value="<?php echo $user['short_description']; ?>" maxlength="255" />
                                        <small class="text-muted ml-2"><span id="shortdescription-counter"><?php echo strlen($user['short_description']); ?></span> / 255</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changeshortdescription" class="btn btn-outline-primary" role="button">Update</button>
                                    </div>
                                </div>
                            </form>

                            <form method="post" action="settings.php" class="pb-3">
                                <h6 class="pb-2">What do you like to do?</h6>
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <input type="text" id="interestsdescription-value" class="form-control" name="interestsdescription" placeholder="e.g.: I love to play video games. Sometimes I make trance pony music." value="<?php echo $user['interests_description']; ?>" maxlength="255" />
                                        <small class="text-muted ml-2"><span id="interestsdescription-counter"><?php echo strlen($user['interests_description']); ?></span> / 255</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changeinterestsdescription" class="btn btn-outline-primary" role="button">Update</button>
                                    </div>
                                </div>
                            </form>

                            <form method="post" action="settings.php" class="pb-3">
                                <h6 class="pb-2">Tell us anything else you want about yourself.</h6>
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <textarea id="fulldescription-value" class="form-control" name="fulldescription" placeholder="e.g.: My name is Pony and I'm usually a quiet and shy person looking for somepony that will talk to me." maxlength="1000"><?php echo $user['full_description']; ?></textarea>
                                        <small class="text-muted ml-2"><span id="fulldescription-counter"><?php echo strlen($user['full_description']); ?></span> / 1000</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changefulldescription" class="btn btn-outline-primary" role="button">Update</button>
                                    </div>
                                </div>
                            </form>

                            <form method="post" action="settings.php" class="pb-3">
                                <h6 class="pb-2">When you've became a brony/pegasister?</h6>
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <input type="text" id="bronyintervaldescription-value" class="form-control" name="bronyintervaldescription" placeholder="e.g.: January 2012" value="<?php echo $user['bronyinterval_description']; ?>" maxlength="64" />
                                        <small class="text-muted ml-2"><span id="bronyintervaldescription-counter"><?php echo strlen($user['bronyinterval_description']); ?></span> / 64</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changebronyintervaldescription" class="btn btn-outline-primary" role="button">Update</button>
                                    </div>
                                </div>
                            </form>

                            <form method="post" action="settings.php">
                                <h6 class="pb-2">What is your favourite pony and why?</h6>
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <input type="text" id="favponydescription-value" class="form-control" name="favponydescription" placeholder="e.g.: Fluttershy, because she is so cute!" value="<?php echo $user['favpony_description']; ?>" maxlength="64" />
                                        <small class="text-muted ml-2"><span id="favponydescription-counter"><?php echo strlen($user['favpony_description']); ?></span> / 64</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changefavponydescription" class="btn btn-outline-primary" role="button">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change localization card -->
                <div class="card">
                    <div class="card-header" role="tab" id="headingLocalization">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseLocalization" aria-expanded="false" aria-controls="collapseLocalization">
                                Localization
                            </a>
                        </h5>
                    </div>

                    <div id="collapseLocalization" class="collapse" role="tabpanel" aria-labelledby="headingLocalization">
                        <div class="card-block">
                            <form method="post" action="settings.php">
                                <div class="form-group">
                                    <input type="text" name="country" value="<?php echo $user['country_name']; ?>" placeholder="Your country" class="form-control" aria-describedby="countryHelp" disabled />
                                    <small id="countryHelp" class="form-text text-muted">Change of country is not available yet. It depends on your IP localization when registering.</small>
                                </div>
                            </form>

                            <form method="post" action="settings.php">
                                <div class="form-group row mb-0">
                                    <div class="col-md-10 text-right">
                                        <input type="text" class="form-control" id="city-value" name="city" placeholder="Your city" value="<?php echo $user['city']; ?>" maxlength="58" />
                                        <small class="text-muted ml-2"><span id="city-counter"><?php echo strlen($user['city']); ?></span> / 58</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="submit" value="changecity" class="btn btn-outline-primary" role="button">Change</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        } // if
        // Show warning about required e-mail verification if user has not verified it.
        else {
        ?>
        <section class="my-5">
            <p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to make changes to your account!</p>
        </section>
        <?php
        } // else
        ?>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>

    <script type="text/javascript">
    // First check if document is ready.
    $(document).ready(function() {
        // Count amount of characters used in an description input.
        $("#shortdescription-value").on("input", function() {
            let amount = $("#shortdescription-value").val().length;
            $("#shortdescription-counter").text(amount);
        });

        // Count amount of characters used in an description input.
        $("#interestsdescription-value").on("input", function() {
            let amount = $("#interestsdescription-value").val().length;
            $("#interestsdescription-counter").text(amount);
        });

        // Count amount of characters used in an description input.
        $("#fulldescription-value").on("input", function() {
            let amount = $("#fulldescription-value").val().length;
            $("#fulldescription-counter").text(amount);
        });

        // Count amount of characters used in an description input.
        $("#bronyintervaldescription-value").on("input", function() {
            let amount = $("#bronyintervaldescription-value").val().length;
            $("#bronyintervaldescription-counter").text(amount);
        });

        // Count amount of characters used in an description input.
        $("#favponydescription-value").on("input", function() {
            let amount = $("#favponydescription-value").val().length;
            $("#favponydescription-counter").text(amount);
        });

        // Count amount of characters used in a city name input.
        $("#city-value").on("input", function() {
            let amount = $("#city-value").val().length;
            $("#city-counter").text(amount);
        });
    });
    </script>
</body>
</html>
