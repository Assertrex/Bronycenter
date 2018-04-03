<div id="content-basic" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'basicInformation') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'basicInformationDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'displayName') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changedisplayname">
                <p class="mb-3" style="font-size: .875em;">You can change your display name <b><?= 3 - $userDetails['displayname_changes']; ?></b> more times.</p>

                <input class="form-control mb-2" id="content-input-displayname" type="text" placeholder="<?= $o_translation->getString('common', 'displayName') ?>" value="<?= $userDetails['display_name'] ?? ''; ?>" maxlength="32" autocomplete="off" required />
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-displayname">0</span> / 32
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
            <p class="content-title mb-2"><?= $o_translation->getString('profile', 'gender') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changegender">
                <select class="form-control mb-2" id="content-input-gender" style="flex: 1;">
                    <option value="0"<?= ($userDetails['gender'] == 0) ? 'selected' : ''; ?>><?= ucfirst($o_translation->getString('common', 'unknown')) ?></option>
                    <option value="1"<?= ($userDetails['gender'] == 1) ? 'selected' : ''; ?>><?= ucfirst($o_translation->getString('common', 'male')) ?></option>
                    <option value="2"<?= ($userDetails['gender'] == 2) ? 'selected' : ''; ?>><?= ucfirst($o_translation->getString('common', 'female')) ?></option>
                </select>
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
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'birthDate') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changebirthdate">
                <div class="form-inline">
                    <select class="form-control mr-sm-2 mb-2" id="content-input-birthmonth">
                        <option value="0"<?php if (is_null($userDetails['birthdate_temp'])) { echo ' selected'; } ?>><?= $o_translation->getString('common', 'month') ?></option>
                        <?php
                        $arrayMonths = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'October', 'September', 'November', 'December'];

                        for ($i = 1; $i < count($arrayMonths) + 1; $i++) {
                            if ($i == $userDetails['birthdate_temp']['month']) {
                                echo '<option value="' . $i . '" selected>' . $arrayMonths[$i - 1] . '</option>';
                            } else {
                                echo '<option value="' . $i . '">' . $arrayMonths[$i - 1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <input type="number" class="form-control mr-sm-2 mb-2" id="content-input-birthday" placeholder="<?= $o_translation->getString('common', 'day') ?>" min="0" max="31" value="<?= $userDetails['birthdate_temp']['day'] ?? ''; ?>" autocomplete="off" />
                    <input type="number" class="form-control mb-2" id="content-input-birthyear" placeholder="<?= $o_translation->getString('common', 'year') ?>" min="0" max="<?= date('Y') - 13; ?>" value="<?= $userDetails['birthdate_temp']['year'] ?? ''; ?>" autocomplete="off" />
                </div>
                <div>
                    <button class="btn btn-outline-primary btn-block" type="submit"><?= $o_translation->getString('common', 'change') ?></button>
                </div>
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'currentCity') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form method="post" id="content-form-changecity">
                <div class="form-inline mb-sm-2">
                    <input class="form-control mr-sm-2 mb-2 mb-sm-0" id="content-input-city" type="text" placeholder="<?= $o_translation->getString('common', 'cityName') ?>" value="<?= $userDetails['city'] ?? ''; ?>" maxlength="32" autocomplete="off" />
                    <input class="form-control mb-2 mb-sm-0" type="text" placeholder="Country name" value="<?= $utilities->getCountryName($userDetails['country_code']); ?>" disabled />
                </div>

                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-city">0</span> / 32
                    </small>
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

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('common', 'avatar') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <div class="d-flex align-items-center align-items-sm-end">
                <div class="mr-2">
                    <img src="../media/avatars/<?= $userDetails['avatar'] ?? 'default'; ?>/defres.jpg" class="rounded" id="content-currentavatar" />
                </div>
                <div style="flex: 1;">
                    <form id="content-form-changeavatar" method="post">
                        <input type="file" class="form-control mb-2" id="content-input-avatar" />
                        <button class="btn btn-outline-primary btn-block" type="submit"><?= $o_translation->getString('common', 'change') ?></button>
                    </form>
                    <div id="content-form-changeavatar-process" style="display: none; text-align: center; font-size: 1.25rem;">
                        <?= $o_translation->getString('settings', 'changingAvatar') ?>...
                    </div>
                </div>
            </div>
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'shortDescriptionInProfile') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changeshortdescription" method="post">
                <textarea class="form-control mb-2" id="content-input-shortdescription" rows="3" maxlength="160"><?= $userDetails['short_description'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-shortdescription">0</span> / 160
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
