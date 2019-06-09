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
		@if (session('admin') === true)
			@include('admin_assets/js_assets')
		@endif
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
				$rootUrl = '{{ $rootUrl }}';
				$currentMenuOrder = {{ $currentMenuOrder }};
				$currentPageTitle = '{{ $currentPageTitle }}';
                $currentSlug = '{{ $currentSlug }}';
                $optionsConfirmModal = {
                    //messageHeader: '{{ __("site.delete_image_modal") }}',
                    //modalBoxWidth: '365px',
                    modalVerticalCenter: false,
                    fadeAnimation: false
                };
				$(this).tooltip({
					selector: '.tooltipz',
					placement: 'bottom',
					trigger: 'hover'
				});
			});
		</script>
		@if (session('admin') === true)
            @include(config('site.theme_dir') . config('site.theme') . '/' . 'admin_assets/misc_admin_js')
            @include(config('site.theme_dir') . config('site.theme') . '/' . 'admin_assets/user_js')
		@else
			<script src="{{ asset(config('site.theme_dir') . config('site.theme') . '/' . 'js/misc.js') }}"></script>
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
