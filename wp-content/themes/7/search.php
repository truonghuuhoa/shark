<?php get_header(); ?>
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content" style="background: url('<?php echo get_template_directory_uri(); ?>/assets/images/build.png') bottom no-repeat;">
    <div class="page-content">
        <div class="container-fluid">
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
          <div class="row">
            <?php get_sidebar(); ?>
            <div class="col-lg-9">
              <div class="row mb-3">
                <div class="col-xl-4 col-sm-6">
                  <div class="mt-2">
                      <h5>Kết quả tìm kiếm </h5>
                  </div>
                </div>

              <div class="col-lg-8 col-sm-6">
                <form class="mt-4 mt-sm-0 float-sm-end d-sm-flex align-items-center" action="<?php bloginfo('url'); ?>/" role="search" method="get">
                  <div class="search-box me-2">
                    <div class="position-relative">
                        <input type="text" class="form-control border-0" name="s" id="s "placeholder="Search...">
                        <i class="bx bx-search-alt search-icon"></i>
                    </div>
                  </div>
                </form>
              </div>
          </div>
          <div class="row">
                <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                  <div class="col-xl-4 col-sm-6">
                    <div class="card">
                      <div class="card-body">
                          <div class="product-img position-relative">
                            <div class="avatar-sm product-ribbon">
                              <?php if ( $product->is_on_sale() && $product->get_regular_price() && $product->get_sale_price()) : ?>
                                <span class="avatar-title rounded-circle  bg-primary">- <?php echo percentSale($product->get_regular_price(), $product->get_sale_price()); ?> %</span>
                              <?php endif; ?>
                            </div>
                                <a href="<?php the_permalink(); ?>"><img style="width:100%;height:auto;" src="<?php echo esc_url(wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full')[0]); ?>"></a>
                          </div>
                          <div class="mt-3 text-center">
                              <h5 class="mb-2 text-truncate"><a href="<?php the_permalink(); ?>" class="text-dark"><?php the_title(); ?></a></h5>
                              <h5 class="my-0"><span class="text-muted me-2"></span> <b><?php echo $product->get_price_html(); ?></b></h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endwhile;  ?>
                  <?php endif; ?>
                </div>
                  <?php ftios_pagenavi($agrs); ?>
              </div>
          </div>
                     <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
</div>
<?php get_footer(); ?>
