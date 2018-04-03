<?php if ($profileDetails['filled_about']) { ?>
<div id="aside-about" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'about') ?></h6>

    <div class="p-3">
        <?php if (!empty($profileDetails['full_description'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_1') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['full_description']); ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['contact_methods'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_2') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['contact_methods']); ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['favourite_music'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_3') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['favourite_music']); ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['favourite_movies'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_4') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['favourite_movies']); ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['favourite_games'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_1_custom_field_5') ?></p>
            <div><?= $utilities->doEscapeString($profileDetails['favourite_games']); ?></div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
