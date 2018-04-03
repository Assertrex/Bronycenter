<section class="fancybox text-center p-4 mt-0">
    <img src="../media/avatars/<?= $profileDetails['avatar']; ?>/defres.jpg" class="rounded mb-3" />

    <?php if (!empty($profileDetails['recent_displaynames_divs'])) { ?>
    <h5 class="mb-0" style="cursor: help;" data-toggle="tooltip" data-html="true" title="<div class='my-1'>Previous display name:</div><div class='mb-2' style='color: #BDBDBD;'><?= $utilities->doEscapeString($profileDetails['recent_displaynames_divs'], false); ?></div>"><?= $utilities->doEscapeString($profileDetails['display_name'], false); ?></h5>
    <?php } else { ?>
    <h5 class="mb-0"><?= $utilities->doEscapeString($profileDetails['display_name'], false); ?></h5>
    <?php } ?>

    <p class="mb-0 text-muted" style="margin-top: -2px;">
        <small>@<?= $profileDetails['username']; ?></small>
    </p>

    <p class="mb-0 mt-2">
        <?= $profileDetails['account_type_badge'] ?? ''; ?>
        <?= $profileDetails['account_standing_badge'] ?? ''; ?>
        <?= $profileDetails['is_online_badge']; ?>
    </p>

    <?php if (!empty($profileDetails['short_description'])) { ?>
    <p class="mt-3 mb-0" style="font-size: 90%; line-height: 1.4;">
        <?= $utilities->doEscapeString($profileDetails['short_description'], false); ?>
    </p>
    <?php } ?>
</section>
