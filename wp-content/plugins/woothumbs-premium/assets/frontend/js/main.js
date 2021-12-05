(function( $, document ) {

	var fcms_woothumbs = {
		/**
		 * Set up cache with common elements and vars
		 */
		cache: function() {
			if ( fcms_woothumbs.cache_run ) {
				return;
			}

			fcms_woothumbs.els = {};
			fcms_woothumbs.vars = {};
			fcms_woothumbs.tpl = {};
			fcms_woothumbs.products = {};
			fcms_woothumbs.wishlist_adding = [];

			fcms_woothumbs.vars.d = new Date();

			// common elements
			fcms_woothumbs.els.all_images_wrap = $( '.fcms-woothumbs-all-images-wrap' );
			fcms_woothumbs.els.gallery = false;
			fcms_woothumbs.els.video_template = $( '#fcms-woothumbs-video-template' );

			// common vars
			fcms_woothumbs.vars.zoom_setup = false;
			fcms_woothumbs.vars.media_touch_timer = false;
			fcms_woothumbs.vars.window_resize_timeout = false;
			fcms_woothumbs.vars.is_dragging_image_slide = false;
			fcms_woothumbs.vars.is_rtl = fcms_woothumbs.is_true( fcms_woothumbs_vars.is_rtl );
			fcms_woothumbs.vars.images_are_vertical = fcms_woothumbs_vars.settings.carousel_general_mode === "vertical";
			fcms_woothumbs.vars.thumbnails_are_vertical = fcms_woothumbs_vars.settings.navigation_thumbnails_position === "left" || fcms_woothumbs_vars.settings.navigation_thumbnails_position === "right";
			fcms_woothumbs.vars.loading_class = "fcms-woothumbs-loading";
			fcms_woothumbs.vars.reset_class = "fcms-woothumbs-reset";
			fcms_woothumbs.vars.thumbnails_active_class = "fcms-woothumbs-thumbnails__slide--active";
			fcms_woothumbs.vars.wishlist_adding_class = "fcms-woothumbs-wishlist-buttons--adding";
			fcms_woothumbs.vars.wishlist_added_class = "fcms-woothumbs-wishlist-buttons--added";
			fcms_woothumbs.vars.is_zoom_enabled = fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.zoom_general_enable );
			fcms_woothumbs.vars.is_fullscreen_enabled = fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_enable );
			fcms_woothumbs.vars.show_variation_trigger = "fcms_woothumbs_show_variation";
			fcms_woothumbs.vars.loading_variation_trigger = "fcms_woothumbs_loading_variation";
			fcms_woothumbs.vars.fullscreen_trigger = fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_click_anywhere ) ? ".fcms-woothumbs-fullscreen, img" : ".fcms-woothumbs-fullscreen";
			fcms_woothumbs.vars.play_trigger = ".fcms-woothumbs-play";
			fcms_woothumbs.vars.media_controls_class = 'fcms-woothumbs-responsive-media__controls';
			fcms_woothumbs.vars.play_button_class = 'fcms-woothumbs-icon-play-alt';
			fcms_woothumbs.vars.pause_button_class = 'fcms-woothumbs-icon-pause';
			fcms_woothumbs.vars.play_controls_class = 'fcms-woothumbs-responsive-media__controls--play';
			fcms_woothumbs.vars.pause_controls_class = 'fcms-woothumbs-responsive-media__controls--pause';
			fcms_woothumbs.vars.window_size = {
				height: $( window ).height(),
				width: $( window ).width()
			};
			fcms_woothumbs.vars.fullscreeen_flag = false;

			// common templates
			fcms_woothumbs.tpl.prev_arrow = '<a href="javascript: void(0);" class="fcms-woothumbs-images__arrow fcms-woothumbs-images__arrow--prev"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-left-open-mini"></i></a>';
			fcms_woothumbs.tpl.next_arrow = '<a href="javascript: void(0);" class="fcms-woothumbs-images__arrow fcms-woothumbs-images__arrow--next"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-right-open-mini"></i></a>';
			fcms_woothumbs.tpl.prev_arrow_rtl = fcms_woothumbs.tpl.next_arrow;
			fcms_woothumbs.tpl.next_arrow_rtl = fcms_woothumbs.tpl.prev_arrow;

			fcms_woothumbs.tpl.fullscreen_button = '<a href="javascript: void(0);" class="fcms-woothumbs-fullscreen" data-fcms-woothumbs-tooltip="' + fcms_woothumbs_vars.text.fullscreen + '"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-fullscreen"></i></a>';
			fcms_woothumbs.tpl.play_button = '<a href="javascript: void(0);" class="fcms-woothumbs-play" data-fcms-woothumbs-tooltip="' + fcms_woothumbs_vars.text.video + '"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-play"></i></a>';
			fcms_woothumbs.tpl.temp_images_container = '<div class="fcms-woothumbs-temp"><div class="fcms-woothumbs-temp__images"></div><div class="fcms-woothumbs-icon fcms-woothumbs-temp__thumbnails"></div></div>';
			fcms_woothumbs.tpl.image_slide = '<div class="fcms-woothumbs-images__slide"><img class="fcms-woothumbs-images__image no-lazyload" src="{{image_src}}" srcset="{{image_srcset}}" sizes="{{image_sizes}}" data-caption="{{image_caption}}" data-large_image="{{large_image_src}}" data-large_image_width="{{large_image_width}}" data-large_image_height="{{large_image_height}}" width="{{image_width}}" height="{{image_height}}" title="{{title}}" alt="{{alt}}" {{style}} {{data_src}}></div>';
			fcms_woothumbs.tpl.media_slide = '<div class="fcms-woothumbs-images__slide">{{media_embed}}</div>';
			fcms_woothumbs.tpl.thumbnail_slide = '<div class="fcms-woothumbs-thumbnails__slide {{slide_class}}" data-index="{{index}}"><div class="fcms-woothumbs-thumbnails__image-wrapper">{{play_icon}}<img class="fcms-woothumbs-thumbnails__image" src="{{image_src}}" srcset="{{image_srcset}}" sizes="{{image_sizes}}" title="{{title}}" alt="{{alt}}" width="{{image_width}}" height="{{image_height}}"></div></div>';
			fcms_woothumbs.tpl.thumbnail_play_icon = '<div class="fcms-woothumbs-thumbnails__play-overlay"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-play"></i></div>';
			fcms_woothumbs.tpl.photoswipe = wp.template( 'fcms-woothumbs-pswp' );
			fcms_woothumbs.tpl.media = '<div class="fcms-woothumbs-fullscreen-video-wrapper">{{media_embed}}</div>';

			fcms_woothumbs.cache_run = true;

		},

		/**
		 * Run on doc ready
		 */

		on_load: function() {
			fcms_woothumbs.cache_run = false;
			fcms_woothumbs.cache();

			fcms_woothumbs.prepare_products();
			fcms_woothumbs.init();
		},

		/**
		 * Run on resize
		 */

		on_resize: function() {
			fcms_woothumbs.cache();

			clearTimeout( fcms_woothumbs.vars.window_resize_timeout );

			fcms_woothumbs.vars.window_resize_timeout = setTimeout( function() {
				var new_window = {
					height: $( window ).height(),
					width: $( window ).width()
				};
			
				// Dont trigger resize-end event if it is a fullscreen change.
				if (  fcms_woothumbs.vars.window_size.width !== new_window.width && ! fcms_woothumbs.vars.fullscreeen_flag ) {
					fcms_woothumbs.vars.window_size.width = new_window.width;
					fcms_woothumbs.vars.window_size.height = new_window.height;

					$( window ).trigger( 'resize-end' );
				}
			}, 100 );
		},

		/**
		 * Helper: Check whether a settings value is true
		 *
		 * @param str val
		 */

		is_true: function( val ) {
			return (parseInt( val ) === 1) ? true : false;
		},

		/**
		 * Helper: Check if a plugin or theme is active
		 *
		 * @param str name Name of the plugin or theme to check if is active
		 */

		is_active: function( name ) {

			if ( name === "woothemes_swatches" ) {

				return ($( '#swatches-and-photos-css' ).length > 0) ? true : false;

			}

			return false;

		},

		/**
		 * Get all products on page with WooThumbs
		 * and assign to the fcms_woothumbs.products variable
		 */
		prepare_products: function() {

			if ( fcms_woothumbs.els.all_images_wrap.length <= 0 ) {
				return;
			}

			fcms_woothumbs.els.all_images_wrap.each( function( index, element ) {

				var $all_images_wrap = $( element ),
					$product = $all_images_wrap.closest( '.product' ),
					is_variable = $all_images_wrap.data( 'product-type' ) === "variable" || $all_images_wrap.data( 'product-type' ) === "variable-subscription",
					$variations_form = is_variable ? $product.find( 'form.variations_form' ) : false,
					variations_json = $variations_form ? $variations_form.attr( 'data-product_variations' ) : false;

				fcms_woothumbs.products[ index ] = {
					'product': $product,
					'all_images_wrap': $all_images_wrap,
					'images': $all_images_wrap.find( '.fcms-woothumbs-images' ),
					'images_wrap': $all_images_wrap.find( '.fcms-woothumbs-images-wrap' ),
					'thumbnails': $all_images_wrap.find( '.fcms-woothumbs-thumbnails' ),
					'thumbnails_wrap': $all_images_wrap.find( '.fcms-woothumbs-thumbnails-wrap' ),
					'variations_form': $variations_form,
					'variation_id_field': $variations_form ? $variations_form.find( 'input[name=variation_id]' ) : false,
					'wishlist_buttons': $all_images_wrap.find( '.fcms-woothumbs-wishlist-buttons' ),
					'play_button': $all_images_wrap.find( '.fcms-woothumbs-play' ),
					'wishlist_add_button': $all_images_wrap.find( '.fcms-woothumbs-wishlist-buttons__add' ),
					'wishlist_browse_button': $all_images_wrap.find( '.fcms-woothumbs-wishlist-buttons__browse' ),
					'variations_json': variations_json,
					'maintain_slide_index': $all_images_wrap.attr( 'data-maintain-slide-index' ) === "yes",
					'variations': variations_json ? JSON.parse( variations_json ) : false,
					'product_id': $variations_form ? $variations_form.data( 'product_id' ) : false,
					'default_images': JSON.parse( $all_images_wrap.attr( 'data-default' ) ),
					'imagezoom': false,
					'caption': $all_images_wrap.find( '.fcms-woothumbs-caption' )
				};

			} );

		},

		/**
		 * Init WooThumbs
		 */
		init: function() {
			if ( fcms_woothumbs.products.length <= 0 ) {
				return;
			}

			$.each( fcms_woothumbs.products, function( index, product_object ) {
				fcms_woothumbs.setup_sliders( product_object );
				fcms_woothumbs.watch_variations( product_object );
				fcms_woothumbs.setup_zoom( product_object );
				fcms_woothumbs.setup_fullscreen( product_object );
				fcms_woothumbs.setup_video( product_object );
				fcms_woothumbs.watch_yith_wishlist( product_object );
				fcms_woothumbs.setup_media_controls( product_object );
			} );

			fcms_woothumbs.setup_yith_wishlist();
			fcms_woothumbs.setup_tooltips();
		},

		/**
		 * Helper: Lazy load images to improve loading speed
		 */

		lazy_load_images: function( product_object ) {

			var $images = product_object.images.find( 'img' );

			if ( $images.length > 0 ) {
				$images.each( function( index, el ) {
					var $image = $( el ),
						data_src = $image.attr( 'data-fcms-woothumbs-src' );

					if ( typeof data_src !== "undefined" ) {
						var $image_clone = $image.clone();

						$image_clone
							.attr( 'src', data_src ).css( { paddingTop: "", height: "" } )
							.removeAttr( "data-fcms-woothumbs-src" );
						$image.replaceWith( $image_clone );
					}
				} );
			}

			var $media = product_object.images.find( '.fcms-woothumbs-responsive-media' );

			if ( $media.length > 0 ) {
				$media.each( function( index, el ) {
					$( el ).show();
				} );
			}

		},

		/**
		 * Images Slider Args
		 *
		 * Dynamic so the options are recalculated every time
		 */

		images_slider_args: function( product_object, index ) {

			var args = {},
				image_count = product_object.images.children().length;

			args.initialSlide = typeof index !== 'undefined' && image_count > index ? index : 0;
			args.speed = parseInt( fcms_woothumbs_vars.settings.carousel_general_transition_speed );
			args.arrows = (image_count > 1) ? fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.navigation_general_controls ) : false;
			args.infinite = (image_count > 1) ? fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.carousel_general_infinite_loop ) : false;
			args.touchMove = (image_count > 1) ? true : false;
			args.adaptiveHeight = false;
			args.autoplay = fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.carousel_general_autoplay );
			args.autoplaySpeed = parseInt( fcms_woothumbs_vars.settings.carousel_general_duration );
			args.dots = fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.navigation_bullets_enable );
			args.prevArrow = fcms_woothumbs.vars.is_rtl ? fcms_woothumbs.tpl.prev_arrow_rtl : fcms_woothumbs.tpl.prev_arrow;
			args.nextArrow = fcms_woothumbs.vars.is_rtl ? fcms_woothumbs.tpl.next_arrow_rtl : fcms_woothumbs.tpl.next_arrow;
			args.respondTo = 'slider';
			args.centerPadding = 0;
			args.touchThreshold = fcms_woothumbs_vars.settings.carousel_general_main_slider_swipe_threshold;

			if ( fcms_woothumbs.vars.images_are_vertical ) {
				args.vertical = true;
			} else if ( fcms_woothumbs_vars.settings.carousel_general_mode === "fade" ) {
				args.fade = true;
			}

			if ( fcms_woothumbs.vars.images_are_vertical !== true ) {
				args.rtl = fcms_woothumbs.vars.is_rtl;
			}

			return args;

		},

		/**
		 * Thumbnails Slider Args
		 *
		 * Dynamic so the options are recalculated every time
		 *
		 * @param product_object
		 */

		thumbnails_slider_args: function( product_object ) {
			var args = {};

			args.infinite = false;
			args.speed = parseInt( fcms_woothumbs_vars.settings.navigation_thumbnails_transition_speed );
			args.slidesToShow = fcms_woothumbs.get_slides_to_show();
			args.slidesToScroll = 1;
			args.arrows = false;
			args.vertical = false;
			args.centerMode = false;
			args.swipeToSlide = true;

			if ( fcms_woothumbs.vars.thumbnails_are_vertical ) {
				args.vertical = true;
			} else {
				args.rtl = fcms_woothumbs.vars.is_rtl;
			}

			if ( fcms_woothumbs.is_below_breakpoint() && fcms_woothumbs.move_thumbnails_at_breakpoint() ) {
				args.vertical = false;
			}

			return args;
		},

		/**
		 * Toggle controls.
		 */
		toggle_controls: function( $current_slide, product_object ) {
			if ( fcms_woothumbs.is_media( $current_slide ) ) {
				fcms_woothumbs.hide_controls( product_object );
			} else {
				fcms_woothumbs.show_controls( product_object );
				fcms_woothumbs.toggle_fullscreen_control( $current_slide, product_object );
			}
		},

		/**
		 * Toggle fullscreen control.
		 */
		toggle_fullscreen_control: function( $current_slide, product_object ) {
			var $current_image = $current_slide.find( 'img' ),
				$fullscreen_button = product_object.all_images_wrap.find( fcms_woothumbs.vars.fullscreen_trigger ).not( 'img' );

			if ( fcms_woothumbs.is_placeholder( $current_image ) ) {
				$fullscreen_button.hide();
			} else {
				$fullscreen_button.show();
			}
		},

		/**
		 * Hide controls.
		 */
		hide_controls: function( product_object ) {
			product_object.images_wrap.addClass( 'fcms-woothumbs-images-wrap--hide-controls' );
		},

		/**
		 * Show controls.
		 */
		show_controls: function( product_object ) {
			product_object.images_wrap.removeClass( 'fcms-woothumbs-images-wrap--hide-controls' );
		},

		/**
		 * Helper: Is palcehodler?
		 */
		is_placeholder: function( image, src_only ) {

			var src = src_only === true ? image : image.attr( 'src' );

			if ( src == null ) {
				return false;
			}

			return src.indexOf( "placeholder.png" ) >= 0;

		},

		/**
		 * Helper: Is media?
		 */
		is_media: function( $slide ) {
			var $media = $slide.find( 'iframe, video, object' );

			if ( $media.length > 0 ) {
				return true;
			}

			return false;
		},

		/**
		 * Get slides to show
		 *
		 * @return int
		 */
		get_slides_to_show: function() {

			return fcms_woothumbs.is_below_breakpoint() ? parseInt( fcms_woothumbs_vars.settings.responsive_general_thumbnails_count ) : parseInt( fcms_woothumbs_vars.settings.navigation_thumbnails_count );

		},

		/**
		 * Get thumbnail count
		 *
		 * @param product_object
		 * @return int
		 */
		get_thumbnail_count: function( product_object ) {

			return product_object.thumbnails.find( '.fcms-woothumbs-thumbnails__slide' ).length;

		},

		/**
		 * Setup sliders
		 *
		 * @param product_object
		 */

		setup_sliders: function( product_object ) {

			fcms_woothumbs.setup_images_events( product_object );
			fcms_woothumbs.setup_thumbnails_events( product_object );

			fcms_woothumbs.init_images( product_object );
			fcms_woothumbs.init_thumbnails( product_object );

		},

		/**
		 * Setup events for Images slider
		 *
		 * @param product_object
		 */
		setup_images_events: function( product_object ) {
			// On resize.
			$( window ).on( 'resize-end', function() {
				fcms_woothumbs.maybe_resize_wrap( product_object );
			} );

			// On init
			product_object.images.on( 'init', function( event, slick ) {
				var $current_slide = product_object.images.find( '.slick-active' ),
					$current_image = $current_slide.find( 'img:first' );

				fcms_woothumbs.go_to_thumbnail( slick.currentSlide, product_object );

				fcms_woothumbs.init_zoom( $current_image, product_object );
				fcms_woothumbs.update_caption( $current_image, product_object );
				fcms_woothumbs.toggle_controls( $current_slide, product_object );
				fcms_woothumbs.reveal_slides( product_object );

				$( window ).trigger( 'resize' );
			} );

			product_object.images.on( 'init_zoom', function( event ) {
				var $current_slide = product_object.images.find( '.slick-active' ),
					$current_image = $current_slide.find( 'img:first' );

				fcms_woothumbs.init_zoom( $current_image, product_object );
			} );

			// On before slide change
			product_object.images.on( 'beforeChange', function( event, slick, current_slide_index, next_slide_index ) {
				fcms_woothumbs.go_to_thumbnail( next_slide_index, product_object );

				if ( product_object.imagezoom ) {
					product_object.imagezoom.destroy();
				}
			} );

			// On after slide change
			product_object.images.on( 'afterChange', function( event, slick, current_slide_index ) {
				var $current_slide = fcms_woothumbs.get_slide_by_index( product_object, current_slide_index ),
					$current_image = $current_slide.find( 'img:first' );

				fcms_woothumbs.init_zoom( $current_image, product_object );
				fcms_woothumbs.update_caption( $current_image, product_object );
				fcms_woothumbs.toggle_controls( $current_slide, product_object );
				fcms_woothumbs.stop_media( product_object );
				fcms_woothumbs.start_media( product_object );
			} );

			// setup stop auto
			product_object.all_images_wrap.on( 'click', ".fcms-woothumbs-thumbnails__slide, .fcms-woothumbs-images__arrow, .fcms-woothumbs-zoom-prev, .fcms-woothumbs-zoom-next, .slick-dots button", function() {
				product_object.images.slick( 'slickPause' );
			} );
		},

		/**
		 * Setup events for Thumbnails slider
		 *
		 * @param product_object
		 */
		setup_thumbnails_events: function( product_object ) {
			// On init
			product_object.thumbnails.on( 'init', function( event, slick ) {
				fcms_woothumbs.reveal_thumbnails( product_object );
				fcms_woothumbs.set_thumbnail_controls_visibility( product_object );
			} );

			// On after slide change
			product_object.thumbnails.on( 'afterChange', function( event, slick, current_slide_index ) {
				fcms_woothumbs.set_thumbnail_controls_visibility( product_object );
			} );

			// setup click thumbnail action
			product_object.all_images_wrap.on( 'click', ".fcms-woothumbs-thumbnails__slide", function() {
				if ( product_object.all_images_wrap.hasClass( fcms_woothumbs.vars.loading_class ) ) {
					return;
				}

				if ( !product_object ) {
					return;
				}

				var new_index = parseInt( $( this ).attr( 'data-index' ) );

				fcms_woothumbs.set_active_thumbnail( product_object.thumbnails, new_index );
				product_object.images.slick( 'slickGoTo', new_index );
			} );

			// setup click thumbnail control action
			product_object.all_images_wrap.on( 'click', ".fcms-woothumbs-thumbnails__control", function() {
				if ( !product_object.all_images_wrap.hasClass( fcms_woothumbs.vars.loading_class ) ) {
					var dir = $( this ).attr( 'data-direction' );

					if ( dir === "next" ) {
						product_object.thumbnails.slick( 'slickNext' );
					} else {
						product_object.thumbnails.slick( 'slickPrev' );
					}
				}
			} );

			// On window resize
			$( window ).on( 'resize-end', function() {
				fcms_woothumbs.position_thumbnails( product_object );
				fcms_woothumbs.resize_thumbnails( product_object );
			} );
		},

		/**
		 * Reveal thumbnails
		 *
		 * @param product_object
		 */
		reveal_thumbnails: function( product_object ) {
			product_object.thumbnails_wrap.height( '' ).removeClass( 'fcms-woothumbs-thumbnails-wrap--hidden' );
		},

		/**
		 * Init Images slider
		 *
		 * @param product_object
		 */
		init_images: function( product_object ) {
			if ( product_object.images.length <= 0 ) {
				return;
			}

			fcms_woothumbs.maybe_resize_wrap( product_object );
			product_object.images.not( '.slick-initialized' ).slick( fcms_woothumbs.images_slider_args( product_object ) );

			// Refresh after images are loaded, again.
			fcms_woothumbs.images_loaded( product_object.images, function() {
				product_object.images.slick( 'slickSetOption', 'adaptiveHeight', true, true );
				fcms_woothumbs.lazy_load_images( product_object );
				product_object.images[ 0 ].slick.refresh();
			} );

			product_object.images_slider_data = product_object.images.length > 0;
		},

		/**
		 * Give images a fixed width to prevent fractional width and slide peep.
		 */
		maybe_resize_wrap: function( product_object ) {
			product_object.all_images_wrap.width( '' ).width( 2 * Math.floor( product_object.all_images_wrap.width() / 2 ) );

			var $slick_elements = product_object.all_images_wrap.find( '.slick-slider' );

			if ( $slick_elements.length <= 0 ) {
				return;
			}

			$slick_elements.each( function( index, slick_element ) {
				if ( typeof slick_element.slick === 'undefined' ) {
					return;
				}

				slick_element.slick.refresh();
			} );
		},

		/**
		 * Init Thumbnails slider
		 *
		 * @param product_object
		 */
		init_thumbnails: function( product_object ) {
			if ( product_object.thumbnails.find( 'img' ).length <= 0 ) {
				return;
			}

			if ( !fcms_woothumbs.sliding_thumbnails_enabled() ) {
				fcms_woothumbs.reveal_thumbnails( product_object );
				return;
			}

			fcms_woothumbs.images_loaded( product_object.thumbnails, function () {
				// Don't call slick when slides_to_show is 0, to prevent browser crash.
				if ( fcms_woothumbs.get_slides_to_show() <= 0 ) {
					product_object.thumbnails.hide();
					return;
				}

				product_object.thumbnails.not( '.slick-initialized' ).slick( fcms_woothumbs.thumbnails_slider_args( product_object ) );
				product_object.thumbnails_slider_data = product_object.thumbnails.length > 0;
				fcms_woothumbs.position_thumbnails( product_object );
			} );
		},

		/**
		 * Resize and position thumbnails
		 *
		 * @param product_object
		 */
		resize_thumbnails: function( product_object ) {
			if ( !product_object.thumbnails_slider_data ) {
				return;
			}

			var slides_to_show = fcms_woothumbs.get_slides_to_show();

			product_object.thumbnails.slick( 'slickSetOption', 'slidesToShow', slides_to_show );

			fcms_woothumbs.refresh_slider( product_object.thumbnails );
		},

		/**
		 * Refresh slider (triggers resize)
		 *
		 * @param $slider
		 */
		refresh_slider: function( $slider ) {

			if ( typeof $slider[ 0 ].slick === "undefined" ) {
				return;
			}

			$slider[ 0 ].slick.refresh();
			$slider.trigger( 'refresh' );

		},

		/**
		 * Helper: Get slide by index
		 *
		 * @param product_object
		 * @param int index
		 * @return obj
		 */
		get_slide_by_index: function( product_object, index ) {

			return product_object.images.find( '[data-slick-index="' + index + '"]' );

		},

		/**
		 * Helper: Are sliding thumbnails enabled?
		 *
		 * @param product_object
		 */

		sliding_thumbnails_enabled: function() {

			return fcms_woothumbs_vars.settings.navigation_thumbnails_type === "sliding";

		},

		/**
		 * Helper: Do we have thumbnails at all?
		 *
		 * @param product_object
		 * @param bool thumbnails
		 */

		has_thumbnails: function( product_object ) {

			return (fcms_woothumbs.get_thumbnail_count( product_object ) > 0 || thumbnails) && (fcms_woothumbs_vars.settings.navigation_thumbnails_type === "sliding" || fcms_woothumbs_vars.settings.navigation_thumbnails_type === "stacked");

		},

		/**
		 * Helper: Are thumbnails enabled?
		 */
		thumbnails_enabled: function() {

			return fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.navigation_thumbnails_enable );

		},

		/**
		 * Helper: Move thumbnails at breakpoint?
		 */

		move_thumbnails_at_breakpoint: function() {

			return fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.responsive_general_thumbnails_below ) && fcms_woothumbs_vars.settings.navigation_thumbnails_position !== "below";

		},

		/**
		 * Helper: Is the window width below our breakpoint limit
		 */

		is_below_breakpoint: function() {

			return fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.responsive_general_breakpoint_enable ) && fcms_woothumbs.viewport().width <= parseInt( fcms_woothumbs_vars.settings.responsive_general_breakpoint, 10 );

		},

		/**
		 * Helper: Get viewport dimensions
		 */

		viewport: function() {

			var e = window, a = 'inner';

			if ( !('innerWidth' in window) ) {
				a = 'client';
				e = document.documentElement || document.body;
			}

			return { width: e[ a + 'Width' ], height: e[ a + 'Height' ] };

		},

		/**
		 * Helper: Position thumbnails
		 *
		 * @param product_object
		 */

		position_thumbnails: function( product_object ) {

			if ( !fcms_woothumbs.move_thumbnails_at_breakpoint() ) {
				return;
			}

			if ( fcms_woothumbs.get_thumbnail_count( product_object ) <= 0 ) {
				return;
			}

			var $next_controls = product_object.all_images_wrap.find( '.fcms-woothumbs-thumbnails__control--right, .fcms-woothumbs-thumbnails__control--down' ),
				$prev_controls = product_object.all_images_wrap.find( '.fcms-woothumbs-thumbnails__control--left, .fcms-woothumbs-thumbnails__control--up' );

			if ( fcms_woothumbs.is_below_breakpoint() ) {

				product_object.all_images_wrap.removeClass( 'fcms-woothumbs-all-images-wrap--thumbnails-left fcms-woothumbs-all-images-wrap--thumbnails-right fcms-woothumbs-all-images-wrap--thumbnails-above' ).addClass( 'fcms-woothumbs-all-images-wrap--thumbnails-below' );

				product_object.images_wrap.after( product_object.thumbnails_wrap );
				product_object.thumbnails_wrap.removeClass( 'fcms-woothumbs-thumbnails-wrap--vertical' ).addClass( 'fcms-woothumbs-thumbnails-wrap--horizontal' );

				$next_controls.removeClass( 'fcms-woothumbs-thumbnails__control--down' ).addClass( 'fcms-woothumbs-thumbnails__control--right' )
					.find( 'i' ).removeClass( 'fcms-woothumbs-icon-down-open-mini' ).addClass( 'fcms-woothumbs-icon-right-open-mini' );
				$prev_controls.removeClass( 'fcms-woothumbs-thumbnails__control--up' ).addClass( 'fcms-woothumbs-thumbnails__control--left' )
					.find( 'i' ).removeClass( 'fcms-woothumbs-icon-up-open-mini' ).addClass( 'fcms-woothumbs-icon-left-open-mini' );

				if ( product_object.thumbnails_slider_data && fcms_woothumbs.sliding_thumbnails_enabled() ) {
					product_object.thumbnails.slick( 'slickSetOption', 'vertical', false ).removeClass( 'slick-vertical' );
				}

			} else {

				product_object.all_images_wrap.removeClass( 'fcms-woothumbs-all-images-wrap--thumbnails-below' ).addClass( 'fcms-woothumbs-all-images-wrap--thumbnails-' + fcms_woothumbs_vars.settings.navigation_thumbnails_position );

				if ( fcms_woothumbs_vars.settings.navigation_thumbnails_position === "left" || fcms_woothumbs_vars.settings.navigation_thumbnails_position === "above" ) {

					product_object.images_wrap.before( product_object.thumbnails_wrap );

				}

				if ( fcms_woothumbs_vars.settings.navigation_thumbnails_position === "left" || fcms_woothumbs_vars.settings.navigation_thumbnails_position === "right" ) {

					product_object.thumbnails_wrap.removeClass( 'fcms-woothumbs-thumbnails-wrap--horizontal' ).addClass( 'fcms-woothumbs-thumbnails-wrap--vertical' );

					$next_controls.removeClass( 'fcms-woothumbs-thumbnails__control--right' ).addClass( 'fcms-woothumbs-thumbnails__control--down' )
						.find( 'i' ).removeClass( 'fcms-woothumbs-icon-right-open-mini' ).addClass( 'fcms-woothumbs-icon-down-open-mini' );
					$prev_controls.removeClass( 'fcms-woothumbs-thumbnails__control--left' ).addClass( 'fcms-woothumbs-thumbnails__control--up' )
						.find( 'i' ).removeClass( 'fcms-woothumbs-icon-left-open-mini' ).addClass( 'fcms-woothumbs-icon-up-open-mini' );

					if ( product_object.thumbnails_slider_data && fcms_woothumbs.sliding_thumbnails_enabled() ) {
						product_object.thumbnails.slick( 'slickSetOption', 'vertical', true ).addClass( 'slick-vertical' );
					}

				}

			}

		},

		/**
		 * Helper: Set visibility of thumbnail controls
		 *
		 * @param product_object
		 */

		set_thumbnail_controls_visibility: function( product_object ) {

			var $slick_track = product_object.thumbnails.find( '.slick-track' ),
				track_position = null,
				track_size = null,
				thumbnails_size = null,
				end_position = null,
				$next_controls = product_object.all_images_wrap.find( '.fcms-woothumbs-thumbnails__control--right, .fcms-woothumbs-thumbnails__control--down' ),
				$prev_controls = product_object.all_images_wrap.find( '.fcms-woothumbs-thumbnails__control--left, .fcms-woothumbs-thumbnails__control--up' );

			if ( fcms_woothumbs.thumbnails_slider_args().vertical ) {

				track_position = $slick_track.position().top;
				track_size = $slick_track.height();
				thumbnails_size = product_object.thumbnails.height();

			} else {

				track_position = $slick_track.position().left;
				track_size = $slick_track.width();
				thumbnails_size = product_object.thumbnails.width();

			}

			end_position = - (track_size - thumbnails_size - parseInt( fcms_woothumbs_vars.settings.navigation_thumbnails_spacing ));

			$prev_controls.show();
			$next_controls.show();

			if ( track_position <= 1 && track_position >= - 1 ) {

				$prev_controls.hide();

			} else if ( fcms_woothumbs.get_difference( track_position, end_position ) <= 5 ) {

				$next_controls.hide();
			}

			if ( fcms_woothumbs.get_thumbnail_count( product_object ) <= fcms_woothumbs.get_slides_to_show() ) {

				$prev_controls.hide();
				$next_controls.hide();

			}

		},

		/**
		 * Get difference between 2 numbers
		 *
		 * @param int number_1
		 * @param int number_2
		 * @return int
		 */
		get_difference: function( number_1, number_2 ) {

			return Math.abs( number_1 - number_2 );

		},

		/**
		 * Helper: Set active thumbnail
		 *
		 * @param $thumbnails
		 * @param int index
		 */

		set_active_thumbnail: function( $thumbnails, index ) {

			$thumbnails.find( ".fcms-woothumbs-thumbnails__slide" ).removeClass( fcms_woothumbs.vars.thumbnails_active_class );
			$thumbnails.find( ".fcms-woothumbs-thumbnails__slide[data-index=" + index + "]" ).addClass( fcms_woothumbs.vars.thumbnails_active_class );

		},

		/**
		 * Helper: Go to thumbnail
		 *
		 * @param int index
		 */

		go_to_thumbnail: function( index, product_object ) {

			if ( product_object.thumbnails_slider_data ) {

				var thumbnail_index = fcms_woothumbs.get_thumbnail_index( index, product_object );

				product_object.thumbnails.slick( 'slickGoTo', thumbnail_index );

			}

			fcms_woothumbs.set_active_thumbnail( product_object.thumbnails, index );
		},

		/**
		 * Helper: Get thumbnail index
		 *
		 * @param int index
		 */

		get_thumbnail_index: function( index, product_object ) {

			if ( parseInt( fcms_woothumbs_vars.settings.navigation_thumbnails_count ) === 1 ) {
				return index;
			}

			var last_thumbnail_index = fcms_woothumbs.get_last_thumbnail_index( product_object ),
				new_thumbnail_index = (index > last_thumbnail_index) ? last_thumbnail_index : (index === 0) ? 0 : index - 1;

			return new_thumbnail_index;

		},

		/**
		 * Helper: Get thumbnail index
		 *
		 * @param product_object
		 */

		get_last_thumbnail_index: function( product_object ) {

			var thumbnail_count = fcms_woothumbs.get_thumbnail_count( product_object ),
				last_slide_index = thumbnail_count - fcms_woothumbs_vars.settings.navigation_thumbnails_count;

			return last_slide_index;

		},

		/**
		 * Watch for changes in variations
		 *
		 * @param product_object
		 */

		watch_variations: function( product_object ) {
			if ( !product_object.variations_form ) {
				return;
			}

			product_object.variation_id_field.on( 'change', function() {
				var variation_id = parseInt( $( this ).val() ),
					currently_showing = parseInt( product_object.all_images_wrap.attr( 'data-showing' ) );

				if ( ! isNaN( variation_id ) && variation_id !== currently_showing && variation_id > 0 ) {
					fcms_woothumbs.get_variation_data( product_object, variation_id );
				}
			} );

			// on reset data trigger
			product_object.variations_form.on( 'reset_data', function() {
				fcms_woothumbs.reset_images( product_object );
			} );

			// on loading variation trigger
			product_object.all_images_wrap.on( fcms_woothumbs.vars.loading_variation_trigger, function( event ) {
				product_object.all_images_wrap.addClass( fcms_woothumbs.vars.loading_class );
			} );

			// on show variation trigger
			product_object.all_images_wrap.on( fcms_woothumbs.vars.show_variation_trigger, function( event, variation ) {
				fcms_woothumbs.load_images( product_object, variation );
			} );

			// Manually trigger the change to handle the default variations.
			if( $( product_object.variation_id_field ).val() ) {
				$( product_object.variation_id_field ).trigger( "change" );
			}
		},

		/**
		 * Load Images for variation ID
		 *
		 * @param product_object
		 * @param variation
		 */
		load_images: function( product_object, variation ) {
			if ( variation && typeof variation.jck_additional_images !== "undefined" ) {
				var image_count = variation.jck_additional_images.length;

				if ( image_count > 0 ) {
					product_object.all_images_wrap
						.attr( 'data-showing', variation.variation_id )
						.removeClass( fcms_woothumbs.vars.reset_class );

					fcms_woothumbs.replace_images( product_object, variation.jck_additional_images );
				} else {
					product_object.all_images_wrap.removeClass( fcms_woothumbs.vars.loading_class );
					fcms_woothumbs.reset_images( product_object );
				}
			} else {
				product_object.all_images_wrap.removeClass( fcms_woothumbs.vars.loading_class );
			}
		},

		/**
		 * Replace slider images
		 *
		 * @param product_object
		 * @param images
		 * @param callback
		 */
		replace_images: function( product_object, images, callback ) {
			fcms_woothumbs.remove_temporary_images();

			var temp_images = fcms_woothumbs.create_temporary_images( images, product_object ),
				current_slide_index = product_object.images.slick( 'slickCurrentSlide' ),
				has_thumbnails = temp_images.thumbnails.children().length > 0,
				thumbnails_html = temp_images.thumbnails.html(),
				images_html = temp_images.images.html();

			// once images have loaded, place them into the appropriate sliders
			fcms_woothumbs.images_loaded( temp_images.container, function() {
				if ( product_object.images_slider_data ) {
					product_object.images.slick( 'unslick' );
					product_object.images.html( images_html );
					fcms_woothumbs.init_images( product_object );
				}

				// If thumbnails are enabled
				if ( fcms_woothumbs.thumbnails_enabled() ) {
					product_object.thumbnails_wrap
						.height( product_object.thumbnails_wrap.height() )
						.addClass( 'fcms-woothumbs-thumbnails-wrap--hidden' );

					if ( product_object.thumbnails_slider_data ) {
						product_object.thumbnails.slick( 'unslick' );
						delete product_object.thumbnails[ 0 ].slick;
						product_object.thumbnails_slider_data = false;
					}

					product_object.thumbnails.html( thumbnails_html );

					if ( has_thumbnails && fcms_woothumbs.sliding_thumbnails_enabled() ) {
						fcms_woothumbs.init_thumbnails( product_object );
					} else {
						fcms_woothumbs.reveal_thumbnails( product_object );
					}
				}

				// maintain slide index
				if ( fcms_woothumbs.get_thumbnail_count( product_object ) > current_slide_index && product_object.maintain_slide_index && typeof current_slide_index !== "undefined" ) {
					product_object.images.slick( 'slickGoTo', current_slide_index );
					fcms_woothumbs.go_to_thumbnail( current_slide_index, product_object );
				}

				// remove loading icon
				product_object.all_images_wrap.removeClass( fcms_woothumbs.vars.loading_class );

				fcms_woothumbs.remove_temporary_images();
				fcms_woothumbs.setup_media_ended();

				product_object.all_images_wrap.trigger( 'fcms_woothumbs_images_loaded', [ product_object ] );

				// run a callback, if required
				if ( callback !== undefined ) {
					callback();
				}
			} );
		},

		/**
		 * Remove tempory images
		 */
		remove_temporary_images: function() {
			$( '.fcms-woothumbs-temp' ).remove();
		},

		/**
		 * Create temporary images
		 *
		 * @param images parsed JSON
		 */
		create_temporary_images: function( images, product_object ) {
			// add temp images container
			$( 'body' ).append( $( fcms_woothumbs.tpl.temp_images_container ).css( { width: product_object.images.outerWidth() } ) );

			var image_count = images.length,
				temp_images = {
					'container': $( '.fcms-woothumbs-temp' ),
					'images': $( '.fcms-woothumbs-temp__images' ),
					'thumbnails': $( '.fcms-woothumbs-temp__thumbnails' )
				};

			// loop through additional images
			$.each( images, function( index, image_data ) {
				// add images to temp div
				var src = index === 0 ? image_data.src : "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=",
					data_src = index === 0 ? false : 'data-fcms-woothumbs-src="' + image_data.src + '"',
					aspect = index === 0 ? false : (image_data.src_h / image_data.src_w) * 100,
					style = aspect ? 'style="padding-top: ' + aspect + '%; height: 0px;"' : "",
					slide_html = '';

				if ( image_data.media_embed ) {
					slide_html = fcms_woothumbs.tpl.media_slide.replace( "{{media_embed}}", image_data.media_embed );
				} else {
					slide_html = fcms_woothumbs.tpl.image_slide
						.replace( /{{image_src}}/g, src )
						.replace( "{{image_srcset}}", fcms_woothumbs.maybe_empty( image_data.srcset ) )
						.replace( "{{image_sizes}}", fcms_woothumbs.maybe_empty( image_data.sizes ) )
						.replace( "{{image_caption}}", fcms_woothumbs.maybe_empty( image_data.caption ) )
						.replace( "{{large_image_src}}", fcms_woothumbs.maybe_empty( image_data.large_src ) )
						.replace( "{{large_image_width}}", fcms_woothumbs.maybe_empty( image_data.large_src_w ) )
						.replace( "{{large_image_height}}", fcms_woothumbs.maybe_empty( image_data.large_src_h ) )
						.replace( "{{image_width}}", fcms_woothumbs.maybe_empty( image_data.src_w ) )
						.replace( "{{image_height}}", fcms_woothumbs.maybe_empty( image_data.src_h ) )
						.replace( "{{alt}}", image_data.alt )
						.replace( "{{style}}", style )
						.replace( "{{data_src}}", data_src )
						.replace( "{{title}}", image_data.title );
				}

				temp_images.images.append( slide_html );

				// add thumbnails to temp div if thumbnails are enabled
				if ( image_count > 1 && fcms_woothumbs.thumbnails_enabled() ) {
					var play_icon = image_data.media_embed && image_data.no_media_icon !== true ? fcms_woothumbs.tpl.thumbnail_play_icon : '';

					var thumbnail_html =
						fcms_woothumbs.tpl.thumbnail_slide
							.replace( "{{play_icon}}", play_icon )
							.replace( /{{image_src}}/g, image_data.gallery_thumbnail_src )
							.replace( "{{image_srcset}}", fcms_woothumbs.maybe_empty( image_data.gallery_thumbnail_srcset ) )
							.replace( "{{image_sizes}}", fcms_woothumbs.maybe_empty( image_data.gallery_thumbnail_sizes ) )
							.replace( "{{index}}", index )
							.replace( "{{image_width}}", fcms_woothumbs.maybe_empty( image_data.gallery_thumbnail_src_w ) )
							.replace( "{{image_height}}", fcms_woothumbs.maybe_empty( image_data.gallery_thumbnail_src_h ) )
							.replace( "{{alt}}", image_data.alt )
							.replace( "{{title}}", image_data.title )
							.replace( "{{slide_class}}", index === 0 ? fcms_woothumbs.vars.thumbnails_active_class : "" );

					temp_images.thumbnails.append( thumbnail_html );
				}
			} );

			// pad out the thumbnails if there is less than the
			// amount that are meant to be displayed.
			if ( product_object.thumbnails_slider_data && image_count !== 1 && image_count < fcms_woothumbs_vars.settings.navigation_thumbnails_count ) {
				var empty_count = fcms_woothumbs_vars.settings.navigation_thumbnails_count - image_count;
				i = 0;

				while ( i < empty_count ) {
					temp_images.thumbnails.append( '<div></div>' );
					i ++;
				}
			}

			return temp_images;
		},

		/**
		 * Helper: maybe empty
		 *
		 * @param value
		 * @return str
		 */
		maybe_empty: function( value ) {

			return value ? value : "";

		},

		/**
		 * Reset Images to defaults
		 *
		 * @param product_object
		 */

		reset_images: function( product_object ) {
			if ( product_object.all_images_wrap.hasClass( fcms_woothumbs.vars.reset_class ) || product_object.all_images_wrap.hasClass( fcms_woothumbs.vars.loading_class ) ) {
				return;
			}

			product_object.all_images_wrap.trigger( fcms_woothumbs.vars.loading_variation_trigger );
			product_object.all_images_wrap.attr( 'data-showing', product_object.product_id );

			// set reset class
			product_object.all_images_wrap.addClass( fcms_woothumbs.vars.reset_class );

			// replace images
			fcms_woothumbs.replace_images( product_object, product_object.default_images );
		},

		/**
		 * Helper: Check if final variation has been selected
		 *
		 * @param product_object
		 */
		found_variation: function( product_object ) {
			var variation_id = parseInt( product_object.variation_id_field.val() );

			return !isNaN( variation_id );
		},

		/**
		 * Gat variation data from variation ID
		 *
		 * @param product_object
		 * @param int variation_id
		 */
		get_variation_data: function( product_object, variation_id ) {
			product_object.all_images_wrap.trigger( fcms_woothumbs.vars.loading_variation_trigger );

			var variation_data = false;

			// variation data available

			if ( product_object.variations ) {

				$.each( product_object.variations, function( index, variation ) {

					if ( variation.variation_id === variation_id ) {
						variation_data = variation;
					}

				} );

				product_object.all_images_wrap.trigger( fcms_woothumbs.vars.show_variation_trigger, [ variation_data ] );

				// variation data not available, look it up via ajax

			} else {

				$.ajax( {
					type: "GET",
					url: fcms_woothumbs_vars.ajaxurl,
					cache: false,
					dataType: "jsonp",
					crossDomain: true,
					data: {
						'action': 'fcms_woothumbs_get_variation',
						'variation_id': variation_id,
						'product_id': product_object.product_id
					},
					success: function( response ) {

						if ( response.success ) {
							if ( response.variation ) {
								variation_data = response.variation;

								product_object.all_images_wrap.trigger( fcms_woothumbs.vars.show_variation_trigger, [ variation_data ] );
							}
						}

					}
				} );

			}
		},

		/**
		 * Trigger Photoswipe
		 *
		 * @param bool last_slide
		 */
		trigger_photoswipe: function( product_object, last_slide ) {

			var $photoswipe_template = $( fcms_woothumbs.tpl.photoswipe() );

			$( 'body' ).append( $photoswipe_template );

			var $photoswipe_element = $( '.fcms-woothumbs-pswp' );

			if ( $photoswipe_element.length <= 0 ) {
				return;
			}

			// build items array
			var items = fcms_woothumbs.get_gallery_items( product_object );

			// define options (if needed)
			var options = {
				// optionName: 'option value'
				// for example:
				index: typeof last_slide === "undefined" ? items.index : items.items.length - 1, // start at first slide
				shareEl: false,
				closeOnScroll: false,
				history: false,
				showHideOpacity: true,
				showAnimationDuration: 0
			};

			// Initializes and opens PhotoSwipe
			fcms_woothumbs.els.gallery = new PhotoSwipe( $photoswipe_element[ 0 ], PhotoSwipeUI_Default, items.items, options );

			fcms_woothumbs.els.gallery.init();

			fcms_woothumbs.els.gallery.listen( 'beforeChange', function() {
				fcms_woothumbs.stop_photoswipe_media();
			} );

			fcms_woothumbs.els.gallery.listen( 'close', function() {
				setTimeout( function () {
					$photoswipe_element.remove();
				}, 50 );
			} );
		},

		/**
		 * Pause iframe video
		 */
		stop_photoswipe_media: function() {
			var $media = $( '.fcms-woothumbs-fullscreen-video-wrapper iframe, .fcms-woothumbs-fullscreen-video-wrapper video' );

			if ( $media.length <= 0 ) {
				return;
			}

			$media.each( function( index, media ) {
				var $media_item = $( media );

				if ( $media_item.is( 'iframe' ) ) {
					$media_item.hide().attr( 'src', $media_item.attr( 'src' ) );
					$media_item.load( function() {
						$( this ).show();
					} );
				} else {
					fcms_woothumbs.setup_media_ended();
					fcms_woothumbs.pause_video( $media_item, true );
				}
			} );
		},

		/**
		 * Pause a video.
		 *
		 * @param $video
		 */
		pause_video: function( $video, stop ) {
			stop = stop || false;

			var $video_item = $video.get( 0 ),
				$button = $video.closest( '.fcms-woothumbs-responsive-media' ).find( '.' + fcms_woothumbs.vars.media_controls_class ),
				$icon = $button.find( '.fcms-woothumbs-icon' );

			$video.get( 0 ).pause();

			if ( stop ) {
				$video_item.load();

				if ( $video_item.autoplay && $button.length > 0 ) {
					$button.removeClass( fcms_woothumbs.vars.pause_controls_class );
					$button.removeClass( fcms_woothumbs.vars.play_controls_class );
					$icon.addClass( fcms_woothumbs.vars.pause_button_class );
					$icon.removeClass( fcms_woothumbs.vars.play_button_class );

					return;
				}
			}

			if ( $button.length <= 0 ) {
				return;
			}

			$button.removeClass( fcms_woothumbs.vars.pause_controls_class );
			$button.addClass( fcms_woothumbs.vars.play_controls_class );
			$icon.removeClass( fcms_woothumbs.vars.pause_button_class );
			$icon.addClass( fcms_woothumbs.vars.play_button_class );
		},

		/**
		 * Setup fullscreen
		 *
		 * @param product_object
		 */
		setup_fullscreen: function( product_object ) {
			if ( !fcms_woothumbs.vars.is_fullscreen_enabled ) {
				return;
			}

			product_object.images_wrap.on( 'click', fcms_woothumbs.vars.fullscreen_trigger, function() {
				fcms_woothumbs.trigger_photoswipe( product_object );
			} );

			if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_click_anywhere ) ) {
				$( document ).on( 'click', '.zm-handler', function() {
					var $zm_handler = $( this ),
						$el = $zm_handler.data( 'el' );

					$el.click();
				} );
			}
		},

		/**
		 * Setup video
		 */
		setup_video: function( product_object ) {

			product_object.images_wrap.on( 'click touchstart', fcms_woothumbs.vars.play_trigger, function() {

				fcms_woothumbs.trigger_photoswipe( product_object, true );

			} );

		},

		/**
		 * Get Gallery Items
		 *
		 * @param product_object
		 * @return obj index and items
		 */

		get_gallery_items: function( product_object ) {
			var $slides = product_object.images.find( '.fcms-woothumbs-images__slide' ),
				items = [],
				index = product_object.images.slick( 'slickCurrentSlide' );

			if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_enable ) ) {
				if ( $slides.length > 0 ) {
					$slides.each( function( i, slide ) {
						var $slide = $( slide );

						if ( $slide.closest( '.slick-cloned' ).length > 0 ) {
							return;
						}

						if ( fcms_woothumbs.is_media( $slide ) ) {
							media_html = fcms_woothumbs.tpl.media.replace( "{{media_embed}}", $slide.html() );

							items.push( {
								html: media_html
							} );

							return;
						}

						var img = $slide.find( 'img' );

						if ( img.length <= 0 ) {
							return;
						}

						if ( fcms_woothumbs.is_placeholder( img ) ) {
							return;
						}

						var large_image_src = img.attr( 'data-large_image' ),
							large_image_w = img.attr( 'data-large_image_width' ),
							large_image_h = img.attr( 'data-large_image_height' ),
							item = {
								src: large_image_src,
								w: large_image_w,
								h: large_image_h
							};

						if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_image_title ) ) {
							var title = img.attr( 'title' );

							item.title = title;
						}

						items.push( item );
					} );
				}
			}

			if ( fcms_woothumbs.els.video_template.length > 0 ) {
				items.push( {
					html: fcms_woothumbs.els.video_template.html().trim()
				} );
			}

			return {
				index: index,
				items: items
			};

		},

		/**
		 * Setup Zoom - actions that should only be run once
		 *
		 * @param product_object
		 */

		setup_zoom: function( product_object ) {

			if ( !fcms_woothumbs.vars.is_zoom_enabled ) {
				return;
			}

			// Disable the zoom if using a tocuh device

			product_object.all_images_wrap.on( 'touchmove', '.fcms-woothumbs-images__image', function() {
				fcms_woothumbs.vars.is_dragging_image_slide = true;
			} );

			product_object.all_images_wrap.on( 'touchend', '.fcms-woothumbs-images__image', function( e ) {

				if ( !fcms_woothumbs.vars.is_dragging_image_slide ) {
					e.preventDefault();
					$( this ).click();
				}

			} );

			product_object.all_images_wrap.on( 'touchstart', '.fcms-woothumbs-images__image', function() {
				fcms_woothumbs.vars.is_dragging_image_slide = false;
			} );

			if ( fcms_woothumbs.vars.zoom_setup ) {
				return;
			}

			// Reset zoom after resize

			$( window ).on( 'resize-end', function() {
				var $active_img = product_object.images.find( '.slick-active img:first' );

				fcms_woothumbs.init_zoom( $active_img, product_object );
			} );

			fcms_woothumbs.vars.zoom_setup = true;

		},

		/**
		 * Init Hover Zoom
		 *
		 * @param $image
		 * @param product_object
		 */

		init_zoom: function( $image, product_object ) {
			if ( !fcms_woothumbs.vars.is_zoom_enabled || fcms_woothumbs.is_placeholder( $image ) ) {
				return;
			}

			var $parent_slide = $image.closest( '.fcms-woothumbs-images__slide' ),
				slide_image_width = $image.width(),
				large_image = $image.attr( 'data-large_image' ),
				large_image_width = parseInt( $image.attr( 'data-large_image_width' ) );

			if ( slide_image_width >= large_image_width ) {
				return;
			}

			if ( product_object.imagezoom ) {
				product_object.imagezoom.destroy();
			}

			$parent_slide.ImageZoom( {
				type: fcms_woothumbs_vars.settings.zoom_general_zoom_type,
				bigImageSrc: large_image,
				zoomSize: [
					fcms_woothumbs_vars.settings.zoom_outside_follow_zoom_lens_width,
					fcms_woothumbs_vars.settings.zoom_outside_follow_zoom_lens_height
				],
				zoomViewerClass: (fcms_woothumbs_vars.settings.zoom_general_zoom_type === "follow") ? 'shape' + fcms_woothumbs_vars.settings.zoom_follow_zoom_zoom_shape : "shapesquare",
				position: fcms_woothumbs_vars.settings.zoom_outside_zoom_zoom_position,
				preload: false,
				showDescription: false,
				hoverIntent: fcms_woothumbs_vars.settings.zoom_general_zoom_type === "follow",
				onShow: function() {
					fcms_woothumbs.add_zoom_controls( product_object );
					product_object.images.slick( 'slickPause' );
				},
				onHide: function() {
					if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.carousel_general_autoplay ) ) {
						product_object.images.slick( 'slickPlay' );
					}
				}
			} );

			product_object.imagezoom = $parent_slide.data( 'imagezoom' );
		},

		/**
		 * Destroy Hover Zoom
		 *
		 * @param product_object
		 */

		destroy_zoom: function( product_object ) {
			var $current_zoom = product_object.images.find( '.currZoom' ),
				zoom = $current_zoom.data( 'imagezoom' );

			if ( zoom && typeof zoom !== "undefined" ) {

				$current_zoom.removeClass( 'currZoom' );
				zoom.destroy();

			}

			$( '.zm-viewer' ).remove();
			$( '.zm-handler' ).remove();
		},

		/**
		 * Add Zoom Controls
		 *
		 * @param product_object
		 */

		add_zoom_controls: function( product_object ) {

			var $viewer = product_object.imagezoom.$viewer;

			if ( $viewer.find( '.fcms-woothumbs-zoom-controls' ).length <= 0 && fcms_woothumbs_vars.settings.zoom_general_zoom_type === "inner" ) {

				if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.display_general_icons_tooltips ) ) {
					$viewer.addClass( 'fcms-woothumbs-tooltips-enabled' );
				}

				$viewer.append( '<div class="fcms-woothumbs-zoom-controls"></div>' );

				var $zoom_controls = $viewer.find( '.fcms-woothumbs-zoom-controls' );

				if ( product_object.wishlist_buttons.length > 0 ) {
					$zoom_controls.append( product_object.wishlist_buttons.clone() );
				}

				if ( product_object.play_button.length > 0 ) {
					$zoom_controls.append( fcms_woothumbs.tpl.play_button );

					$viewer.on( 'click', fcms_woothumbs.vars.play_trigger, function() {
						fcms_woothumbs.trigger_photoswipe( product_object, true );
					} );
				}

				if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.fullscreen_general_enable ) ) {
					$zoom_controls.append( fcms_woothumbs.tpl.fullscreen_button );

					$viewer.on( 'click', fcms_woothumbs.vars.fullscreen_trigger, function() {
						fcms_woothumbs.trigger_photoswipe( product_object );
					} );
				}

				if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.navigation_general_controls ) && fcms_woothumbs.get_thumbnail_count( product_object ) > 1 ) {

					var dir = fcms_woothumbs.vars.is_rtl ? 'slickNect' : 'slickPrev';

					if ( !product_object.images_wrap.find( '.fcms-woothumbs-images__arrow--prev' ).hasClass( 'slick-disabled' ) ) {
						$zoom_controls.append( '<a class="fcms-woothumbs-zoom-prev" href="javascript: void(0);"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-left-open-mini"></i></a>' );
					}

					if ( !product_object.images_wrap.find( '.fcms-woothumbs-images__arrow--next' ).hasClass( 'slick-disabled' ) ) {
						$zoom_controls.append( '<a class="fcms-woothumbs-zoom-next" href="javascript: void(0);"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-right-open-mini"></i></a>' );
					}

					// Arrow nav
					$viewer.on( 'click', '.fcms-woothumbs-zoom-prev', function() {

						var dir = fcms_woothumbs.vars.is_rtl ? 'slickNext' : 'slickPrev';

						product_object.images.slick( dir );

					} );

					$viewer.on( 'click', '.fcms-woothumbs-zoom-next', function() {

						var dir = fcms_woothumbs.vars.is_rtl ? 'slickPrev' : 'slickNext';

						product_object.images.slick( dir );

					} );

				}

				if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.navigation_bullets_enable ) ) {

					var $bullets = product_object.all_images_wrap.find( '.slick-dots' );

					if ( $bullets.children().length > 1 ) {

						var $bullets_clone = $bullets.clone();

						$bullets_clone.appendTo( $zoom_controls ).wrap( "<div class='fcms-woothumbs-zoom-bullets'></div>" );

						// Bullet nav

						$viewer.on( 'click', '.fcms-woothumbs-zoom-bullets button', function() {

							var selected_index = $( this ).parent().index();

							// change main slide
							product_object.images.slick( 'slickGoTo', selected_index );

							return false;

						} );

					}

				}

				fcms_woothumbs.setup_tooltips();

			}

		},

		/**
		 * Setup Yith Wishlist
		 *
		 * @param product_object
		 */
		setup_yith_wishlist: function() {

			$( 'body' ).on( 'added_to_wishlist', function() {

				if ( fcms_woothumbs.wishlist_adding.length <= 0 ) {
					return;
				}

				var adding_id = fcms_woothumbs.wishlist_adding.shift(),
					$wishlist_buttons = $( '[data-fcms-woothumbs-yith-wishlist-adding-id="' + adding_id + '"]' );

				$wishlist_buttons.addClass( fcms_woothumbs.vars.wishlist_added_class );

			} );

		},

		/**
		 * Watch Yith Wishlist Buttons
		 *
		 * @param product_object
		 */
		watch_yith_wishlist: function( product_object ) {

			if ( product_object.wishlist_buttons.length <= 0 ) {
				return;
			}

			product_object.wishlist_add_button.on( 'click', function() {

				var adding_id = fcms_woothumbs.wishlist_adding.length + 1;

				product_object.wishlist_buttons.attr( 'data-fcms-woothumbs-yith-wishlist-adding-id', adding_id );

				fcms_woothumbs.wishlist_adding.push( adding_id );

			} );

		},

		/**
		 * Setup Tooltips
		 */

		setup_tooltips: function() {

			if ( fcms_woothumbs.is_true( fcms_woothumbs_vars.settings.display_general_icons_tooltips ) ) {

				$( '[data-fcms-woothumbs-tooltip]' ).each( function() {

					var tooltip = $( this ).attr( 'data-fcms-woothumbs-tooltip' );

					$( this ).tooltipster( {
						content: tooltip,
						debug: false
					} );
				} );

			}

		},

		/**
		 * Update caption
		 */

		update_caption: function( $current_slide, product_object ) {

			if ( product_object.caption.length <= 0 ) {
				return;
			}

			var caption = $current_slide.data( 'caption' );

			if ( typeof caption === "undefined" || caption === "" ) {
				product_object.caption.html( "&mdash;" );
			} else {
				product_object.caption.text( caption );
			}

		},

		/**
		 * Stop media.
		 */
		stop_media: function( product_object ) {
			var $media = product_object.images.find( 'iframe, video' );

			if ( $media.length <= 0 ) {
				return;
			}

			$media.each( function( index, media ) {
				var $media_item = $( media ),
					active = $media_item.closest( '.slick-active' ).length > 0;

				if ( active ) {
					return;
				}

				if ( $media_item.is( 'iframe' ) ) {
					$media_item.attr( 'src', $( media ).attr( 'src' ) );
				} else {
					var paused = $media_item.get( 0 ).paused;

					if ( !paused ) {
						$media_item.data( 'playing', true );
						$media_item.get( 0 ).pause();
					}
				}
			} );
		},

		/**
		 * Start video media (if it was playing before).
		 */
		start_media: function( product_object ) {
			var $media = product_object.images.find( 'video' );

			if ( $media.length <= 0 ) {
				return;
			}

			$media.each( function( index, media ) {
				var $media_item = $( media ),
					active = $media_item.closest( '.slick-active' ).length > 0;

				if ( !active || $media_item.data( 'playing' ) !== true ) {
					return;
				}

				$media_item.get( 0 ).play();

				// Seek video to 0 second the first time it is played. To fix
				// the issue with Edge browser where video starts from 3 seconds
				if ( ! $media_item.data( 'has-played' ) ) {
					$media_item.get( 0 ).currentTime = 0;
					$media_item.data( 'has-played', 1 );
				}

				// Remove now as it'll be added when sliding anyway
				$media_item.data( 'playing', false );
			} );
		},

		/**
		 * Setup media controls.
		 *
		 * @param product_object
		 */
		setup_media_controls: function( product_object ) {
			$( document.body ).on( 'click', '.' + fcms_woothumbs.vars.media_controls_class, function( e ) {
				var $button = $( this ),
					$media = $button.closest( '.fcms-woothumbs-responsive-media' ).find( 'video' ),
					$icon = $button.find( '.fcms-woothumbs-icon' );

				if ( $media.length <= 0 ) {
					return;
				}

				$media_item = $media.get( 0 );

				var playing = !$media_item.paused;

				if ( playing ) {
					fcms_woothumbs.pause_video( $media );
				} else {
					$button.removeClass( fcms_woothumbs.vars.play_controls_class );
					$button.addClass( fcms_woothumbs.vars.pause_controls_class );
					$icon.removeClass( fcms_woothumbs.vars.play_button_class );
					$icon.addClass( fcms_woothumbs.vars.pause_button_class );
					$media_item.play();
				}
			} );

			// Add hover class.
			$( document ).on( 'mouseover touchstart', '.fcms-woothumbs-responsive-media', function () {
				$( this ).addClass( 'fcms-woothumbs-responsive-media--hover' );
			});

			// Remove hover class.
			$( document ).on( 'mouseout ', '.fcms-woothumbs-responsive-media' , function() {
				$( this ).removeClass( 'fcms-woothumbs-responsive-media--hover' );
			} );

			// Auto hide the controls after 1 second of mobile tap event.
			// Since there is no mouseout event in mobile, we need to auto-hide controls.
			jQuery(document).on( 'touchend', '.fcms-woothumbs-responsive-media' , function() {
				if ( fcms_woothumbs.vars.media_touch_timer ) {
					clearTimeout( fcms_woothumbs.vars.media_touch_timer );
				}

				fcms_woothumbs.vars.media_touch_timer = setTimeout( function() {
					jQuery(".fcms-woothumbs-responsive-media").removeClass('fcms-woothumbs-responsive-media--hover');
				}, 1000 );
			});

			fcms_woothumbs.setup_media_ended();
		},

		/**
		 * Run code when embeded video ends.
		 *
		 * @param $images
		 */
		setup_media_ended: function() {
			var $responsive_media = $( '.fcms-woothumbs-responsive-media' ).filter( function() {
				return $( this ).data( 'fcms-onended-bound' ) !== true;
			} );

			if ( $responsive_media.length <= 0 ) {
				return;
			}

			var $controls = $responsive_media.find( '.' + fcms_woothumbs.vars.media_controls_class );

			if ( $controls.length <= 0 ) {
				return;
			}

			var $media_items = $responsive_media.find( '.fcms-woothumbs-responsive-media__manual-embed' );

			if ( $media_items.length <= 0 ) {
				return;
			}

			$.each( $media_items, function( index, media_item ) {
				var $media_item = $( media_item ),
					$media = $media_item.get( 0 );

				$media.onended = function() {
					var $media = $( this ),
						$button = $media.closest( '.fcms-woothumbs-responsive-media' ).find( '.' + fcms_woothumbs.vars.media_controls_class ),
						$icon = $button.find( '.fcms-woothumbs-icon' );

					$button.removeClass( fcms_woothumbs.vars.pause_controls_class );
					$button.addClass( fcms_woothumbs.vars.play_controls_class );
					$icon.removeClass( fcms_woothumbs.vars.pause_button_class );
					$icon.addClass( fcms_woothumbs.vars.play_button_class );
				};
			} );

			$responsive_media.data( 'fcms-onended-bound', true );
		},

		/**
		 * Reveal slides.
		 */
		reveal_slides: function( product_object ) {
			product_object.images.find( '.fcms-woothumbs-images__slide' ).show();
		},

		/**
		 * Images loaded with srcset.
		 */
		images_loaded: function( selector, on_complete, on_progress ) {
			var $images = $( selector ).find( 'img, iframe, video' ),
				success = 0,
				error = 0,
				iteration = 0,
				total = $images.length;

			var check = function( el, status ) {
				iteration ++;
				var data = {
					img: el,
					iteration: iteration,
					success: success,
					error: error,
					total: total,
					status: status
				};

				if ( typeof on_progress === 'function' ) {
					on_progress( data );
				}

				if ( success + error === total && typeof on_complete === 'function' ) {
					on_complete( data );
				}
			};

			$images.each( function() {
				var $el = $( this );

				if ( !$el.is( 'img' ) ) {
					success ++;
					check( this, 'success' );
					return;
				}

				var tmpImg = new Image();

				tmpImg.onload = function() {
					success ++;
					check( this, 'success' );
				};

				tmpImg.onerror = function() {
					error ++;
					check( this, 'error' );
				};

				tmpImg.src = this.src;
			} );
		},

		/**
		 * On fullscreen change event.
		 */
		on_fullscreenchange: function () {
			fcms_woothumbs.vars.fullscreeen_flag = true;

			// set the flag back to 'false' after a while.
			setTimeout( function () {
				fcms_woothumbs.vars.fullscreeen_flag = false;
			}, 1000 );

		}

	};

	$( window ).on( 'load', fcms_woothumbs.on_load );
	$( 'body' ).on( 'jckqv_open', fcms_woothumbs.on_load );
	$( window ).on( 'resize', fcms_woothumbs.on_resize );
	$( document ).on( 'fullscreenchange', fcms_woothumbs.on_fullscreenchange );

}( jQuery, document ));
/*
*	ImageZoom - Responsive jQuery Image Zoom Pluin
*   version: 1.1.1
*	by hkeyjun & jamesckemp
*   http://codecanyon.net/user/hkeyjun
*/
;(function( $, window, undefined ) {
	$.ImageZoom = function(el,options){
		var base = this;
		base.$el = $(el);
		base.$img = base.$el.is( 'img' ) ? base.$el : base.$el.find( 'img:first' );

		base.$el.data('imagezoom',base);

		base.init = function(options){
			base.options = $.extend({},$.ImageZoom.defaults,options);
			base.$viewer = $('<div class="zm-viewer '+base.options.zoomViewerClass+'"></div>').appendTo('body');
			base.$handler = $('<div class="zm-handler'+base.options.zoomHandlerClass+'"></div>').data( 'el', el ).appendTo('body');
			base.isBigImageReady = -1;
			base.$largeImg = null;
			base.isActive = false;
			base.$handlerArea = null;
			base.isWebkit = /chrome/.test(navigator.userAgent.toLowerCase()) || /safari/.test(navigator.userAgent.toLowerCase());
			base.evt ={x:-1,y:-1};
			base.options.bigImageSrc =base.options.bigImageSrc ==''?base.$img.attr('src'):base.options.bigImageSrc;
			if(base.options.preload) (new Image()).src=this.options.bigImageSrc;
			base.callIndex = $.ImageZoom._calltimes +1;
			base.animateTimer = null;
			$.ImageZoom._calltimes +=1;

			$(document).on('mousemove',function(e) {
                window.mouseX = e.pageX;
                window.mouseY = e.pageY;
            });

			$(document).on('mousemove.imagezoom'+base.callIndex,function(e){
				if(base.isActive)
				{
					base.moveHandler(e.pageX,e.pageY);
				}
			});

			if( base.options.hoverIntent ) {

    			base.$el.hoverIntent({
                    over: base.over,
                    out: base.out,
                    sensitivity: 10
                });

            } else {

                base.$el.on('mouseover.imagezoom',function(e){
                    base.isActive = true;
                    base.showViewer(e);
                });

            }

		};

		base.over = function( e ) {

    		base.isActive = true;
            base.showViewer(e);
            base.moveHandler(mouseX,mouseY);

		};

		base.out = function() {};

		//Move
		base.moveHandler = function(x,y){


			var offset = base.$el.offset(),width=base.$el.outerWidth(false),height=base.$el.outerHeight(false);

			if(x>=offset.left && x<=offset.left+width && y>=offset.top && y<=offset.top+height)
			{
				offset.left = offset.left +toNum(base.$el.css('borderLeftWidth'))+toNum(base.$el.css('paddingLeft'));
				offset.top = offset.top + toNum(base.$el.css('borderTopWidth'))+toNum(base.$el.css('paddingTop'));
				width = base.$el.width();
				height = base.$el.height();
				if(x>=offset.left && x<=offset.left+width && y>=offset.top && y<=offset.top+height)
				{
					base.evt = {x:x,y:y};
				if(base.options.type=="follow")
				{
					base.$viewer.css({top:y-base.$viewer.outerHeight(false)/2,left:x-base.$viewer.outerWidth(false)/2});
				}
				if(base.isBigImageReady ==1)
				{
					var bigTop,bigLeft;
					var innerTop = y - offset.top,innerLeft = x-offset.left;
					if(base.options.type=='inner')
					{
						bigTop = -base.$largeImg.height()*innerTop/height + innerTop;
						bigLeft = -base.$largeImg.width()*innerLeft/width + innerLeft;
					}
					else if(base.options.type=="standard")
					{
						var hdLeft=innerLeft-base.$handlerArea.width()/2,hdTop=innerTop - base.$handlerArea.height()/2,
						hdWidth = base.$handlerArea.width(),hdHeight = base.$handlerArea.height();
						if(hdLeft <0)
						{
							hdLeft =0;
						}
						else if(hdLeft>width - hdWidth)
						{
							hdLeft = width - hdWidth;
						}
						if(hdTop<0)
						{
							hdTop =0;
						}
						else if(hdTop > height -hdHeight)
						{
							hdTop = height - hdHeight;
						}
						bigLeft = -hdLeft / base.scale;
						bigTop = -hdTop /base.scale;


						if(base.isWebkit)
						{
							base.$handlerArea.css({opacity:.99});
							setTimeout(function(){
									base.$handlerArea.css({top:hdTop,left:hdLeft,opacity:1});
							},0);
						}
						else
						{
							base.$handlerArea.css({top:hdTop,left:hdLeft});
						}
					}
					else if(base.options.type=="follow")
					{

						bigTop = -base.$largeImg.height()/height * innerTop +base.options.zoomSize[1]/2;
						bigLeft = -base.$largeImg.width()/width *  innerLeft +base.options.zoomSize[0]/2;

						if(-bigTop > base.$largeImg.height() -base.options.zoomSize[1])
						{
							bigTop = -(base.$largeImg.height()-base.options.zoomSize[1]);
						}
						else if(bigTop>0)
						{
							bigTop =0;
						}

						if(-bigLeft >base.$largeImg.width() -base.options.zoomSize[0])
						{
							bigLeft = -(base.$largeImg.width()-base.options.zoomSize[0]);
						}
						else if(bigLeft>0)
						{
							bigLeft =0;
						}
					}

					if(base.options.smoothMove)
					{
						window.clearTimeout(base.animateTimer);
						base.smoothMove(bigLeft,bigTop);
					}
					else
					{
						base.$viewer.find('img').css({top:bigTop,left:bigLeft});
					}
				}
				}

			}
			else
			{
				base.isActive = false;
				//hidden the viewer
				base.$viewer.hide();
				base.$handler.hide();
				base.options.onHide(base);
				window.clearTimeout(base.animateTimer);
				base.animateTimer =null;
			}
		};
		//Show the zoom view
		base.showViewer = function(e){

			var top = base.$el.offset().top,borderTopWidth = toNum(base.$el.css('borderTopWidth')),paddingTop = toNum(base.$el.css('paddingTop')),left = base.$el.offset().left,borderLeftWidth =toNum(base.$el.css('borderLeftWidth')),paddingLeft = toNum(base.$el.css('paddingLeft'));
			top = top + borderTopWidth+paddingTop;
			left = left +borderLeftWidth+paddingLeft;

			var width = base.$el.width();
			var height = base.$el.height();
			//log(base.isBigImageReady);
			if(base.isBigImageReady <1)
			{
				$('div',base.$viewer).remove();
			}



			if(base.options.type=='inner')
			{
				base.$viewer.css({top:top,left:left,width:width,height:height}).fadeIn(200);
			}
			else if(base.options.type=='standard')
			{
				var $alignTarget = base.options.alignTo == '' ? base.$el:$('#'+base.options.alignTo);
				var viewLeft,viewTop;
				if(base.options.position == 'left')
				{
					viewLeft = $alignTarget.offset().left - base.options.zoomSize[0] - base.options.offset[0];
					viewTop = $alignTarget.offset().top + base.options.offset[1];
				}
				else if(base.options.position == 'right')
				{
					viewLeft = $alignTarget.offset().left +$alignTarget.width() + base.options.offset[0];
					viewTop = $alignTarget.offset().top + base.options.offset[1];
				}

				base.$viewer.css({top:viewTop,left:viewLeft,width:base.options.zoomSize[0],height:base.options.zoomSize[1]}).fadeIn(200);
				//zoom handler ajust
				if(base.$handlerArea)
				{
					//been change
					 base.scale = width / base.$largeImg.width();
					base.$handlerArea.css({width:base.$viewer.width()*base.scale,height:base.$viewer.height()*base.scale});
				}
			}
			else if(base.options.type=="follow")
			{
				base.$viewer.css({width:base.options.zoomSize[0],height:base.options.zoomSize[1],top:e.pageY-(base.options.zoomSize[1]/2),left:e.pageX-(base.options.zoomSize[0]/2)}).fadeIn(200);
			}


			base.$handler.css({top:top,left:left,width:width,height:height}).fadeIn(200);

			base.options.onShow(base);

			if(base.isBigImageReady ==-1)
			{
				base.isBigImageReady =0;

				fastImg(base.options.bigImageSrc, function () {

					if( $(this).attr('src').trim() == base.options.bigImageSrc.trim() )
					{
						var $baseImg = base.$el.is( 'img' ) ? base.$el : base.$el.find( 'img:first' );
						base.$viewer.append('<img src="'+$baseImg.attr('src')+'" class="zm-fast" style="position:absolute;width:'+this.width+'px;height:'+this.height+'px" nopin="nopin" \>');
						base.isBigImageReady = 1;
						base.$largeImg = $('<img src="'+base.options.bigImageSrc+'" style="position:absolute;width:'+this.width+'px;height:'+this.height+'px" nopin="nopin" \>')
						base.$viewer.append(base.$largeImg);
						if(base.options.type=='standard')
						{
							var scale = width / this.width;
							base.$handlerArea = $('<div class="zm-handlerarea" style="width:'+base.$viewer.width()*scale+'px;height:'+base.$viewer.height()*scale+'px"></div>').appendTo(base.$handler);
base.scale = scale;

						}
						//if mouse is in the img before bind mouse move event we can not get x/y from base.evt
						if(base.evt.x ==-1 && base.evt.y ==-1)
						{
							base.moveHandler(e.pageX,e.pageY);
						}
						else
						{
							base.moveHandler(base.evt.x,base.evt.y);
						}

						//add description
						if(base.options.showDescription&&base.$img.attr('alt')&& base.$img.attr('alt').trim()!='')
						{
							base.$viewer.append('<div class="'+base.options.descriptionClass+'">'+base.$img.attr('alt')+'</div>');
						}
					}
					else
					{
						//log('change onload');
					}

				},function(){
					//log('load complete');

				},function(){
					//log('error');
				});
			}
					};


		//Change Img

		base.changeImage = function(elementImgSrc,bigImgSrc)
		{
			//console.log(this.$el);
			this.$el.attr('src',elementImgSrc);
			this.isBigImageReady=-1;
			this.options.bigImageSrc = typeof bigImgSrc ==='string'?bigImgSrc:elementImgSrc;
			if(base.options.preload) (new Image()).src=this.options.bigImageSrc;
			this.$viewer.hide().empty();
			this.$handler.hide().empty();
			this.$handlerArea =null;
		};

		base.changeZoomSize = function(w,h){
			base.options.zoomSize = [w,h];
		};

		base.destroy = function(){
			$(document).off('mousemove.imagezoom'+base.callIndex);
			this.$el.off('.imagezoom');
			this.$viewer.remove();
			this.$handler.remove();
			this.$el.removeData('imagezoom');
		};
		base.smoothMove = function(left,top)
		{
			var times = 10;
			var oldTop = parseInt(base.$largeImg.css('top'));
			oldTop = isNaN(oldTop)? 0:oldTop;
			var oldLeft = parseInt(base.$largeImg.css('left'));
			oldLeft = isNaN(oldLeft)? 0:oldLeft;
			top = parseInt(top),left = parseInt(left);

			if(oldTop == top && oldLeft ==left)
			{
				window.clearTimeout(base.animateTimer);
				base.animateTimer = null;
				//console.log('clear timer');
				return;
			}
			else
			{
				var topStep = top-oldTop;
				var leftStep = left -oldLeft;

				var newTop = oldTop + topStep/Math.abs(topStep)* Math.ceil(Math.abs(topStep/times));
				var newLeft = oldLeft + leftStep/Math.abs(leftStep) *Math.ceil(Math.abs(leftStep/times));

				base.$viewer.find('img').css({top:newTop,left:newLeft});

				base.animateTimer = setTimeout(function(){
					base.smoothMove(left,top);
				},10);
			}
		};

		//tools
		function toNum(strVal)
		{
			var numVal = parseInt(strVal);
			numVal = isNaN(numVal)? 0:numVal;
			return numVal;
		}

		base.init(options);
	};
	//defaults
	$.ImageZoom.defaults = {
		bigImageSrc:'',
		preload:true,
		type:'inner',
		smoothMove: true,
		position:'right',
		offset:[10,0],
		alignTo:'',
		zoomSize:[100,100],
		descriptionClass:'zm-description',
		zoomViewerClass:'',
		zoomHandlerClass:'',
		showDescription:true,
		hoverIntent:false,
		onShow:function(target){},
		onHide:function(target){}
	};

	$.ImageZoom._calltimes = 0;

	//$.fn
	$.fn.ImageZoom = function(options){
		return this.each(function(){
			new $.ImageZoom(this,options);
		});
	};

})(jQuery,window);



var fastImg = (function () {
	var list = [], intervalId = null,
	tick = function () {
		var i = 0;
		for (; i < list.length; i++) {
			list[i].end ? list.splice(i--, 1) : list[i]();
		};
		!list.length && stop();
	},
	stop = function () {
		clearInterval(intervalId);
		intervalId = null;
	};

	return function (url, ready, load, error) {
		var onready, width, height, newWidth, newHeight,
			img = new Image();
		img.src = url;
		if (img.complete) {
			ready.call(img);
			load && load.call(img);
			return;
		};
		width = img.width;
		height = img.height;
		img.onerror = function () {
			error && error.call(img);
			onready.end = true;
			img = img.onload = img.onerror = null;
		};
		onready = function () {
			newWidth = img.width;
			newHeight = img.height;
			if (newWidth !== width || newHeight !== height ||newWidth * newHeight > 1024) {
				ready.call(img);
				onready.end = true;
			};
		};
		onready();
		img.onload = function () {
			!onready.end && onready();
			load && load.call(img);
			img = img.onload = img.onerror = null;
		};
		if (!onready.end) {
			list.push(onready);
			if (intervalId === null) intervalId = setInterval(tick, 40);
		};
	};
})();