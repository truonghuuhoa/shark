<?php

function register_menus() {
  register_nav_menus(
    array(
     'header' => __( 'Menu Header' )
   )
 );
}

add_action( 'init', 'register_menus' );
