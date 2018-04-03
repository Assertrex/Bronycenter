<?php if ($loggedIn && !$readonlyState) { ?>
<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('headings', 'quickActions') ?></h6>

    <div class="px-4 my-3">
        <?php if ($profileDetails['id'] == $_SESSION['account']['id']) { ?>
        <button type="button" role="button" class="btn btn-outline-primary btn-sm btn-block" style="cursor: not-allowed" disabled>
            <?= $o_translation->getString('profile', 'sendMessage') ?>
        </button>
        <?php } else { ?>
        <button type="button" role="button" data-toggle="modal" data-target="#mainModal" id="btn-profile-sendmessage" class="btn btn-outline-primary btn-sm btn-block" data-userid="<?= $profileDetails['id']; ?>"  data-userdisplayname="<?= $utilities->doEscapeString($profileDetails['display_name']); ?>">
            <?= $o_translation->getString('profile', 'sendMessage') ?>
        </button>
        <?php } ?>
    </div>
</section>
<?php } ?>
