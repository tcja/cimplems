<script type="text/javascript">
	$(function(){
		$('#loginForm').validate({
			errorClass: 'ml-4 errorf alert-error',
            onfocusout: false,
            onkeyup: false,
            //onclick: false,
			rules: {
				password: {
					required: true,
					minlength: 3
				},
                email: {
					required: true,
                    email: true/* ,
					minlength: 3 */
				}
			},
			submitHandler: function(form) {
				$('.sendForm').attr('disabled', 'disabled');
				var data_string = 'password=' + $('#inputPassword').val() + '&email=' + $('#inputEmail').val();
				$.ajax({
					type: 'POST',
					url: $rootUrl+'/login',
					data: data_string,
					dataType: 'json'
				}).done(function(datas) {
					if (datas === 'updateBrowser') {
						document.location.href = $rootUrl+"/login";
                    } else if (datas === 'logged') {
						/* $('.toast').toast({
							delay: 6000
						});
						$('.modalLogin').one('hidden.bs.modal', function (e) {
							$('.sendForm').removeAttr('disabled');
							$('.toast').on('hidden.bs.toast', function () {
								$('.toast-header').children('strong').html('Opération effectuée !');
								$('.toast').toast({
									delay: 2500
								});
							});
							$('.toast-header').children('strong').html('Authentification réussie !');
							$('.toast-body').html("Vous êtes désormais connecté en tant qu'adminstrateur.");
							$('.toast').toast('show');
						});
						$('.modalLogin').modal('hide'); */
						document.location.href = $rootUrl + '/' + ($currentSlug == 'home' ? '' : $currentSlug);
					} else if (datas === 'wrongInputs') {
						$('.sendForm').removeAttr('disabled');
						$('#inputPassword').removeClass('valid')/* .addClass('ml-4 errorf alert-error') */;
						$('#inputPassword').after('<label style="max-width: 90%;" class="ml-4 errorf alert-error">{{ __("site.wrong_inputs") }}</label>');
						$('#inputPassword').keyup(function() {
							$('#inputPassword').removeClass('ml-4 errorf alert-error');
							$('#inputPassword-error').remove();
						});
					} else {
						alert('{{ __("site.request_failed") }}');
                    }
				});
			}
		});

		$(this).on('click', '.sendContactForm', function(e) {
			e.preventDefault();
			var button = this;
			$(this).parents('form').validate({
				rules: {
					name: {
						required: true,
						normalizer: function(value) {
							return $.trim(value);
						},
						minlength: 2,
						maxlength: 30
					},
					email: {
						required: true,
						normalizer: function(value) {
							return $.trim(value);
						},
						email: true
                    },
                    subject: {
						required: true,
						normalizer: function(value) {
							return $.trim(value);
						},
						minlength: 3,
						maxlength: 60
					},
					message: {
						required: true,
						normalizer: function(value) {
							return $.trim(value);
						},
						minlength: 3,
						maxlength: 1000
					},
				},
				submitHandler: function(form) {
					$('.sendForm').attr('disabled', 'disabled');
					var data_string = 'name=' + $(form).find('input[name="name"]').val() + '&email=' + $(form).find('input[name="email"]').val() + '&subject=' + $(form).find('input[name="subject"]').val() + '&message=' + encodeURIComponent($(form).find('textarea[name="message"]').val());
					$.ajax({
						beforeSend: function() {
							$(form).find('input[name="name"]').attr('disabled', 'disabled');
							$(form).find('input[name="email"]').attr('disabled', 'disabled');
							$(form).find('input[name="subject"]').attr('disabled', 'disabled');
							$(form).find('textarea[name="message"]').attr('disabled', 'disabled');
							if ($isMobile) {
                                $(button).removeClass('d-block');
                                $(button).addClass('d-none');
                            } else {
                                $(button).removeClass('d-md-block');
                            }
							$(form).find('.formSending').fadeIn(0);
						},
						type: 'POST',
						context: this,
						url: $rootUrl+'/contact',
						data: data_string,
						dataType: 'json'
					}).done(function(datas) {
						$.timer(1500, function() {
							$(form).find('input[name="name"]').val('');
							$(form).find('input[name="email"]').val('');
							$(form).find('input[name="subject"]').val('');
							$(form).find('textarea[name="message"]').val('');
							$('.sendForm').removeAttr('disabled');
							$(form).find('input[name="name"]').removeAttr('disabled');
							$(form).find('input[name="email"]').removeAttr('disabled');
							$(form).find('input[name="subject"]').removeAttr('disabled');
							$(form).find('textarea[name="message"]').removeAttr('disabled');
							$(form).find('.formSending').fadeOut(150, function() {
								$(form).find('.formSent').fadeIn(150, function() {
									$.timer(1000, function() {
										$(form).find('.formSent').fadeOut(150, function() {
											if ($isMobile) {
												$(button).addClass('d-block');
												$(button).removeClass('d-none');
											} else {
												$(button).addClass('d-md-block');
                                            }
											if ($(form).next().length == 0) {
												$(form).after('<div class="alert alert-success alert-dismissible col-auto text-left fade" role="alert"><i class="far fa-envelope"></i><strong> {{ __("site.message_received") }}</strong><br> {{ __("site.thanks_for_contacting_us") }}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                                                $.scrollTo('.alert', 300, {
                                                    onAfter: function() {
                                                        $('.alert').addClass('show');
                                                    }
                                                });
											}
										});
									});
								});
							});
							$('.sendContactForm').off('click');
							$('.sendContactForm').one('click');
						});
					});
				}
			});
			$(this).parents('form').trigger('submit');
		});
	});
</script>
