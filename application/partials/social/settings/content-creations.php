<div id="content-creations" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'shareYourCreativity') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'shareYourCreativityDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'shareWhatYouMade') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changecreationslinks">
                <textarea class="form-control mb-2" id="content-input-creationslinks" rows="5" maxlength="1000"><?= $userDetails['creations_links'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-creationslinks">0</span> / 1000
                    </small>
                </div>
                <button class="btn btn-outline-primary btn-block"><?= $o_translation->getString('common', 'change') ?></button>
            </form>
            <?php
            } else {
            ?>
            <p class="text-center text-danger mb-0">
                <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                <?= $translationAccountReadonly ?>
            </p>
            <?php
            }
            ?>
        </div>
    </div>
</div>
