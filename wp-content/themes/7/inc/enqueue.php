<?php


function fcms_enqueue_style() {

    $theme_version = wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css',false,$theme_version,'all');

    wp_enqueue_style( 'icons', get_template_directory_uri() . '/assets/css/icons.min.css',false,$theme_version,'all');

    wp_enqueue_style( 'app', get_template_directory_uri() . '/assets/css/app.css?scs',false,$theme_version,'all');

    wp_enqueue_style( 'dropzone', get_template_directory_uri() . '/assets/libs/dropzone/min/dropzone.min.css',false,$theme_version,'all');

    wp_enqueue_style( 'sweetalert2', get_template_directory_uri() . '/assets/libs/sweetalert2/sweetalert2.min.css',false,$theme_version,'all');

    // wp_enqueue_style( 'carousel', get_template_directory_uri() . '/assets/libs/owl.carousel/assets/owl.carousel.min.css',false,$theme_version,'all');
    //
    // wp_enqueue_style( 'carousel-2', get_template_directory_uri() . '/assets/libs/owl.carousel/assets/owl.theme.default.min.css',false,$theme_version,'all');


    wp_deregister_script( 'jquery' );                                                                               //cái false này có nghĩa k đưa xuống footer
    wp_register_script( 'jquery', get_template_directory_uri() . '/assets/libs/jquery/jquery.min.js', false, NULL, false );
    wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/libs/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), $theme_version, true );

    wp_enqueue_script( 'metisMenu', get_template_directory_uri() . '/assets/libs/metismenu/metisMenu.min.js', false, $theme_version, true );

    wp_enqueue_script( 'simplebar', get_template_directory_uri() . '/assets/libs/simplebar/simplebar.min.js', false, $theme_version, true );

    // wp_enqueue_script( 'waves', get_template_directory_uri() . '/assets/libs/node-waves/waves.min.js', false, $theme_version, true );


    wp_enqueue_script( 'apexcharts', get_template_directory_uri() . '/assets/libs/apexcharts/apexcharts.min.js', false, $theme_version, true );

    wp_enqueue_script( 'app', get_template_directory_uri() . '/assets/js/app.js?2', false, $theme_version, true );

    wp_enqueue_script( 'dropzone', get_template_directory_uri() . '/assets/libs/dropzone/min/dropzone.min.js', false, $theme_version, true );

    wp_enqueue_script( 'sweet-alerts', get_template_directory_uri() . '/assets/js/pages/sweet-alerts.init.js', false, $theme_version, true );

    wp_enqueue_script( 'sweetalert2', get_template_directory_uri() . '/assets/libs/sweetalert2/sweetalert2.min.js', false, $theme_version, true );

    wp_enqueue_script( 'ico', get_template_directory_uri() . '/assets/js/pages/ico-landing.init.js', false, $theme_version, true );

    // wp_enqueue_script( 'carousel', get_template_directory_uri() . '/assets/libs/owl.carousel/owl.carousel.min.js', false, $theme_version, true );

}

add_action('wp_enqueue_scripts', 'fcms_enqueue_style', 5);
