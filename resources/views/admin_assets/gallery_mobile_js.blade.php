<script type="text/javascript">
    $(function(){
        $(this).off('click', '.delete_image_mobile');
        $(this).on('click', '.delete_image_mobile', function(e) {
            e.preventDefault();
            $(this).closest('div.flex-wrap').children().each(function(key, val) { $(val).removeAttr('id'); });
            var options = {
                messageHeader: '{{ __("site.delete_image_modal") }}',
                backgroundBlur: ['.edit_image_mobile .modal-content']
            };
            $.confirmModal('{{ __("site.delete_image_message_modal") }}', options, function(obj) {
                var image_name = $(obj).parents('div.row').prev().children().find('input[name="photo_name"]').attr('value');
                var gallery_name = $(obj).parents('div.thumbs_resp').parent().parent().attr('id');
                var gal = '#' + gallery_name + ' .thumb_resp';
                var total_photos_gal = $(gal).length;
                var total_galleries = $('#galleries').children().length;
                var page_now = ($(obj).closest('div.flex-wrap').next().length) ? $(obj).closest('div.flex-wrap').next().children().children('.active').children().html() : 1;
                var data_string = 'file=' + image_name + '&galleryID=' + gallery_name.replace(/[a-z]+/gi, '') + '&page=' + page_now;
                $('.delete_image_mobile').prop('disabled', true);
                $('.accept').prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/delete_image',
                    context: $(obj).parents('div.thumbs_resp'),
                    data: data_string
                }).done(function(data) {
                    $(this).children('div.edit_image_mobile').modal('hide');
                    $(this).children('div.edit_image_mobile').on('hidden.bs.modal', function () {
                        $(this).parent().fadeOut(600, function(el) {
                            if (!data[0][0]) {
                                if (total_galleries == 1) {
                                    $('#' + gallery_name).remove();
                                    $('#galleries').append('<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __("site.no_image") }}</h2>');
                                } else {
                                    $('#' + gallery_name).remove();
                                }
                            } else {
                                var last_page = data[0]['galleryInfos'].paginator.last_page;
                                var page = ($(el).closest('.flex-wrap').next().children().children('.active').children().html() == undefined) ? 1 : $(el).closest('.flex-wrap').next().children().children('.active').children().html();
                                var paginator = data[0]['galleryInfos'].paginatorHTML;
                                if (total_photos_gal == 1) {
                                    $(el).closest('.flex-wrap').next().children().children('.active').prev().children().trigger('click');
                                    $(document).ajaxComplete(function(event, request) {
                                        $(el).parent().children().fadeIn(500);
                                        $(document).off(event);
                                    });
                                } else if (total_photos_gal == 2 && page == last_page) {
                                    $(el).remove();
                                } else if (page == last_page && total_photos_gal != Object.keys(data[0]).length - 1) {
                                    $(el).closest('.flex-wrap').next().replaceWith(paginator);
                                    $(el).remove();
                                } else {
                                    $(el).children('.edit_image_mobile').remove();
                                    var thumbnail = $(el).clone();
                                    var last_image = data[0][Object.keys(data[0]).length - 2];

                                    thumbnail.children('.phswipe').children().attr('src', thumbnail.children('.phswipe').children().attr('src').split('/min/')[0] + '/min/' + last_image.fileName);
                                    thumbnail.children('.phswipe').children().attr('alt', last_image.fileName);
                                    thumbnail.children('.phswipe').children().attr('timestamp', last_image.timestamp);
                                    thumbnail.children('.phswipe').attr('href', thumbnail.children('.phswipe').attr('href').split('/big/')[0] + '/big/' + last_image.fileName);
                                    thumbnail.children('.phswipe').attr('title', last_image.title);
                                    thumbnail.children('.phswipe').attr('data-med', thumbnail.children('.phswipe').attr('href').split('/big/')[0] + '/big/' + last_image.fileName);
                                    thumbnail.children('.phswipe').attr('alt', last_image.fileName);

                                    thumbnail.appendTo($(el).parent()).fadeIn(500);

                                    $(el).closest('.flex-wrap').next().replaceWith(paginator);
                                    $(el).remove();
                                }
                            }
                            $('.sendForm').prop('disabled', false);
                            $('.delete_image_mobile').prop('disabled', false);
                            $('.accept').prop('disabled', false);
                            $('.thumb_resp2').off('contextmenu');
                            $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                            $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                            $('.phswipe').off('taphold');
                            $('.phswipe').on('taphold', $tapz);
                        });
                    });
                }).fail(function() {
                    alert('{{ __("site.request_failed") }}');
                });
            });
        });

        $.event.special.tap.tapholdThreshold = 500;
        $tapz = function tapEditImage()
        {
            if ($(this).next().length == 0) {
                var data_string = 'image_name=' + $(this).attr('alt');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: $rootUrl + '/edit_image_show_form',
                    data: data_string,
                    context: this
                }).done(function(data) {
                    $(this).parent().append(data);
                    $(this).parent().find('.modal').modal('show');
                });
            } else {
                if ($(this).next().css('display') == 'none') {
                    $(this).next().modal('show');
                } else {
                    $(this).next().modal('hide');
                }
            }
        }
        $('.phswipe').on('taphold', $tapz);

        $(document).on('keypress', '.edit_image_mobile', function(e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode == '13') {
                $('.accept').trigger('click');
            }
        });

        $(this).on('click', '.accept', function(e) {
            e.preventDefault();
            $(this).parent().prev().children().validate({
                rules: {
                    change_title: {
                        maxlength: 80,
                    },
                    gallery: {
                        selectCheck: true
                    }
                },
                submitHandler: function(form) {
                    var title = $(form).children().find('input[name="change_title"]').val();
                    var gallery = $(form).children().find('select[name="gallery"]').val();

                    if ($(form).find('input[name="change_title"]')[0].defaultValue == title && $(form).find('input[name="gallery_default"]').val() == gallery) {
                        $(form).parents('.modal').modal('hide');
                        return false;
                    }

                    var onlyTitleChanged = 0;
                    if ($(form).find('input[name="change_title"]')[0].defaultValue != title && $(form).find('input[name="gallery_default"]').val() == gallery) {
                        onlyTitleChanged = 1;
                    }
                    var name = $(form).children().find('input[name="photo_name"]').val();
                    var page = $(form).closest('div.flex-wrap').next().children().find('.active').children().length ? $(form).closest('div.flex-wrap').next().children().find('.active').children().html() : '';
                    var form_data = 'change_title=' + title + '&gallery=' + gallery + '&photo_name=' + name + '&modify_one_image=' + onlyTitleChanged + '&page=' + page;
                    $('.accept').prop('disabled', true);
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: form_data,
                        url: $rootUrl + '/edit_image',
                        context: form
                    }).done(function(data) {
                        $(this).parent().parent().parent().parent().modal('hide');
                        $(this).parent().parent().parent().parent().on('hidden.bs.modal', function () {
                            $(this).parent().fadeOut(350, function(el) {
                                $('.accept').prop('disabled', false);
                                if (onlyTitleChanged) {
                                    $('#galleries a[alt="' + data.name + '"]').attr('title', data.title);
                                    $('#galleries a[alt="' + data.name + '"]').next().find('input[name="change_title"]')[0].defaultValue = escapeHtml($('<div/>').html(data.title).text());
                                    $(el).fadeIn(500);
                                    $('.thumb_resp2').off('contextmenu');
                                    $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                                    $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                                    $('.phswipe').off('taphold');
                                    $('.phswipe').on('taphold', $tapz);
                                    $(el).children('div.edit_image_mobile').off('hidden.bs.modal');
                                    return false;
                                }
                                var imgf = data.name.split('.').join('').split('-').join('').split('_').join('');
                                var gallery_nbr = data.galleryID;
                                var gallery_name = $(form).children().find('select[name="gallery"]').find(':selected').html();
                                var gal_id = 'gallery' + gallery_nbr;
                                var gal_find = '#gallery' + gallery_nbr;
                                var gal_exist = $(document.body).find(gal_find).length;
                                var current_gal_images = $(el).parents('.flex-wrap').children('div').length;
                                var gal_remove = $(el).parents('.flex-wrap').parent();
                                var done = false;
                                if (data.paginatorHTML) {
                                    if ($('div[id="' + gal_id + '"]').find('ul .active').children().html() > 1) {
                                        $('div[id="' + gal_id + '"]').find('ul').children().first().next().children().trigger('click');
                                        $(document).ajaxComplete(function(event, request) {
                                            $('div[id="' + gal_id + '"]').children().fadeIn(500);
                                            $(document).off(event);
                                        });
                                        $(el).remove();
                                        done = true;
                                    } else {
                                        $('div[id="' + gal_id + '"]').children('div.flex-wrap').children().last().fadeOut(500, function(el) {
                                            $(el).remove();
                                        });

                                        if ($('div[id="' + gal_id + '"]').find('ul').parent().length) {
                                            $('div[id="' + gal_id + '"]').find('ul').parent().replaceWith(data.paginatorHTML);
                                        } else {
                                            $('div[id="' + gal_id + '"]').append(data.paginatorHTML);
                                        }
                                        if ($(gal_remove).find('ul').parent().length) {
                                            $(gal_remove).find('ul').parent().replaceWith(data.old_paginatorHTML);
                                        } else {
                                            $(gal_remove).append(data.old_paginatorHTML);
                                        }
                                    }
                                }
                                if (data.old_paginatorHTML == '') {
                                    $(gal_remove).find('ul').parent().replaceWith('');
                                }

                                $(el).remove();
                                var gal_origin = gal_remove.attr('id').replace(/[^0-9]/gi,'');
                                if (gal_exist == 1 || gal_origin == gallery_nbr) {
                                    if (current_gal_images == 1) {
                                        if (gal_origin != gallery_nbr) {
                                            if ($(gal_remove).find('ul').length) {
                                                $(gal_remove).find('ul').children().first().next().children().trigger('click');
                                                $(document).ajaxComplete(function(event, request) {
                                                    $(gal_remove).children().fadeIn(500);
                                                    $(document).off(event);
                                                });
                                            } else {
                                                gal_remove.remove();
                                            }
                                        }
                                    }
                                    $('#gallery' + data.galleryID).children('div.flex-wrap').prepend('<div id="' + imgf + '" style="display:none;" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" rel="external" title="' + escapeHtml($('<div/>').html(data.title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" data-med-size="1280x960" alt="' + data.name + '" class="phswipe"><img alt="" src="' + $rootUrl + '/storage/images_gallery/min/' + data.name + '" class="thumb_resp2 img-fluid"></a></div>');
                                    $('div[id="' + imgf + '"]').fadeIn(500);
                                    if (data.old_new_image) {
                                        $(gal_remove).children('div.flex-wrap').append('<div style="display:nonse;" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(data.old_new_image.title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" data-med-size="1280x960" alt="' + data.old_new_image.fileName + '" class="phswipe"><img alt="" src="' + $rootUrl + '/storage/images_gallery/min/' + data.old_new_image.fileName + '" class="thumb_resp2 img-fluid"></a></div>')/* .fadeIn(500) */;
                                    }
                                } else {
                                    if (current_gal_images == 1) {
                                        if ($(gal_remove).find('ul').length) {
                                            $(gal_remove).find('ul').children().first().next().children().trigger('click');
                                            $(document).ajaxComplete(function(event, request) {
                                                $(gal_remove).children().fadeIn(500);
                                                $(document).off(event);
                                            });
                                        } else {
                                            gal_remove.remove();
                                        }
                                    }
                                    if (!done) {
                                        var html = '<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2>' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" rel="external" title="' + escapeHtml($('<div/>').html(data.title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" data-med-size="1280x960" alt="' + data.name + '" class="phswipe"><img alt="" src="' + $rootUrl + '/storage/images_gallery/min/' + data.name + '" class="thumb_resp2 img-fluid"></a></div></div></div>';

                                        if (gallery_nbr == 1) {
                                            $('#galleries').prepend(html);
                                        } else {
                                            var galtab = [];
                                            gallery_nbr = parseInt(gallery_nbr);
                                            $("#galleries > div").each(function(i) {
                                                galtab.push(parseInt($(this).attr('id').replace(/[^0-9]/gi,'')));
                                            });
                                            galtab.push(gallery_nbr);
                                            function sortNumbers(a, b)
                                            {
                                                return a - b;
                                            }
                                            galtab.sort(sortNumbers);
                                            var index = galtab.findIndex(key => key == gallery_nbr);
                                            if (index == 0) {
                                                $('#galleries').prepend(html);
                                            } else {
                                                $('#galleries > div').eq(index - 1).after(html);
                                            }
                                        }
                                        $('div[id="' + gal_id + '"]').fadeIn(500);
                                    }

                                    if (data.old_new_image) {
                                        $(gal_remove).children('div.flex-wrap').append('<div style="display:nonse;" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(data.old_new_image.title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" data-med-size="1280x960" alt="' + data.old_new_image.fileName + '" class="phswipe"><img alt="" src="' + $rootUrl + '/storage/images_gallery/min/' + data.old_new_image.fileName + '" class="thumb_resp2 img-fluid"></a></div>')/* .fadeIn(500) */;
                                    }
                                }

                                $('.thumb_resp2').off('contextmenu');
                                $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                                $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                                $('.phswipe').off('taphold');
                                $('.phswipe').on('taphold', $tapz);
                            });
                        })
                    }).fail(function() {
                        alert('{{ __("site.request_failed") }}');
                    });
                }
            });
            $(this).parent().prev().children().trigger('submit');
        });
    });
</script>
