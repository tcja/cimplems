$(function(){
    $.validator.setDefaults({
        errorClass: 'errorf alert-error',
        //onfocusout: false,
        //onkeyup: false,
        onclick: false
    });
    $.validator.addMethod('selectCheck', function(value) {
        return (value != '0');
    }, '<i class="far fa-times-circle"></i>');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.modalReset').on('hidden.bs.modal', function() {
        $(this).find('.alert-error:not(label)').each(function(i, el) {
            if ($(el).is('INPUT')) {
                $(el).val('');
            } else {
                $(el).val(0);
            }
            $(el).removeClass('errorf alert-error');
            $(el).next().remove();
        });
    });

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
            document.location.href = $rootUrl+"/gallery";
        if (slugNavBar == 'gallery' && slugLink == 'home')
            document.location.href = $rootUrl;
        else if (slugNavBar == 'gallery')
            document.location.href = $rootUrl+"/"+slugLink; */

        var data_string = 'page_name_show=' + slugLink;
        $.ajax({
            beforeSend: function() {
                NProgress.start();
            },
            type: 'POST',
            url: $rootUrl+'/show_page_ajax',
            context: this,
            data: data_string,
            dataType: 'json',
            complete: function() {
                NProgress.done();
            }
        }).done(function(datas) {
            if (history.pushState) {
                var targetTitleLink = $(this).attr('title');
                var originTitleLink = $('#menu').children('span').html();
                var originSlug = $currentSlug;
                //$currentPageTitle = datas.currentPageTitle;
                $currentSlug = datas.currentSlug;
                $(document).prop('title', datas.currentPageTitle);
                originSlug = originSlug.replace(/_/gi,'-');
                if (originSlug == 'home' || originSlug == 'contact' || originSlug == 'gallery') {
                    if (originSlug == 'contact' || originSlug == 'gallery') {
                        $('#menu').children('span').replaceWith('<a href="'+$rootUrl+'/'+originSlug+'" class="pageLink mr-1" title="'+originTitleLink+'">'+originTitleLink+'</a>');
                    } else {
                        $('#menu').children('span').replaceWith('<a href="'+$rootUrl+'" class="pageLink mr-1" title="'+originTitleLink+'">'+originTitleLink+'</a>');
                    }
                } else {
                    $('#menu').children('span').replaceWith('<a href="'+$rootUrl+'/'+originSlug+'" class="pageLink mr-1" title="'+originTitleLink+'">'+originTitleLink+'</a>');
                }
                $(this).replaceWith('<span>'+targetTitleLink+'</span>');

                $('#content').parent().fadeOut(200, function(el) {
                    $(el).empty();
                    if ($('.alert-dismissible').length === 1) {
                        $('.alert-dismissible').remove();
                    }
                    var speed = 300;
                    if (slugLink == 'contact') {
                        var isEmpty = $(datas.content['content']).text();
                        if (isEmpty == '') {
                            $(el).append('<div style="display: none;" id="content" class="col-12">'+datas.content['content']+'</div><div id="contactForm" class="col-12">'+datas.content['contactForm']+'</div>').fadeIn(speed);
                        } else {
                            $(el).append('<div id="content" class="col-12">'+datas.content['content']+'</div><div id="contactForm" class="col-12">'+datas.content['contactForm']+'</div>').fadeIn(speed);
                        }
                    } else if (slugLink == 'gallery') {
                        var isEmpty = $(datas.content['content']).text();
                        if (isEmpty == '') {
                            $(el).append('<div style="display: none;" id="content" class="col-12">'+datas.content['content']+'</div><div id="galleriesWrapper" class="col-12">'+datas.content['galleries']+'</div>').fadeIn(speed);
                        } else {
                            $(el).append('<div id="content" class="col-12">'+datas.content['content']+'</div><div id="galleriesWrapper" class="col-12">'+datas.content['galleries']+'</div>').fadeIn(speed);
                        }
                        if ($isMobile) {
                            $('.thumb_resp2').off('contextmenu');
                            $('.thumb_resp2').on('contextmenu',function(e){ e.preventDefault(); });
                            $(".phswipe").jqPhotoSwipe({ forceSingleGallery: true });
                        }
                    } else {
                        $(el).append('<div id="content" class="col-12">'+datas.content+'</div>').fadeIn(speed);
                    }
                    if (slugLink == 'home') {
                        window.history.pushState(null, "Title", $rootUrl);
                    } else {
                        window.history.pushState(null, "Title", $rootUrl+"/"+slugLink);
                    }
                });
            } else {
                document.location.href = $rootUrl;
            }
        });
    });
    $('.toast').toast({
        delay: 3000
    });
    $('.toast').on('show.bs.toast', function () {
        $('.toast_center').css('z-index', '1000');
    });
});
