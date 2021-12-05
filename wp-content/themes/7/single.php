<?php get_header(); ?>
<section class="section" style="background: url('<?php echo get_template_directory_uri(); ?>/assets/images/build.png') bottom no-repeat;">
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
      <div class="col-xl-9">
        <div class="card">
            <div class="card-body">
                <div class="pt-3">
                    <div class="row justify-content-center single">
                        <div>
                            <div class="text-center">
                                <div class="mb-4">
                                  <a href="javascript: void(0);" class="text-muted">
                                      <i class="bx bx-purchase-tag-alt align-middle text-muted me-1"></i> <?php the_category(', '); ?>
                                  </a>
                                </div>
                                <h4><?php the_title(); ?></h4>
                                <p class="text-muted mb-4"><i class="mdi mdi-calendar me-1"></i> <?php echo get_the_date('d-m-Y'); ?></p>
                            </div>
                            <div class="mt-4">
                              <?php if(have_posts()) : ?>
                              <?php while (have_posts()) : the_post(); ?>
                                <div class="text-muted font-size-14">
                                    <p><?php the_content(); ?></p>
                                </div>
                              <?php endwhile; ?>
                            <?php endif; ?>
                                <hr>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
  <!-- end col -->
      <div class="col-xl-3">
        <div class="card">
            <div class="card-body p-4">
                <div>
                  <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
                  <?php dynamic_sidebar( 'sidebar-2' ); ?>
                  <?php endif; ?>
                </div>

            </div>
        </div>
        <!-- end card -->
      </div>
  </div>
  <!-- end row -->
</div>
</section>
<?php get_footer(); ?>
