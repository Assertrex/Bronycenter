<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../system/inc/init.php');

// Check if user is authorized to enter.
if ($_SESSION['account']['type'] !== 9 && $_SESSION['account']['type'] !== 8) {
    // Redirect user into index.php if user is not logged in.
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="noindex" />

    <title>Manage :: BronyCenter</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="../resources/css/style.css?v=<?php echo $systemVersion['commit']; ?>" />

    <style type="text/css">
    aside { min-height: calc(100vh - 56px); }
    aside nav h5 { color: #FFF; border-bottom: 1px solid rgba(255, 255, 255, .25); }
    aside nav li { border-bottom: 1px solid rgba(255, 255, 255, .25); }
    aside nav a { display: block; padding: .625rem 1.5rem; color: rgba(255, 255, 255, .5); }
    aside nav a.active { color: rgba(255, 255, 255, .75); }
    aside nav a:hover { color: rgba(255, 255, 255, .75); text-decoration: none; }
    aside nav a span { display: inline-block; width: 24px; margin-right: .25em; text-align: center; }
    </style>
</head>
<body>
    <?php
    // Require HTML of header for not social pages.
    require_once('inc/header.php');

    // Require code to display system messages.
    require_once('../system/inc/messages.php');

    // Set current page to index if haven't been selected.
    $_GET['p'] = $_GET['p'] ?? 'index';
    ?>

    <div class="container-fluid d-flex flex-column px-0" id="main-container">
        <div class="d-flex">
            <aside class="bg-dark" style="flex: 0 0 240px;">
                <nav>
                    <h5 class="text-center mb-0 pt-3 pb-3">Navigation</h5>
                    <ul class="pl-0">
                        <li>
                            <a <?php if ($_GET['p'] === 'index') { echo 'class="active"'; } ?> href="dashboard.php">
                                <span><i class="fa fa-tachometer" aria-hidden="true"></i></span> Dashboard
                            </a>
                        </li>
                        <li>
                            <a <?php if ($_GET['p'] === 'members') { echo 'class="active"'; } ?> href="dashboard.php?p=members">
                                <span><i class="fa fa-user-o" aria-hidden="true"></i></span> Members
                            </a>
                        </li>
                        <li>
                            <a <?php if ($_GET['p'] === 'removedposts') { echo 'class="active"'; } ?> href="dashboard.php?p=removedposts">
                                <span><i class="fa fa-file-text-o" aria-hidden="true"></i></span> Removed posts
                            </a>
                        </li>
                        <li>
                            <a <?php if ($_GET['p'] === 'bannedmembers') { echo 'class="active"'; } ?> href="dashboard.php?p=bannedmembers">
                                <span><i class="fa fa-user-times" aria-hidden="true"></i></span> Banned members
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <section class="container-fluid" style="flex: 100%;">
                <?php
                switch ($_GET['p']) {
                    case 'members':
                        include_once('inc/manage/members.php');
                        break;
                    case 'removedposts':
                        include_once('inc/manage/removedposts.php');
                        break;
                    case 'bannedmembers':
                        include_once('inc/manage/bannedmembers.php');
                        break;
                    default:
                        include_once('inc/manage/index.php');
                }
                ?>
            </section>
        </div>
    </div>

    <?php
    // Require footer for social pages.
    require_once('inc/footer.php');
    ?>

    <script type="text/javascript">
    // First check if document is ready.
    $(document).ready(function() {

    });
    </script>
</body>
</html>
