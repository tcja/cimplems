<script src="{{ asset('js/jquery.exif.js') }}"></script>
<script src="{{ asset('js/jquery.canvasResize.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.44.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.44.0/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.min.js"></script>
@if (app()->getLocale() != 'en')
    @php $js_location = 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/lang/summernote-'.app()->getLocale().'-'.strtoupper(app()->getLocale()).'.min.js'; @endphp
    <script src="{{ $js_location }}"></script>
@endif
<script src="{{ asset('js/jquery.ui.widget.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.20.1/load-image.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload-process.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload-image.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload-ui.min.js"></script>
