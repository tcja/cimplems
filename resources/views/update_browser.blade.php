<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ __('site.update_browser_title') }}</title>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,400italic,700,700italic' rel='stylesheet'>
    <style>
      h2,h3,h4,h5,h6,html{font-family:Lato,'Lucida Grande','Lucida Sans Unicode',Tahoma,Sans-Serif}.h4,.h5,h4,h5{text-transform:uppercase}body{color:#000;background-color:#fff}.modal-unsupported{height:auto;max-height:100%}p.redirect-for-item{background:#202125;padding:1rem;border:1px solid #004fd1}.modal{position:fixed;width:600px;min-width:380px;left:50%;top:50px;margin-left:-300px;padding:20px;background-color:#f9f9f9;z-index:201}a:link,a:visited{color:#367ced}.modal.modal-error{border:10px solid #004fd1}@media (max-width:830px){.modal{top:20px;left:10px;right:10px;width:auto;min-width:0;max-height:375px;overflow:auto;margin-left:0}}@media (max-width:600px){.modal{-webkit-box-sizing:border-box;box-sizing:border-box;width:90%;left:0;right:0;margin:auto;min-width:auto}}html{line-height:1.5;font-size:15px;font-weight:400}@media (max-width:830px){html{font-size:14px}}@media (max-width:550px){html{font-size:13px}}h1,h2,h3,h4,h5,h6{line-height:1.2;margin:0 0 10px;font-weight:400}.h3,.h4,.h5,dt,h3,h4,h5,h6{font-weight:700}.h1,h1{-webkit-font-smoothing:antialiased;color:red;-moz-osx-font-smoothing:grayscale;font-family:'Telefon Black',Sans-Serif;line-height:1.1;font-size:3rem}@media (max-width:550px){.h1,h1{font-size:2.2rem}}.h2,h2{font-family:Telefon,Sans-Serif;font-size:1.8rem}.h3,dt,h3{font-size:1.4rem}.h4,h4{font-size:1.2rem}.modal p{color:#7c7c7c;margin:0 0 25px}
    </style>
  </head>
  <body class="">
    <div class="modal modal-error modal-unsupported">
      <h1>{{ __('site.update_browser_h1') }}</h1>
      <h2>{{ __('site.update_browser_h2') }}</h2>
      <p>{{ __('site.update_browser_p_st') }} <a href="http://outdatedbrowser.com/{{ app()->getLocale() }}">{{ __('site.update_browser_get_here') }}</a>.</p>
      <h2>{{ __('site.update_browser_h2_sec') }}</h2>
      <p>{{ __('site.update_browser_p_sec') }} <a href="http://outdatedbrowser.com/{{ app()->getLocale() }}">{{ __('site.update_browser_get_here') }}</a>.</p>
    </div>
  </body>
</html>
