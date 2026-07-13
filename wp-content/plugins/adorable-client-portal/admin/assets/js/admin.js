jQuery(function ($) {

    $('#acpClientForm').on('submit', function (e) {

        e.preventDefault();

        const form = $(this);

        $.ajax({

            url: ajaxurl,

            type: 'POST',

            data: form.serialize(),

            beforeSend: function () {

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .text('Saving...');

            },

            success: function (response) {

                if (response.success) {

                    alert(response.data.message);

                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }

                } else {

                    alert(response.data.message);

                }

            },

            error: function () {

                alert('Unexpected server error.');

            },

            complete: function () {

                form.find('button[type=submit]')
                    .prop('disabled', false)
                    .text('Save Client');

            }

        });

    });

});