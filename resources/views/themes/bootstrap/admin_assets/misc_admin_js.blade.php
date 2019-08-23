<script type="text/javascript">
    $(function(){
        $(this).on('click', '.pageLink', function(e) {
            e.preventDefault();
            if ($(this).attr('href') == 'javascript:;') return false;
            var slugLink = $(this).attr('href').split('/');
            $(this).attr('href', 'javascript:;');
            slugLink = slugLink[slugLink.length - 1];
            /* var slugNavBar = document.location.href.split('/')
            slugNavBar = slugNavBar[slugNavBar.length - 1]; */

            if (slugLink == $rootUrl.split('/')[$rootUrl.split('/').length - 1]) {
                slugLink = 'home';
            }
            /* if (slugLink == 'gallery')
                document.location.href = $rootUrl + "/gallery";
            if (slugNavBar == 'gallery' && slugLink == 'home')
                document.location.href = $rootUrl;
            else if (slugNavBar == 'gallery')
                document.location.href = $rootUrl + "/" + slugLink; */

            var data_string = 'page_name_show=' + slugLink;
            $.ajax({
                beforeSend: function() {
                    NProgress.configure({ showSpinner: false });
                    NProgress.start();
                },
                type: 'POST',
                url: $rootUrl + '/show_page_ajax',
                context: this,
                data: data_string,
                dataType: 'json',
                complete: function() {
                    NProgress.done();
                }
            }).done(function(data) {
                if (history.pushState) {
                    $('.navbar-collapse').collapse('hide');
                    var oldMenuOrder = $menuOrder;
                    $menuOrder = data.menuOrder;
                    var targetTitleLink = $(this).attr('title');
                    var originTitleLink = $('#menu').children('.nav-item.active').children().html();
                    var originSlug = $pageSlug;
                    $pageTitle = data.pageTitle;
                    $pageSlug = data.slug;
                    $(document).prop('title', data.pageTitle);
                    originSlug = originSlug.replace(/_/gi,'-');
                    $('#EditPage').find('input[name="slug"]').val(slugLink);
                    if (originSlug != 'home') {
                        $('#order_menu_new').children().eq(oldMenuOrder - 2).after('<option value="' + oldMenuOrder + '">' + originTitleLink + '</option>');
                    }
                    $('#order_menu_new').children('option[value="' + $menuOrder + '"]').remove();
                    $('#pageNameNew').val($('#EditPage').find('input[name="slug"]').val());
                    $('#summernote').summernote('reset');
                    if (slugLink != 'contact' && slugLink != 'gallery') {
                        $('#summernote').val(data.content);
                        $('.note-editable').empty().append(data.content);
                    } else {
                        $('#summernote').val(data.content['content']);
                        $('.note-editable').empty().append(data.content['content']);
                    }
                    $('#pageNameOld').val($('#pageNameNew').val());
                    $('.page_name_span').html(data.pageTitle);
                    if (originSlug == 'home' || originSlug == 'contact' || originSlug == 'gallery') {
                        if (originSlug == 'contact' || originSlug == 'gallery') {
                            $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '/' + originSlug + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                        } else {
                            $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                            if ($('.view_as_visitor').children().attr('class') == 'far fa-eye-slash') {
                                $('#view_as_visitor').after('<button style="display: none;" type="button" class="tooltipz change_menu_order mt-3 mb-3 page_option_icon ml-2 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __("site.change_pos_menu") }}" data-target="#ChangeOrderMenu"><i class="fas fa-exchange-alt"></i></button>');
                            } else {
                                $('#view_as_visitor').after('<button type="button" class="tooltipz change_menu_order mt-3 mb-3 page_option_icon ml-2 btn btn-secondary btn-sm" data-toggle="modal" title="{{ __("site.change_pos_menu") }}" data-target="#ChangeOrderMenu"><i class="fas fa-exchange-alt"></i></button>');
                            }
                        }
                        if (slugLink == 'home') {
                            $('.delete_page').remove();
                            $('.change_menu_order').remove();
                        } else if (slugLink == 'contact' || slugLink == 'gallery') {
                            $('.delete_page').remove();
                        } else {
                            $('.edit_page').after('<button type="button" class="tooltipz delete_page page_option_icon mt-1 mb-3 ml-2 float-right btn btn-danger btn-sm" title="{{ __("site.delete_page") }}"><i class="far fa-trash-alt"></i></button>');
                        }
                    } else {
                        $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '/' + originSlug + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                        if (slugLink == 'home') {
                            $('.delete_page').remove();
                            $('.change_menu_order').remove();
                        } else if (slugLink == 'contact' || slugLink == 'gallery') {
                            $('.delete_page').remove();
                        }
                    }
                    $(this).parent().replaceWith('<li class="nav-item active"><span class="nav-link">' + targetTitleLink + '</span></li>');

                    $('#content').parent().fadeOut(200, function(el) {
                        $(el).empty();
                        if ($('.alert-dismissible').length === 1) {
	                        $('.alert-dismissible').remove();
                        }
                        var speed = 300;
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
                        if (slugLink == 'contact') {
                            var isEmpty = $(data.content['content']).text();
                            if (isEmpty == '') {
                                $(el).append('<div style="display: none;" id="content" class="col-12">' + data.content['content'] + '</div><div id="contactForm" class="col-12">' + data.content['contactForm'] + '</div>').fadeIn(speed);
                            } else {
                                $(el).append('<div id="content" class="col-12">' + data.content['content'] + '</div><div id="contactForm" class="col-12">' + data.content['contactForm'] + '</div>').fadeIn(speed);
                            }
                        }
                        else if (slugLink == 'gallery') {
                            if ($isMobile) {
                                $(document).off('click', '.accept');
                            } else {
                                $(document).off('click', '.acceptEdit');
                                $(document).off('click', '.edit_image');
                            }
                            $(document).off('submit', '#modify_gal');
                            var isEmpty = $(data.content['content']).text();
                            if (isEmpty == '') {
                                $(el).append('<div style="display: none;" id="content" class="col-12">' + data.content['content'] + '</div><div id="galleriesWrapper" class="col-12">' + data.content['galleries'] + '</div>').fadeIn(speed);
                            } else {
                                $(el).append('<div id="content" class="col-12">' + data.content['content'] + '</div><div id="galleriesWrapper" class="col-12">' + data.content['galleries'] + '</div>').fadeIn(speed);
                            }
                            if ($('.view_as_visitor').children().attr('class') == 'far fa-eye-slash') {
                                $('.delete_image').hide();
                                $('.edit_image').hide();
                                $('#galleriesWrapper').children('div').slice(1).hide();
                            }
                            if ($isMobile) {
                                $('.thumb_resp2').off('contextmenu');
                                $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                                $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                            }
                        } else {
                            $(el).append('<div id="content" class="col-12">' + data.content + '</div>').fadeIn(speed);
                        }
                        if (slugLink == 'home') {
                            window.history.pushState(null, 'Title', $rootUrl);
                        } else {
                            window.history.pushState(null, 'Title', $rootUrl + "/" + slugLink);
                        }
                    });
                } else {
                    document.location.href = $rootUrl;
                }
            });
        });
    });
</script>
