<div id="content-login" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'loginHistory') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'loginHistoryDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'currentConnection') ?></p>

            <p class="mb-2"><?= $o_translation->getString('settings', 'yourIP') ?>: <b><?= $utilities->getVisitorIP(); ?></b></p>
            <p style="line-height: 1.2;"><?= $o_translation->getString('settings', 'userAgent') ?>: <b><?= $utilities->getVisitorAgent(); ?></b></p>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'recentLogins') ?></p>

            <ul class="list-group">
                <?php
                if (!empty($loginHistory)) {
                    foreach ($loginHistory as $loginItem) {
                        $currentIP = $utilities->getVisitorIP() == $loginItem['ip'];
                        $loginItem['datetimeString'] = $utilities->getDateIntervalString($utilities->countDateInterval($loginItem['datetime']));

                        echo '<li class="list-group-item">';
                            echo '<p class="mb-0"><span style="cursor: help;" data-toggle="tooltip" data-placement="top" title="' . $loginItem['datetime'] . ' (UTC)">' . $loginItem['datetimeString'] . '</span> &bull; ' .
                                 $loginItem['ip'] . ($currentIP ? ' <small class="text-success">(' . $o_translation->getString('settings', 'currentIP') . ')</small>' : '') . '</p>';
                            echo '<p class="mb-0 text-muted" style="line-height: 1.1;"><small>' . $loginItem['agent'] . '</small></p>';
                        echo '</li>';
                    } // foreach
                } // if
                ?>
        </div>
    </div>
</div>
