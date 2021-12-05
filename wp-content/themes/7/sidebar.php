<?php
	/**
	 * The sidebar containing the main widget area
	 *
	 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
	 *
	 * @package Bootscore
	 */

	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		return;
	}
	?>

<div class="col-lg-3 d-none d-lg-block">
  <div class="card">
    <div class="card-body">


        <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
        <?php endif; ?>


    </div>
  </div>
</div>
