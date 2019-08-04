<footer>
    <div class="container">
        @if (session('admin') === true)
            <a href="{{ url('/logout') }}" id="logout">{{ __('site.logout') }}</a>
            <button type="button" style="right: 0.5rem; width: 34px;" class="position-absolute tooltipz configure_user page_option_icon ml-1 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __('site.user_config_button') }}" data-target="#User"><i class="fas fa-user-cog"></i></button>
        @else
            <a href="javascript:;" data-toggle="modal" data-target="#Login" id="login">{{ __('site.login') }}</a>
        @endif
    </div>
</footer>
