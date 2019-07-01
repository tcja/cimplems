<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ $currentPageTitle }}</title>
		<link rel="shortcut icon" href="{{ asset('css/favicon.png') }}">
		@if (session('admin') === true)
			@include('admin_assets/css_assets')
		@endif
		@include('assets/css_common_assets')
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css">
		<link rel="stylesheet" href="{{ asset(config('site.theme_dir') . config('site.theme') . '/' . 'css/style.css') }}">
		@include('assets/js_common_assets')
		<script src="{{ asset('js/jquery.mobile.custom.min.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js"></script>
		<script src="{{ asset('js/jqPhotoSwipe.min.js') }}"></script>
		<script type="text/javascript">
			$(function(){
				$('.thumb_resp2').on("contextmenu",function(e){	e.preventDefault();	});

				//By default, plugin uses `data-fancybox-group` attribute to create galleries.
				$(".phswipe").jqPhotoSwipe({
					/* galleryOpen: function (gallery) {
						//with `gallery` object you can access all methods and properties described here http://photoswipe.com/documentation/api.html
						//console.log(gallery);
						//console.log(gallery.currItem);
						//console.log(gallery.getCurrentIndex());
						//gallery.zoomTo(1, {x:gallery.viewportSize.x/2,y:gallery.viewportSize.y/2}, 500);
						gallery.toggleDesktopZoom();
					} */
					forceSingleGallery: true
				});
				//This option forces plugin to create a single gallery and ignores `data-fancybox-group` attribute.
				/* $(".forcedgallery > a").jqPhotoSwipe({
					forceSingleGallery: true
				}); */
				//$('.mobileResize').width($('.mobileResize').width() + ($('.mobileResize').width() * 25 / 100));
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
