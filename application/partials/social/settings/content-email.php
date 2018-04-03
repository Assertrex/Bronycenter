<div id="content-email" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'emailSettings') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'emailSettingsDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'emailAddress') ?></p>
            <p class="mb-2 text-info text-center">
                <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                <?= $o_translation->getString('common', 'featureNotAvailable') ?>
            </p>
            <div class="form-inline align-items-top mb-2">
                <input class="form-control" id="content-input-email" type="email" placeholder="E-mail address" value="<?= $userDetails['email'] ?? ''; ?>" style="flex: 1;" disabled />
            </div>
            <div class="d-flex justify-content-end">
                <small class="letters-counter text-muted">
                    <span id="content-counter-email">0</span> / 64
                </small>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'typesOfReceivedEmails') ?></p>
            <div>
                <p class="mb-0 text-info text-center">
                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                    <?= $o_translation->getString('common', 'featureNotAvailable') ?>
                </p>
            </div>
        </div>
    </div>
</div>
