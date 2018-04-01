<section class="fancybox mt-0" id="post-creator">
    <div id="post-creator-types">
        <ul class="d-flex" style="line-height: 20.2px;">
            <li class="active">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-pencil-square-o" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span><?= $o_translation->getString('postcreator', 'post') ?></span>
            </li>
            <li class="d-none disabled" style="cursor: not-allowed;">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-picture-o" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span><?= $o_translation->getString('postcreator', 'photo') ?></span>
            </li>
            <li class="d-none disabled" style="cursor: not-allowed;">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-bar-chart" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span><?= $o_translation->getString('postcreator', 'poll') ?></span>
            </li>
        </ul>
    </div>

    <?php
    if (!$readonlyState) {
    ?>
    <div id="post-creator-input">
        <textarea id="post-creator-textarea" placeholder="<?= $o_translation->getString('postcreator', 'textareaPlaceholder') ?>" maxlength="1000" style="overflow: hidden;"></textarea>

        <div>
            <p id="post-creator-notification-waittime" class="text-center text-danger mb-0" style="display: none;">
                <small><i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i> <?= $o_translation->getString('postcreator', 'timeRestriction') ?></small>
            </p>
        </div>
    </div>

    <div id="post-creator-submitbar" class="d-flex align-items-center justify-content-end">
        <small class="text-muted mr-3">
            <span id="post-creator-lettercounter">0</span> / 1000
        </small>
        <button id="post-creator-submit" class="btn btn-sm btn-outline-primary" disabled style="cursor: not-allowed;"><?= $o_translation->getString('postcreator', 'publish') ?></button>
    </div>
    <?php
    } else {
    ?>
    <div class="py-3">
        <p class="text-center text-danger mb-0">
            <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
            <?php
            switch ($_SESSION['account']['reason_readonly']) {
                case 'unverified':
                    echo $o_translation->getString('postcreator', 'accountUnverified');
                    break;
                case 'muted':
                    echo $o_translation->getString('postcreator', 'accountMuted');
                    break;
                default:
                    echo $o_translation->getString('postcreator', 'accountReadonly');
            }
            ?>
        </p>
    </div>
    <?php
    }
    ?>
</section>
