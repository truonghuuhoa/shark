<?php get_header(); ?>
<div class="account-pages my-5 pt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h1 class="display-2 fw-medium">4<i class="bx bx-buoy bx-spin text-primary display-3"></i>4</h1>
                    <h4 class="text-uppercase">Sorry, page not found</h4>
                    <div class="mt-5 text-center">
                        <a class="btn btn-info waves-effect waves-light" href="<?php bloginfo('url'); ?>">Go to shop</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-xl-6">
                <div>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/error-img.png" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
