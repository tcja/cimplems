<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ $pageTitle }}</title>
		<link rel="shortcut icon" href="{{ asset('css/favicon.png') }}">
		@if (session('admin') === true)
			@include('admin_assets/css_assets')
		@endif
		@include('assets/css_assets')
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css">
		<link rel="stylesheet" href="{{ asset(config('site.theme_dir') . config('site.theme') . '/' . 'css/style.css') }}">
		@include('assets/js_assets')
		<script src="{{ asset('js/jquery.mobile.custom.min.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js"></script>
		<script src="{{ asset('js/jqPhotoSwipe.min.js') }}"></script>
		<script type="text/javascript">
			$(function(){
				$('.thumb_resp2').on("contextmenu",function(e){	e.preventDefault();	});
				$(".phswipe").jqPhotoSwipe({
					forceSingleGallery: true
				});
				$isMobile = true;
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
        @if (config('site.gallery_page_ajax_transition'))
            @include('assets/gallery_change_page_js')
        @endif
	</head>
	<body>
		@include('misc_top')
		@include(config('site.theme_dir') . config('site.theme') . '/' . 'menu')
		@include(config('site.theme_dir') . config('site.theme') . '/' . $page)
		@include(config('site.theme_dir') . config('site.theme') . '/' . 'footer')
	</body>
</html>
