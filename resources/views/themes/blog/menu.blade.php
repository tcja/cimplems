<nav class="navbar navbar-expand-lg navbar-light bg-light static-top mb-5 shadow">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('site.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            @if (session('admin') === true)
                    <div id="admin_menu" class="">
                        <div id="view_as_visitor" style="margin-top: -15px;" class="d-inline">
                            <button style="width: 33px;" type="button" class="tooltipz view_as_visitor page_option_icon mt-3 mb-3 btn btn-warning btn-sm" title="{{ __('site.view_as_visitor') }}"><i class="far fa-eye"></i></button>
                        </div>
                        @if ($currentSlug != 'home')
                            <button type="button" class="tooltipz change_menu_order mt-3 mb-3 page_option_icon ml-1 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __('site.change_pos_menu') }}" data-target="#ChangeOrderMenu"><i class="fas fa-exchange-alt"></i></button>
                        @endif
                        <button type="button" class="tooltipz change_page_name mt-3 mb-3 page_option_icon ml-1 btn btn-info btn-sm" data-toggle="modal" title="{{ __('site.change_page_name_menu') }}" data-target="#ChangePageName"><i class="fas fa-pen-square"></i></button>
                        <button type="button" class="tooltipz mt-3 mb-3 page_option_icon ml-1 btn btn-success btn-sm" data-toggle="modal" title="{{ __('site.create_page_menu') }}" data-target="#AddPage"><i class="far fa-file-alt"></i></button>
                    </div>
            @endif
            <ul id="menu" class="navbar-nav ml-auto">
                @foreach ($pageLinks as $links)
					@foreach ($links as $link => $title)
						@if ($link == $currentSlug)
                            <li class="nav-item active">
                                <span class="nav-link">{{ $title }}</span>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="pageLink nav-link" href="{{ url(($link == 'home') ? '/' : str_replace('_', '-', $link)) }}" title="{{ $title }}">{{ $title }}</a>
                            </li>
						@endif
					@endforeach
				@endforeach
            </ul>
        </div>
    </div>
</nav>
