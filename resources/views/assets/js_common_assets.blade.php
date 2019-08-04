<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('js/jquery.transit.js') }}"></script>
<script src="{{ asset('js/jquery.fadecss.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
@if (app()->getLocale() != 'en')
    @php $js_location = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/localization/messages_'.app()->getLocale().'.min.js'; @endphp
    <script src="{{ $js_location }}"></script>
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/jquery.confirmModal.js') }}"></script>
<script src="{{ asset('js/jquery-timer.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
<script type="text/javascript">
    $(function(){
        $locale = '{{ app()->getLocale() }}';
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
        $rootUrl = '{{ $rootUrl }}';
        $pageTitle = '{{ $pageTitle }}';
        $pageSlug = '{{ $pageSlug }}';
    });
</script>
