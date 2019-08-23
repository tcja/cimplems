<script type="text/javascript">
    $(function(){
        var tools = [
                ['para', ['style']],
                ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontsize', ['fontname', 'fontsize']],
                ['color', ['forecolor', 'backcolor']],
                ['para2', ['ul', 'ol', 'paragraph']],
                ['insert', ['table', 'hr']],
                ['insert2', ['picture', 'link', 'video', 'map']],
                ['height', ['height']],
                ['misc', ['undo', 'redo', 'codeview', 'fullscreen', 'help']]
            ];
        if ($isMobile)
            tools = [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontname', 'fontsize']],
                ['color', ['color']],
                ['para2', ['ul', 'paragraph']],
                ['insert', ['table']],
                ['insert2', ['picture', 'link']],
                ['misc', ['undo', 'redo', 'codeview', 'fullscreen']]
            ];
        $('#summernote').summernote({
            lang: '{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}',
            //placeholder: 'Hello bootstrap 4',
            //airMode: true,
            /* codemirror: { // codemirror options
                //theme: 'monokai'
            }, */
            //dialogsFade: true,
            tabsize: 2,
            height: $isMobile ? screen.height - 430 : screen.height - 420,
            dialogsInBody: true,
            toolbar: tools,
            callbacks: {
                onImageUpload: function(image) {
                    $.canvasResize(image[0], {
                        width: 1280,
                        height: 0,
                        quality: 90,
                        callback: function(data) {
                            resizedImage = $.canvasResize('dataURLtoBlob', data);
                            var data = new FormData();
                            data.append('image', resizedImage);
                            data.append('image_name', image[0].name.split('.').slice(0, -1).join('.'));
                            $.ajax({
                                type: 'POST',
                                url: $rootUrl + '/upload_image',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: data,
                                dataType: 'json'
                            }).done(function(image_name) {
                                var image = $('<img>').attr({src: './storage/images_site/' + image_name, class: 'img-fluidR' });
                                $('#summernote').summernote("insertNode", image[0]);
                            }).fail(function(data){
                                console.log(data);
                            });
                        }
                    });
                },
                onMediaDelete : function(target) {
                     var data_string = 'image_name=' + target[0].src.split('/')[target[0].src.split('/').length - 1];
                     $.ajax({
                        type: 'POST',
                        url: $rootUrl + '/delete_site_image',
                        data: data_string,
                        dataType: 'json'
                    }).done(function(data) {
                    }).fail(function(data) {
                        console.log(data);
                    });
                }
            }
        });

        $(this).on('click', '.acceptPageEdit', function(e) {
            e.preventDefault();
            $('.sendForm').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: $rootUrl + '/edit_page',
                data: { content : $('#summernote').val(), slug : $('input[name="slug"]').val() },
                dataType: 'json',
                context: this
            }).done(function(content) {
                $('.modalEditPage').one('hidden.bs.modal', function (e) {
                    $('.sendForm').prop('disabled', false);
                    $('#content').empty();
                    if ($.trim($('.note-editable').text()) == '' && !/(img|iframe|canvas)/i.test($('.note-editable').html())) {
                        $('#content').hide();
                    } else {
                        $('#content').show();
                    }
                    $('#content').append(content);
                    $('.toast-body').html('{{ __("site.page_changed") }}');
                    $('.toast').toast('show');
                });
                $('.modalEditPage').modal('hide');
            });
        });

        $('#change_menu_order').validate({
            rules: {
                order_menu_new: {
                    required: true,
                    selectCheck: true
                }
            },
            submitHandler: function(form) {
                $('.sendForm').prop('disabled', true);
                var data_string = 'page_name_menu=' + $('#pageNameNew').val() + '&order_menu_new=' + $('#order_menu_new').val();
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/change_menu_order',
                    data: data_string,
                    dataType: 'json'
                }).done(function(data) {
                    var replace = $('#menu').children('.nav-item.active').clone();
                    $('#menu').children('.nav-item.active').remove();
                    $('#menu').children().eq(data.menuOrder-2).after(replace);

                    var updatedMenu = [];
                    if ($menuOrder - data.menuOrder < 0) {
                        $('#order_menu_new option').each(function(i, el) {
                            var menuNumber = parseInt(el.value);
                            if (menuNumber == 0 || menuNumber == 1 || menuNumber < $menuOrder + 1 || menuNumber > data.menuOrder) {
                            } else {
                                updatedMenu.push([menuNumber - 1, el.text]);
                                el.remove();
                            }
                        });
                        var finalUpdate = '';
                        for (var i = 0; i < updatedMenu.length; i++) {
                            finalUpdate += '<option value="' + updatedMenu[i][0] + '">' + updatedMenu[i][1] + '</option>';
                        }
                        $('#order_menu_new').children().eq($menuOrder - 2).after(finalUpdate);
                    } else {
                        $('#order_menu_new option').each(function(i, el) {
                            var menuNumber = parseInt(el.value);
                            if (menuNumber == 0 || menuNumber == 1 || menuNumber > $menuOrder || menuNumber < data.menuOrder) {
                            } else {
                                updatedMenu.push([menuNumber + 1, el.text]);
                                el.remove();
                            }
                        });
                        var finalUpdate = '';
                        for (var i = 0; i < updatedMenu.length; i++) {
                            finalUpdate += '<option value="' + updatedMenu[i][0] + '">' + updatedMenu[i][1] + '</option>';
                        }
                        $('#order_menu_new').children().eq(data.menuOrder - 2).after(finalUpdate);
                    }
                    $menuOrder = data.menuOrder;
                    var orderMenuNew = $('#order_menu_new').children('option[value!="0"]').clone();
                    var home = $('#orderList').children('option[value="1"]').clone();
                    $('#orderList').children('option[value!="0"]').remove();
                    $('#orderList').append(home);
                    $('#orderList').append(orderMenuNew);
                    $('#orderList').children().eq(data.menuOrder - 1).after('<option value="' + data.menuOrder + '">' + data.menu_name + '</option>');

                    $('.modalChangeOrderMenu').one('hidden.bs.modal', function (e) {
                        $('#order_menu_new').val(0);
                        $('.sendForm').prop('disabled', false);
                        $('.toast-body').html('{{ __("site.menu_updated") }}');
                        $('.toast').toast('show');
                    });
                    $('.modalChangeOrderMenu').modal('hide');
                });
            }
        });

        $('#create_page').validate({
            rules: {
                page_name: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    minlength: 3,
                    maxlength: 20
                },
                orderList: {
                    required: true,
                    selectCheck: true
                }
            },
            submitHandler: function(form) {
                $('.sendForm').prop('disabled', true);
                var data_string = 'page_name=' + $('#pageName').val() + '&afterMenu=' + $('#orderList').val();
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/add_page',
                    data: data_string,
                    dataType: 'json'
                }).done(function(data) {
                    $('#menu').children().eq(data.menuOrder-2).after('<li class="nav-item"><a href="' + $rootUrl + '/' + data.page_link + '" class="pageLink nav-link" title="' + data.page_name + '">' + data.page_name + '</a></li>');
                    var updatedMenu = [];
                    $('#orderList option').each(function(i, el) {
                        var menuNumber = parseInt(el.value);
                        if (menuNumber == 0 || menuNumber == 1 || menuNumber < data.menuOrder) {
                        } else {
                            updatedMenu.push([menuNumber + 1, el.text]);
                            el.remove();
                        }
                    });
                    updatedMenu.push([data.menuOrder, data.page_name]);
                    updatedMenu.sort(function(a, b){return a[0]-b[0]});
                    var finalUpdate = '';
                    for (var i = 0; i < updatedMenu.length; i++) {
                        finalUpdate += '<option value="' + updatedMenu[i][0] + '">' + updatedMenu[i][1] + '</option>';
                    }
                    $('#orderList').append(finalUpdate);
                    $('#orderList').val(0);
                    var orderList = $('#orderList').children('option[value!="0"]').clone();
                    $('#order_menu_new').children().remove();
                    $('#order_menu_new').append(orderList);
                    $('#order_menu_new').children('option[value="1"]').remove();
                    $('#order_menu_new').children('option[value="' + $menuOrder + '"]').remove();
                    $('#order_menu_new').prepend('<option value="0">{{ __("site.in_the_menu_instead_of") }}</option>');
                    $('#order_menu_new').val(0);
                    $('.modalAddPage').one('hidden.bs.modal', function (e) {
                        $('#pageName').val('');
                        $('.sendForm').prop('disabled', false);
                        $('.toast-body').html('{{ __("site.page_created") }}');
                        $('.toast').toast('show');
                    });
                    $('.modalAddPage').modal('hide');
                });
            }
        });

        $('#change_page_name').validate({
            rules: {
                page_name_menu_change: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    minlength: 3,
                    maxlength: 20
                }
            },
            submitHandler: function(form) {
                $('.sendForm').prop('disabled', true);
                var data_string = 'page_name_menu_change=' + $('#pageNameChangeNew').val() + '&page_name_old=' + $('#pageNameOld').val();
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/change_page_name',
                    data: data_string,
                    dataType: 'json'
                }).done(function(data) {
                    if (history.pushState) {
                        $(document).prop('title', data.new_page_title);
                        $('#menu').children('.nav-item.active').children().html(data.new_page_title);
                        $('#orderList').children('option[value="' + $menuOrder + '"]').html(data.new_page_title);
                        $('#EditPage').find('input[name="slug"]').val(data.new_page_slug);
                        if (data.new_page_slug == 'home') {
                            window.history.pushState(null, 'Title', $rootUrl + "/");
                        } else {
                            window.history.pushState(null, 'Title', $rootUrl + "/" + data.new_page_slug);
                        }
                        $pageSlug = data.new_page_slug;
                        $pageTitle = data.new_page_title;
                        $('.modalChangePageName').one('hidden.bs.modal', function (e) {
                            $('.sendForm').prop('disabled', false);
                            $('#pageNameChangeNew').val('');
                            $('#pageNameOld').val(data.new_page_slug);
                            $('#pageNameNew').val(data.new_page_slug);
                            $('.page_name_span').html(data.new_page_title);
                            $('.toast-body').html('{{ __("site.page_name_changed") }}');
                            $('.toast').toast('show');
                        });
                        $('.modalChangePageName').modal('hide');
                    } else {
                        document.location.href = $rootUrl + "/" + data.new_page_slug;
                    }
                });
            }
        });

        $(this).on('click', '.delete_page', function(e) {
            e.preventDefault();
            var options = {
                messageHeader: '{{ __("site.delete_page_modal") }} : ',
                modalBoxWidth: '405px'
            };
            $.confirmModal('{{ __("site.delete_page_message_modal") }}', options, function(obj) {
                var slug = $('#EditPage').find('input[name="slug"]').val();
                var array_images = [];
                $('.note-editable').find('.img-fluidR').each(function(i, el) {
                    array_images[i] = $(el).attr('src').split('/')[$(el).attr('src').split('/').length-1];
                });
                var data = { page_name_delete: slug, array_images: array_images };
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/delete_page',
                    context: obj,
                    data: data,
                    dataType: 'json'
                }).done(function(data) {
                    if (history.pushState) {
                        $menuOrder = 1;
                        $pageSlug = 'home'
                        $(document).prop('title', data.home_page_name);
                        $('#EditPage').find('input[name="slug"]').val('home');
                        $('#summernote').summernote('reset');
                        $('#summernote').val(data.content);
                        $('.note-editable').empty().append(data.content);
                        $('.delete_page').remove();
                        $('.change_menu_order').remove();
                        $('#menu').children('.nav-item.active').remove();
                        $('#menu').find('a[href="' + $rootUrl + '"]').parent().addClass('active');
                        $('#menu').find('a[href="' + $rootUrl + '"]').replaceWith('<span class="nav-link">'+$('#menu').find('a[href="' + $rootUrl + '"]').attr('title')+'</span>');
                        $('#orderList').find('option[value="' + data.menuOrder + '"]').remove();
                        $('.page_name_span').html(data.home_page_name);
                        $('#pageNameOld').val('home');
                        $(data.menu_update).each(function(i, el) {
                            $('#orderList').find('option[value="' + el + '"]').val(el - 1)
                        });
                        $(data.menu_update).each(function(i, el) {
                            $('#order_menu_new').find('option[value="' + el + '"]').val(el - 1)
                        });
                        $('#content').fadeOut(100, function(el) {
                            $(el).empty()
                            if (data.publishState === 1) {
                                $('.publish').replaceWith('<input class="publish" type="checkbox" checked="checked">');
                                if ($pageSlug == 'home') {
                                    $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_private") }}');
                                } else {
                                    $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_private") }}');
                                }
                            } else {
                                $('.publish').replaceWith('<input class="publish" type="checkbox">');
                                if ($pageSlug == 'home') {
                                    $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_public") }}');
                                } else {
                                    $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_public") }}');
                                }
                            }
                            $('#content').append(data.content).hide().fadeIn(300);
                            window.history.pushState(null, 'Title', $rootUrl + "/");
                            $('.toast-body').html('{{ __("site.page_deleted") }}');
                            $('.toast').toast('show');
                        });
                    } else {
                        document.location.href = $rootUrl;
                    }
                }).fail(function(data) {
                    alert('{{ __("site.request_failed") }}');
                });
            });
            if ($('.modalConfirm').find('.modal-title > span').length == 0) {
                $('.modalConfirm').find('.modal-title').append('<span class="page_name_span badge badge-dark">' + $pageTitle + '</span>');
            } else {
                $('.modalConfirm').find('.modal-title > span').html($pageTitle);
            }
        });

        $(this).on('click', '.publish', function(e) {
            if ($(this).attr('checked') === undefined) {
                $(this).attr('checked', 'checked');
                if ($pageSlug == 'home') {
                    $(this).parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_private") }}');
                } else {
                    $(this).parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_private") }}');
                }
                var state = 1;
            } else {
                $(this).removeAttr('checked');
                if ($pageSlug == 'home') {
                    $(this).parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_public") }}');
                } else {
                    $(this).parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_public") }}');
                }
                var state = 0;
            }
            var data_string = 'page_name=' + $pageSlug + '&page_state=' + state;
            $.ajax({
                type: 'POST',
                url: $rootUrl + '/change_page_state',
                context: this,
                data: data_string,
                dataType: 'json'
            }).done(function(data) {
                if (data) {
                    if ($pageSlug == 'home') {
                        $('.toast-body').html('{{ __("site.site_public") }}');
                    } else {
                        $('.toast-body').html('{{ __("site.page_public") }}');
                    }
                    $('.toast').toast('show');
                } else {
                    if ($pageSlug == 'home') {
                        $('.toast-body').html('{{ __("site.site_private") }}');
                    } else {
                        $('.toast-body').html('{{ __("site.page_private") }}');
                    }
                    $('.toast').toast('show');
                }
            });
        });

        $(this).on('click', '.view_as_visitor', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true);
            if ($pageSlug == 'home' && $('.publish').attr('checked') === undefined) {
                if ($('.button_visitor_warning').length === 0) {
                    $.ajax({
                        type: 'GET',
                        url: $rootUrl + '/list_private_pages',
                        dataType: 'json',
                        context: this
                    }).done(function(data) {
                        $(data).each(function(i, el) {
                            if (el == $('#menu').find('a[title="' + el + '"]').html()) {
                                $('#menu').find('a[title="' + el + '"]').parent().fadeOut();
                            }
                        });
                        $('#admin_menu > :not(#view_as_visitor)').fadeOut(250);
                        $('#page_admin_menu').fadeOut();
                        $('.configure_user').fadeOut();
                        $('.view_as_visitor').after('<button type="button" style="cursor: default; height: 30px; display:none;" class="tooltipz button_visitor_warning mt-3 mb-3 ml-2 mr-1 btn btn-danger btn-sm" title="" data-original-title="{{ __("site.site_in_constr_warning_label") }}"><i class="fas fa-lock"></i>&nbsp;&nbsp;{{ __("site.site_in_constr_warning_title") }}</button>');
                        $.timer(260, function(){
                            $('.button_visitor_warning').fadeIn(200);
                        });
                        $(this).prop('disabled', false);
                        $(this).attr('title', '').attr('data-original-title', '{{ __("site.view_as_admin") }}');
                        $(this).children().removeClass('fa-eye').addClass('fa-eye-slash');
                    });
                } else {
                    $('#menu').children().each(function(i, el) {
                        if ($(el).css('opacity') == 0) {
                            $(el).fadeIn(700);
                        }
                    });
                    $('#admin_menu > :not(#view_as_visitor)').fadeIn();
                    $('#page_admin_menu').fadeIn();
                    $('.configure_user').fadeIn();
                    $('.button_visitor_warning').remove();
                    $(this).prop('disabled', false);
                    $(this).attr('title', '').attr('data-original-title', '{{ __("site.view_as_visitor") }}');
                    $(this).children().removeClass('fa-eye-slash').addClass('fa-eye');
                }
            } else {
                if ($('.change_page_name').css('opacity') == 1) {
                    $.ajax({
                        type: 'GET',
                        url: $rootUrl + '/list_private_pages',
                        dataType: 'json',
                        context: this
                    }).done(function(privatePages) {
                        $(this).prop('disabled', false);
                        $(this).attr('title', '').attr('data-original-title', '{{ __("site.view_as_admin") }}');
                        $(this).children().removeClass('fa-eye').addClass('fa-eye-slash');
                        if ($('.publish').attr('checked') === undefined) {
                            $(privatePages).each(function(i, el) {
                                if (el == $('#menu').find('a[title="' + el + '"]').html() && el != $('#menu').children().first().children().html()) {
                                    $('#menu').find('a[title="' + el + '"]').parent().fadeOut();
                                } else {
                                    $('#menu').find('span').parent().fadeOut();
                                }
                            });
                        } else {
                            $(privatePages).each(function(i, el) {
                                if (el == $('#menu').find('a[title="' + el + '"]').html() && el != $('#menu').children().first().children().html()) {
                                    $('#menu').find('a[title="' + el + '"]').parent().fadeOut();
                                }
                            });
                        }
                        $('#admin_menu > :not(#view_as_visitor)').fadeOut(250);
                        $('#page_admin_menu').fadeOut();
                        $('.configure_user').fadeOut();
                        if ($('#submit_img').length === 1) {
                            $('.delete_image').fadeOut();
                            $('.edit_image').fadeOut();
                            $('#galeries').find('.edit_image_form').each(function(i, el) {
                                if ($(el).css('display') === 'block') {
                                    $(el).attr('show', 'show');
                                    $(el).fadeOut();
                                }
                            });
                            $('#galleriesWrapper').children('div').slice(1).fadeOut();
                        }
                        if ($('.publish').attr('checked') === undefined) {
                            $.ajax({
                                type: 'GET',
                                url: $rootUrl + '/show_home_page',
                                dataType: 'json',
                                context: this
                            }).done(function(homePage) {
                                $('.delete_page').remove();
                                $('.change_menu_order').remove();
                                $pageSlug = 'home';
                                $('#content').fadeOut(100, function(content) {
                                    $(content).empty();
                                    $(content).next().remove();
                                    if (homePage.publishState === 1) {
                                        $('.publish').replaceWith('<input class="publish" type="checkbox" checked="checked">');
                                        if ($pageSlug == 'home') {
                                            $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_private") }}');
                                        } else {
                                            $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_private") }}');
                                        }
                                    } else {
                                        $('.publish').replaceWith('<input class="publish" type="checkbox">');
                                        $('.view_as_visitor').after('<button type="button" style="cursor: default; height: 30px; display:none;" class="tooltipz button_visitor_warning mt-3 mb-3 ml-2 mr-1 btn btn-danger btn-sm" title="" data-original-title="{{ __("site.site_in_constr_warning_label") }}"><i class="fas fa-lock"></i>&nbsp;&nbsp;{{ __("site.site_in_constr_warning_title") }}</button>');
                                        $.timer(260, function(){
                                            $('.button_visitor_warning').fadeIn(200);
                                        });
                                        if ($pageSlug == 'home') {
                                            $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_site_in_public") }}');
                                        } else {
                                            $('.publish').parent().parent().attr('title', '').attr('data-original-title', '{{ __("site.put_in_public") }}');
                                        }
                                    }
                                    $('#content').append(homePage.content).hide().fadeIn(300);
                                    var titleLink = $('#menu').find('span').html();
                                    var slugLink = window.location.href.split('/');
                                    slugLink = slugLink[slugLink.length - 1];
                                    $('#menu').find('span').parent().removeClass('active');
                                    $('#menu').find('span').replaceWith('<a href="' + $rootUrl + '/' + slugLink + '" class="pageLink nav-link" title="' + titleLink + '">' + titleLink + '</a>');
                                    $('#menu').children().first().addClass('active').children().replaceWith('<span class="nav-link">' + $('#menu').children().first().children().html() + '</span>');
                                    window.history.pushState(null, 'Title', $rootUrl + "/");
                                    $(document).prop('title', $('#menu').find('span').html());
                                });
                            });
                        } else {
                            $.ajax({
                                type: 'GET',
                                url: $rootUrl + '/show_home_page',
                                dataType: 'json',
                                context: this
                            }).done(function(data) {
                                $(data).each(function(i, el) {
                                    if (el == $('#menu').find('a[title="' + el + '"]').html()) {
                                        $('#menu').find('a[title="' + el + '"]').parent().fadeOut();
                                    }
                                });
                                if (data.publishState === 0) {
                                    $('.view_as_visitor').after('<button type="button" style="cursor: default; height: 30px; display:none;" class="tooltipz button_visitor_warning mt-3 mb-3 ml-2 mr-1 btn btn-danger btn-sm" title="" data-original-title="{{ __("site.site_in_constr_warning_label") }}"><i class="fas fa-lock"></i>&nbsp;&nbsp;{{ __("site.site_in_constr_warning_title") }}</button>');
                                    $.timer(260, function(){
                                        $('.button_visitor_warning').fadeIn(200);
                                    });
                                } else {
                                    $('.button_visitor_warning').remove();
                                }
                            });
                        }
                    });
                } else {
                    $(this).prop('disabled', false);
                    $(this).attr('title', '').attr('data-original-title', '{{ __("site.view_as_visitor") }}');
                    $(this).children().removeClass('fa-eye-slash').addClass('fa-eye');
                    $('#menu').children().each(function(i, el) {
                        if ($(el).css('opacity') == 0) {
                            $(el).fadeIn(700);
                        }
                    });
                    $('#admin_menu > :not(#view_as_visitor)').fadeIn();
                    $('#page_admin_menu').fadeIn();
                    $('.configure_user').fadeIn();
                    $('.button_visitor_warning').remove();
                    if ($('#submit_img').length === 1) {
                        $('.delete_image').fadeIn(700);
                        $('.edit_image').fadeIn(700);
                        $('#galeries').find('.edit_image_form').each(function(i, el) {
                            if ($(el).attr('show') === 'show') {
                                $(el).fadeIn(700);
                            }
                        });
                        $('#galleriesWrapper').children('div').slice(1).fadeIn(700);
                    }
                }
            }
        });
    });
</script>
