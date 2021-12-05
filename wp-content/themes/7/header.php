<!doctype html>
<html lang="en">

<head>

  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <link href="<?php echo get_template_directory_uri(); ?>/assets/css/custom.css?<?php echo rand(6,9999); ?>" rel="stylesheet" type="text/css" id="theme-opt" />
  <?php wp_head(); ?>
</head>

<body>
  <div id="preloader">
    <div id="status">
      <div class="spinner-chase">
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
        <div class="chase-dot"></div>
      </div>
    </div>
  </div>
  <!-- Begin page -->
  <div id="layout-wrapper">
    <header id="page-topbar">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box">
            <a href="<?php echo home_url(); ?>" class="logo">
                <?php
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                    ?>
                <img src="<?php echo $image[0]; ?>" height="40" alt="">
            </a>
          </div>

          <button type="button" class="btn btn-sm px-0 font-size-16 d-lg-none header-item" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
            <i class="fa fa-fw fa-bars"></i>
          </button>

          <!-- App Search-->
          <form class="app-search d-none d-lg-block" action="<?php bloginfo('url'); ?>/" role="search" method="get">
            <div class="position-relative">
              <input type="text" class="form-control" placeholder="Search..." name="s" id="s">
              <span class="bx bx-search-alt"></span>
            </div>
          </form>

        </div>

        <div class="d-flex topnav">

          <div class="dropdown d-inline-block d-lg-none ms-2">
            <button type="button" class="btn header-item noti-icon" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-magnify"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">

              <form class="p-3" action="<?php bloginfo('url'); ?>/" role="search" method="get">
                <div class="form-group m-0">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Search input" name="s" id="s">
                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
      <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
        <div class="collapse navbar-collapse" id="topnav-menu-content">
          <?php

          if ( has_nav_menu( 'header' ) ) {

              wp_nav_menu(

                  array( 'theme_location'=> 'header',
                          'container'    => false,
                          'echo'         => 1,
                          'menu_class'   => 'navbar-nav',
                          'walker'       => new Header_Menu_Walker(),
                          'link_before'  => '<span>',
                          'link_after'   => '</span>'

                        )

                );

          }

       ?>
       </div>
       </nav>
       <?php if(is_user_logged_in()) {
           $user_id = get_current_user_id();
           $current_user = wp_get_current_user();
           $profile_url = get_author_posts_url($user_id);
           $edit_profile_url = get_edit_profile_url($user_id);
       ?>
       <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bx bx-user-check font-size-24 text-danger"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                <a class="dropdown-item" href="#"><span key="t-profile">Hi, <?php echo $current_user->display_name; ?></span></a>
                <a class="dropdown-item" href="<?php bloginfo('url'); ?>/admin"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-my-wallet">Profile</span></a>
                <a class="dropdown-item" href="<?php bloginfo('url'); ?>/tai-khoan/edit-account"><i class="bx bxs-edit font-size-16 align-middle me-1"></i> <span key="t-my-wallet">Change Password</span></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
            </div>
        </div>
       <?php } else { ?>
       <div class="dropdown d-inline-block">
           <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="bx bx-user-circle font-size-24"></i>
           </button>
           <div class="dropdown-menu dropdown-menu-end">
               <!-- item-->
               <a class="dropdown-item" href="<?php bloginfo('url'); ?>/register"><i class="bx bx-user-plus align-middle font-size-20"></i> <span key="t-my-wallet">Register</span></a>
               <a class="dropdown-item" href="<?php bloginfo('url'); ?>/login"><i class="bx bx-user-check align-middle font-size-20"></i> <span key="t-profile">Login</span></a>
           </div>
       </div>
     <?php } ?>

        <div class="dropdown d-inline-block">
              <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="bx bx-cart-alt"></i></a>
                  <span class="badge bg-danger rounded-pill"><?php echo sprintf (_n( '%d', '%d', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ); ?></span>
              </button>
        </div>

      </div>
    </header>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
