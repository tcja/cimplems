<footer class="bg-dark">
    <div class="container position-relative">
        @if (session('admin') === true)
            <a style="z-index: 100;" title="Se déconnecter de l'admin" class="d-inline-block position-absolute text-white" href="{{ url('/logout') }}" id="logout">{{ __('site.logout') }}</a>
        @else
            <a style="z-index: 100;" class="d-inline-block position-absolute text-white" href="javascript:;" data-toggle="modal" data-target="#Login" id="login">{{ __('site.login') }}</a>
        @endif
        @if (config('site.footer_credits'))
            {!! config('site.footer_credits') !!}
        @endif
        @if (session('admin') === true)
            <button type="button" style="right: 0.5rem; top: 0.5rem; width: 34px;" class="d-inline-block position-absolute tooltipz configure_user page_option_icon ml-1 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __('site.user_config_button') }}" data-target="#User"><i class="fas fa-user-cog"></i></button>
        @endif
    </div>
</footer>
