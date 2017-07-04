// Check if document is ready first
$(document).ready(function() {
    // Extend online session every 60 seconds
    setInterval(extendSession, 60000);

    // Function for extending online session for next 60 seconds
    function extendSession() {
        $.ajax({
            url: "../system/ajax/extendSession.php",
            success: function(result) {
                
            }
        });
    }
});
