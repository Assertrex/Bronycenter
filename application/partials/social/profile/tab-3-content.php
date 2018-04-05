<?php if ($profileDetails['filled_fandom']) { ?>
<div id="aside-fandom" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('profile', 'fandom') ?></h6>

    <div class="p-3">
        <?php if (!empty($profileDetails['fandom_becameabrony'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_2_custom_field_1') ?></p>
            <div><?= $utilities->replaceURLsWithLinks($utilities->doEscapeString($profileDetails['fandom_becameabrony'])) ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['fandom_favouritepony'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_2_custom_field_2') ?></p>
            <div><?= $utilities->replaceURLsWithLinks($utilities->doEscapeString($profileDetails['fandom_favouritepony'])) ?></div>
        </div>
        <?php } ?>

        <?php if (!empty($profileDetails['fandom_favouriteepisode'])) { ?>
        <div class="aside-content-blocks mb-3">
            <p class="aside-content-titles mb-2"><?= $o_translation->getString('profile', 'tab_2_custom_field_3') ?></p>
            <div><?= $utilities->replaceURLsWithLinks($utilities->doEscapeString($profileDetails['fandom_favouriteepisode'])) ?></div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
