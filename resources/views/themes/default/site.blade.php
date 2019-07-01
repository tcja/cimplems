<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ $currentPageTitle }}</title>
		<link rel="shortcut icon" href="{{ asset('css/favicon.png') }}">
		@if (session('admin') === true)
			@include('admin_assets/css_assets')
		@endif
		@include('assets/css_common_assets')
		<link rel="stylesheet" href="{{ asset('css/fancybox/jquery.fancybox-1.3.6.css') }}">
		<link rel="stylesheet" href="{{ asset(config('site.theme_dir') . config('site.theme') . '/' . 'css/style.css') }}">
		@include('assets/js_common_assets')
		<script src="{{ asset('js/jquery.fancybox-1.3.6.min.js') }}"></script>
		<script type="text/javascript">
			$(function(){
				$(this).on('click','.fancyboxThumb',function(e){
					e.preventDefault();
					$('.fancyboxThumb').fancybox({
						'transitionIn'		: 'elastic',
						'transitionOut'		: 'elastic',
						'speedIn'			: 270,
						'speedOut'			: 270,
						'changeSpeed'		: 220,
						//'titlePosition'	: 'inside',
						'overlayColor'		: '#F4F4F4',
						'overlayOpacity'	: 0.8,
						'margin'			: 50
					});
					$(this).trigger('click.fb');
				});
				$isMobile = false;
			});
		</script>
        @if (session('admin') === true)
            @include('admin_assets/js_assets')
            @if (config('site.page_ajax_transition'))
                @include(config('site.theme_dir') . config('site.theme') . '/' . 'admin_assets/misc_admin_js')
            @endif
            @include(config('site.theme_dir') . config('site.theme') . '/' . 'admin_assets/user_js')
        @else
            @if (config('site.page_ajax_transition'))
                @include(config('site.theme_dir') . config('site.theme') . '/' . 'assets/misc_js')
            @endif
        @endif
	</head>
	<body>
		@include('misc_top')
		<nav>
			@include(config('site.theme_dir') . config('site.theme') . '/' . 'menu')
		</nav>
		<div class="my-1 container">
			@include(config('site.theme_dir') . config('site.theme') . '/' . $page)
		</div>
		<footer>
			@include(config('site.theme_dir') . config('site.theme') . '/' . 'footer')
		</footer>
	</body>
</html>
