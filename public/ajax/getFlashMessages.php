<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Display flash messages
if (!empty($flashMessages)) {
    foreach ($flashMessages as $flashMessage) {
    ?>

    <div class="alert <?php echo $flashMessage[1]; ?> alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        <strong><?php echo ucfirst($flashMessage[0]); ?>!</strong>
        <span class="alert-message"><?php echo $flashMessage[2]; ?></span>
    </div>

    <?php
    } // foreach
} // if
