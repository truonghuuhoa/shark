<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$attr_feature = wc_get_product_terms( get_the_ID(), 'pa_feature', array ('fields' => 'names') );

?>
<?php if ( $product->is_on_sale() && $product->get_regular_price() && $product->get_sale_price()) : ?>

<div class="product-img position-relative">
		<div class="avatar-sm product-ribbon">
			<ul class="label list-unstyled mb-2 mt-2" style="text-align: right; font-size: 16px;">
				<?php foreach ( $attr_feature as $key => $value) { ?>
					<li><a href="javascript:void(0)" class="badge badge-link rounded-pill bg-success"><?php echo $value; ?></a></li>
				<?php } ?>
			</ul>
			<span class="avatar-title rounded-circle  bg-primary">- <?php echo percentSale($product->get_regular_price(), $product->get_sale_price()); ?> %</span>
		</div>
</div>

<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
