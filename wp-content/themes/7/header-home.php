
<!doctype html>
<html lang="en">

    <head>
      <meta charset="<?php bloginfo( 'charset' ); ?>" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
      <link href="<?php echo get_template_directory_uri(); ?>/assets/css/custom.css?<?php echo rand(6,9999); ?>" rel="stylesheet" type="text/css" id="theme-opt" />
      <?php wp_head(); ?>
    </head>

    <body data-bs-spy="scroll" data-bs-target="#topnav-menu" data-bs-offset="60">

        <nav class="navbar navbar-expand-lg navigation fixed-top sticky">
            <div class="container">
                <a href="<?php echo home_url(); ?>" class="navbar-logo">
                    <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                        ?>
                    <img src="<?php echo $image[0]; ?>" height="52" alt="">
                </a>

                <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav ms-auto" id="topnav-menu" >
                        <li class="nav-item">
                            <a class="nav-link active" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#card">Card</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#roadmap">Roadmap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#team">Team</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#news">News</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faqs">FAQs</a>
                        </li>

                    </ul>

                    <div class="my-2 ms-lg-2">
                        <a href="<?php bloginfo('url'); ?>/cua-hang" class="btn btn-outline-success w-xs">Demo</a>
                    </div>
                </div>
            </div>
        </nav>
