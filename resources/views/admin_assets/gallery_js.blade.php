<script type="text/javascript">
    $(function(){
        $(this).off('click', '.delete_image');
        $(this).on('click', '.delete_image', function(e) {
            e.preventDefault();
            $(this).closest('div.flex-wrap').children().each(function(key, val) { $(val).removeAttr('id'); });
            var options = {
                messageHeader: '{{ __("site.delete_image_modal") }}',
                modalBoxWidth: '365px'
            };
            $.confirmModal('{{ __("site.delete_image_message_modal") }}', options, function(obj) {
                var image_name = $(obj).attr('alt');
                var gallery_name = $(obj).parents('.thumbs').parent().parents().attr('id');
                var gal = '#' + gallery_name;
                var total_photos_gal = $(gal + ' div.thumbs').length;
                var total_gals = $('#galeries').children().length;
                var page_now = ($(obj).closest('div.flex-wrap').next().length) ? $(obj).closest('div.flex-wrap').next().children().children('.active').children().html() : 1;
                var data_string = 'file=' + image_name + '&galleryID=' + gallery_name.replace(/[a-z]+/gi, '') + '&page=' + page_now;
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/delete_image',
                    context: $(obj).parents('.thumbs'),
                    data: data_string
                }).done(function(data) {
                    $(this).fadeOut(500, function(el) {
                        if (!data[0]) {
                            if (total_gals == 1) {
                                $('#' + gallery_name).remove();
                                $('#galeries').append('<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __("site.no_image") }}</h2>');
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
                                var thumbnail = $(el).clone();
                                var last_image = data[0][Object.keys(data[0]).length - 2];

                                thumbnail.children('.fancyboxThumb').children().attr('src', thumbnail.children('.fancyboxThumb').children().attr('src').split('/min/')[0] + '/min/' + last_image.fileName);
                                thumbnail.children('.fancyboxThumb').children().attr('alt', last_image.fileName);
                                thumbnail.children('.fancyboxThumb').children().attr('timestamp', last_image.timestamp);
                                thumbnail.children('.fancyboxThumb').attr('href', thumbnail.children('.fancyboxThumb').attr('href').split('/big/')[0] + '/big/' + last_image.fileName);
                                thumbnail.children('.fancyboxThumb').attr('title', last_image.title);
                                thumbnail.children('.delete_image').attr('alt', last_image.fileName);
                                thumbnail.children('.edit_image').attr('alt', last_image.fileName);

                                thumbnail.appendTo($(el).parent()).fadeIn(500);

                                $(el).closest('.flex-wrap').next().replaceWith(paginator);
                                $(el).remove();
                            }
                        }
                    });
                }).fail(function(data) {
                    alert('{{ __("site.request_failed") }}');
                });
            });
        });

        $(this).on('click', '.edit_image', function(e) {
            e.preventDefault();
            if ($(this).attr('href') == 'disabled')	return false;
            if ($(this).nextAll('form').length == 0) {
                $(this).attr('href', 'disabled');
                var data_string = 'image_name=' + $(this).attr('alt');
                $.ajax({
                    beforeSend: function() {
                        $(this).after('<div class="spinner-border spinner-border-sm text-primary" role="status" style="position: absolute; top: 160px; left: 50px; right: 50px; margin: auto;"><span class="sr-only">Loading...</span></div>');
                    },
                    type: 'POST',
                    dataType: 'json',
                    url: $rootUrl + '/edit_image_show_form',
                    data: data_string,
                    context: this,
                    complete: function() {
                        $(this).next('div').remove();
                    }
                }).done(function(data) {
                    $(this).attr('href', 'javascript:;');
                    $(this).parent().append(data);
                });
            } else {
                if ($(this).nextAll('form').css('display') == 'none') {
                    $(this).nextAll('form').fadeIn(0);
                } else {
                    $(this).nextAll('form').removeAttr('show');
                    $(this).nextAll('form').fadeOut(0);
                }
            }
        });

        $(this).on('click', '.acceptEdit', function(e) {
            e.preventDefault();
            $(this).parents('form').validate({
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
                        $(form).removeAttr('show');
                        $(form).fadeOut(0);
                        return false;
                    }

                    var onlyTitleChanged = 0;
                    if ($(form).find('input[name="change_title"]')[0].defaultValue != title && $(form).find('input[name="gallery_default"]').val() == gallery) {
                        onlyTitleChanged = 1;
                    }

                    var name = $(form).children().find('input[name="photo_name"]').val();
                    var page = $(form).closest('div.flex-wrap').next().children().find('.active').children().length ? $(form).closest('div.flex-wrap').next().children().find('.active').children().html() : '';
                    var form_data = 'change_title=' + title + '&gallery=' + gallery + '&photo_name=' + name + '&modify_one_image=' + onlyTitleChanged + '&page=' + page;

                    $.ajax({
                        beforeSend: function() {
                            $(form).hide();
                        },
                        type: 'POST',
                        dataType: 'json',
                        data: form_data,
                        url: $rootUrl + '/edit_image',
                        context: form
                    }).done(function(data) {
                        $(this).parent().fadeOut(350, function(el) {
                            if (onlyTitleChanged) {
                                $('#galeries img[alt="' + data.name + '"]').parent().attr('title', $('<div/>').html(data.title).text());
                                $('#galeries img[alt="' + data.name + '"]').parent().nextAll('form').find('input[name="change_title"]')[0].defaultValue = escapeHtml($('<div/>').html(data.title).text());
                                $(el).fadeIn(500);
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
                                $('#gallery' + data.galleryID).children('div.flex-wrap').prepend('<div id="' + imgf + '" style="display:none;" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" rel="external" title="' + escapeHtml($('<div/>').html(data.title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + data.name + '" src="' + $rootUrl + '/storage/images_gallery/min/' + data.name + '" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="' + data.name + '" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + data.name + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>');
                                $('div[id="' + imgf + '"]').fadeIn(500);
                                if (data.old_new_image) {
                                    $(gal_remove).children('div.flex-wrap').append('<div style="display:nonse;" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(data.old_new_image.title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + data.old_new_image.fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + data.old_new_image.fileName + '" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="' + data.old_new_image.fileName + '" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + data.old_new_image.fileName + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>')/* .fadeIn(500) */;
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
                                    var html = '<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2>' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.name + '" rel="external" title="' + escapeHtml($('<div/>').html(data.title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + data.name + '" src="' + $rootUrl + '/storage/images_gallery/min/' + data.name + '" class="img-thumbnail img-thumbnailz"></a><a alt="' + data.name + '" class="delete_image" title="{{ __("site.delete_image") }}" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + data.name + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>';

                                    if (gallery_nbr == 1) {
                                        $('#galeries').prepend(html);
                                    } else {
                                        var galtab = [];
                                        gallery_nbr = parseInt(gallery_nbr);
                                        $("#galeries > div").each(function(i) {
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
                                            $('#galeries').prepend(html);
                                        } else {
                                            $('#galeries > div').eq(index - 1).after(html);
                                        }
                                    }
                                    $('div[id="' + gal_id + '"]').fadeIn(600);
                                }

                                if (data.old_new_image) {
                                    $(gal_remove).children('div.flex-wrap').append('<div style="display:nonse;" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + data.old_new_image.fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(data.old_new_image.title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + data.old_new_image.fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + data.old_new_image.fileName + '" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="' + data.old_new_image.fileName + '" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + data.old_new_image.fileName + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>')/* .fadeIn(500) */;
                                }
                            }
                        });
                    }).fail(function() {
                        alert('{{ __("site.request_failed") }}');
                    });
                }
            });
            $(this).parents('form').trigger('submit');
        });

        $(this).on('click','.cancel', function(e) {
            e.preventDefault();
            $(this).parent().removeAttr('show');
            $(this).parent().fadeOut(0);
        });
    });
</script>
