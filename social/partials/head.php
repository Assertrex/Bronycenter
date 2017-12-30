<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle ?? 'Unknown page :: BronyCenter'; ?></title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand" />
    <link type="text/css" rel="stylesheet" href="../resources/stylesheets/lib/bootstrap/4.0.0-beta.3/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../resources/stylesheets/lib/font-awesome/4.7.0/font-awesome.min.css" />
    <link type="text/css" rel="stylesheet" href="../resources/stylesheets/style.css?v=<?php echo $websiteVersion['commit']; ?>" />

    <?php if (!empty($pageStylesheet)) { ?>
    <style type="text/css">
    <?php echo $pageStylesheet; ?>
    </style>
    <?php } ?>
</head>
