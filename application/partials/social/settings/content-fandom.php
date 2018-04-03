<div id="content-fandom" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'youInFandom') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'youInFandomDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'becameBrony') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefandombecameabrony" method="post">
                <input class="form-control mb-2" id="content-input-fandombecameabrony" type="text" value="<?= $userDetails['fandom_becameabrony'] ?? ''; ?>" maxlength="300" />
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-fandombecameabrony">0</span> / 300
                    </small>
                </div>
                <button class="btn btn-outline-primary btn-block" type="submit"><?= $o_translation->getString('common', 'change') ?></button>
            </form>
            <?php
            } else {
            ?>
            <p class="text-center text-danger mb-0">
                <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                <?= $translationAccountReadonly ?>
            </p>
            <?php
            }
            ?>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'favouritePony') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefandomfavouritepony" method="post">
                <input class="form-control mb-2" id="content-input-fandomfavouritepony" type="text" value="<?= $userDetails['fandom_favouritepony'] ?? ''; ?>" maxlength="300" />
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-fandomfavouritepony">0</span> / 300
                    </small>
                </div>
                <button class="btn btn-outline-primary btn-block" type="submit"><?= $o_translation->getString('common', 'change') ?></button>
            </form>
            <?php
            } else {
            ?>
            <p class="text-center text-danger mb-0">
                <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                <?= $translationAccountReadonly ?>
            </p>
            <?php
            }
            ?>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'favouriteEpisode') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefandomfavouriteepisode" method="post">
                <input class="form-control mb-2" id="content-input-fandomfavouriteepisode" type="text" value="<?= $userDetails['fandom_favouriteepisode'] ?? ''; ?>" maxlength="300" />
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-fandomfavouriteepisode">0</span> / 300
                    </small>
                </div>
                <button class="btn btn-outline-primary btn-block" type="submit"><?= $o_translation->getString('common', 'change') ?></button>
            </form>
            <?php
            } else {
            ?>
            <p class="text-center text-danger mb-0">
                <i class="fa fa-exclamation-circle mr-1" aria-hidden="true"></i>
                <?= $translationAccountReadonly ?>
            </p>
            <?php
            }
            ?>
        </div>
    </div>
</div>
