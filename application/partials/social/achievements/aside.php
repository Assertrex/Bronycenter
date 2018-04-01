<section class="fancybox mt-lg-0">
    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'recentAchievements') ?></h6>

    <div class="p-3">
        <div class="text-info text-center">
            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
            <?= $o_translation->getString('common', 'featureNotAvailable') ?>
        </div>
    </div>
</section>

<?php
// Include social scripts for all pages
require(__DIR__ . '/../aside/website-details.php');
?>
