<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<section class="section" style="background: url('<?php echo get_template_directory_uri(); ?>/assets/images/build.png') bottom no-repeat;">
  <div class="container">
		<div class="card">
      	<div class="card-body">
      	<div class="breadcrumbs">
      			<?php
      			if ( function_exists('yoast_breadcrumb') ) {
      			yoast_breadcrumb('
      			<p id="breadcrumbs">',' <i class="mdi mdi-heart text-danger"></i></p>');
      			}
      			?>
      	</div>
      </div>
    </div>
    <div class="row my-4">
      <!-- BLog Start -->
      	<?php get_sidebar(); ?>

      <div class="col-12 <?php if ( is_active_sidebar( 'sidebar-1' ) ) { ?>col-lg-9<?php } ?>">
        <!--end col-->
        <header class="woocommerce-products-header">
          <?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
        </header>

        <?php
if ( woocommerce_product_loop() ) {

  echo '<div class="row align-items-center mb-2">';

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	    do_action( 'woocommerce_before_shop_loop' );


      echo '</div>';

	    woocommerce_product_loop_start();

	    if ( wc_get_loop_prop( 'total' ) ) {
	        while ( have_posts() ) {
	            the_post();

	            /**
	             * Hook: woocommerce_shop_loop.
	             */
	            do_action( 'woocommerce_shop_loop' );

	            wc_get_template_part( 'content', 'product' );
	        }
	    } ?>

        <?php

	    woocommerce_product_loop_end();

	    /**
	     * Hook: woocommerce_after_shop_loop.
	     *
	     * @hooked woocommerce_pagination - 10
	     */
	    do_action( 'woocommerce_after_shop_loop' );
	} else {
	    /**
	     * Hook: woocommerce_no_products_found.
	     *
	     * @hooked wc_no_products_found - 10
	     */
	    do_action( 'woocommerce_no_products_found' );
	}

?>


      </div>

    </div>
    <!--end row-->
  </div>
  <!--end container-->
</section>
<!--end section-->

<?php

get_footer( 'shop' );
