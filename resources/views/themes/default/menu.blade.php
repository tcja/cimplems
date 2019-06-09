<div class="container">
	<div class="row">
		<div class="mt-2 mb-2 col-12 d-inline-flex">
            @if (session('admin') === true)
                <button type="button" style="top: 0; right: 0;" class="tooltipz view_as_visitor position-absolute page_option_icon mt-1 mr-2 btn btn-warning btn-sm" title="{{ __('site.view_as_visitor') }}"><i class="far fa-eye"></i></button>
				<div id="admin_menu" class="">
					@if ($currentSlug != 'home')
						<button type="button" class="tooltipz change_menu_order mt-3 mb-3 page_option_icon ml-1 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __('site.change_pos_menu') }}" data-target="#ChangeOrderMenu"><i class="fas fa-exchange-alt"></i></button>
					@endif
					<button type="button" class="tooltipz change_page_name mt-3 mb-3 page_option_icon ml-1 btn btn-info btn-sm" data-toggle="modal" title="{{ __('site.change_page_name_menu') }}" data-target="#ChangePageName"><i class="fas fa-pen-square"></i></button>
					<button type="button" class="tooltipz mt-3 mb-3 page_option_icon ml-1 btn btn-success btn-sm" data-toggle="modal" title="{{ __('site.create_page_menu') }}" data-target="#AddPage"><i class="far fa-file-alt"></i></button>
				</div>
			@endif
			<div id="menu" class="col-6">
				@foreach ($pageLinks as $links)
					@foreach ($links as $link => $title)
						@if ($link == $currentSlug)
							<span>{{ $title }}</span>
						@else
							<a href="{{ url(($link == 'home') ? '/' : str_replace('_', '-', $link)) }}" class="pageLink mr-1" title="{{ $title }}">{{ $title }}</a>
						@endif
					@endforeach
				@endforeach
			</div>
		</div>
	</div>
</div>
