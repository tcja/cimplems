<div class="row">
	<div id="galeries" class="col-12">
		@if (!empty($array_images))
			@foreach ($array_images as $images)
			@php if (!array_key_exists(0, $images)) continue @endphp
				<div class="row mb-4" id="gallery{{ $images['galleryInfos']['galleryID'] }}">
					@foreach ($images as $image)
						@if (!empty($image['gallery']))
							<h2 style="word-break: break-all;">{{ $image['gallery'] }}</h2>
							<div class="col-12 d-flex flex-wrap">
						@else
							@if (!empty($image['fileName']))
								<div class="thumbs">
									<a class="fancyboxThumb text-decoration-none" title="{{ htmlspecialchars($image['title']) }}" rel="external" href="{{ asset('storage/images_gallery/big/' . $image['fileName']) }}">
										<img class="img-thumbnail img-thumbnailz" timestamp="{{ $image['timestamp'] }}" src="{{ asset('storage/images_gallery/min/' . $image['fileName']) }}" alt="{{ $image['fileName'] }}">
									</a>
									@if (session('admin') === true)
										<a href="javascript:;" title="{{ __('site.delete_image') }}" alt="{{ $image['fileName'] }}" class="delete_image"><i class="far fa-trash-alt"></i></a>
										<a href="javascript:;" title="{{ __('site.edit_image') }}" alt="{{ $image['fileName'] }}" class="edit_image"><i class="far fa-edit"></i></a>
									@endif
								</div>
							@endif
						@endif
					@endforeach
                            </div>
                    {{ $images['galleryInfos']['paginator'] }}
				</div>
			@endforeach
		@else
			<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __('site.no_image') }}</h2>
		@endif
	</div>
</div>
@if (session('admin') === true)
	@include('admin_assets/gallery_js')
	@include(config('site.theme_dir') . config('site.theme') . '/' . 'manage_galleries')
@endif
