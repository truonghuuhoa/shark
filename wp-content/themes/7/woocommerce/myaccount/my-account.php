<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
 ?>
 <div class="row">
	 <div class="col-xl-3">
		 <div class="card">
       <div class="card-body">
         <div class="mt-4 mt-md-0 text-center">
            <img class="img-thumbnail rounded-circle avatar-xl" alt="200x200" src="<?php echo get_template_directory_uri(); ?>/assets/images/users/u2.jpg" data-holder-rendered="true">
            <h4 class="mt-4">Hi, <?php echo $current_user->display_name; ?></h4>
         </div>
		 	     <?php do_action( 'woocommerce_account_navigation' ); ?>
      </div>
		</div>
	</div>
	<div class="col-xl-9">
		<div class="card">
      <div class="card-body">
			<div class="woocommerce-MyAccount-content">
				<?php
					/**
					 * My Account content.
					 *
					 * @since 2.6.0
					 */
					do_action( 'woocommerce_account_content' );
				?>
      </div>
			</div>
		</div>
	</div>
</div>
