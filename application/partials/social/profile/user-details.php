<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'aboutMe') ?></h6>

    <div class="px-4 my-3">
        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'location') ?>">
                <i class="fa fa-map-marker text-primary" aria-hidden="true"></i>
            </span>
            <?= $profileDetails['city'] ? $utilities->doEscapeString($profileDetails['city'], false) . ', ' : ''; ?><?= $profileDetails['country_name'] ?? '<span class="text-danger">Unknown country</span>'; ?>
        </p>

        <?php if (!empty($profileDetails['gender'])) { ?>
        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'gender') ?>">
                <i class="fa fa-transgender text-primary" aria-hidden="true"></i>
            </span>
            <?= $profileDetails['gender_name']; ?>
        </p>
        <?php } ?>

        <?php if (!empty($profileDetails['birthdate'])) { ?>
        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'age') ?>">
                <i class="fa fa-user-o text-primary" aria-hidden="true"></i>
            </span>
            <?= $profileDetails['birthdate_years']; ?>
        </p>
        <?php } ?>

        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'accountCreated') ?>">
                <i class="fa fa-address-book-o text-primary" aria-hidden="true"></i>
            </span>
            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $profileDetails['registration_datetime']; ?> (UTC)"><?= $profileDetails['registration_interval']; ?></span>
        </p>

        <?php if (!empty($profileDetails['last_online'])) { ?>
        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('profile', 'lastSeen') ?>">
                <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
            </span>
            <span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $profileDetails['last_online']; ?> (UTC)">
                <?= $profileDetails['is_online'] ? $o_translation->getString('dates', 'justNow') : $profileDetails['last_online_interval']; ?>
            </span>
        </p>
        <?php } ?>

        <p class="mb-0">
            <span class="d-inline-block text-center mr-2" style="width: 16px; cursor: help;" data-toggle="tooltip" data-placement="top" title="<?= $o_translation->getString('common', 'userPoints') ?>">
                <i class="fa fa-star-half-o text-primary" aria-hidden="true"></i>
            </span>
            <?= $profileDetails['user_points'] . ' ' . $o_translation->getString('common', 'points') ?>
        </p>
    </div>
</section>
