<div class="modal fade edit_image_mobile" tabindex="-1" role="dialog" aria-labelledby="editimage" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content" style="width:230px; margin:auto;">
			<div class="modal-header" style="height:40px; padding-top:9px;">
				<h6 class="modal-title" style="font-weight:bold;">{{ __('site.edit_image_title') }}</h6>
				<button style="margin-right:-20px;" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
			</div>
			<div class="modal-body" style="padding-top:10px; padding-bottom:10px;">
				{!! Form::open([url('/abort'), 'class' => 'edit_image_form container', 'style' => '']) !!}
						<div class="row">
							<div class="col-12">
								<img style="object-fit: cover; width: 166px; height: 155px;" class="img-thumbnail" src="{{ asset('storage/images_gallery/min/'.$array_image['name']) }}" alt="img">
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="form-group mt-2 mb-2">
									<input type="text" class="form-control form-control-sm" name="change_title" placeholder="{{ __('site.title') }}" value="{{ $array_image['title'] }}"/>
								</div>
								<div class="form-group mb-2">
									<select size="1" name="gallery" class="form-control form-control-sm">
										@foreach ($galleries_name as $key => $gallery)
											@if ($key == $array_image['gallery'])
												@php $gallery_default = $key @endphp
											@endif
											<option value="{{ $key }}" @if($key == $array_image['gallery']) {{ 'selected="true"' }} @endif>{{ $gallery }}</option>
										@endforeach
									</select>
								</div>
								<input type="hidden" name="photo_name" value="{{ $array_image['name'] }}" />
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="button" class="delete_image_mobile btn btn-danger btn-sm w-100">{{ __('site.delete_image_button') }}</button>
							</div>
						</div>
						<input type="hidden" name="gallery_default" value="{{ $gallery_default }}" />
						<input type="hidden" name="edit_image_infos" value="1" />
				{!! Form::close() !!}
			</div>
			<div class="noselect modal-footer" style="height:45px; padding-right: 10px;">
				<button style="width: 66px;" type="button" class="accept btn btn-primary btn-sm">{{ __('site.ok_button') }}</button>
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
			</div>
		</div>
	</div>
</div>
