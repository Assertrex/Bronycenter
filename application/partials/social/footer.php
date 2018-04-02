<footer class="text-center py-2" style="line-height: 1.35;">
    <p class="mb-0"><?= $o_config->getWebsiteTitle() ?> &copy; <?= ($o_config->getWebsiteYear() == date('Y')) ? $o_config->getWebsiteYear() : $o_config->getWebsiteYear() . ' - ' . date('Y') ?></p>
    <p class="mb-0"><small><?= $o_translation->getString('common', 'pageLoadedIn') ?>: <?= $utilities->getLoadtimeInMs() ?> ms</small></p>
</footer>
