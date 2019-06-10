<script type="text/javascript">
    $(function(){
        $(this).off('click', '.delete_image');
        $(this).on('click', '.delete_image', function(e) {
            e.preventDefault();
            var options = {
                messageHeader: '{{ __("site.delete_image_modal") }}',
                modalBoxWidth: '365px'
            };
            $.confirmModal('{{ __("site.delete_image_message_modal") }}', options, function(obj) {
                var image_name = $(obj).attr('alt');
                var gallery_name = $(obj).parents('.thumbs').parent().parents().attr('id');
                var gal = ('#' + gallery_name);
                var total_photos_gal = $(gal + ' div.thumbs').length;
                var total_galz = $('#galeries').children().length;
                var data_string = 'file=' + image_name;
                $.ajax({
                    type: 'POST',
                    url: $rootUrl+'/delete_image',
                    context: $(obj).parents('.thumbs'),
                    data: data_string
                }).done(function(datas) {
                    $(this).fadeOut(500, function(el) {
                        if (total_photos_gal == 1) {
                            if (total_galz == 1) {
                                $('#' + gallery_name).remove();
                                $('#galeries').append('<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __("site.no_image") }}</h2>');
                            } else {
                                $('#' + gallery_name).remove();
                            }
                        } else {
                            $(el).remove();
                        }
                    });
                }).fail(function(datas) {
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
                    url: $rootUrl+'/edit_image_show_form',
                    data: data_string,
                    context: this,
                    complete: function() {
                        $(this).next('div').remove();
                    }
                }).done(function(datas) {
                    $(this).attr('href', 'javascript:;');
                    $(this).parent().append(datas);
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
                    var title = $(form).children().find("input[name='change_title']").val();
                    var gallery = $(form).children().find("select[name='gallery']").val();

                    if ($(form).find("input[name='change_title']")[0].defaultValue == title && $(form).find("input[name='gallery_default']").val() == gallery) {
                        $(form).removeAttr('show');
                        $(form).fadeOut(0);
                        return false;
                    }

                    var onlyTitleChanged = 0;
                    if ($(form).find("input[name='change_title']")[0].defaultValue != title && $(form).find("input[name='gallery_default']").val() == gallery) {
                        onlyTitleChanged = 1;
                    }

                    var name = $(form).children().find("input[name='photo_name']").val();
                    var form_datas = 'change_title=' + title + '&gallery=' + gallery + '&photo_name=' + name + '&modify_one_image=' + onlyTitleChanged;

                    $.ajax({
                        beforeSend: function() {
                            $(form).hide();
                        },
                        type: 'POST',
                        dataType: 'json',
                        data: form_datas,
                        url: $rootUrl+'/edit_image',
                        context: form
                    }).done(function(datas) {
                        $(this).parent().fadeOut(350, function(el) {
                            var imgf = datas.name.split('.').join("").split('-').join("").split('_').join("");
                            var gallery_nbr = datas.gallery;
                            var gallery_name = $(form).children().find("select[name='gallery']").find(":selected").html();
                            var gal_id = 'gallery'+gallery_nbr;
                            var gal_find = '#gallery' + gallery_nbr;
                            var gal_exist = $(document.body).find(gal_find).length;
                            var current_gal_images = $(el).parents('.flex-wrap').children('div').length;
                            var gal_remove = $(el).parents('.flex-wrap').parent();
                            if (onlyTitleChanged) {
                                $('#galeries img[alt="'+datas.name+'"]').parent().attr('title', datas.title);
                                $('#galeries img[alt="'+datas.name+'"]').parent().nextAll('form').find('input[name="change_title"]')[0].defaultValue = datas.title;
                                $(el).fadeIn(500);
                                return false;
                            }
                            $(el).remove();
                            gal_origin = gal_remove.attr('id').replace(/[^0-9]/gi,'');
                            if (gal_exist == 1 || gal_origin == gallery_nbr) {
                                if (current_gal_images == 1) {
                                    if (gal_origin != gallery_nbr) {
                                        gal_remove.remove();
                                    }
                                }
                                $('#gallery'+datas.gallery).children('div').append('<div id="'+imgf+'" style="display:none;" class="thumbs"><a href="'+$rootUrl+'/storage/images_gallery/big/'+datas.name+'" rel="gallery1" title="'+datas.title+'" class="fancyboxThumb text-decoration-none"><img alt="'+datas.name+'" src="'+$rootUrl+'/storage/images_gallery/min/'+datas.name+'" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="delete" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="'+datas.name+'" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>');
                                $('div[id='+imgf+']').fadeIn(500);
                            } else {
                                if (current_gal_images == 1) {
                                    gal_remove.remove();
                                }
                                var html = '<div id="'+gal_id+'" style="display:none;" class="row mb-4"><h2>'+gallery_name+'</h2><div class="col-12 d-flex flex-wrap"><div id="'+imgf+'" class="thumbs"><a href="'+$rootUrl+'/storage/images_gallery/big/'+datas.name+'" rel="gallery1" title="'+datas.title+'" class="fancyboxThumb text-decoration-none"><img alt="'+datas.name+'" src="'+$rootUrl+'/storage/images_gallery/min/'+datas.name+'" class="img-thumbnail img-thumbnailz"></a><a class="delete_image" title="{{ __("site.delete_image") }}" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="'+datas.name+'" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>';

                                if (gallery_nbr == 1) {
                                    $('#galeries').prepend(html);
                                } else {
                                    var galtabz = [];
                                    gallery_nbr = parseInt(gallery_nbr);
                                    $("#galeries > div").each(function(i) {
                                        galtabz.push(parseInt($(this).attr('id').replace(/[^0-9]/gi,'')));
                                    });
                                    galtabz.push(gallery_nbr);
                                    function sortNumbers(a, b)
                                    {
                                        return a - b;
                                    }
                                    galtabz.sort(sortNumbers);
                                    var index = galtabz.findIndex(key => key == gallery_nbr);
                                    if (index == 0) {
                                        $('#galeries').prepend(html);
                                    } else {
                                        $('#galeries > div').eq(index - 1).after(html);
                                    }
                                }
                                $('div[id='+gal_id+']').fadeIn(600);
                            }
                        });
                    }).fail(function() {
                        alert('{{ __("site.request_failed") }}');
                    });
                }
            });
            $(this).parents('form').trigger('submit');
        });

        $(this).on('click','.cancel',function(e) {
            e.preventDefault();
            $(this).parent().removeAttr('show');
            $(this).parent().fadeOut(0);
        });
    });
</script>
