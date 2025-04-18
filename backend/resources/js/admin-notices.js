// Review Notics 
jQuery(document).ready(function($) {
    $('#sppcfw-dismiss-btn').on('click', function() {
        const data = {
            action: 'sppcfw_dismiss_review_notice',
            nonce: sppcfw_obj.nonce 
        };

        const ajaxURL = sppcfw_obj.ajax_url;

        $.post(ajaxURL, data, function(response) {
            if (response.success) {
                $('#sppcfw-review-notice').hide(); // Hide the review notice
            } else {
                console.log(response.data);
            }
        });
    });

    // Prepare the admin email notification
    $('#sppcfw-not-good-enough-btn').on('click', function () {
        const data = {
            action: 'sppcfw_send_admin_notification',
            nonce: sppcfw_obj.nonce,
            message: 'User reported that the plugin was not good enough.'
        };

        const ajaxURL = sppcfw_obj.ajax_url;

         // Disable the button to prevent duplicate clicks
        $(this).prop('disabled', true).text('Processing...');

        // Perform the AJAX request
        $.post(ajaxURL, data, function (response) {
            if (response.success) {
                alert('Your feedback has been sent to the admin. Thank you for your input.');
            } else {
                alert('An error occurred while sending feedback. Please try again.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Status: ' + textStatus);        
            console.error('Error: ' + errorThrown);        
            console.error('Response Text: ' + jqXHR.responseText);
        }).always(function () {
            $('#sppcfw-not-good-enough-btn').prop('disabled', false).text('No, not good enough');
        });
    });
});







