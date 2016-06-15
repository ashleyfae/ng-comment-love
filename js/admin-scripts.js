jQuery.noConflict();

jQuery(document).ready(function ($) {

    $('#ng-remove-love').click(function (e) {
        e.preventDefault();

        var loveButtonContainer = $(this).parent();
        var commentID = $(this).data('comment-id');

        if (commentID == '') {
            return false;
        }

        $(this).attr('disabled', true);
        $(this).after('<span id="ng-spinner" class="spinner is-active" style="float: none;"></span>');

        var data = {
            action: 'ng_remove_love',
            comment_id: commentID,
            nonce: NGLOVE.nonce
        };

        $.ajax({
            type: 'POST',
            data: data,
            url: ajaxurl,
            xhrFields: {
                withCredentials: true
            },
            success: function (response) {

                // Failed.
                if (response.success != true) {
                    // Remove the spinner.
                    $('#ng-spinner').remove();

                    // Error message.
                    loveButtonContainer.after('<p>' + response.data + '</p>');

                    return false;
                }

                $('#ng_commentlove .inside').empty().append(response.data);

            }
        });
    });

});