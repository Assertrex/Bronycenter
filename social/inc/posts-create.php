<?php
// Display post creating form for logged user.
if ($emailVerified) {
?>
<form method="post" action="index.php">
    <div class="form-group">
        <textarea name="message" class="form-control" id="createpost-content" placeholder="Dear Princess Celestia..." maxlength="1000" required autofocus></textarea>
    </div>
    <div class="form-group text-right mb-0">
        <small class="text-muted mr-2"><span id="createpost-characters-counter">0</span> / 1000</small>
        <button type="submit" name="submit" value="createpost" class="btn btn-outline-primary" role="button">Send to Princess</button>
    </div>
</form>
<?php
} // if
// Show warning about required e-mail verification if user has not verified it.
else {
?>
<p class="text-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> You need to verify your e-mail address before you'll be able to write anything public!</p>
<?php
} // else
?>
