<div class="row">
	<div class="col-12">
		<h4 class="mt-4 mb-3" style="color: red;">{{ __('site.send_image_title') }}</h4>
	{!! Form::open(['url' => url('/abort'), 'enctype' => 'multipart/form-data', 'id' => 'submit_img', 'class' => '']) !!}
			<input type="hidden" name="send_image" value="1"/>
			<div class="form-group row">
				<label for="image_title" class="col-2 col-sm-2 col-md-1 col-form-label text-right">{{ __('site.title') }}</label>
				<div class="col-9 col-sm-8 col-md-7 col-lg-4">
					<input type="text" class="mt-1 form-control form-control-sm" id="image_title" name="image_title" placeholder="{{ __('site.insert_title') }}" value=""/>
				</div>
			</div>
			<div class="form-group row">
				<label for="gallery" class="col-2 col-sm-2 col-md-1 col-form-label text-right">{{ __('site.gallery') }}</label>
				<div class="col-9 col-sm-8 col-md-7 col-lg-4">
					<select size="1" id="gallery" name="gallery" class="mt-1 form-control form-control-sm">
						<option value="0">{{ __('site.chose_gallery') }}</option>
						@foreach ($galleries_names as $key => $gallery)
							<option value="{{ $key }}">{{ $gallery }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-11 col-sm-10 col-md-8 col-lg-5 d-flex justify-content-end">
					<input type="file" accept="image/*" title="Ajouter des images" id="file_upload" style="{{ ($isMobile ? 'margin-left: 2rem;' : 'margin-left: 12rem;') }}" class="upload" multiple="multiple" name="image_path[]">
					<label style="height:38px; right: 6.5rem;" class="mr-2 noselect position-absolute" for="file_upload"><i class="fas fa-file-image"></i>&nbsp;{{ __('site.your_photos') }}</label>
					<button style="height:38px;" id="import" class="btn btn-primary" type="submit">{{ __('site.import_button') }}</button>
				</div>
			</div>
			<br>
	{!! Form::close() !!}
	<br>
	</div>
</div>
<div class="row">
	<div id="loader" class="col-12 d-none flex-column align-items-center">
		<div class="spinnerR">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
		<span style="font-size: 0.9rem;">{{ __('site.sending_images') }}</span>
	</div>
	<div id="loader2" class="col-12 d-none flex-column align-items-center">
		<div class="spinnerR">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
		<span style="font-size: 0.9rem;">{{ __('site.loading_images') }}</span>
	</div>
	<div id="img_selected" class="col-12 hidden">
		<h3>{{ __('site.zero_images_selected') }}</h3>
	</div>
	@if ($isMobile)
		<div id="files_list" class="col-12 d-flex flex-wrap">
			<h4 class="m-auto">{{ __('site.your_images') }}</h4>
		</div>
	@else
		<div id="files_list" class="col-12 d-flex flex-wrap">
			<h5 class="m-auto">{{ __('site.put_your_images_here') }}</h5>
		</div>
	@endif
</div>
<div class="row space"></div>
<div class="row">
	<div class="col-12">
		<h4 style="color: red; margin-bottom: 15px;">{{ __('site.create_gallery') }}</h4>
		{!! Form::open(['url' => url('/abort'), 'id' => 'create_gal', 'class' => 'form-row']) !!}
				<input type="hidden" name="create_gallery" value="1"/>
				<label for="gal_title" class="col-md-1 col-sm-2 col-2 gallery_title text-center">{{ __('site.title') }}</label>
				<div class="col-md-4 col-sm-6 col-7" style="{{-- {{ ($isMobile ? 'margin-left:-5px;' : 'margin-left:-35px;') }} --}}">
					<input type="text" id="gal_title" class="form-control form-control-sm" name="gallery_title"/>
				</div>
				<div class="col-md-3 col-sm-3 col-2">
					<button class="sendForm btn btn-primary btn-sm" style="width:4rem" type="submit">{{ __('site.create_button') }}</button>
					<button style="display:none; {{ ($isMobile ? 'width:64px;' : '') }}" class="formSending btn btn-primary btn-sm" type="button" disabled>
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
						{{ ($isMobile ? '' : __('site.creating_gallery')) }}
					</button>
					<button style="display:none; {{ ($isMobile ? 'width:64px;' : '') }}" type="button" class="formSent btn btn-sm btn-success"><i class="far fa-check-circle"></i> {!! ($isMobile ? '' : '<b>'.__("site.gallery_created").'</b>') !!}</button>
				</div>
		{!! Form::close() !!}
	</div>
</div>
<div class="row space"></div>
<div class="row">
	<div class="col-12">
		<h4 class="mb-3" style="color: red;">{{ __('site.manage_galleries') }}</h4>
			@if (!empty($galleries_names))
				{!! Form::open(['url' => url('/abort'), 'id' => 'modify_gal', 'class' => '']) !!}
						@foreach ($galleries_names as $key => $gallery)
							<div class="form-group row">
								<div class="col-2 col-sm-2 col-md-1 text-center">
									<label for="{{ $key }}" class="col-form-label">{{ __('site.title') }}</label>
								</div>
								<div class="col-10 col-sm-10 col-md-5 mt-1">
									<div class="input-group input-group-sm">
										<input type="text" class="form-control galEdit" id="{{ $key }}" name="{{ $key }}" value="{{ $gallery }}"/>
										<div class="input-group-append ml-2 {{ ($isMobile ? 'mt-1' : '') }}">
											<a href="javascript:;" class="delete_gallery" title="{{ __('site.delete_gallery') }}"><i style="{{ ($isMobile ? 'font-size: 1.3rem;' : '') }}" class="far fa-trash-alt"></i></a>
										</div>
									</div>
								</div>
							</div>
						@endforeach
						<input type="hidden" name="change_gallery_title" value="1"/>
						<button class="mt-1 btn btn-primary btn-sm" style="{{ ($isMobile ? 'margin-left: 4.9rem;' : 'margin-left: 12rem;') }}" type="submit">{{ __('site.apply_changes') }}</button>
						<button style="display:none; {{ ($isMobile ? 'margin-left: 4.9rem;' : 'margin-left: 12rem;') }}" class="btn btn-primary btn-sm" type="button" disabled>
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							{{ __('site.changes_in_progress') }}
						</button>
						<button style="display:none; {{ ($isMobile ? 'margin-left: 4.9rem;' : 'margin-left: 12rem;') }}" type="button" class="btn btn-sm btn-success"><i class="far fa-check-circle"></i> <b>{{ __('site.changes_done') }}</b></button>
				{!! Form::close() !!}
			@else
				<h6 id="nogallery" class="ml-3">{{ __('site.no_gallery') }}</h6>
			@endif
	</div>
</div><br>
@include('admin_assets/manage_galleries_js')
