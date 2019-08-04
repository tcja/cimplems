{{-- Edit page modal --}}
<div class="modal fade modalEditPage" id="EditPage" tabindex="-1" role="dialog" aria-labelledby="EditPage" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('site.edit_page_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => url('/abort'), 'id' => '', 'class' => '']) !!}
                    <input type="hidden" name="slug" value="{{ $pageSlug }}">
                    <textarea id="summernote" name="editordata">
                        @if ($pageSlug == 'contact' || $pageSlug == 'gallery')
                            {{ $content['content'] }}
                        @else
                            {{ $content }}
                        @endif
                    </textarea>
                {!! Form::close() !!}
            </div>
            <div class="noselect modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
                <button type="button" class="btn btn-primary btn-sm sendForm acceptPageEdit">{{ __('site.save_button') }}</button>
            </div>
        </div>
    </div>
</div>
{{-- Add new page modal --}}
<div class="modal modalReset fade modalAddPage" id="AddPage" tabindex="-1" role="dialog" aria-labelledby="AddPage" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div style="padding-top: 10px; padding-bottom: 10px;" class="modal-header">
                <h5 class="modal-title">{{ __('site.add_page_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['url' => url('/abort'), 'id' => 'create_page', 'class' => '']) !!}
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="pageName" class="col-6 col-md-4">{{ __('site.page_name_title') }}</label>
                        <div class="col-12 col-md-7" style="margin-top: -5px;{{-- {{ ($isMobile ? 'margin-left:-5px;' : 'margin-left:-35px;') }} --}}">
                            <input type="text" id="pageName" class="form-control form-control-sm" name="page_name" placeholder="...">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="orderList" class="col-6 col-md-4">{{ __('site.appearance_order') }}</label>
                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                            <select size="1" id="orderList" name="orderList" class="mt-1 form-control form-control-sm">
                                <option value="0">{{ __('site.appearance_order_after') }}</option>
                                @foreach ($pageLinks as $orderNumber => $page)
                                    @foreach ($page as $slug => $title)
                                        <option value="{{ $orderNumber }}">{{ $title }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div style="padding-top: 10px; padding-bottom: 10px;" class="noselect modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm sendForm acceptPageAdd">{{ __('site.add_button') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
{{-- Change menu order modal // ***&& $orderNumber != $menuOrder - 1*** --}}
<div class="modal modalReset fade modalChangeOrderMenu" id="ChangeOrderMenu" tabindex="-1" role="dialog" aria-labelledby="ChangeOrderMenu" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div style="padding-top: 10px; padding-bottom: 10px;" class="modal-header">
                <h5 class="modal-title">{{ __('site.change_appearance_order') }} <span class="page_name_span badge badge-dark">{{ $pageLinks[$menuOrder][$pageName] }}</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['url' => url('/abort'), 'id' => 'change_menu_order', 'class' => '']) !!}
                <div class="modal-body">
                    <input type="hidden" id="pageNameNew" name="page_name_menu" value="{{ $pageSlug }}">
                    <div class="form-group row">
                        <label for="order_menu_new" class="col-auto">{{ __('site.appearance_order') }}</label>
                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                            <select size="1" id="order_menu_new" name="order_menu_new" class="mt-1 form-control form-control-sm">
                                <option value="0">{{ __('site.in_the_menu_instead_of') }}</option>
                                @foreach ($pageLinks as $orderNumber => $page)
                                    @foreach ($page as $slug => $title)
                                        @if ($slug != $pageSlug && $slug != 'home')
                                            <option value="{{ $orderNumber }}">{{ $title }}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div style="padding-top: 10px; padding-bottom: 10px;" class="noselect modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm sendForm acceptMenuChange">{{ __('site.change_button') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
{{-- Change page name modal --}}
<div class="modal modalReset fade modalChangePageName" id="ChangePageName" tabindex="-1" role="dialog" aria-labelledby="ChangePageName" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div style="padding-top: 10px; padding-bottom: 10px;" class="modal-header">
                <h5 class="modal-title">{{ __('site.change_page_name') }} <span class="page_name_span badge badge-dark">{{ $pageLinks[$menuOrder][$pageName] }}</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['url' => url('/abort'), 'id' => 'change_page_name', 'class' => '']) !!}
                <div class="modal-body">
                    <input type="hidden" id="pageNameOld" name="page_name_old" value="{{ $pageSlug }}">
                    <div class="form-group row">
                        <label for="pageNameChangeNew" class="col-auto col-form-label">{{ __('site.new_name') }}</label>
                        <div class="col-12 col-md-7 mt-1" style="msargin-left: -45px;">
                            <input type="text" class="form-control form-control-sm" id="pageNameChangeNew" name="page_name_menu_change" placeholder="{{ __('site.of_the_page') }}">
                        </div>
                    </div>
                </div>
                <div style="padding-top: 10px; padding-bottom: 10px;" class="noselect modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('site.cancel_button') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm sendForm acceptMenuChange">{{ __('site.change_button') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
{{-- user config modal --}}
<div class="modal modalReset fade modalUser" id="User" tabindex="-1" role="dialog" aria-labelledby="User" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('site.configure_account') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div style="padding: 0;" class="card-header" id="headingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">{{ __('site.change_email') }}</button>
                            </h2>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                                {!! Form::open(['url' => url('/abort'), 'id' => 'changeUserEmailForm', 'class' => 'my-2']) !!}
                                    <div class="form-row">
                                        <label for="inputEmailUser" class="col-auto">{{ __('site.email') }}</label>
                                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                                            <input type="email" id="inputEmailUser" name="email_user" {{-- style="margin: auto; max-width: 90%;" --}} class="form-control">
                                            <input type="hidden" id="oldEmailUser" name="email_old_user">
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-2 d-none d-sm-block">
                                            <button style="margin-top: -5px;" class="sendForm btn btn-primary" type="submit">{{ __('site.change_button') }}</button>
                                            <button style="display:none; width: 215px; margin-top: -5px;" class="formSending btn btn-primary" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                {{ __('site.change_in_progress') }}
                                            </button>
                                            <button style="display:none; width: 220px; margin-top: -5px;" type="button" class="formSent btn btn-success"><i class="far fa-check-circle"></i> <b>{{ __('site.change_done') }}</b></button>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-2 d-block d-sm-none mt-3">
                                            <button style="margin-top: -5px;" class="sendForm btn btn-primary" type="submit">{{ __('site.change_button') }}</button>
                                            <button style="display:none; width: 215px; margin-top: -5px;" class="formSending btn btn-primary" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                {{ __('site.change_in_progress') }}
                                            </button>
                                            <button style="display:none; width: 220px; margin-top: -5px;" type="button" class="formSent btn btn-success"><i class="far fa-check-circle"></i> <b>{{ __('site.change_done') }}</b></button>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div style="padding: 0;" class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">{{ __('site.change_password') }}</button>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                            <div class="card-body">
                                {!! Form::open(['url' => url('/abort'), 'id' => 'changeUserPasswordForm', 'class' => 'my-2']) !!}
                                    <div class="form-row my-3">
                                        <label for="inputPasswordUser" class="col-12 col-md-3">{{ __('site.old_password') }}</label>
                                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                                            <input type="password" id="inputPasswordUser" name="old_password_user" style="margin: auto; max-width: 90%;" class="form-control" placeholder="{{ __('site.dots') }}">
                                        </div>
                                    </div>
                                    <div class="form-row my-3">
                                        <label for="inputNewPasswordUser" class="col-12 col-md-3">{{ __('site.new_password') }}</label>
                                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                                            <input type="password" id="inputNewPasswordUser" name="new_password_user" style="margin: auto; max-width: 90%;" class="form-control" placeholder="{{ __('site.dots') }}">
                                        </div>
                                    </div>
                                    <div class="form-row my-3">
                                        <label for="inputConfirmPasswordUser" class="col-12 col-md-3">{{ __('site.confirm_password') }}</label>
                                        <div class="col-12 col-md-7" style="margin-top: -5px;">
                                            <input type="password" id="inputConfirmPasswordUser" name="confirm_password_user" style="margin: auto; max-width: 90%;" class="form-control" placeholder="{{ __('site.dots') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-10 d-flex justify-content-end">
                                        <button style="margin-top: -5px;" class="sendForm btn btn-primary" type="submit">{{ __('site.change_button') }}</button>
                                        <button style="display:none; width: 215px; margin-top: -5px;" class="formSending btn btn-primary" type="button" disabled="">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('site.change_in_progress') }}
                                        </button>
                                        <button style="display:none; width: 220px; margin-top: -5px;" type="button" class="formSent btn btn-success"><i class="far fa-check-circle"></i> <b>{{ __('site.change_done') }}</b></button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="noselect modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">{{ __('site.close_button') }}</button>
            </div>
        </div>
    </div>
</div>
