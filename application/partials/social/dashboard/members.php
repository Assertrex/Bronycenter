<?php
$membersByAccountTypeCounter = $user->countMembersByAccountType();
$membersByAccountStandingCounter = $user->countMembersByAccountStanding();
?>

<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'statistics') ?></h6>

    <div class="row">
        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-list-ol text-primary mr-2" aria-hidden="true"></i> Accounts counters</p>

            <p class="mb-0">Created accounts: <b><?= number_format($user->countCreatedAccounts(), 0, '.', ' ') ?></b></p>
            <p>Active accounts: <b><?= number_format($user->countExistingMembers(), 0, '.', ' ') ?></b></p>

            <p class="mb-0">Online accounts: <b><?= number_format($user->countRecentlyOnlineMembers('40 SECOND'), 0, '.', ' ') ?></b></p>
            <p class="mb-0">Online accounts (30 days): <b><?= number_format($user->countRecentlyOnlineMembers('30 DAY'), 0, '.', ' ') ?></b></p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-bar-chart text-primary mr-2" aria-hidden="true"></i> Accounts by type</p>

            <p class="mb-0"><i class="fa fa-user-circle text-secondary mr-1" aria-hidden="true"></i> Unverified: <b><?= number_format($membersByAccountTypeCounter[0]['amount'], 0, '.', ' ') ?></b></p>
            <p class="mb-0"><i class="fa fa-user-circle text-primary mr-1" aria-hidden="true"></i> Standard: <b><?= number_format($membersByAccountTypeCounter[1]['amount'], 0, '.', ' ') ?></b></p>
            <p class="mb-0"><i class="fa fa-user-circle text-purple mr-1" aria-hidden="true"></i> Moderators: <b><?= number_format($membersByAccountTypeCounter[2]['amount'], 0, '.', ' ') ?></b></p>
            <p class="mb-0"><i class="fa fa-user-circle text-danger mr-1" aria-hidden="true"></i> Administrators: <b><?= number_format($membersByAccountTypeCounter[3]['amount'], 0, '.', ' ') ?></b></p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-exclamation-circle text-primary mr-2" aria-hidden="true"></i> Account by standing</p>

            <p class="mb-0">Safe accounts: <b><?= number_format($membersByAccountStandingCounter[0]['amount'], 0, '.', ' ') ?></b></p>
            <p class="mb-0">Muted accounts: <span class="text-info">Coming soon!</span></p>
            <p class="mb-0">Banned accounts: <span class="text-info">Coming soon!</span></p>
            <p class="mb-0">Hidden accounts: <span class="text-info">Coming soon!</span></p>
            <p class="mb-0">Deleted accounts: <span class="text-info">Coming soon!</span></p>
        </div>
    </div>
</section>

<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'manage') ?></h6>

    <div class="col-lg m-3" style="font-size: 14px;">
        <p class="text-info text-center mb-0">
            <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
            <?= $o_translation->getString('common', 'featureNotAvailable') ?>.
        </p>
    </div>
</section>
