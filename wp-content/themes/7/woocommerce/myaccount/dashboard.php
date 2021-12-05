<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>


<!-- <p>
	<?php
	/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
	$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	if ( wc_shipping_enabled() ) {
		/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	}
	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p> -->
<div class="row">
	<h4 class="mb-4">Trang tài khoản</h4>
    <div class="col-sm-4">
        <div class="mb-3">
            <a href="<?php bloginfo('url'); ?>/tai-khoan/orders"><label class="card-radio-label mb-2">
                <div class="card-radio">
                    <div>
                        <i class="bx bx-notepad font-size-24 text-warning align-middle me-2"></i>
                        <span>Đơn hàng</span>
                    </div>
                </div>
            </label></a>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="mb-3">
            <a href="<?php bloginfo('url'); ?>/tai-khoan/edit-address"><label class="card-radio-label mb-2">
                <div class="card-radio">
                    <div>
                        <i class="bx bx-run font-size-24 text-primary align-middle me-2"></i>
                        <span>Địa chỉ thanh toán</span>
                    </div>
                </div>
            </label></a>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="mb-3">
            <a href="<?php bloginfo('url'); ?>/tai-khoan/edit-account"><label class="card-radio-label mb-2">
                <div class="card-radio">
                    <div>
                        <i class="bx bx-compass font-size-24 text-info align-middle me-2"></i>
                        <span>Thay đổi mật khẩu</span>
                    </div>
                </div>
            </label></a>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-lg-12">
        <div class="text-center">
            <div class="row justify-content-center mt-5">
                <div class="col-sm-4">
                    <div class="maintenance-img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/coming-soon.svg" alt="" class="img-fluid mx-auto d-block">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
