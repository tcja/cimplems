<?php

    /**************************************************************************
    |--------------------------------------------------------------------------
    | cimplems global config
    |--------------------------------------------------------------------------
    |
    | Here are the different variables to configure the app
    |
    |**************************************************************************/

return [

    /*
    |--------------------------------------------------------------------------
    | Website's name and footer credits
    |--------------------------------------------------------------------------
    |
    | This is the website's default name and footer credits
    |
    */

    'name' => 'cimplems',

    'footer_credits' => '<span style="left: 0; right: 0;" class="d-inline-block position-absolute text-center text-white small">&copy; cimplems 2019</span>', // HTML markup accepted but not required

    /*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
    | This is the website's default locale
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Page transition style
    |--------------------------------------------------------------------------
    |
    | This defines if the page transitions should be done via ajax or not
    |
    */

    'page_ajax_transition' => true,

    'gallery_page_ajax_transition' => true,

    /*
    |--------------------------------------------------------------------------
    | Default theme
    |--------------------------------------------------------------------------
    |
    | Those are the default theme configs vars
    |
    */

    'theme_dir' => 'themes/',

    'theme' => 'bootstrap',

    /*
    |--------------------------------------------------------------------------
    | Gallery
    |--------------------------------------------------------------------------
    |
    | Those are all options related to gallery management
    |
    */

    'images_per_page' => 24,

    'big_images_path' => 'app/public/images_gallery/big/',  //path to the original/resized images uploaded for the galleries
    'min_images_path' => 'app/public/images_gallery/min/',  //path to the minified images uploaded for the galleries
    'widen_big_width' => 1280,  //Width in pixel to resize the uploaded images for the gallery
    'widen_min_width' => 300,   //Width in pixel to resize the miniatures of the uploaded images for the gallery

    'page_images_path' => 'app/public/images_site/',    //path to the images uploaded for a page
    'widen_width' => 1280,  //Width in pixel to resize the images uploaded for a custom page

];
