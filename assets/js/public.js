function spd_toast (message, type = "error") {
    if(message != null){
        Toastify({
            text: message,
            duration: 5000,
            newWindow: true,
            close: true,
            style: {
                background:
                    type === "error" ? "#cc3e3e" : type === "success" ? "#25ae25" : "#373636", // Custom background colors for different types
            },
            gravity: "bottom", // `top` or `bottom`
            position: "left", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
        }).showToast();
    }
}

jQuery(document).ready(function($) {
    // Handle Send OTP form submission
    $('#send-otp-form').on('submit', function(e) {
        e.preventDefault();
        var Form = $(this),
            submitBtn = Form.find('.spd-button'),
            messageEl = Form.find('.form-message')

        var phone = $(this).find('#phone').val();
        var timerDuration = 60; // Countdown timer duration in seconds

        $.ajax({
            url: sepid_pub_obj.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'send_otp_code', // AJAX action (send_otp)
                phone: phone,
            },
            beforeSend: function(){
                submitBtn.addClass('loading')
            },
            success: function(response) {
                if (response.success) {
                    spd_toast(response.data.message, 'success')
                    $('#phone-clone').text(phone)
                    // Hide the Send OTP form and show the Verify OTP form
                    $('#send-otp-form').hide();
                    $('#verify-otp-form').show();
                    startTimer(timerDuration); // Start the countdown timer
                } else {
                    spd_toast(response.data.message)
                }
            },
            error: function(e) {
                console.log(e);
                spd_toast('مشکل ارتباط با سرور')
            },
            complete: function(){
                submitBtn.removeClass('loading')
            }
        });
    });


    $('#resend-otp').on('click', '.resend-btn', function(e){
        e.preventDefault()
        $('#send-otp-form').submit()
    })

    function startTimer(duration) {
        var timer = duration, minutes, seconds;
        var displayEl = $('#resend-otp'),
            timerEl = displayEl.find('.timer'),
            resendBtnEl = displayEl.find('.resend-btn')

        displayEl.show(0); // Show the timer
        timerEl.show(0);
        resendBtnEl.hide(0)

        var countdown = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            timerEl.html( minutes + ":" + seconds);

            if (--timer < 0) {
                clearInterval(countdown);
                timerEl.hide(0)
                resendBtnEl.show(0)
            }
        }, 1000);
    }

    // Handle Verify OTP form submission (remains unchanged)
    $('#verify-otp-form').on('submit', function(e) {
        e.preventDefault();
        var Form = $(this),
            submitBtn = Form.find('.spd-button'),
            messageEl = Form.find('.form-message')

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
            beforeSend: function(){
                submitBtn.addClass('loading')
            },
            success: function(response) {
                if (response.success) {
                    spd_toast(response.data.message, 'success')

                    // Check for new user registration
                    if (response.data.is_new_user) {
                        $('#verify-otp-form').hide();
                        $('#reg_phone').val(phone);
                        $('#reg_redirect_url').val(redirect_url);
                        $('#complete-registration-form').show();
                    } else {
                        setTimeout(function(){
                            window.location.href = redirect_url;
                        }, 1000);
                    }
                } else {
                    spd_toast(response.data.message)
                }
            },
            error: function() {
                spd_toast('مشکل ارتباط با سرور.')
            },
            complete: function(){
                submitBtn.removeClass('loading')
            }
        });
    });

    $('#complete-registration-form').on('submit', function(e) {
        e.preventDefault();
        var Form = $(this),
            submitBtn = Form.find('.spd-button'),
            messageEl = Form.find('.form-message')

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
            beforeSend: function(){
                submitBtn.addClass('loading')
            },
            success: function(response) {
                if (response.success) {
                    spd_toast(response.data.message, 'success')
                    setTimeout(function(){
                        window.location.href = $('#reg_redirect_url').val();
                    }, 1000);
                } else {
                    spd_toast(response.data.message)
                }
            },
            error: function(xhr){
                spd_toast('مشکل ارتباط با سرور')
                console.log(xhr);
            },
            complete: function(){
                submitBtn.removeClass('loading')
            }
        });
    });
});