<?php
// Store system messages and remove them from session.
// This will get them in the right place to make sure it will catch all messages.
$systemMessages = $o_system->getMessages();
$o_system->clearMessages();

// Display system messages if there are any existing.
if (count($systemMessages) !== 0) {
?>

<div class="container" id="system_messages">
    <?php
    // Display every system message.
    foreach ($systemMessages as $message) {
    ?>
    <div class="mt-3 alert <?php echo $message['alert-class']; ?> alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" role="button">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong><?php echo $message['alert-title']; ?></strong> <?php echo $message['message']; ?>
    </div>
    <?php
    }
    ?>
</div>

<?php
} // if
?>
