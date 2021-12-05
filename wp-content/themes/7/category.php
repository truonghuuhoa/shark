<?php get_header(); ?>
<?php $class_col = "col-12";

if ( is_active_sidebar( 'sidebar-2' ) ) {
    $class_col = "col-12 col-xl-9 col-lg-8";
} ?>
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
      <div class="row">
          <div class="<?php echo $class_col; ?>">
                  <div class="tab-content">
                      <div class="tab-pane active" id="all-post" role="tabpanel">
                          <div>
                              <div class="row">
                                  <div>
                                      <div class="row">
                                        <?php if (have_posts()) : ?>
                                        <?php while (have_posts()) : the_post(); ?>
                                          <div class="col-sm-4">
                                              <div class="card p-2 border shadow-none">

                                                  <?php if (has_post_thumbnail() ) { ?>
                                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="position-relative">

                                                      <div class="single-thumbnail" style="width: 100%;height: 200px;background-size: cover;display: block;background-position: center;background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>')"></div>
                                                    </a>
                                                  <?php } ?>

                                                  <div class="p-3">
                                                      <h5><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark"><?php the_title(); ?></a></h5>
                                                      <ul class="list-inline">
                                                          <li class="list-inline-item me-3">
                                                              <a href="javascript: void(0);" class="text-muted">
                                                                  <i class="bx bx-purchase-tag-alt align-middle text-muted me-1"></i> <?php the_category(', '); ?>
                                                              </a>
                                                          </li>
                                                          <li class="list-inline-item me-3">
                                                              <a href="javascript: void(0);" class="text-muted">
                                                                  <i class="mdi mdi-calendar me-1"></i><?php echo get_the_date('d-m-Y'); ?>
                                                              </a>
                                                          </li>
                                                      </ul>
                                                      <!-- <p><?php the_excerpt(); ?></p> -->

                                                      <div class="float-end">
                                                          <a href="<?php echo esc_url( get_permalink() ); ?>" class="text-primary">Read more <i class="mdi mdi-arrow-right"></i></a>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                        <?php endwhile;  ?>
                                        <?php endif; ?>
                                      </div>
                                      <div class="text-center">
                                          <?php ftios_pagenavi(); ?>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
          </div>

          <?php if ( is_active_sidebar( 'sidebar-2' ) ) { ?>
          <div class="col-xl-3 col-lg-4">
              <div class="card">
                  <div class="card-body p-4">
                      <div>
                        <?php dynamic_sidebar( 'sidebar-2' ); ?>
                      </div>

                  </div>
              </div>
          </div>
        <?php } ?>
      </div>
      <!-- end row -->
  </div> <!-- container-fluid -->
</section>
<?php get_footer(); ?>
