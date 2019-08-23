<script type="text/javascript">
    $(function(){//return false;
        $(this).on('click','.page-link', function(e) {
            e.preventDefault();
            $(this).closest('div').prev().children().each(function(key, val) { $(val).removeAttr('id'); });
            //if ($(this).attr('href') == 'javascript:;') return false;
            if ($(this).parent().hasClass('active')) return false;

            var paginationMenu = $(this).closest('ul');
            var initialPageButton = paginationMenu.children('.page-item.active');
            var nextPageButton = initialPageButton.next();
            var prevPageButton = initialPageButton.prev();
            var prevLink = paginationMenu.find('[rel=prev]');
            var nextLink = paginationMenu.find('[rel=next]');
            var targetLink = $(this);
            //var prevButton = paginationMenu.children().first();
            //var nextButton = paginationMenu.children().last();
            //var initialPageNumber = initialPageButton.children().html();
            var gallery = targetLink.closest('div.row').attr('id').replace(/[a-z]+/gi, '');

            if (targetLink[0] == nextLink[0]) {
                var targetPage = nextPageButton.children().html();
            } else if (targetLink[0] == prevLink[0]) {
                var targetPage = prevPageButton.children().html();
            } else {
                var targetPage = targetLink.html();
            }

            var data_string = 'galleryID=' + gallery + '&page=' + targetPage;
            $.ajax({
                beforeSend: function() {
                    paginationMenu.parent().prev().css('opacity', 0.2);
                },
                type: 'POST',
                dataType: 'json',
                data: data_string,
                url: $rootUrl + '/change_gal_page',
                context: paginationMenu.parent().prev()
            }).done(function(data) {
                $.timer(300, function() {
                    var paginator = data['galleryInfos'].paginatorHTML;
                    delete data.galleryInfos;
                    if (paginationMenu.parent().prev().children().length >= Object.keys(data).length) {
                        paginationMenu.parent().prev().children().each(function(key, val) {
                            if (data[key]) {
                                $(val).children('.phswipe, .fancyboxThumb').parent().children('.edit_image_mobile').remove();
                                $(val).children('.phswipe, .fancyboxThumb').children().attr('src', $(val).children('.phswipe, .fancyboxThumb').children().attr('src').split('/min/')[0] + '/min/' + data[key].fileName);
                                $(val).children('.phswipe, .fancyboxThumb').children().attr('alt', data[key].fileName);
                                $(val).children('.phswipe, .fancyboxThumb').children().attr('timestamp', data[key].timestamp);
                                $(val).children('.phswipe, .fancyboxThumb').attr('href', $(val).children('.phswipe, .fancyboxThumb').attr('href').split('/big/')[0] + '/big/' + data[key].fileName);
                                $(val).children('.phswipe').attr('data-med', $(val).children('.phswipe, .fancyboxThumb').attr('href').split('/big/')[0] + '/big/' + data[key].fileName);
                                $(val).children('.phswipe, .fancyboxThumb').attr('title', data[key].title);
                                $(val).children('.phswipe, .fancyboxThumb').attr('alt', data[key].fileName);
                                if ($(val).children('.delete_image').length && $(val).children('.edit_image').length) {
                                    $(val).children('.delete_image').attr('alt', data[key].fileName);
                                    $(val).children('.edit_image').attr('alt', data[key].fileName);
                                }
                            } else {
                                $(val).remove();
                            }
                            $(val).children('form').remove();
                        });
                    } else {
                        $.each(data, function(key, val) {
                            function replaceThumbs() {
                                thumbnail.children('.phswipe, .fancyboxThumb').parent().children('.edit_image_mobile').remove();
                                thumbnail.children('.phswipe, .fancyboxThumb').children().attr('src', thumbnail.children('.phswipe, .fancyboxThumb').children().attr('src').split('/min/')[0] + '/min/' + data[key].fileName);
                                thumbnail.children('.phswipe, .fancyboxThumb').children().attr('alt', data[key].fileName);
                                thumbnail.children('.phswipe, .fancyboxThumb').children().attr('timestamp', data[key].timestamp);
                                thumbnail.children('.phswipe, .fancyboxThumb').attr('href', thumbnail.children('.phswipe, .fancyboxThumb').attr('href').split('/big/')[0] + '/big/' + data[key].fileName);
                                thumbnail.children('.phswipe').attr('data-med', thumbnail.children('.phswipe, .fancyboxThumb').attr('href').split('/big/')[0] + '/big/' + data[key].fileName);
                                thumbnail.children('.phswipe, .fancyboxThumb').attr('title', data[key].title);
                                thumbnail.children('.phswipe, .fancyboxThumb').attr('alt', data[key].fileName);
                                if (thumbnail.children('.delete_image').length && thumbnail.children('.edit_image').length) {
                                    thumbnail.children('.delete_image').attr('alt', data[key].fileName);
                                    thumbnail.children('.edit_image').attr('alt', data[key].fileName);
                                }
                            }
                            if (paginationMenu.parent().prev().children()[key]) {
                                var thumbnail = $(paginationMenu.parent().prev().children()[key]);
                                replaceThumbs();
                            } else {
                                var thumbnail = $(paginationMenu.parent().prev().children()[0]).clone();
                                replaceThumbs();
                                thumbnail.appendTo(paginationMenu.parent().prev());
                            }
                            thumbnail.children('form').remove();
                        });
                    }
                    if ($isMobile) {
						$('.thumb_resp2').off('contextmenu');
						$('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
						$(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                        @if (session('admin') === true)
                            $('.phswipe').off('taphold');
                            $('.phswipe').on('taphold', $tapz);
                        @endif
					}
                    window.history.pushState(null, 'Title', $rootUrl + '/gallery?galid=' + gallery + '&p=' + targetPage + '#' + paginationMenu.parent().parent().attr('id'));
                    paginationMenu.parent().prev().removeAttr('style');
                    paginationMenu.parent().replaceWith(paginator);
                });
            }).fail(function() {
                alert('{{ __("site.request_failed") }}');
            });
        });
    });
</script>
