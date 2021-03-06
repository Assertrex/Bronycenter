<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'details') ?></h6>

    <div class="row">
        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-code text-primary mr-2" aria-hidden="true"></i> Website software</p>

            <p class="mb-0">Name: <b><?= $o_config->getWebsiteTitle(true) ?></b></p>
            <p class="mb-0">Version: <b><?= $o_config->getWebsiteVersion(true) ?></b> (<b><?= $o_config->getWebsiteDate(true) ?></b>)</p>
            <p>Build: <i class="fa fa-github" aria-hidden="true"></i> <b><?= $o_config->getWebsiteCommit(true) ?></b></p>

            <p class="text-info mb-0"><i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i> These values are used for main software versioning. You'll be able to edit version of your modifications soon.</p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-server text-primary mr-2" aria-hidden="true"></i> Server software</p>

            <?php $PHPVersion = explode('-', PHP_VERSION)[0]; ?>
            <p class="mb-0">Webserver: <b><?= ucfirst(explode('/', $_SERVER['SERVER_SOFTWARE'])[0]) ?></b></p>
            <p>Server-side language: <b>PHP <?= $PHPVersion ?></b></p>

            <p class="mb-0">HTTPS enabled: <b><?= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>' ?></b></p>
            <p class="mb-0">PHPMailer found: <b><?= file_exists('../../application/lib/PHPMailer/PHPMailer.php') ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>' ?></b></p>
            <p class="mb-0">Imagick installed: <b><?= extension_loaded('imagick') ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>' ?></b></p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-sliders text-primary mr-2" aria-hidden="true"></i> Used settings</p>


            <p class="mb-0">PHP errors: <?= $websiteSettings['enableErrors'] ? '<span class="text-danger"><b>Enabled</b></span>' : '<span class="text-success"><b>Disabled</b></span>' ?></p>
            <p class="mb-0">Performance debug: <?= $websiteSettings['enableDebug'] ? '<span class="text-secondary"><b>Enabled</b></span>' : '<span class="text-success"><b>Disabled</b></span>' ?></p>
            <p class="mb-0">Login form: <?= $websiteSettings['enableLogin'] ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>' ?></p>
            <p class="mb-0">Registration form: <?= $websiteSettings['enableRegistration'] ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>' ?></p>
        </div>
    </div>
</section>

<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'statistics') ?></h6>

    <div class="row">
        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-users text-primary mr-2" aria-hidden="true"></i> Members</p>

            <p class="mb-0">Online members: <b><?= number_format($user->countRecentlyOnlineMembers('40 SECOND'), 0, '.', ' ') ?></b></p>
            <p class="mb-0">- last 1 hour: <b><?= number_format($user->countRecentlyOnlineMembers('1 HOUR'), 0, '.', ' ') ?></b></p>
            <p>- last 24 hours: <b><?= number_format($user->countRecentlyOnlineMembers('1 DAY'), 0, '.', ' ') ?></b></p>

            <p class="mb-0">Active accounts: <b><?= number_format($user->countExistingMembers(), 0, '.', ' ') ?></b></p>
            <p class="mb-0">All created accounts: <b><?= number_format($user->countCreatedAccounts(), 0, '.', ' ') ?></b></p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-file-text-o text-primary mr-2" aria-hidden="true"></i> Posts</p>

            <p class="mb-0">Existing posts: <b><?= number_format($o_post->countAvailablePosts(), 0, '.', ' ') ?></b></p>
            <p>Posts to review: <b><?= number_format($o_post->countReportedPosts(), 0, '.', ' ') ?></b></p>

            <p class="mb-0">All created posts: <b><?= number_format($o_post->countCreatedPosts(), 0, '.', ' ') ?></b></p>
            <p class="mb-0">All reported posts: <b><?= number_format($o_post->countAllReportedPosts(), 0, '.', ' ') ?></b></p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-star text-primary mr-2" aria-hidden="true"></i> Activity</p>

            <ul style="padding: 0; margin: 0; list-style: none;">
                <?php
                $mostActiveMembersArray = $statistics->getMostActiveMembersArray(6);

                foreach ($mostActiveMembersArray as $activeMemberIndex => $activeMember) {
                ?>

                <li>
                    <span class="d-inline-block" style="width: 12px"><?= ++$activeMemberIndex ?>:</span>
                    <?= $utilities->doEscapeString($activeMember['display_name'], false) ?>
                    | <i class="fa fa-star-o text-success" aria-hidden="true"></i> <?= $activeMember['user_points'] ?>
                </li>

                <?php
                }

                unset($mostActiveMembersArray);
                ?>
            </ul>
        </div>
    </div>
</section>

<section class="fancybox">
    <h6 class="text-center mb-0"><?= $o_translation->getString('dashboard', 'performance') ?></h6>

    <div class="row">
        <?php
        // Calculate disk usage info
        $diskTotalSpace = disk_total_space('/');
        $diskTotalSpaceInGB = $diskTotalSpace/1024/1024/1024;
        $diskFreeSpace = disk_free_space('/');
        $diskFreeSpaceInGB = $diskFreeSpace/1024/1024/1024;
        $diskUsedSpace = $diskTotalSpace - $diskFreeSpace;
        $diskUsedSpaceInGB = $diskUsedSpace/1024/1024/1024;
        $diskFreeSpaceInPercentage = $diskFreeSpace / $diskTotalSpace * 100;
        $diskUsedSpaceInPercentage = $diskUsedSpace / $diskTotalSpace * 100;
        ?>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-bar-chart text-primary mr-2" aria-hidden="true"></i>CPU usage</p>

            <p class="text-info text-center mb-0">
                <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                <?= $o_translation->getString('common', 'featureNotAvailable') ?>.
            </p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-bar-chart text-primary mr-2" aria-hidden="true"></i>RAM usage</p>

            <p class="text-info text-center mb-0">
                <i class="fa fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                <?= $o_translation->getString('common', 'featureNotAvailable') ?>.
            </p>
        </div>

        <div class="col-lg m-3" style="font-size: 14px;">
            <p class="text-center font-weight-bold"><i class="fa fa-hdd-o text-primary mr-2" aria-hidden="true"></i> Disk usage</p>

            <p class="mb-0">
                Used space:
                <b><?= $diskUsedSpaceInGB ? '<span>' . number_format($diskUsedSpaceInGB, 2) . ' GB</span>' : '<span class="text-muted">Unknown</span>' ?></b>
                /
                <b><?= $diskTotalSpaceInGB ? '<span>' . number_format($diskTotalSpaceInGB, 2) . ' GB</span>' : '<span class="text-muted">Unknown</span>' ?></b>
                (<b><?= number_format($diskUsedSpaceInPercentage, 1) ?>%</b>)
            </p>

            <p class="mb-0">Free space: <b><?= number_format($diskFreeSpaceInGB, 2) ?> GB</b> (<b><?= number_format($diskFreeSpaceInPercentage, 1) ?>%</b>)</p>
        </div>
    </div>
</section>
