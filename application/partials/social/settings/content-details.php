<div id="content-details" style="display: none;">
    <h6 class="text-center mb-0"><?= $o_translation->getString('settings', 'tellAboutYourself') ?></h6>

    <div class="p-3">
        <p><small><?= $o_translation->getString('settings', 'tellAboutYourselfDescription') ?></small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'fullDescription') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefulldescription" method="post">
                <textarea class="form-control mb-2" id="content-input-fulldescription" rows="5" maxlength="1000"><?= $userDetails['full_description'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-fulldescription">0</span> / 1000
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'contactDetails') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changecontactmethods" method="post">
                <textarea class="form-control mb-2" id="content-input-contactmethods" rows="4" maxlength="500"><?= $userDetails['contact_methods'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-contactmethods">0</span> / 500
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'favouriteMusic') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefavouritemusic" method="post">
                <textarea class="form-control mb-2" id="content-input-favouritemusic" rows="4" maxlength="500"><?= $userDetails['favourite_music'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-favouritemusic">0</span> / 500
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'favouriteMovies') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefavouritemovies" method="post">
                <textarea class="form-control mb-2" id="content-input-favouritemovies" rows="4" maxlength="500"><?= $userDetails['favourite_movies'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-favouritemovies">0</span> / 500
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
            <p class="content-title mb-2"><?= $o_translation->getString('settings', 'favouriteGames') ?></p>

            <?php
            if (!$readonlyState) {
            ?>
            <form id="content-form-changefavouritegames" method="post">
                <textarea class="form-control mb-2" id="content-input-favouritegames" rows="4" maxlength="500"><?= $userDetails['favourite_games'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-favouritegames">0</span> / 500
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
