<script type="text/javascript">
    $(function(){
        $('.modalUser').on('show.bs.modal', function (e) {
            if ($('#inputEmailUser').val() == '') {
                $.ajax({
                    type: 'GET',
                    url: $rootUrl + '/get_user_email',
                    dataType: 'json',
                    context: this
                }).done(function(data) {
                    $('#oldEmailUser').val(data);
                    $('#inputEmailUser').val(data).select();
                    $('#inputEmailUser').focusin(function() {
                        $(this).select();
                    });
                });
            }
        });

        $('#changeUserEmailForm').validate({
            errorClass: 'errorf alert-error',
            onfocusout: false,
            onkeyup: false,
            //onclick: false,
			rules: {
                email_user: {
					required: true,
                    email: true/* ,
					minlength: 3 */
				}
			},
            submitHandler: function(form) {
                if ($('#inputEmailUser').val() == $('#oldEmailUser').val()) return false;
                $('.sendForm').prop('disabled', true);
                var data_string = 'old_user_email=' + $('#oldEmailUser').val() + '&new_user_email=' + $('#inputEmailUser').val();
                $.ajax({
                    beforeSend : function() {
                        $('#inputEmailUser').prop('disabled', true);
						$(form).find('.sendForm').hide();
						$(form).find('.formSending').fadeIn(0);
					},
                    type: 'POST',
                    url: $rootUrl + '/change_user_email',
                    data: data_string,
                    dataType: 'json'
                }).done(function(data) {
                    $('#oldEmailUser').val(data);
                    $.timer(1000, function() {
						$(form).find('.formSending').fadeOut(150, function() {
							$(form).find('.formSent').fadeIn(150);
						});
						$.timer(1500, function() {
							$(form).find('.formSent').fadeOut(150, function() {
                                $('#inputEmailUser').prop('disabled', false);
                                $('.sendForm').prop('disabled', false);
								$(form).find('.sendForm').fadeIn(150);
							});
						});
					});
                });
            }
        });

        $('#changeUserPasswordForm').validate({
            errorClass: 'ml-4 errorf alert-error',
            onfocusout: false,
            onkeyup: false,
            //onclick: false,
			rules: {
                old_password_user: {
                    required: true
				},
                new_password_user: {
                    required: true,
                },
                confirm_password_user: {
                    required: true,
                    equalTo: "#inputNewPasswordUser"
                }
			},
            submitHandler: function(form) {
                if ($('#inputPasswordUser').val() == '' && $('#inputNewPasswordUser').val() == '' && $('#inputConfirmPasswordUser').val() == '') return false;

                $('.sendForm').prop('disabled', true);
                var data_string = 'old_password_user=' + $('#inputPasswordUser').val();
                $.ajax({
                    beforeSend : function() {
                        $('#inputPasswordUser').prop('disabled', true);
                    },
                    type: 'POST',
                    url: $rootUrl + '/check_user_password',
                    data: data_string,
                    dataType: 'json'
                }).done(function(data) {
                    if (data) {
                        var data_string = 'user_email=' + $('#oldEmailUser').val() + '&new_password_user=' + $('#inputNewPasswordUser').val();
                        $.ajax({
                            beforeSend : function() {
                                $('#inputPasswordUser').prop('disabled', true);
                                $('#inputNewPasswordUser').prop('disabled', true);
                                $('#inputConfirmPasswordUser').prop('disabled', true);
                                $(form).find('.sendForm').hide();
                                $(form).find('.formSending').fadeIn(0);
                            },
                            type: 'POST',
                            url: $rootUrl + '/change_user_password',
                            data: data_string,
                            dataType: 'json'
                        }).done(function(data) {
                            $.timer(1000, function() {
                                $(form).find('.formSending').fadeOut(150, function() {
                                    $(form).find('.formSent').fadeIn(150);
                                });
                                $.timer(1500, function() {
                                    $(form).find('.formSent').fadeOut(150, function() {
                                        $('#inputPasswordUser').val('');
                                        $('#inputNewPasswordUser').val('');
                                        $('#inputConfirmPasswordUser').val('');
                                        $('#inputPasswordUser').prop('disabled', false);
                                        $('#inputNewPasswordUser').prop('disabled', false);
                                        $('#inputConfirmPasswordUser').prop('disabled', false);
                                        $('.sendForm').prop('disabled', false);
                                        $(form).find('.sendForm').fadeIn(150);
                                    });
                                });
                            });
                        });
                    } else {
                        $('#inputPasswordUser').prop('disabled', false);
                        $('.sendForm').prop('disabled', false);
                        $('#inputPasswordUser').removeClass('valid').addClass('ml-4 errorf alert-error');
                        $('#inputPasswordUser').after('<label style="max-width: 90%;" class="ml-4 errorf alert-error">{{ __("site.wrong_password") }}</label>');
                        $('#inputPasswordUser').keyup(function() {
                            $('#inputPasswordUser').removeClass('ml-4 errorf alert-error');
                            $('#inputPasswordUser').next().remove();
                        });
                        return false;
                    }
                });
            }
        });
    });
</script>
