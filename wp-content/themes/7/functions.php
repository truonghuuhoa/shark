<?php

add_filter('show_admin_bar', '__return_false');

/*fcms support*/

if ( ! function_exists( 'fcms_theme_support' ) ) :

function fcms_theme_support() {

  // add_theme_support( 'automatic-feed-links' );

  add_theme_support( 'custom-logo' );

  add_theme_support( 'title-tag' );

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support(
    'html5',
    array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
      'script',
      'style',
    )
  );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
   */
  add_theme_support( 'post-thumbnails' );

  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   * If you're building a theme based on Twenty Twenty, use a find and replace
   * to change 'twentytwenty' to the name of your theme in all the template files.
   */


  // Add support for editor styles.
  add_theme_support( 'editor-styles' );

  // Enqueue editor styles.
  add_editor_style( trailingslashit( get_template_directory_uri() ) . 'assets/css/editor-style.css' );

  load_theme_textdomain( 'fcms', get_template_directory().'/languages' );

  //woocommerce
  add_theme_support( 'wc-product-gallery-slider' );
  add_theme_support( 'wc-product-gallery-zoom' );
  add_theme_support( 'wc-product-gallery-lightbox' );
  add_theme_support('woocommerce');

}

endif;

add_action( 'after_setup_theme', 'fcms_theme_support' );


foreach (glob( get_template_directory() . '/inc/*') as $file) {

  require $file;

}


foreach (glob( get_template_directory() . '/shortcode/*') as $file) {

  require $file;

}

function theme_settup() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array(
          'name' => esc_html__('Sidebar', 'hoa'),
          'id'   => 'sidebar-1',
          'description' => esc_html__('Add widgets here.', 'hoa'),
          'before_widget' => '<div class="mt-0 pt-2">',
          'after_widget' => '</div><hr class="my-2">',
          'before_title' => '<h5 class="font-size-14 mb-2">',
          'after_title' => '</h5>',
        ));
    }

    if (function_exists('register_sidebar')) {
        register_sidebar(array(
          'name' => esc_html__('Sidebar-blog', 'hoa'),
          'id'   => 'sidebar-2',
          'description' => esc_html__('Add widgets here.', 'hoa'),
          'before_widget' => '<div class="mt-0 pt-2">',
          'after_widget' => '</div><hr class="my-2">',
          'before_title' => '<p class="text-muted">',
          'after_title' => '</p>',
        ));
    }

    register_sidebar( array(
    'name' => esc_html__( 'Footer', 'mytheme' ),
    'id' => 'footer',
    'description' => esc_html__( 'Add widgets here.', 'mytheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h5 class="mt-2 footer-list-title">',
    'after_title' => '</h5><hr class="my-3">',
    ) );

    register_sidebar( array(
    'name' => esc_html__( 'Footer 1', 'mytheme' ),
    'id' => 'footer-1',
    'description' => esc_html__( 'Add widgets here.', 'mytheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h5 class="mt-4 footer-list-title">',
    'after_title' => '</h5><hr class="my-3">',
    ) );

    register_sidebar( array(
    'name' => esc_html__( 'Footer 2', 'mytheme' ),
    'id' => 'footer-2',
    'description' => esc_html__( 'Add widgets here.', 'mytheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h5 class="mt-4 footer-list-title">',
    'after_title' => '</h5><hr class="my-3">',
    ) );

    register_sidebar( array(
    'name' => esc_html__( 'Footer 3', 'mytheme' ),
    'id' => 'footer-3',
    'description' => esc_html__( 'Add widgets here.', 'mytheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h5 class="mt-4 footer-list-title">',
    'after_title' => '</h5><hr class="my-3">',
    ) );

    register_sidebar( array(
    'name' => esc_html__( 'Footer 4', 'mytheme' ),
    'id' => 'footer-4',
    'description' => esc_html__( 'Add widgets here.', 'mytheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h5 class="mt-4 footer-list-title">',
    'after_title' => '</h5><hr class="my-3">',
    ) );
}
add_action('widgets_init', 'theme_settup');

//class widget footer
$footer_col = 0;

function cx_footer_class($from = 1, $to = 1) {

    global $footer_col;

    $from = absint($from);

    $to   = absint($to) + 1;

    if($from < 1 || $to < 2) {
      return '';
    }

    $active_sidebars = 0;

    $class = '';

    for ($from; $from < $to; $from++) {
        if ( is_active_sidebar( 'footer-'.$from ) ) {
            $active_sidebars++;
        }
    }

    $footer_col = $active_sidebars;

    switch ( $active_sidebars ) {
        case '1':
            $class = 'col-12';
            break;
        case '2':
            $class = 'col-12 col-md-6';
            break;
        case '3':
            $class = 'col-12 col-md-6 col-lg-4';
            break;
         case '4':
            $class = 'col-12 col-md-6 col-lg-3';
            break;
    }

    return $class;
}


//widget cũ
function wpshare247_theme_support() {
    remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'wpshare247_theme_support' );

//sale %
function percentSale($price, $price_sale) {
    $sale = ($price_sale*100)/$price;
    $percent = 100 - $sale;
    return number_format($percent);
}

//PAGANATION
function ftios_pagenavi( $args = array() ) {
	if ( !is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key ) {
			$args[ $key ] = isset( $argv[ $i ]) ? $argv[ $i ] : '';
		}
	}

	$args = wp_parse_args( $args, array(
		'before' => '<div class="col-lg-12">',
		'after' => '</div>',
		'wrapper_tag' => 'ul',
		'wrapper_class' => 'pagination pagination-rounded justify-content-center mt-3 mb-4 pb-1',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );

	$options = array(
    'num_pages' => 3,
    'num_larger_page_numbers' => 3,
    'larger_page_numbers_multiple' => 10,
    'always_show' => false,
    'use_pagenavi_css' => true
  );

  $query = $args['query'];

	list( $posts_per_page, $paged, $total_pages ) = array( intval( $query->get( 'posts_per_page' ) ), max( 1, absint( $query->get( 'paged' ) ) ), max( 1, absint( $query->max_num_pages ) ) );

	if ( 1 == $total_pages && !$options['always_show'] )
		return;

	$pages_to_show = absint( $options['num_pages'] );
	$larger_page_to_show = absint( $options['num_larger_page_numbers'] );
	$larger_page_multiple = absint( $options['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1/2 );
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;

	if ( $start_page <= 0 )
		$start_page = 1;

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
		$end_page = $start_page + $pages_to_show_minus_1;

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page = $total_pages;
	}

	if ( $start_page < 1 )
		$start_page = 1;

	$out = '';

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First
				$out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( 1 ) . '" aria-label="First"><i class="mdi mdi-chevron-double-left"></i></a></li>';
			}

			// Previous
			if ( $paged > 1 ) {
				$out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $paged - 1 ) . '" aria-label="Previous"><i class="mdi mdi-chevron-left"></i></a></li>';
			}

      //Dot left
			// if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
			// 		$out .= '<li class="page-item"><span class="page-link">...</span></li>';
			// }

			// Smaller pages
			$larger_pages_array = array();
			if ( $larger_page_multiple )
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
					$larger_pages_array[] = $i;

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
					$out .= '<li class="page-item"><a class="page-link" href="javascript:void(0)">' . $larger_page . '</a></li>';
					$larger_page_start++;
				}
			}

			if ( $larger_page_start )
				$out .= '<li class="page-item"><span class="page-link">...</span></li>';

			// Page numbers
			$timeline = 'smaller';
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged ) {
					$out .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
					$timeline = 'larger';
				} else {
					$out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
				}
			}

			// Large pages
			$larger_page_end = 0;
			$larger_page_out = '';
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
					$larger_page_out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $larger_page ) . '">' . $larger_page . '</a></li>';
					$larger_page_end++;
				}
			}

			if ( $larger_page_out ) {
				$out .='<li class="page-item"><span class="page-link">...</span></li>';
			}
			$out .= $larger_page_out;

        //Dot right
			// if ( $end_page < $total_pages ) {
			// 		$out .= '<li class="page-item"><span class="page-link">...</span></li>';
			// }

			// Next
			if ( $paged < $total_pages ) {
				$out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $paged + 1 ) . '" aria-label="Next"><i class="mdi mdi-chevron-right"></i></a></li>';
			}

			if ( $end_page < $total_pages ) {
				// Last
				$out .= '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $total_pages ) . '" aria-label="Last"><i class="mdi mdi-chevron-double-right"></i></a></li>';
			}

	$out = $before . "<" . $wrapper_tag . " class='" . $wrapper_class . "' role='navigation'>\n$out\n</" . $wrapper_tag . ">" . $after;

	if ( !$echo )
		return $out;

	echo $out;
}


/*xóa tab đánh giá*/
add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );

function wcs_woo_remove_reviews_tab( $tabs ) {
	unset( $tabs['reviews'] ); // Remove the reviews tab

	return $tabs;
}


// remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products', 20);
remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash', 10);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action('woocommerce_single_product_summary','woocommerce_template_single_meta', 6 );

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action('woocommerce_single_product_summary', 'woocommerce_breadcrumb', 4 );

/*Mua ngay*/
add_action('woocommerce_after_add_to_cart_button','cx_after_addtocart_button');
function cx_after_addtocart_button(){
    global $product;
  ?>
    <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn-success waves-effect  mt-2 waves-light" id="buy_now_button">
        <?php _e('<i class="bx bx-shopping-bag me-2"></i>Mua ngay', 'devvn'); ?>
    </button>
    <input type="hidden" name="is_buy_now" id="is_buy_now" value="0" />
    <script>
        jQuery(document).ready(function(){
            jQuery('body').on('click', '#buy_now_button', function(){
                if(jQuery(this).hasClass('disabled')) return;
                var thisParent = jQuery(this).closest('form.cart');
                jQuery('#is_buy_now', thisParent).val('1');
                thisParent.submit();
            });
        });
    </script>
    <?php
}
add_filter('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout($redirect_url) {
    if (isset($_REQUEST['is_buy_now']) && $_REQUEST['is_buy_now']) {
        $redirect_url = wc_get_checkout_url();
    }
    return $redirect_url;
}

add_action( 'woocommerce_single_product_summary', 'wc_product_sold_count', 20 );
function wc_product_sold_count() {
 global $product;
 $units_sold = get_post_meta( $product->get_id(), 'total_sales', true );
 echo '<p class="text-muted mb-4">' . sprintf( __( 'Sản phẩm đã bán: %s', 'woocommerce' ), $units_sold ) . '</p>';
}


//Bỏ continue shoping
add_filter('wc_add_to_cart_message_html', 'remove_continue_shoppping_button', 10, 2);
function remove_continue_shoppping_button($message, $product){
  if (strpos($message, 'Tiếp tục xem sản phẩm') !==false) {
    return preg_replace('/<a.*<\/a>/m','',$message);
  } else {
    return $message;
  }
}


function woocommerce_template_loop_product_title() {
    echo '<div class="text-center mt-3"><h5 class="mb-2 text-truncate">' . get_the_title() . '</h5></div>';
}

//hiện trên 1 dòng
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns($num) {
		return 6; // 3 products per row
	}
}


//slider 1
function slider_post_type(){
    /*
     * Biến $label để chứa các text liên quan đến tên hiển thị của Post Type trong Admin
     */
    $label = array(
        'name' => 'Ảnh slider', //Tên post type dạng số nhiều
        'singular_name' => 'Ảnh slider' //Tên post type dạng số ít
    );
    $args = array(
        'labels' => $label, //Gọi các label trong biến $label ở trên
        'description' => 'Ảnh slider', //Mô tả của post type
        'supports' => array(
            'title',
            'thumbnail'
        ),
        'hierarchical' => false, //Cho phép phân cấp, nếu là false thì post type này giống như Post, true thì giống như Page
        'public' => true, //Kích hoạt post type
        'show_ui' => true, //Hiển thị khung quản trị như Post/Page
        'show_in_menu' => true, //Hiển thị trên Admin Menu (tay trái)
        'show_in_nav_menus' => true, //Hiển thị trong Appearance -> Menus
        'show_in_admin_bar' => true, //Hiển thị trên thanh Admin bar màu đen.
        'menu_position' => 5, //Thứ tự vị trí hiển thị trong menu (tay trái)
        'menu_icon' => 'dashicons-format-image', //Đường dẫn tới icon sẽ hiển thị
        'can_export' => true, //Có thể export nội dung bằng Tools -> Export
        'has_archive' => true, //Cho phép lưu trữ (month, date, year)
        'exclude_from_search' => false, //Loại bỏ khỏi kết quả tìm kiếm
        'publicly_queryable' => true, //Hiển thị các tham số trong query, phải đặt true
        'capability_type' => 'post' //
    );

    register_post_type('slider', $args); //Tạo post type với slug tên là sanpham và các tham số trong biến $args ở trên

}
add_action('init', 'slider_post_type');



/* Sua footer trong admin */
function cx_admin_footer_credits ( $text ) {
    $text = '<p> Website được thiết kế bởi Cam Xanh.</p>';
    return $text;
}
add_filter('admin_footer_text', 'cx_admin_footer_credits');


/* Tự động chuyển đến một trang khác sau khi login */
function my_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	global $user;
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			return admin_url();
		} else {
			return home_url();
		}
	} else {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

// function redirect_login_page() {
//     $login_page  = home_url( '/admin/' );
//     $page_viewed = basename($_SERVER['REQUEST_URI']);
//     if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
//         wp_redirect($login_page);
//         exit;
//     }
// }
// add_action('init','redirect_login_page');

/* Kiểm tra lỗi đăng nhập */
// function login_failed() {
//     $login_page  = home_url( '/admin/' );
//     wp_redirect( $login_page . '?login=failed' );
//     exit;
// }
// add_action( 'wp_login_failed', 'login_failed' );
// function verify_username_password( $user, $username, $password ) {
//     $login_page  = home_url( '/admin/' );
//     if( $username == "" || $password == "" ) {
//         wp_redirect( $login_page . "?login=empty" );
//         exit;
//     }
// }
// add_filter( 'authenticate', 'verify_username_password', 1, 3);

/*logout acount */
add_filter( 'logout_url', 'my_logout_url' );
function my_logout_url( $url ) {
    $redirect = home_url();
    return $url.'&redirect_to='.$redirect;
}
