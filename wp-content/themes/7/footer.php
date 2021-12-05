
<footer class="landing-footer">
    <div class="container">
        <div class="row">
          <div class="col-12">
              <?php if ( is_active_sidebar( 'footer' )) : ?>
                  <div class="row">
                    <?php dynamic_sidebar( 'footer' ); ?>
                  </div>
              <?php endif; ?>
          </div>

      <?php $class_footer = cx_footer_class($from = 1, $to = 4);
            global $footer_col;

            if ($footer_col == 3) {
                $class_footer = "col-12 col-lg-6";
            }

       ?>

      <!-- Footer 1 Widget -->
      <?php if ( is_active_sidebar( 'footer-1' )) : ?>
          <div class="<?php echo $class_footer; ?>">
            <?php dynamic_sidebar( 'footer-1' ); ?>
          </div>
      <?php endif; ?>

      <?php if ($footer_col == 3) {
           $class_footer = "col-12 col-lg-3";
       } ?>

      <!-- Footer 2 Widget -->
      <?php if ( is_active_sidebar( 'footer-2' )) : ?>
      <div class="<?php echo $class_footer; ?>">
        <?php dynamic_sidebar( 'footer-2' ); ?>
      </div>
      <?php endif; ?>


      <!-- Footer 3 Widget -->
      <?php if ( is_active_sidebar( 'footer-3' )) : ?>
          <div class="<?php echo $class_footer; ?>">
            <?php dynamic_sidebar( 'footer-3' ); ?>
          </div>
      <?php endif; ?>


      <!-- Footer 4 Widget -->
      <?php if ( is_active_sidebar( 'footer-4' )) : ?>
          <div class="<?php echo $class_footer; ?>">
            <?php dynamic_sidebar( 'footer-4' ); ?>
          </div>
      <?php endif; ?>

    </div>
    <!-- end container -->
</footer>
<!-- Footer end -->
<div class="offcanvas offcanvas-end bg-white shadow" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header border-bottom  p-3 ">
      <div class="col">
          <h6 class="m-0" key="t-notifications"><i class="bx bx-cart-alt"></i> Cart Mini </h6>
      </div>
      <div class="col-auto">
            <button type="button" class="btn-close d-flex align-items-center text-dark" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bx bx-x"></i></button>
      </div>
  </div>
  <div class="offcanvas-body p-4">
    <div class="row">
      <div class="col-12">
        <?php global $woocommerce; ?>
        <?php $items = $woocommerce->cart->get_cart(); ?>
        <?php if(count($items) >= 1) { ?>
        <div class="pb-4">
          <?php foreach ($items as $key => $value) { ?>
            <?php $cart_items_remove_url = wc_get_cart_remove_url($key); ?>
            <a class="d-flex align-items-center mt-2" title="<?php echo get_the_title($value['product_id']); ?>" href="<?php echo get_permalink($value['product_id']); ?>">
                <img src="<?php echo esc_url(wp_get_attachment_image_src( get_post_thumbnail_id($value['product_id']), 'thumbnail')[0]); ?>" class="shadow rounded" style="max-height: 64px;" alt="">
                <div class="flex-1 text-start ms-3">
                    <h5 class="text-dark h6"><?php echo get_the_title($value['product_id']); ?></h5>
                    <p class="text-muted mb-0"><?php echo number_format($value['line_total']/$value['quantity'],0,",","."); ?>đ x <?php echo $value['quantity']; ?></p>
                </div>
            </a>
          <?php } ?>
            <div class="offcanvas-footer text-center">
                <div class="p-3">
                  <div class="d-flex align-items-center justify-content-between mb-4 pt-4 border-top">
                    <h6 class="text-dark mb-0">Total($):</h6>
                    <h6 class="text-dark mb-0"><?php echo WC()->cart->get_cart_total(); ?></h6>
                  </div>
                </div>
                <div class="py-4 border-top">
                  <a href="<?php bloginfo('url'); ?>/gio-hang" class="btn btn-primary me-2">Xem giỏ hàng</a>
                  <a href="<?php bloginfo('url'); ?>/thanh-toan" class="btn btn-success">Mua ngay</a>
                </div>
              </div>
        </div>
        <?php } else { ?>
          <div class="text-center">
            <p class="text-dark mt-1 mb-0">Không có sản phẩm nào trong giỏ hàng.</p>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart-empty.jpg" height="229px" class="mt-4" alt="">
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php wp_footer(); ?>

</body>

</html>
