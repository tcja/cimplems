<script type="text/javascript">
    $(function(){
        $('.modalUser').on('show.bs.modal', function (e) {
            if ($('#inputEmailUser').val() == '') {
                $.ajax({
                    type: 'GET',
                    url: $rootUrl + '/get_user_email',
                    dataType: 'json',
                    context: this
                }).done(function(datas) {
                    $('#oldEmailUser').val(datas);
                    $('#inputEmailUser').val(datas).select();
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
                $('.sendForm').attr('disabled', 'disabled');
                var data_string = 'old_user_email=' + $('#oldEmailUser').val() + '&new_user_email=' + $('#inputEmailUser').val();
                $.ajax({
                    beforeSend : function() {
                        $('#inputEmailUser').attr('disabled', 'disabled');
						$(form).find('.sendForm').hide();
						$(form).find('.formSending').fadeIn(0);
					},
                    type: 'POST',
                    url: $rootUrl + '/change_user_email',
                    data: data_string,
                    dataType: 'json'
                }).done(function(datas) {
                    $('#oldEmailUser').val(datas);
                    $.timer(1000, function() {
						$(form).find('.formSending').fadeOut(150, function() {
							$(form).find('.formSent').fadeIn(150);
						});
						$.timer(1500, function() {
							$(form).find('.formSent').fadeOut(150, function() {
                                $('#inputEmailUser').removeAttr('disabled');
                                $('.sendForm').removeAttr('disabled');
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

                $('.sendForm').attr('disabled', 'disabled');
                var data_string = 'old_password_user=' + $('#inputPasswordUser').val();
                $.ajax({
                    beforeSend : function() {
                        $('#inputPasswordUser').attr('disabled', 'disabled');
                    },
                    type: 'POST',
                    url: $rootUrl + '/check_user_password',
                    data: data_string,
                    dataType: 'json'
                }).done(function(datas) {
                    if (datas) {
                        var data_string = 'user_email=' + $('#oldEmailUser').val() + '&new_password_user=' + $('#inputNewPasswordUser').val();
                        $.ajax({
                            beforeSend : function() {
                                $('#inputPasswordUser').attr('disabled', 'disabled');
                                $('#inputNewPasswordUser').attr('disabled', 'disabled');
                                $('#inputConfirmPasswordUser').attr('disabled', 'disabled');
                                $(form).find('.sendForm').hide();
                                $(form).find('.formSending').fadeIn(0);
                            },
                            type: 'POST',
                            url: $rootUrl + '/change_user_password',
                            data: data_string,
                            dataType: 'json'
                        }).done(function(datas) {
                            $.timer(1000, function() {
                                $(form).find('.formSending').fadeOut(150, function() {
                                    $(form).find('.formSent').fadeIn(150);
                                });
                                $.timer(1500, function() {
                                    $(form).find('.formSent').fadeOut(150, function() {
                                        $('#inputPasswordUser').val('');
                                        $('#inputNewPasswordUser').val('');
                                        $('#inputConfirmPasswordUser').val('');
                                        $('#inputPasswordUser').removeAttr('disabled');
                                        $('#inputNewPasswordUser').removeAttr('disabled');
                                        $('#inputConfirmPasswordUser').removeAttr('disabled');
                                        $('.sendForm').removeAttr('disabled');
                                        $(form).find('.sendForm').fadeIn(150);
                                    });
                                });
                            });
                        });
                    } else {
                        $('#inputPasswordUser').removeAttr('disabled');
                        $('.sendForm').removeAttr('disabled');
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
