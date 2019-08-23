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
                    var targetTitleLink = $(this).attr('title');
                    var originTitleLink = $('#menu').children('.nav-item.active').children().html();
                    var originSlug = $pageSlug;
                    //$pageTitle = data.pageTitle;
                    $pageSlug = data.slug;
                    $(document).prop('title', data.pageTitle);
                    originSlug = originSlug.replace(/_/gi,'-');
                    if (originSlug == 'home' || originSlug == 'contact' || originSlug == 'gallery') {
                        if (originSlug == 'contact' || originSlug == 'gallery') {
                            $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '/' + originSlug + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                        } else {
                            $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                        }
                    } else {
                        $('#menu').children('.nav-item.active').replaceWith('<li class="nav-item"><a href="' + $rootUrl + '/' + originSlug + '" class="pageLink nav-link" title="' + originTitleLink + '">' + originTitleLink + '</a></li>');
                    }
                    $(this).parent().replaceWith('<li class="nav-item active"><span class="nav-link">' + targetTitleLink + '</span></li>');

                    $('#content').parent().fadeOut(200, function(el) {
                        $(el).empty();
                        if ($('.alert-dismissible').length === 1) {
                            $('.alert-dismissible').remove();
                        }
                        var speed = 300;
                        if (slugLink == 'contact') {
                            var isEmpty = $(data.content['content']).text();
                            if (isEmpty == '') {
                                $(el).append('<div style="display: none;" id="content" class="col-12">' + data.content['content'] + '</div><div id="contactForm" class="col-12">' + data.content['contactForm'] + '</div>').fadeIn(speed);
                            } else {
                                $(el).append('<div id="content" class="col-12">' + data.content['content'] + '</div><div id="contactForm" class="col-12">' + data.content['contactForm'] + '</div>').fadeIn(speed);
                            }
                        } else if (slugLink == 'gallery') {
                            var isEmpty = $(data.content['content']).text();
                            if (isEmpty == '') {
                                $(el).append('<div style="display: none;" id="content" class="col-12">' + data.content['content'] + '</div><div id="galleriesWrapper" class="col-12">' + data.content['galleries'] + '</div>').fadeIn(speed);
                            } else {
                                $(el).append('<div id="content" class="col-12">' + data.content['content'] + '</div><div id="galleriesWrapper" class="col-12">' + data.content['galleries'] + '</div>').fadeIn(speed);
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
