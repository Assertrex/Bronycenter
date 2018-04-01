<div id="content-standing" style="display: none;">
    <h6 class="text-center mb-0">Account standing</h6>

    <div class="p-3">
        <p><small>Check if you've broke any rules.</small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Account standing</p>
            <div>
                <p class="mb-1">Account type: <?= $userDetails['account_type_badge']; ?></p>
                <p>Account standing: <?= $userDetails['account_standing_badge']; ?></p>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Received warnings</p>
            <div>
                <p class="mb-0 text-info text-center">
                    <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                    <?= $o_translation->getString('common', 'featureNotAvailable') ?>
                </p>
            </div>
        </div>
    </div>
</div>
