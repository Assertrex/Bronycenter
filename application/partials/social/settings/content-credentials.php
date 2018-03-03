<div id="content-credentials">
    <h6 class="text-center mb-0">Login credentials</h6>

    <div class="p-3">
        <p><small>Configure how you want to log in to the website.</small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Username</p>

            <div>
                <input class="form-control mr-2 mb-2" id="content-input-username"
                       type="text" placeholder="Username"
                       value="<?php echo $userDetails['username'] ?? ''; ?>"
                       aria-describedby="usernameInputDescription" disabled />

                <div class="d-flex justify-content-end">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-username">0</span> / 24
                    </small>
                </div>

                <small id="usernameInputDescription" class="form-text text-muted">
                    You can't change your username. If you have a good reason for doing that, please contact an administrator.
                </small>
            </div>
        </div>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Password</p>

            <form method="post" id="content-form-changepassword">
                <input class="form-control mr-2 mb-2" id="content-input-oldpassword" type="password" placeholder="Current password" autocomplete="off" required />

                <div class="form-inline mb-sm-2">
                    <input class="form-control mr-sm-2 mb-2 mb-sm-0" id="content-input-newpassword" type="password" placeholder="New password" autocomplete="off" required />
                    <input class="form-control mb-2 mb-sm-0" id="content-input-repeatpassword" type="password" placeholder="Repeat password" autocomplete="off" required />
                </div>

                <button type="submit" class="btn btn-outline-primary btn-block">Change</button>
            </form>
        </div>
    </div>
</div>
