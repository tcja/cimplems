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
                var total_galleries = $('#galleries').children().length;
                var page_now = ($(obj).closest('div.flex-wrap').next().length) ? $(obj).closest('div.flex-wrap').next().children().children('.active').children().html() : 1;
                var data_string = 'file=' + image_name + '&galleryID=' + gallery_name.replace(/[a-z]+/gi, '') + '&page=' + page_now;
                $.ajax({
                    type: 'POST',
                    url: $rootUrl + '/delete_image',
                    context: $(obj).parents('.thumbs'),
                    data: data_string
                }).done(function(data) {
                    $(this).fadeOut(500, function(el) {
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
                                $('#galleries img[alt="' + data.name + '"]').parent().attr('title', $('<div/>').html(data.title).text());
                                $('#galleries img[alt="' + data.name + '"]').parent().nextAll('form').find('input[name="change_title"]')[0].defaultValue = escapeHtml($('<div/>').html(data.title).text());
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

		$('#submit_img').append('<input type="hidden" value="1" name="file_ajax">');

		$('#create_gal').validate({
			rules: {
				gallery_title: {
					required: true,
					normalizer: function(value) {
						return $.trim(value);
					},
					minlength: 3,
					maxlength: 60
				}
			},
			submitHandler: function(form) {
				var gallery = $('<div/>').html($.trim($(form).find('input[name="gallery_title"]').val())).text();
				var data_string = 'gallery_title=' + gallery;
				$(form).find('input[name="gallery_title"]').prop('disabled', true);
				$.ajax({
					beforeSend : function() {
						$(form).find('.sendForm').hide();
						$(form).find('.formSending').fadeIn(0);
					},
					type: 'POST',
					url: $rootUrl + '/create_gallery',
					dataType: 'json',
					data: data_string
				}).done(function(data) {
					var newGalleryName = escapeHtml(data);
					var arraygal = [];
					$('#gallery').children().each(function(i, el) {
						arraygal.push($(el).val());
					});
					var newgallery = Math.max.apply(Math, arraygal) + 1;
					$('#gallery').append('<option value="' + newgallery + '">' + gallery + '</option>');
					$('#galleries').find('select').each(function(i, el) {
						$(el).append('<option value="' + newgallery + '">' + gallery + '</option>');
					});

					if ($('#nogallery').length == 1) {
						var form_open = '{!! Form::open([url("/abort"), "id" => "modify_gal", "class" => ""]) !!}';

						var galleryz = '<div class="form-group row"><div class="col-2 col-sm-2 col-md-1 text-center"><label for="' + newgallery + '" class="col-form-label">{{ __("site.title") }}</label></div><div class="col-10 col-sm-10 col-md-5 mt-1"><div class="input-group input-group-sm"><input type="text" class="form-control galEdit" id="' + newgallery + '" name="' + newgallery + '" value="' + newGalleryName + '"/><div class="input-group-append ml-2 ' + ($isMobile ? "mt-1" : "") + '"><a href="javascript:;" class="delete_gallery" title="{{ __("site.delete_gallery") }}"><i style="'+ ($isMobile ? "font-size: 1.3rem;" : "")+'" class="far fa-trash-alt"></i></a></div></div></div></div><input type="hidden" name="change_gallery_title" value="1"/><button class="mt-1 btn btn-primary btn-sm" style="' + ($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;") + '" type="submit">{{ __("site.apply_changes") }}</button><button style="display:none; '+ ($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;")+'" class="btn btn-primary btn-sm" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("site.changes_in_progress") }}</button><button style="display:none; ' + ($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;") + '" type="button" class="btn btn-sm btn-success"><i class="far fa-check-circle"></i> <b>{{ __("site.changes_done") }}</b></button>';

						var form_close =  '{!! Form::close() !!}';

						var full_form = form_open + galleryz + form_close;
					} else {
						var galleryz = '<div class="form-group row"><div class="col-2 col-sm-2 col-md-1 text-center"><label for="' + newgallery + '" class="col-form-label">{{ __("site.title") }}</label></div><div class="col-10 col-sm-10 col-md-5 mt-1"><div class="input-group input-group-sm"><input type="text" class="form-control galEdit" id="' + newgallery + '" name="' + newgallery + '" value="' + newGalleryName + '"><div class="input-group-append ml-2 "><a href="javascript:;" class="delete_gallery" title="{{ __("site.delete_gallery") }}"><i style="' + ($isMobile ? 'font-size: 1.3rem;' : '') + '" class="far fa-trash-alt"></i></a></div></div></div></div>';
					}

					$.timer(1000, function() {
						$(form).find('.formSending').fadeOut(150, function() {
							$(form).find('.formSent').fadeIn(150);
						});
						$.timer(1500, function() {
							$(form).find('.formSent').fadeOut(150, function() {
								$(form).find('.sendForm').fadeIn(150);
								if (full_form) {
									$('#nogallery').hide();
									$('#nogallery').after(full_form);
									$('#nogallery').remove();
								} else {
									$('#modify_gal > div').last().after(galleryz);
                                }
								$('#modify_gal > div').last().hide().fadeIn(200);
								$(form).find('input[name=gallery_title]').val('');
								$(form).find('input[name=gallery_title]').prop('disabled', false);
								$(form).find('input[name=gallery_title]').fadeIn(150);
							});
						});
					});
				}).fail(function() {
					alert('{{ __("site.request_failed") }}');
				});
			}
		});

		$('#submit_img').validate({
			rules: {
				image_title: {
					maxlength: 50
				},
				gallery: {
					required: true,
					selectCheck: true
				}
			},
			submitHandler: function(form) {
				if ($('#files_list').children().length == 1) return false;

				var data_string = new FormData();

				if ($fileNumbers != $tabz.length) {
					var remImage = $tabz.length - $fileNumbers;
					$tabz.sort();
					$tabz.splice(0, remImage);
				}

				$('#loader').removeClass('d-none').addClass('d-flex');

				data_string.append('image_title', $('input[name=image_title]').val());
				data_string.append('file_ajax', $('input[name=file_ajax]').val());
				data_string.append('gallery', $('select[name=gallery]').val());
				data_string.append('total_files', $fileNumbers);

				function parsedata(data, image_numb)
				{
					var image = data.images;
					var total_char_images = image[image_numb].fileName.length;
					var imgf = image[image_numb].fileName.split('.').join('').split('-').join('').split('_').join('');
					var gallery_nbr = data.gallery;
					var gallery_name = $('select[name=gallery]').find(':selected').html();
					var gal_id = 'gallery' + gallery_nbr;
					var gal_find = '#gallery' + gallery_nbr;
					var gal_exist = $(document.body).find(gal_find).length;
					var has_no_galleries = $(document.body).find('#noimage').length;

					if ($isMobile) {
						if (image.errors) {
							$('#files_list').append(image.errors[0] + '<br>');
						} else {
							if (gal_exist == 1) {
                                var html = '<div id="' + imgf + '" style="display:none;" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" data-med-size="1280x960" alt="' + image[image_numb].fileName + '" class="phswipe"><img timestamp="' + image[image_numb].timestamp + '" alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="thumb_resp2 img-fluid"></a></div>';
                                if ($('#gallery' + data.gallery).find('img[alt="' + image[0].fileName + '"]').length) {
                                    $('#gallery' + data.gallery).find('img[alt="' + image[image_numb - 1].fileName + '"]').closest('.thumbs_resp').after(html);
                                } else {
                                    $('#gallery' + data.gallery).children('div').children().first().before(html);
                                }
								if (image_numb === 0) {
                                    $.scrollTo('#' + gal_id, 600, {
                                        onAfter: function() {
                                            $('div[id="' + imgf + '"]').fadeIn(600);
                                        }
                                    });
                                } else {
                                    $('div[id="' + imgf + '"]').fadeIn(600);
                                }
							} else if (has_no_galleries == 1) {
								$('#galleries').empty();
								$('#galleries').append('<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2 style="word-break: break-all;">' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" data-med-size="1280x960" alt="' + image[image_numb].fileName + '" class="phswipe"><img alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="thumb_resp2 img-fluid"></a></div></div></div>');
								$('div[id="' + gal_id + '"]').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id="' + gal_id + '"]').fadeIn(600);
                                    }
                                });
							} else {
								var html = '<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2 style="word-break: break-all;">' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumb_resp thumbs_resp"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" data-size="1280x960" data-med="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" data-med-size="1280x960" alt="' + image[image_numb].fileName + '" class="phswipe"><img alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="thumb_resp2 img-fluid"></a></div></div></div>';

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
								$('div[id="' + gal_id + '"]').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id="' + gal_id + '"]').fadeIn(600);
                                    }
                                });
							}
						}
					} else {
						if (image.errors) {
							$('#files_list').append(image.errors + '<br>');
						} else {
							if (gal_exist == 1) {
                                var html = '<div id="' + imgf + '" style="display:none;" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" class="fancyboxThumb text-decoration-none"><img timestamp="' + image[image_numb].timestamp + '" alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="' + image[image_numb].fileName + '" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + image[image_numb].fileName + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>';
                                if ($('#gallery' + data.gallery).find('img[alt="' + image[0].fileName + '"]').length) {
                                    $('#gallery' + data.gallery).find('img[alt="' + image[image_numb - 1].fileName + '"]').closest('.thumbs').after(html);
                                } else {
                                    $('#gallery' + data.gallery).children('div').children().first().before(html);
                                }
                                if (image_numb === 0) {
                                    $.scrollTo('#' + gal_id, 600, {
                                        onAfter: function() {
                                            $('div[id="' + imgf + '"]').fadeIn(600);
                                        }
                                    });
                                } else {
                                    $('div[id="' + imgf + '"]').fadeIn(600);
                                }
							} else if (has_no_galleries == 1) {
                                $('#galleries').empty();
                                $('#galleries').append('<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2 style="word-break: break-all;">' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="img-thumbnail img-thumbnailz"></a><a class="delete_image" title="{{ __("site.delete_image") }}" alt="' + image[image_numb].fileName + '" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + image[image_numb].fileName + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>');
                                $('div[id="' + gal_id + '"]').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id="' + gal_id + '"]').fadeIn(600);
                                    }
                                });
							} else {
								var html = '<div id="' + gal_id + '" style="display:none;" class="row mb-4"><h2 style="word-break: break-all;">' + gallery_name + '</h2><div class="col-12 d-flex flex-wrap"><div id="' + imgf + '" class="thumbs"><a href="' + $rootUrl + '/storage/images_gallery/big/' + image[image_numb].fileName + '" rel="external" title="' + escapeHtml($('<div/>').html(image[image_numb].title).text()) + '" class="fancyboxThumb text-decoration-none"><img alt="' + image[image_numb].fileName + '" src="' + $rootUrl + '/storage/images_gallery/min/' + image[image_numb].fileName + '" class="img-thumbnail img-thumbnailz"></a><a class="delete_image" alt="' + image[image_numb].fileName + '" title="{{ __("site.delete_image") }}" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="' + image[image_numb].fileName + '" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>';

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
                                $('div[id="' + gal_id + '"]').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id="' + gal_id + '"]').fadeIn(600);
                                    }
                                });
							}
						}
					}
				}

				for (var i = 0; i < $fileNumbers; i++) {
					data_string.append($('#file_upload').attr('name'), $tabz[i]);
					data_string.append('image_number_' + i, i);
				}
				$('#file_upload').prop('disabled', true);
				$('#import').prop('disabled', true);
				$.ajax({
					type: 'POST',
					url: $rootUrl + '/do_upload',
					context: this,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
                    data: data_string,
                    complete: function() {
						$('#file_upload').prop('disabled', false);
					    $('#import').prop('disabled', false);
                    }
				}).done(function(data) {
                    if (!$('#placeholder1337').length) {
                        $('#gallery' + data.gallery).children('div.flex-wrap').empty().append('<div id="placeholder1337"></div>');
                    }
					for (var i = 0; i < Object.keys(data.images).length; i++) {
						parsedata(data, i);
                    }
					if ($('#placeholder1337').length) {
						$('#placeholder1337').remove();
					}

                    var paginator = data.galleryInfos.paginatorHTML;
                    window.history.pushState(null, 'Title', $rootUrl + '/gallery?galid=' + data.gallery + '&p=1#gallery' + $('select[name=gallery]').val());

                    if (data.galleryInfos.paginator.last_page > 1) {
                        if (!$('#gallery' + data.gallery).children().last().children('ul').length) {
                            $('#gallery' + data.gallery).append(paginator);
                        } else {
                            $('#gallery' + data.gallery).children().last().replaceWith(paginator);
                        }
					}

					if ($isMobile) {
						$('.thumb_resp2').off('contextmenu');
						$('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
						$(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
						$('.phswipe').off('taphold');
						$('.phswipe').on('taphold', $tapz);
					}

					$('input[name=image_title]').val('');
					$('#gallery').val(0)
					$fileNumbers = 0;
					$('#loader').removeClass('d-flex').addClass('d-none');
					$('#img_selected').children().empty();
					$('#img_selected').children().append('{{ __("site.zero_images_selected") }}');
				});

				$('.file-input-name').empty();
				$('#files_list').empty();
				if ($isMobile) {
					var header = '<h4 class="m-auto">{{ __("site.your_images") }}</h4>';
                } else {
					var header = '<h5 class="m-auto">{{ __("site.put_your_images_here") }}</h5>';
                }
				$('#files_list').prepend(header);

				$('#file_upload').empty();
				$('#files_list').children().show();
				$('#files_list').addClass('col-12');
				$tabz = [];
				$fileNumb = 0;
			}
		});

		$tabz = [];
		$imgTimeStamp = [];
		$fileNumbers = 0;
		$fileNumb = 0;

		/**
		* Load the mime type based on the signature of the first bytes of the file
		* Modified for custom needs by Trim C. (added mimes patterns for gif and bmp)
		* @param  {File}   file        A instance of File
		* @param  {Function} callback  Callback with the result
		* @author Victor www.vitim.us
		* @date   2017-03-23
		*/
		function checkMime(file, callback)
		{
			//List of known mimes
			var mimes = [
				{
					mime: 'image/jpeg',
					pattern: [0xFF, 0xD8, 0xFF],
					mask: [0xFF, 0xFF, 0xFF],
				},
				{
					mime: 'image/png',
					pattern: [0x89, 0x50, 0x4E, 0x47],
					mask: [0xFF, 0xFF, 0xFF, 0xFF],
				},
				{
					mime: 'image/gif',
					pattern: [0x47, 0x49, 0x46, 0x38],
					mask: [0xFF, 0xFF, 0xFF, 0xFF],
				},
				{
					mime: 'image/bmp',
					pattern: [0x42, 0x4D],
					mask: [0xFF, 0xFF],
				}
				// you can expand this list @see https://mimesniff.spec.whatwg.org/#matching-an-image-type-pattern
			];

			function check(bytes, mime) {
				for (var i = 0, l = mime.mask.length; i < l; ++i) {
					if ((bytes[i] & mime.mask[i]) - mime.pattern[i] !== 0) {
						return false;
					}
				}
				return true;
			}

			var blob = file.slice(0, 4); //read the first 4 bytes of the file

			var reader = new FileReader();
			reader.onloadend = function(e) {
				if (e.target.readyState === FileReader.DONE) {
					var bytes = new Uint8Array(e.target.result);

					for (var i = 0, l = mimes.length; i<l; ++i) {
						if (check(bytes, mimes[i])) return callback(mimes[i].mime);
					}

					return callback("Mime: unknown <br> Browser:" + file.type);
				}
			};
			reader.readAsArrayBuffer(blob);
		}

		$('#file_upload').fileupload({
			autoUpload: false
		}).on('fileuploadadd', function (e, data) {
			$('#files_list').children().first().hide();
			$('#files_list').removeClass('col-12');
			$('#files_list').css('height', 'auto');
			var typepre = data.files[0].type
			if (typepre == 'image/gif' || typepre == 'image/png' || typepre == 'image/bmp' || typepre == 'image/jpeg') {
				$fileNumbers++;
            }
			$('#img_selected').children().empty();
			if ($fileNumbers > 1) {
				$('#img_selected').children().append($fileNumbers + ' {{ __("site.images_selected") }}');
            } else {
				$('#img_selected').children().append($fileNumbers + ' {{ __("site.image_selected") }}');
            }
		}).on('fileuploadprocess', function (e, data) {
			var index = data.index;
				file = data.files[index];
			var type = file.type;

			checkMime(file, function(mime) {
				if (type == 'image/gif' || type == 'image/png' || type == 'image/bmp' || type == 'image/jpeg') {
					fileMIMEChecked = new File([file], file.name, { type: mime });
					if ($isMobile) {
						var thumbs_label = 'thumbs_label';
						var Wwidth = 'col-2 thumb_pre_image';
						var dimension = 'width:154px; height:169px; position:relative;';
					} else {
						var thumbs_label = 'thumbs_label';
						var Wwidth = 'col-2 thumb_pre_image';
						var dimension = 'width:154px; height:169px; position:relative;';
					}
					if (!$('#import').prop('disabled')) {
						$('#loader2').removeClass('d-none').addClass('d-flex');
						$('#file_upload').prop('disabled', true);
						$('#import').prop('disabled', true);
					}
					$('#files_list').append('<div style="' + dimension + '" class="' + $fileNumb + '"><div style="position: absolute; top:30%; left: 40%;" class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                    loadImage(fileMIMEChecked, {
                        meta: true,
                        canvas: true,
                        maxWidth: {{ config('site.widen_big_width') }}
                    }).then(function (data) {
                        let canvas = data.image;
						let fileNumbz = $fileNumb;
                        if (canvas.toBlob) {
                            canvas.toBlob(function (blob) {
                                let name = fileMIMEChecked.name;
                                let sizeMO = (fileMIMEChecked.size / 1048576).toFixed(2);
                                let size = fileMIMEChecked.size;
                                let type = fileMIMEChecked.type;

                                if (type == 'image/gif')
                                    typespan = '<span class="badge badge-secondary mr-1">GIF</span>';
                                else if (type == 'image/png')
                                    typespan = '<span class="badge badge-secondary mr-1">PNG</span>';
                                else if (type == 'image/bmp')
                                    typespan = '<span class="badge badge-secondary mr-1">BMP</span>';
                                else
                                    typespan = '<span class="badge badge-secondary mr-1">JPG</span>';

                                $('.' + fileNumbz).removeAttr('style');
                                $('.' + fileNumbz).addClass(Wwidth);
                                $('.' + fileNumbz).empty();

                                let reader = new FileReader();
                                reader.onloadend = function() {
                                    let blobz = reader.result;
                                    $('.' + fileNumbz).append('<img src="' + blobz + '" class="img-thumbs"><div class="' + thumbs_label + '">'+'<span class="mr-1">' + name.replace(/\.[^/.]+$/, "").substr(0,14) + '</span>' + typespan + '<a class="delete_pre_image" title="{{ __("site.remove_from_queue") }}" alt="' + fileNumbz + '" href="javascript:;"><i style="' + ($isMobile ? 'font-size: 1.3rem;' : '') + '" class="far fa-trash-alt"></i></a></div>');
                                }
                                reader.readAsDataURL(blob);
                                if (type == 'image/gif') {
                                    $tabz[fileNumbz] = fileMIMEChecked;
                                } else {
                                    resizedImage = blob;
                                    $tabz[fileNumbz] = resizedImage;
                                }
                            }, type);
                        }
						$fileNumb++;
                    });
				}
			});
		}).on('fileuploadprocessalways', function (e, data) {
			if (data.files[0].name == data.originalFiles[data.originalFiles.length-1].name) {
				$('#loader2').removeClass('d-flex').addClass('d-none');
				$('#file_upload').prop('disabled', false);
				$('#import').prop('disabled', false);
			}
		});

		$('#files_list').on('dragover', function (e) {
			e.stopPropagation();
			e.preventDefault();
			$('#files_list').css({'border': '3px dashed #4FA4FF', 'height': '150px'}).children().css('color', '#4FA4FF');
		}).on('dragleave', function (e) {
			e.stopPropagation();
			e.preventDefault();
			$('#files_list').css({'border': '3px dashed #8C8C8C', 'height': 'auto'}).children().css('color', '#000000');
		}).on('drop', function (e) {
			$('#files_list').css({'border': '3px dashed #8C8C8C', 'height': 'auto'}).children().css('color', '#000000');
		});

		$(this).on('click', '.delete_pre_image', function(e) {
			e.preventDefault();
			var num = $(this).attr('alt');
			$(this).parent().parent().fadeOut(150, function(el) {
				$(el).remove();
				$tabz[num] = 0;
				$fileNumbers = $fileNumbers - 1;
				$('#img_selected').children().empty();
				if ($fileNumbers > 1) {
					$('#img_selected').children().append($fileNumbers + ' {{ __("site.images_selected") }}');
                } else {
					$('#img_selected').children().append($fileNumbers + ' {{ __("site.image_selected") }}');
                }
				if ($fileNumbers == 0) {
					$('#files_list').children().show();
					$('#files_list').addClass('col-12')
				}
			});
		});

        $(this).off('click', '.delete_gallery');
		$(this).on('click', '.delete_gallery', function(e) {
            e.preventDefault();
            var options = {
                messageHeader: '{{ __("site.delete_gallery_modal") }} : ',
                modalBoxWidth: '405px'
            };
			$.confirmModal('{{ __("site.delete_gallery_message_modal") }}', options, function(obj) {
				var gallery_id = $(obj).parent().prev().attr('id');
				var galleryID = 'gallery' + gallery_id;
				var data_string = 'gallery_id=' + gallery_id;
				var total_galleries = $(obj).parents('form').children('div').length;
				var total_images = $('#galleries > div > div > div').length;
				if ($isMobile) {
					var total_gallery_images = $('#gallery' + $(obj).parent().prev().attr('name') + ' div.thumbs_resp').length;
                } else {
					var total_gallery_images = $('#gallery' + $(obj).parent().prev().attr('name') + ' div.thumbs').length;
                }
				$.ajax({
					type: 'POST',
					url: $rootUrl + '/delete_gallery',
					context: $(obj).parents('div.form-group'),
					data: data_string
				}).done(function(data) {
					$('#gallery option[value="' + gallery_id + '"]').remove();
					$('#galleries select option[value="' + gallery_id + '"]').remove();
					if (total_galleries == 1) {
						$(this).fadeOut(500, function(el) {
							$(el).parent().parent().append('<h6 id="nogallery" class="ml-3">{{ __("site.no_gallery") }}</h6>');
							$(el).parent().remove();
						});
					} else {
						$(this).fadeOut(500, function(el) { $(el).remove(); });
                    }
                    $.scrollTo('#' + galleryID, 600, {
                        onAfter: function() {
                            $('#' + galleryID).fadeOut(900, function() {
                                $('#' + galleryID).remove();
                                if (total_images == total_gallery_images) {
                                    $('#galleries').append('<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __("site.no_image") }}</h2>');
                                }
                                if ($isMobile) {
                                    $('.thumb_resp2').off('contextmenu');
                                    $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                                    $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                                    $('.phswipe').off('taphold');
                                    $('.phswipe').on('taphold', $tapz);
                                }
                            });
                        }
                    });
				}).fail(function() {
					alert('{{ __("site.request_failed") }}');
				});
            });
            if ($('.modalConfirm').find('.modal-title > span').length == 0) {
                $('.modalConfirm').find('.modal-title').append('<span class="page_name_span badge badge-dark">' + $('<div/>').html($(this).parent().prev().val()).text() + '</span>');
            } else {
                $('.modalConfirm').find('.modal-title > span').html($('<div/>').html($(this).parent().prev().val()).text());
            }
		});

		$(this).on('submit', '#modify_gal', function(e) {
			e.preventDefault();
			$(this).validate({
				submitHandler: function(form) {
					var data_string = new FormData();
					var haveData = false;
					var arraygalleries = [];
					$('.galEdit').each(function() {
						var val = $.trim(this.value);
						var defaultVal = $.trim(this.defaultValue);
						if (val != defaultVal) {
							data_string.append(this.name, val);
							arraygalleries[this.name] = val;
							haveData = true;
						}
					});
					if (!haveData) return false;
					$('.galEdit').prop('disabled', true);
					$.ajax({
						beforeSend: function() {
							$(form).find('button').first().hide();
							$(form).find('button').first().next().fadeIn(0);
						},
						type: 'POST',
						url: $rootUrl + '/edit_galleries',
						context: form,
						dataType: 'json',
						cache: false,
						contentType: false,
						processData: false,
						data: data_string
					}).done(function(data) {
						arraygalleries.forEach(function(val, i){
							$('#gallery' + i + ' > h2').html($('<div/>').html(val).text());
							$('#gallery').children('option[value="' + i + '"]').html($('<div/>').html(val).text());
							$('#galleries').find('select').children('option[value="' + i + '"]').html($('<div/>').html(val).text());
							$(form).find('input[name="' + i + '"]')[0].defaultValue = $('<div/>').html(val).text();
						});
						$.timer(1000, function() {
							$(form).find('button').first().next().fadeOut(150, function() {
								$(form).find('button').last().fadeIn(150);
							});
							$.timer(1500, function() {
								$(form).find('button').last().fadeOut(150, function() {
									$(form).find('button').first().fadeIn(150);
									$('.galEdit').prop('disabled', false);
								});
							});
						});
					}).fail(function(data) {
						alert('{{ __("site.request_failed") }}');
					});
				}
			});
			$('.galEdit').each(function() {
				$(this).rules('add', {
					required: true,
					normalizer: function(value) {
						return $.trim(value);
					},
					minlength: 3,
					maxlength: 60
				});
			});
			$(this).trigger('submit');
		});
	});
</script>
