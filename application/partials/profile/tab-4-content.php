<?php if ($profileDetails['filled_creations']) { ?>
<div id="aside-creations" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'creations') ?></h6>

    <div class="p-3">
        <div class="aside-content-blocks mb-3 text-info text-center">
            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
            <?= $o_translation->getString('profile', 'creationsInFutureUpdate') ?>.
        </div>

        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_3_custom_field_1') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['creations_links']); ?></div>
        </div>
    </div>
</div>
<?php } ?>
