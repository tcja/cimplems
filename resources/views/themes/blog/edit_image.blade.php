{!! Form::open([url('/abort'), 'class' => 'edit_image_form', 'style' => 'width: 155px;']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="form-group mt-2 mb-2">
					<input type="text" class="form-control form-control-sm" name="change_title" placeholder="Titre" value="{{ $array_image['title'] }}"/>
				</div>
				<div class="form-group mt-2 mb-2">
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
		<input type="hidden" name="gallery_default" value="{{ $gallery_default }}" />
		<input type="hidden" name="edit_image_infos" value="1" />
		<button  style="width:66px;" class="acceptEdit btn btn-primary btn-sm" type="submit">{{ __('site.ok_button') }}</button>
		<button class="cancel btn btn-danger btn-sm float-right">{{ __('site.cancel_button') }}</button>
{!! Form::close() !!}
