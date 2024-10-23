
jQuery(document).ready(function($) {
    // Handle Send OTP form submission
    $('#send-otp-form').on('submit', function(e) {
        e.preventDefault();

        var phone = $(this).find('#phone').val();

        $.ajax({
            url: sepid_pub_obj.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'send_otp_code', // AJAX action (send_otp)
                phone: phone,
            },
            success: function(response) {
                if (response.success) {
                    $('#send-otp-message').html('<p class="success">' + response.data.message + '</p>');
                    // Hide the Send OTP form and show the Verify OTP form
                    $('#send-otp-form').hide();
                    $('#verify-otp-form').show();
                } else {
                    $('#send-otp-message').html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function(e) {
                console.log(e)
                $('#send-otp-message').html('<p class="error">Ajax Error</p>');
            }
        });

    });

    // Handle Verify OTP form submission
    $('#verify-otp-form').on('submit', function(e) {
        e.preventDefault();

        var phone = $('#phone').val();
        var otp_code = $(this).find('#otp_code').val();
        var redirect_url = $(this).find('#redirect_url').val();

        $.ajax({
            url: sepid_pub_obj.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'user_login', // AJAX action (user_login)
                phone: phone,
                otp_code: otp_code,
            },
            success: function(response) {
                if (response.success) {
                    $('#verify-otp-message').html('<p class="success">' + response.data.message + '</p>');

                    // check for new user registeration
                    if (response.success) {
                        if (response.data.is_new_user) {
                            $('#verify-otp-form').hide();
                            $('#reg_phone').val(phone);
                            $('#reg_redirect_url').val(redirect_url);
                            $('#complete-registration-form').show();
                        } else {
                            $('#verify-otp-message').html('<p class="success">' + response.data.message + '</p>');
                            setTimeout(function(){
                                window.location.href = redirect_url
                            }, 1000);
                        }
                    }

                } else {
                    $('#verify-otp-message').html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('#verify-otp-message').html('<p class="error">An error occurred while verifying the OTP. Please try again.</p>');
            }
        });
    });

    $('#complete-registration-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: sepid_pub_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'complete_registration',
                phone: $('#reg_phone').val(),
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
            },
            success: function(response) {
                if (response.success) {
                    $('#registration-message').html('<p class="success">' + response.data.message + '</p>');
                    setTimeout(function(){
                        window.location.href = $('#reg_redirect_url').val();
                    }, 1000);
                } else {
                    $('#registration-message').html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function(xhr){
                $('#registration-message').html('<p class="error">Ajax error</p>');
                console.log(xhr)
            }
        });
    });

});