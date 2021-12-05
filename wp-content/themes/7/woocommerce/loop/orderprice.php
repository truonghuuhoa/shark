
<?php

	if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
	} elseif  ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
			$link = get_post_type_archive_link( 'product' );
	} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
	} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
	} else {
			$queried_object = get_queried_object();
			$link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
	}

	if(!empty($_GET['orderby'])) {
		$link = add_query_arg(array('orderby' => $_GET['orderby']),$link);
	}

 $link = remove_query_arg(array('min_price','max_price'),$link);

?>

<div class="row">
<div class="col-12">
	<div class="float-end d-block mt-3 mb-2 filter-price">
	<ul class="filter">
	  <li class="frange">
			<a class="btn btn-outline-success btn-sm mb-2" href="<?php echo $link; ?>">
	      Tất cả
	    </a>
	    <a class="btn btn-outline-success btn-sm mb-2" href="<?php echo add_query_arg(array('max_price' => 2000000),$link); ?>">
	      Dưới 2 triệu
	    </a>
	    <a class="btn btn-outline-success btn-sm mb-2" href="<?php echo add_query_arg(array('min_price' => 2000000,'max_price' => 5000000),$link); ?>">
	      Từ 2 - 5 triệu
	    </a>
	    <a class="btn btn-outline-success btn-sm mb-2" href="<?php echo add_query_arg(array('min_price' => 5000000,'max_price' => 7000000),$link); ?>">
	      Từ 5 - 7 triệu
	    </a>
	    <a class="btn btn-outline-success btn-sm mb-2" href="<?php echo add_query_arg(array('min_price' => 7000000,'max_price' => 10000000),$link); ?>">
	      Từ 7 - 10 triệu
	    </a>
	    <a class="btn btn-outline-success btn-sm mb-2" href="<?php echo add_query_arg(array('min_price' => 10000000),$link); ?>">
	      Trên 10 triệu
	    </a>
	  </li>
	</ul>
	</div>
</div>
</div>
