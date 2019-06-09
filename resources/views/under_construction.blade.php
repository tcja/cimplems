<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ __('site.under_construction_title') }}</title>
		<link rel="shortcut icon" href="{{ asset('css/favicon.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
        <link rel="stylesheet" href="{{ asset('css/styless.css') }}">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        @if (app()->getLocale() != 'en')
            @php $js_location = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/localization/messages_'.app()->getLocale().'.min.js'; @endphp
            <script src="{{ $js_location }}"></script>
        @endif
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
        <style>
            body,html {
                height: 100%;
            }
        </style>
    </head>
	<body>
        <div class="container h-100">
            <div class="row align-items-center h-100">
                <div class="col-12">
                        <div style="" class="text-center">
                            <i style="font-size: 4rem;" class="mb-2 fas fa-cogs"></i>
                            <h2>{{ __('site.under_construction_h2') }}</h2>
                            <div>
                                <p>{{ __('site.under_construction') }}</p>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <footer class="position-absolute" style="left:20px; bottom:20px;">
            <a href="javascript:;" data-toggle="modal" data-target="#Login" id="login">{{ __('site.under_construction_admin') }}</a>
        </footer>
        {{-- login modal --}}
        <div class="modal modalReset fade modalLogin" id="Login" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
            <div class="modal-dialog {{-- modal-lg --}}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('site.admin_auth') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {!! Form::open(['url' => url('/abort'), 'id' => 'loginForm', 'class' => '']) !!}
                        <div class="modal-body">
                            <i style="font-size: 4.5rem; color: #007bff;" class="w-100 text-center fas fa-sign-in-alt"></i>
                            <h5 class="my-3 text-center font-weight-normal">{{ __('site.fill_inputs') }}</h5>
                            <label for="inputEmail" class="sr-only">{{ __('site.email') }}</label>
                            <input type="email" id="inputEmail" name="email" style="margin: auto; max-width: 90%;" class="form-control" placeholder="{{ __('site.email') }}">
                            <div class="mb-2"></div>
                            <label for="inputPassword" class="sr-only">{{ __('site.password') }}</label>
                            <input type="password" id="inputPassword" name="password" style="margin: auto; max-width: 90%;" class="form-control" placeholder="{{ __('site.password') }}">
                            {{-- <div class="checkbox mb-3">
                                <label>
                                <input type="checkbox" value="remember-me"> Remember me
                                </label>
                            </div> --}}
                        </div>
                        <div class="noselect modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
                            <button type="submit" class="btn btn-primary btn-sm sendForm confirmLogin">{{ __('site.confirm_button') }}</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function(){
                $.validator.setDefaults({
                    errorClass: 'errorf alert-error',
                    onfocusout: false,
                    onkeyup: false,
                    //onclick: false
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.modalReset').on('hidden.bs.modal', function() {
                    $(this).find('.alert-error:not(label)').each(function(i, el) {
                        if ($(el).is('INPUT')) {
                            $(el).val('');
                        } else {
                            $(el).val(0);
                        }
                        $(el).removeClass('errorf alert-error');
                        $(el).next().remove();
                    });
                });

                $rootUrl = '{{ $rootUrl }}';

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
                                document.location.href = $rootUrl + "/login";
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
                                document.location.href = $rootUrl + '/';
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
            });
        </script>
	</body>
</html>
