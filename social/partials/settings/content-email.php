<div id="content-email" style="display: none;">
    <h6 class="text-center mb-0">E-mail settings</h6>

    <div class="p-3">
        <p><small>Change your email address and what types of emails you want to receive.</small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2 text-danger">E-mail address (not implemented)</p>
            <p class="mb-2 text-info text-center">
                <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                Feature is not available yet.
            </p>
            <div class="form-inline align-items-top mb-2">
                <input class="form-control" id="content-input-email" type="email" placeholder="E-mail address" value="<?php echo $userDetails['email'] ?? ''; ?>" style="flex: 1;" disabled />
            </div>
            <div class="d-flex justify-content-end">
                <small class="letters-counter text-muted">
                    <span id="content-counter-email">0</span> / 64
                </small>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Types of received e-mails</p>
            <div>
                <p class="mb-0 text-info text-center">
                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                    Feature is not available yet.
                </p>
            </div>
        </div>
    </div>
</div>
