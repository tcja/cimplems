<script type="text/javascript">
	$(function(){
		$('#submit_img').append('<input type="hidden" value="1" name="file_ajax">');

		$('#create_gal').validate({
			rules: {
				gallery_title: {
					required: true,
					normalizer: function(value) {
						return $.trim(value);
					},
					minlength: 3,
					maxlength: 85
				}
			},
			submitHandler: function(form) {
				var gallery = $("<div/>").html($.trim($(form).find('input[name="gallery_title"]').val())).text();
				var data_string = 'gallery_title=' + gallery;
				$(form).find('input[name="gallery_title"]').attr('disabled', 'disabled');
				$.ajax({
					beforeSend : function() {
						$(form).find('.sendForm').hide();
						$(form).find('.formSending').fadeIn(0);
					},
					type: 'POST',
					url: $rootUrl+'/create_gallery',
					dataType: 'json',
					data: data_string
				}).done(function(datas) {
					var newGalleryName = escapeHtml(datas);
					var arraygal = [];
					$('#gallery').children().each(function(i, el) {
						arraygal.push($(el).val());
					});
					var newgallery = Math.max.apply(Math, arraygal) + 1;
					$('#gallery').append('<option value="'+newgallery+'">'+gallery+'</option>');
					$('#galeries').find('select').each(function(i, el) {
						$(el).append('<option value="'+newgallery+'">'+gallery+'</option>');
					});

					if ($('#nogallery').length == 1) {
						var form_open = '{!! Form::open([url("/abort"), "id" => "modify_gal", "class" => ""]) !!}';

						var galleryz = '<div class="form-group row"><div class="col-2 col-sm-2 col-md-1 text-center"><label for="'+newgallery+'" class="col-form-label">{{ __("site.title") }}</label></div><div class="col-10 col-sm-10 col-md-5 mt-1"><div class="input-group input-group-sm"><input type="text" class="form-control galzor" id="'+newgallery+'" name="'+newgallery+'" value="'+newGalleryName+'"/><div class="input-group-append ml-2 '+($isMobile ? "mt-1" : "")+'"><a href="javascript:;" class="delete_gallery" title="{{ __("site.delete_gallery") }}"><i style="'+($isMobile ? "font-size: 1.3rem;" : "")+'" class="far fa-trash-alt"></i></a></div></div></div></div><input type="hidden" name="change_gallery_title" value="1"/><button class="mt-1 btn btn-primary btn-sm" style="'+($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;")+'" type="submit">{{ __("site.apply_changes") }}</button><button style="display:none; '+($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;")+'" class="btn btn-primary btn-sm" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("site.changes_in_progress") }}</button><button style="display:none; '+($isMobile ? "margin-left: 4.9rem;" : "margin-left: 12rem;")+'" type="button" class="btn btn-sm btn-success"><i class="far fa-check-circle"></i> <b>{{ __("site.changes_done") }}</b></button>';

						var form_close =  '{!! Form::close() !!}';

						var full_form = form_open + galleryz + form_close;
					} else {
						var galleryz = '<div class="form-group row"><div class="col-2 col-sm-2 col-md-1 text-center"><label for="'+newgallery+'" class="col-form-label">{{ __("site.title") }}</label></div><div class="col-10 col-sm-10 col-md-5 mt-1"><div class="input-group input-group-sm"><input type="text" class="form-control galzor" id="'+newgallery+'" name="'+newgallery+'" value="'+newGalleryName+'"><div class="input-group-append ml-2 "><a href="javascript:;" class="delete_gallery" title="{{ __("site.delete_gallery") }}"><i style="'+ ($isMobile ? 'font-size: 1.3rem;' : '') +'" class="far fa-trash-alt"></i></a></div></div></div></div>';
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
								$(form).find('input[name=gallery_title]').removeAttr('disabled');
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
					maxlength: 80,
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

				function parseDatas(datas, image_numb)
				{
					var image = datas;
					var total_char_images = image.name[image_numb][0].length;
					var imgf = image.name[image_numb][0].split('.').join("").split('-').join("").split('_').join("");
					var gallery_nbr = image.gallery;
					var gallery_name = $('select[name=gallery]').find(":selected").html();
					var gal_id = 'gallery'+gallery_nbr;
					var gal_find = '#gallery' + gallery_nbr;
					var gal_exist = $(document.body).find(gal_find).length;
					var has_no_galeries = $(document.body).find('#noimage').length;

					if ($isMobile) {
						if (image.errors) {
							$('#files_list').append(image.errors[0] + '<br>');
						} else {
							if (gal_exist == 1) {
								$('#gallery'+image.gallery).children('div').append('<div id="'+imgf+'" style="display:none;" class="thumb_resp thumbs_resp"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="external" title="'+escapeHtml($("<div/>").html(image.title).text())+'" data-size="1280x960" data-med="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" data-med-size="1280x960" alt="'+image.name[image_numb][0]+'" class="phswipe"><img alt="" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="thumb_resp2 img-fluid"></a></div>');
								if (image_numb === 0) {
                                    $.scrollTo('#' + gal_id, 600, {
                                        onAfter: function() {
                                            $('div[id='+imgf+']').fadeIn(600);
                                        }
                                    });
                                } else {
                                    $('div[id='+imgf+']').fadeIn(600);
                                }
							} else if (has_no_galeries == 1) {
								$('#noimage').remove();
								$('#galeries').append('<div id="'+gal_id+'" style="display:none;" class="row mb-4"><h2>'+gallery_name+'</h2><div class="col-12 d-flex flex-wrap"><div id="'+imgf+'" class="thumb_resp thumbs_resp"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="external" title="'+escapeHtml($("<div/>").html(image.title).text())+'" data-size="1280x960" data-med="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" data-med-size="1280x960" alt="'+image.name[image_numb][0]+'" class="phswipe"><img alt="" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="thumb_resp2 img-fluid"></a></div></div></div>');
								$('div[id='+gal_id+']').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id='+gal_id+']').fadeIn(600);
                                    }
                                });
							} else {
								var html = '<div id="'+gal_id+'" style="display:none;" class="row mb-4"><h2>'+gallery_name+'</h2><div class="col-12 d-flex flex-wrap"><div id="'+imgf+'" class="thumb_resp thumbs_resp"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="external" title="'+escapeHtml($("<div/>").html(image.title).text())+'" data-size="1280x960" data-med="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" data-med-size="1280x960" alt="'+image.name[image_numb][0]+'" class="phswipe"><img alt="" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="thumb_resp2 img-fluid"></a></div></div></div>';

								if (gallery_nbr == 1) {
									$('#galeries').prepend(html);
                                } else {
									var galtabz = [];
									gallery_nbr = parseInt(gallery_nbr);
									$("#galeries > div").each(function(i) {
										galtabz.push(parseInt($( this ).attr('id').replace(/[^0-9]/gi,'')));
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
								$('div[id='+gal_id+']').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id='+gal_id+']').fadeIn(600);
                                    }
                                });
							}
						}
					} else {
						if (image.errors) {
							$('#files_list').append(image.errors + '<br>');
						} else {
							if (gal_exist == 1) {
                                $('#gallery'+image.gallery).children('div').append('<div id="'+imgf+'" style="display:none;" class="thumbs"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="gallery1" title="'+escapeHtml($("<div/>").html(image.title).text())+'" class="fancyboxThumb text-decoration-none"><img alt="'+image.name[image_numb][0]+'" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="img-thumbnail img-thumbnailz"></a><a title="{{ __("site.delete_image") }}" alt="'+image.name[image_numb][0]+'" class="delete_image" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="'+image.name[image_numb][0]+'" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div>');
                                if (image_numb === 0) {
                                    $.scrollTo('#' + gal_id, 600, {
                                        onAfter: function() {
                                            $('div[id='+imgf+']').fadeIn(600);
                                        }
                                    });
                                } else {
                                    $('div[id='+imgf+']').fadeIn(600);
                                }
							} else if (has_no_galeries == 1) {
								$('#galeries').empty();
								$('#galeries').append('<div id="'+gal_id+'" style="display:none;" class="row mb-4"><h2>'+gallery_name+'</h2><div class="col-12 d-flex flex-wrap"><div id="'+imgf+'" class="thumbs"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="gallery1" title="'+escapeHtml($("<div/>").html(image.title).text())+'" class="fancyboxThumb text-decoration-none"><img alt="'+image.name[image_numb][0]+'" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="img-thumbnail img-thumbnailz"></a><a class="delete_image" title="{{ __("site.delete_image") }}" alt="'+image.name[image_numb][0]+'" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="'+image.name[image_numb][0]+'" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>');
                                $('div[id='+gal_id+']').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id='+gal_id+']').fadeIn(600);
                                    }
                                });
							} else {
								var html = '<div id="'+gal_id+'" style="display:none;" class="row mb-4"><h2>'+gallery_name+'</h2><div class="col-12 d-flex flex-wrap"><div id="'+imgf+'" class="thumbs"><a href="'+$rootUrl+'/storage/images_gallery/big/'+image.name[image_numb][0]+'" rel="gallery1" title="'+escapeHtml($("<div/>").html(image.title).text())+'" class="fancyboxThumb text-decoration-none"><img alt="'+image.name[image_numb][0]+'" src="'+$rootUrl+'/storage/images_gallery/min/'+image.name[image_numb][0]+'" class="img-thumbnail img-thumbnailz"></a><a class="delete_image" alt="'+image.name[image_numb][0]+'" title="{{ __("site.delete_image") }}" href="javascript:;"><i class="far fa-trash-alt"></i></a><a class="edit_image" alt="'+image.name[image_numb][0]+'" title="{{ __("site.edit_image") }}" href="javascript:;"><i class="far fa-edit"></i></a></div></div></div>';

								if (gallery_nbr == 1) {
									$('#galeries').prepend(html);
                                } else {
									var galtabz = [];
									gallery_nbr = parseInt(gallery_nbr);
									$("#galeries > div").each(function(i) {
										galtabz.push(parseInt($( this ).attr('id').replace(/[^0-9]/gi,'')));
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
                                $('div[id='+gal_id+']').css('opacity', '0').css('display', '');
                                $.scrollTo('#' + gal_id, 600, {
                                    onAfter: function() {
                                        $('div[id='+gal_id+']').fadeIn(600);
                                    }
                                });
							}
						}
					}
				}

				for (var i=0; i<$fileNumbers; i++) {
					data_string.append($('#file_upload').attr('name'), $tabz[i]);
					data_string.append('image_number_'+i, i);
				}
				$('#file_upload').attr('disabled', 'disabled');
				$('#import').attr('disabled', 'disabled');
				$.ajax({
					type: 'POST',
					url: $rootUrl+'/do_upload',
					context: this,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
                    data: data_string,
                    complete: function() {
						$('#file_upload').removeAttr('disabled');
					    $('#import').removeAttr('disabled');
                    }
				}).done(function(datas) {
					for (var i=0; i<datas.name.length; i++) {
						parseDatas(datas, i);
                    }

					if ($isMobile) {
						$('.thumb_resp2').off('contextmenu');
						$('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
						$(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
						$('.phswipe').off('taphold');
						$('.phswipe').on('taphold', $tapz);
					}

					$('input[name=image_title]').val('');
					/* $('#file_upload').removeAttr('disabled');
					$('#import').removeAttr('disabled'); */
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
			reader.onloadend = function(e) { //console.log(e.target.error, e.target.readyState, FileReader.DONE);
				if (e.target.readyState === FileReader.DONE) {
					var bytes = new Uint8Array(e.target.result);

					for (var i=0, l = mimes.length; i<l; ++i) {
						if (check(bytes, mimes[i])) return callback(mimes[i].mime);
					}

					return callback("Mime: unknown <br> Browser:" + file.type);
				}
			};
			reader.readAsArrayBuffer(blob);
		}

		$('#file_upload').fileupload({
			//loadImageMaxFileSize: 10,
			//previewMaxWidth: 100,
			//previewMaxHeight: 70,
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
					if ($('#import').attr('disabled') === undefined) {
						$('#loader2').removeClass('d-none').addClass('d-flex');
						$('#file_upload').attr('disabled', 'disabled');
						$('#import').attr('disabled', 'disabled');
					}
					$('#files_list').append('<div style="'+dimension+'" class="'+$fileNumb+'"><div style="position: absolute; top:30%; left: 40%;" class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
					$.canvasResize(fileMIMEChecked, {
						width: 1280,
						height: 0,
						quality: 90,
						i: $fileNumb,
						callback: function(data, width, height) {
							var name = fileMIMEChecked.name;
							var sizeMO = (fileMIMEChecked.size / 1048576).toFixed(2);
							var size = fileMIMEChecked.size;
							var type = fileMIMEChecked.type;

							if (type == 'image/gif')
								typez = '<span class="badge badge-secondary mr-1">GIF</span>';
							else if (type == 'image/png')
								typez = '<span class="badge badge-secondary mr-1">PNG</span>';
							else if (type == 'image/bmp')
								typez = '<span class="badge badge-secondary mr-1">BMP</span>';
							else
								typez = '<span class="badge badge-secondary mr-1">JPG</span>';

							$('.'+this.i).removeAttr('style');
							$('.'+this.i).addClass(Wwidth);
							$('.'+this.i).empty();
							$('.'+this.i).append('<img src="'+data+'" class="img-thumbs"><div class="'+thumbs_label+'">'+'<span class="mr-1">'+name.replace(/\.[^/.]+$/, "").substr(0,14)+'</span>'+typez+'<a class="delete_pre_image" title="{{ __("site.remove_from_queue") }}" alt="'+this.i+'" href="javascript:;"><i style="'+ ($isMobile ? 'font-size: 1.3rem;' : '') +'" class="far fa-trash-alt"></i></a></div>');

							if (type == 'image/gif') {
								$tabz[this.i] = fileMIMEChecked;
							/*else if (size <= 512000)
								$tabz[this.i] = this.numb;*/
                            } else {
								resizedImage = $.canvasResize('dataURLtoBlob', data);
								$tabz[this.i] = resizedImage;
							}
						}
					});
					$fileNumb++;
				}
			});
		}).on('fileuploadprocessalways', function (e, data) {
			if (data.files[0].name == data.originalFiles[data.originalFiles.length-1].name) {
				$('#loader2').removeClass('d-flex').addClass('d-none');
				$('#file_upload').removeAttr('disabled');
				$('#import').removeAttr('disabled');
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
				var total_images = $('#galeries > div > div > div').length;
				if ($isMobile) {
					var total_gallery_images = $('#gallery'+$(obj).parent().prev().attr('name')+' div.thumbs_resp').length;
                } else {
					var total_gallery_images = $('#gallery'+$(obj).parent().prev().attr('name')+' div.thumbs').length;
                }
				$.ajax({
					type: 'POST',
					url: $rootUrl+'/delete_gallery',
					context: $(obj).parents('div.form-group'),
					data: data_string
				}).done(function(datas) {
					$('#gallery option[value="'+gallery_id+'"]').remove();
					$('#galeries select option[value="'+gallery_id+'"]').remove();
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
                                    $('#galeries').append('<h2 class="ml-4 mt-5 mb-4" id="noimage">{{ __("site.no_image") }}</h2>');
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
                $('.modalConfirm').find('.modal-title').append('<span class="page_name_span badge badge-dark">' + $("<div/>").html($(this).parent().prev().val()).text() + '</span>');
            } else {
                $('.modalConfirm').find('.modal-title > span').html($("<div/>").html($(this).parent().prev().val()).text());
            }
		});

		$(this).on('submit', '#modify_gal', function(e) {
			e.preventDefault();
			$(this).validate({
				submitHandler: function(form) {
					var data_string = new FormData();
					var haveData = false;
					var arraygalleries = [];
					$('.galzor').each(function() {
						var val = $.trim(this.value);
						var defaultVal = $.trim(this.defaultValue);
						if (val != defaultVal) {
							data_string.append(this.name, val);
							arraygalleries[this.name] = val;
							haveData = true;
						}
					});
					if (!haveData) return false;
					$('.galzor').attr('disabled', 'disabled');
					$.ajax({
						beforeSend : function() {
							$(form).find('button').first().hide();
							$(form).find('button').first().next().fadeIn(0);
						},
						type: 'POST',
						url: $rootUrl+'/edit_galleries',
						context: form,
						dataType: 'json',
						cache: false,
						contentType: false,
						processData: false,
						data: data_string
					}).done(function(datas) {
						arraygalleries.forEach(function(val, i){
							$('#gallery'+i+' > h2').html($("<div/>").html(val).text());
							$('#gallery').children('option[value="'+i+'"]').html($("<div/>").html(val).text());
							$('#galeries').find('select').children('option[value="'+i+'"]').html($("<div/>").html(val).text());
							$(form).find('input[name="'+i+'"]')[0].defaultValue = $("<div/>").html(val).text();
						});
						$.timer(1000, function() {
							$(form).find('button').first().next().fadeOut(150, function() {
								$(form).find('button').last().fadeIn(150);
							});
							$.timer(1500, function() {
								$(form).find('button').last().fadeOut(150, function() {
									$(form).find('button').first().fadeIn(150);
									$('.galzor').removeAttr('disabled');
								});
							});
						});
					}).fail(function(datas) {
						alert('{{ __("site.request_failed") }}');
					});
				}
			});
			$('.galzor').each(function() {
				$(this).rules('add', {
					required: true,
					normalizer: function(value) {
						return $.trim(value);
					},
					/* errorPlacement: function(error, element) {console.log(error, element)
						error.appendTo( element.parent() );
					}, */
					minlength: 3,
					maxlength: 85
				});
			});
			$(this).trigger('submit');
		});
	});
</script>
