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
