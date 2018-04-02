<section class="fancybox mt-lg-0 mb-0">
    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'websiteDetails') ?></h6>

    <div class="p-3 text-center" style="font-size: 14px;">
        <ul class="mb-2 pl-0 list-unstyled">
            <li><?= $o_translation->getString('common', 'version') ?>: <?= $o_config->getWebsiteVersion() ?> (<?= $o_config->getWebsiteDate() ?>)</li>

            <?php if (!$o_config->isUsingCustomVersion()) { ?>
            <li><a href="#">Changelog</a> &bull; <a href="#">Suggest a Change</a></li>
            <li><a href="https://github.com/Assertrex/BronyCenter" target="_blank">Github</a> &bull; <a href="https://trello.com/b/cjkeP4yt/bronycenter" target="_blank">Trello</a></li>
            <?php } ?>
        </ul>

        <ul class="mb-0 pl-0 list-unstyled">
            <li><?= $o_config->getWebsiteTitle() ?> &copy; <?= ($o_config->getWebsiteYear() == date('Y')) ? $o_config->getWebsiteYear() : $o_config->getWebsiteYear() . ' - ' . date('Y') ?></li>

            <?php if (!$o_config->isUsingCustomVersion()) { ?>
            <li><a href="../terms.php" target="_blank">Terms of Service</a> &bull; <a href="../policy.php" target="_blank">Privacy Policy</a></li>
            <?php } ?>
        </ul>

        <?php if ($websiteSettings['enableDebug']) { ?>
        <p class="mt-2 mb-0"><?= $o_translation->getString('common', 'pageLoadedIn') ?>: <?= $utilities->getLoadtimeInMs() ?> ms</p>
        <?php } ?>
    </div>
</section>
