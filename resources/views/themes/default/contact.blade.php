{!! Form::open(['url' => url('/abort'), 'id' => 'contact', 'class' => '']) !!}
	<fieldset>
		<legend class="mb-3 ml-2">{{ __('site.contact_us') }}</legend>
		<div class="form-group">
			<label class="col-auto control-label" for="name">{{ __('site.contact_name') }}</label>
			<div class="col-12 col-md-5 col-lg-4">
				<input id="name" name="name" type="text" placeholder="{{ __('site.your_name') }}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-auto control-label" for="email">{{ __('site.contact_email') }}</label>
			<div class="col-12 col-md-5 col-lg-4">
				<input id="email" name="email" type="text" placeholder="{{ __('site.your_email') }}" class="form-control">
			</div>
        </div>
        <div class="form-group">
			<label class="col-auto control-label" for="subject">{{ __('site.contact_subject') }}</label>
			<div class="col-12 col-md-8 col-lg-6">
				<input id="subject" name="subject" type="text" placeholder="{{ __('site.the_subject') }}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-auto control-label" for="message">{{ __('site.contact_message') }}</label>
			<div class="col-12 col-md-8 col-lg-6">
				<textarea class="form-control" id="message" name="message" placeholder="{{ __('site.your_message') }}" rows="6"></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-12 col-md-8 col-lg-6 d-flex justify-content-end">
				<button type="submit" class="sendContactForm sendForm btn btn-primary d-none d-xs-none d-sm-none d-md-block">{{ __('site.send_contact_form') }}</button>
				<button type="submit" class="sendContactForm sendForm btn btn-primary d-md-none d-xs-block d-sm-block d-block">{{ __('site.send_contact_form') }}</button>
				<button style="display:none;" class="formSending btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;&nbsp;{{ __('site.sending_contact_form') }}</button>
				<button style="display:none;" type="button" class="formSent btn btn-success"><i class="far fa-check-circle"></i> {{ __('site.contact_form_sent') }}</button>
			</div>
		</div>
	</fieldset>
{!! Form::close() !!}
