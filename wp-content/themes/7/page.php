<?php get_header(); ?>
<div class="main-content" style="background: url('<?php echo get_template_directory_uri(); ?>/assets/images/build.png') bottom no-repeat;">
    <div class="page-content mt-2">
      <?php if(have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
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
            <?php the_content(); ?>
          </div>
        </div> <!-- container-fluid -->
      <?php endwhile; ?>
      <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
