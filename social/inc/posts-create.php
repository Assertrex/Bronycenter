<h2 class="text-center mb-4">Write a friendship letter</h2>

<?php
// Display post creating form for logged user.
if ($emailVerified) {
?>
<form method="post" action="index.php">
    <div class="form-group">
        <textarea name="message" class="form-control" placeholder="Deer Princess Celestia..." required autofocus></textarea>
    </div>
    <div class="form-group">
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
