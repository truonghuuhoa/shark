<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                      <?php
                        $args = array(
                          'posts_per_page' => 3,
                          'post_type'      => 'slider'
                        );
                        $the_query = new WP_Query( $args );
                        $i = 1;
                      ?>
                     <?php if( $the_query->have_posts() ): ?>
                     <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
                       <?php if($i == 1){ ?>
                        <div class="carousel-item active">
                            <div class="d-block img-fluid" style="width: 100%;height: 335px;background-size: cover;display: block;background-position: center;background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>')"></div>
                        </div>
                        <?php } else { ?>
                        <div class="carousel-item">
                          <div class="d-block img-fluid" style="width: 100%;height: 335px;background-size: cover;display: block;background-position: center;background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>')"></div>
                        </div>
                      <?php } ?>
                      <?php $i++; ?>
                      <?php endwhile; ?>
                      <?php endif; ?>
                      <?php wp_reset_query(); ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <div id="carouselExampleCaption" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/small/33.gif" alt="..." class="d-block img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
