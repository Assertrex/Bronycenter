<div id="content-credentials">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'loginCredentials') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'loginCredentialsDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'username') ?></p>

            <div>
                <input class="form-control mr-2 mb-2" id="content-input-username"
                       type="text" placeholder="Username"
                       value="<?= $userDetails['username'] ?? ''; ?>"
                       aria-describedby="usernameInputDescription" disabled />

                <div class="d-flex justify-content-end">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-username">0</span> / 24
                    </small>
                </div>

                <small id="usernameInputDescription" class="form-text text-muted">
                    <?= $o_translation->getString('settings', 'usernameChangeDescription') ?>
                </small>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'password') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changepassword">
                <input class="form-control mr-2 mb-2" id="content-input-oldpassword" type="password" placeholder="<?= $o_translation->getString('settings', 'currentPassword') ?>" autocomplete="off" required />

                <div class="form-inline mb-sm-2">
                    <input class="form-control mr-sm-2 mb-2 mb-sm-0" id="content-input-newpassword" type="password" placeholder="<?= $o_translation->getString('settings', 'newPassword') ?>" autocomplete="off" required />
                    <input class="form-control mb-2 mb-sm-0" id="content-input-repeatpassword" type="password" placeholder="<?= $o_translation->getString('settings', 'repeatPassword') ?>" autocomplete="off" required />
                </div>

                <button type="submit" class="btn btn-outline-primary btn-block"><?= $o_translation->getString('common', 'change') ?></button>
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
