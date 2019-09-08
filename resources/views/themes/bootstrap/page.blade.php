<div class="container my-3">
    @if (!empty(session('successMessage')))
        {!! session('successMessage') !!}
    @endif
    @if (session('admin') === true)
        <div class="row">
            <div id="page_admin_menu" class="col-12">
                @if ($pageSlug == 'home')
                    <div style="margin-top: 0.6rem" class="tooltipz d-inline-flex float-right ml-2 form-check checkbox-slider--b" title="{{ $publishState ? __('site.put_site_in_private') : __('site.put_site_in_public') }}">
                        <label>
                            <input class="publish" type="checkbox" {{ $publishState ? 'checked=checked' : '' }}><span></span>
                        </label>
                    </div>
                @else
                    <div style="margin-top: 0.6rem" class="tooltipz d-inline-flex float-right ml-2 form-check checkbox-slider--b" title="{{ $publishState ? __('site.put_in_private') : __('site.put_in_public') }}">
                        <label>
                            <input class="publish" type="checkbox" {{ $publishState ? 'checked=checked' : '' }}><span></span>
                        </label>
                    </div>
                @endif
                <button type="button" class="tooltipz edit_page mt-1 mb-1 page_option_icon ml-2 float-right btn btn-primary btn-sm" data-toggle="modal" title="{{ __('site.edit_page') }}" data-target="#EditPage"><i class="far fa-edit"></i></button>
                @if ($pageSlug != 'home' && $pageSlug != 'contact' && $pageSlug != 'gallery')
                    <button type="button" class="tooltipz delete_page page_option_icon mt-1 mb-1 ml-2 float-right btn btn-danger btn-sm" title="{{ __('site.delete_page') }}"><i class="far fa-trash-alt"></i></button>
                @endif
            </div>
        </div>
    @endif
    <div class="row">
        @if ($pageSlug == 'contact' || $pageSlug == 'gallery')
            @php $display = $content['content'] @endphp
        @else
            @php $display = $content @endphp
        @endif
        <div style="{{-- word-break: break-all; --}} {{ $display == '' || $display == '<p><br></p>' ? 'display: none;' : '' }}"	id="content" class="col-12">
            {!! $display !!}
        </div>
        @if ($pageSlug == 'contact')
            <div id="contactForm" class="col-12">
                {!! $content['contactForm'] !!}
            </div>
        @elseif ($pageSlug == 'gallery')
            <div id="galleriesWrapper" class="col-12">
                {!! $content['galleries'] !!}
            </div>
        @endif
    </div>
    @include('assets/page_js')
    @if (session('admin') === true)
        @include(config('site.theme_dir') . config('site.theme') . '/' . 'admin_assets/page_js')
    @endif
</div>
