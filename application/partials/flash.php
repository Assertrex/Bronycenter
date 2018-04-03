<section class="mt-0" id="flash-messages">

    <?php
    // NOTE Exists also in /system/ajax/getFlashMessages.php
    // Display flash messages if any exists
    if (!empty($flashMessages)) {
        foreach ($flashMessages as $message) {
    ?>

    <div class="alert <?php echo $message[1]; ?> alert-dismissible fade show" role="alert">
        <button type="button" class="alert-close close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        <strong class="alert-type"><?php echo ucfirst($message[0]); ?>!</strong>
        <span class="alert-message"><?php echo $message[2]; ?></span>
    </div>

    <?php
        } // foreach
    } // if
    ?>

</section>

<!-- Display modal here, because why not if it's included everywhere -->
<div class="modal fade" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><!-- Empty title --></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Empty content because you've not used it yet! -->
            </div>
            <div class="modal-footer">
                <!-- Empty footer -->
            </div>
        </div>
    </div>
</div>
