<div id="content-standing" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'accountStanding') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'accountStandingDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'accountStanding') ?></p>
            <div>
                <p class="mb-1"><?= $o_translation->getString('settings', 'accountType') ?>: <?= $userDetails['account_type_badge']; ?></p>
                <p><?= $o_translation->getString('settings', 'accountStanding') ?>: <?= $userDetails['account_standing_badge']; ?></p>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'receivedWarnings') ?></p>
            <div>
                <p class="mb-0 text-info text-center">
                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                    <?= $o_translation->getString('common', 'featureNotAvailable') ?>
                </p>
            </div>
        </div>
    </div>
</div>
