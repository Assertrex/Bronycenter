<div id="content-creations" style="display: none;">
    <h6 class="text-center mb-0">Share your creativity</h6>

    <div class="p-3">
        <p><small>If you're creating something, you can share links to anything that you've made.</small></p>

        <div class="content-block mb-3">
            <p class="content-title mb-2">Share what you've made</p>

            <form method="post" id="content-form-changecreationslinks">
                <textarea class="form-control mb-2" id="content-input-creationslinks" rows="5" maxlength="1000"><?php echo $userDetails['creations_links'] ?? ''; ?></textarea>
                <div class="d-flex justify-content-end mb-2">
                    <small class="letters-counter text-muted">
                        <span id="content-counter-creationslinks">0</span> / 1000
                    </small>
                </div>
                <button class="btn btn-outline-primary btn-block">Change</button>
            </form>
        </div>
    </div>
</div>
