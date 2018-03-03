<section class="fancybox mt-0" id="post-creator">
    <div id="post-creator-types">
        <ul class="d-flex" style="line-height: 20.2px;">
            <li class="active">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-pencil-square-o" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span>Post</span>
            </li>
            <li class="disabled" style="cursor: not-allowed;">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-picture-o" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span>Photo</span>
            </li>
            <li class="disabled" style="cursor: not-allowed;">
                <div style="width: 16px; height: 14px; text-align: right; vertical-align: middle;">
                    <i class="fa fa-bar-chart" style="vertical-align: top;" aria-hidden="true"></i>
                </div>
                <span>Poll</span>
            </li>
        </ul>
    </div>

    <div id="post-creator-input">
        <textarea id="post-creator-textarea" placeholder="Tell us something..." maxlength="1000" style="overflow: hidden;"></textarea>
    </div>

    <div id="post-creator-submitbar" class="d-flex align-items-center justify-content-end">
        <small class="text-muted mr-3">
            <span id="post-creator-lettercounter">0</span> / 1000
        </small>
        <button id="post-creator-submit" class="btn btn-sm btn-outline-primary" disabled style="cursor: not-allowed;">Publish</button>
    </div>
</section>
