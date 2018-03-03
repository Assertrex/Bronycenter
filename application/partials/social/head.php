<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle ?? 'Unknown page :: BronyCenter'; ?></title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand" />
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha256-LA89z+k9fjgMKQ/kq4OO2Mrf8VltYml/VES+Rg0fh20=" crossorigin="anonymous" />
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link type="text/css" rel="stylesheet" href="../resources/stylesheets/style.css?v=<?php echo $websiteVersion['commit']; ?>" />

    <?php if (!empty($pageStylesheet)) { ?>
    <style type="text/css">
    <?php echo $pageStylesheet; ?>
    </style>
    <?php } ?>
</head>