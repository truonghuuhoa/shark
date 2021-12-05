(function( $, document ) {
	var fcms_was = {
		cache: function() {
			fcms_was.els = {};
			fcms_was.vars = {};

			// common vars
			fcms_was.vars.swatch_group_class = ".fcms-was-swatches";
			fcms_was.vars.swatch_class = ".fcms-was-swatch";
			fcms_was.vars.selected_class = "fcms-was-swatch--selected";
			fcms_was.vars.disabled_class = "fcms-was-swatch--disabled";
			fcms_was.vars.follow_class = "fcms-was-swatch--follow";
			fcms_was.vars.variations_form_class = ".variations_form";
			fcms_was.vars.attribute_labels_class = ".variations .label";
			fcms_was.vars.chosen_attribute_class = ".fcms-was-chosen-attribute";
			fcms_was.vars.no_selection = '<span class="fcms-was-chosen-attribute__no-selection">' + fcms_was_vars.i18n.no_selection + '</span>';
			fcms_was.vars.attribute_selects_selector = ".variations select";
			fcms_was.vars.change_image_links_class = ".fcms-was-swatch--change-image";
			fcms_was.vars.widget_class = ".fcms-was-swatches--widget ul";
			fcms_was.vars.widget_group_item = ".fcms-was-swatch[data-group]";
			fcms_was.vars.window_size = $(window).width();
		},

		on_ready: function() {
			// on ready stuff here
			fcms_was.cache();
			fcms_was.setup_swatches();
			fcms_was.setup_change_image_links();
			fcms_was.setup_fees();
			fcms_was.setup_filter_groups();
		},

		/**
		 * Setup the swatches on the frontend.
		 */
		setup_swatches: function() {

			/**
			 * When a swatch is clicked
			 */
			$( document.body ).on( 'click', fcms_was.vars.swatch_class, function( event ) {
				var $swatch = $( this ),
					$form = $swatch.closest( fcms_was.vars.variations_form_class ),
					$table = $swatch.closest( 'table' ),
					$swatch_wrapper = $swatch.closest( fcms_was.vars.swatch_group_class ),
					attribute = $swatch_wrapper.data( 'attribute' ),
					attribute_value = $swatch.data( 'attribute-value' ),
					$select = $table.find( 'select[id="' + attribute.replace( 'attribute_', '' ) + '"]' );

				// Compatibility with WooCommerce Product Bundles by Somewherewarm.
				if ( 0 === $select.length && $swatch.closest( '.bundled_item_cart_content' ) ) {
					$form   = $swatch.closest( '.bundled_item_cart_content' );
					$select = $table.find( 'select[id="' + attribute.replace( 'attribute_', '' ) + '_' + $form.data( 'bundled_item_id' ) + '"]' );
				}

				var select_name = $select.attr( 'name' ),
					$cell = $swatch.closest( '.value' ),
					$label_selected = $cell.prev( '.label' ).find( fcms_was.vars.chosen_attribute_class ),
					is_visual = $swatch.hasClass( 'fcms-was-swatch--colour-swatch' ) || $swatch.hasClass( 'fcms-was-swatch--image-swatch' ),
					selected = fcms_was.get_current_values( $form ),
					reselect_values = false;

				
				if ( $swatch.hasClass( fcms_was.vars.follow_class ) ) {
					return true;
				}

				// do nothing if swatch is disabled
				if ( $swatch.hasClass( fcms_was.vars.disabled_class ) ) {
					fcms_was.reset_form( event, $form );
					delete selected[ select_name ];
					reselect_values = true;
				}

				// trigger focusin on the select field to run WooCommerce triggers
				// this refreshes the select field with available options
				$select.trigger( 'focusin' );

				// deselect if swatch is already selected
				if ( $swatch.hasClass( fcms_was.vars.selected_class ) ) {
					$swatch.removeClass( fcms_was.vars.selected_class );
					fcms_was.select_value( $select, '' );
					$label_selected.html( fcms_was.vars.no_selection );

					return;
				}

				var $is_ajax_variations = $( fcms_was.vars.variations_form_class ).data( 'product_variations' ) === false,
					$option_selector = '[value="' + fcms_was.esc_double_quotes( attribute_value ) + '"]';

				if ( !$is_ajax_variations ) {
					$option_selector = '.enabled' + $option_selector;
				}

				// if the select field has the value we want still, select it
				if ( $select.find( $option_selector ).length > 0 ) {
					fcms_was.deselect_swatch_group( $form, attribute );
					$swatch.addClass( fcms_was.vars.selected_class );
					fcms_was.select_value( $select, attribute_value );

					if ( reselect_values ) {
						fcms_was.select_values( $form, selected );
					}
				}
			} );

			/**
			 * Trigger focusin on the select field to run WooCommerce triggers
			 * this refreshes the select field with available options
			 */
			$( document ).on( 'mouseenter mouseleave', fcms_was.vars.swatch_class, function( e ) {
				var $select = $( this ).closest( fcms_was.vars.swatch_group_class ).next( 'div' ).find( 'select' );

				if ( $select.length <= 0 ) {
					return;
				}

				$select.trigger( 'focusin' );
			} );

			/**
			 * When select fields are updated to reflect available atts
			 */
			$( document ).on( 'woocommerce_update_variation_values', fcms_was.vars.variations_form_class, function() {
				var $form = $( this ),
					$selects = $form.find( 'select' );

				$selects.each( function( index ) {
					var $select = $( this ),
						$options = $select.find( 'option' ),
						attribute = $select.data( 'attribute_name' ),
						$swatch_group = $form.find( fcms_was.vars.swatch_group_class + '[data-attribute="' + fcms_was.esc_double_quotes( attribute ) + '"]' );

					$swatch_group.find( fcms_was.vars.swatch_class ).not( fcms_was.vars.swatch_class + '--dummy' ).addClass( fcms_was.vars.disabled_class );

					$options.each( function( index, option ) {
						var $option = $( option ),
							attribute_value = $option.val(),
							$swatch = $swatch_group.find( '[data-attribute-value="' + fcms_was.esc_double_quotes( attribute_value ) + '"]' );

						if ( !$option.hasClass( 'enabled' ) ) {
							$swatch.removeClass( fcms_was.vars.selected_class );
							return;
						}

						$swatch.removeClass( fcms_was.vars.disabled_class );
					} );
				} );
			} );

			/**
			 * When select fields change
			 */
			$( document ).on( 'change', fcms_was.vars.attribute_selects_selector, function() {
				fcms_was.change_label( $( this ) );
			} );

			/**
			 * When form data is reset
			 */
			$( document.body ).on( 'click', '.reset_variations', function( event ) {
				var $form = $( this ).closest( fcms_was.vars.variations_form_class );

				fcms_was.reset_form( event, $form );
			} );

			/**
			 * Deselect unavailable attribute.
			 */
			$( document ).on( 'check_variations', 'form.variations_form', function() {
				var $form = $( this ),
					$disabled = $form.find( '.' + fcms_was.vars.disabled_class );

				if ( $disabled.length <= 0 ) {
					return;
				}

				if ( !$disabled.hasClass( fcms_was.vars.selected_class ) ) {
					return;
				}

				var $row = $disabled.closest( 'tr' ),
					$chosen_label = $row.find( fcms_was.vars.chosen_attribute_class );

				$chosen_label.html( fcms_was.vars.no_selection );
				$disabled.removeClass( fcms_was.vars.selected_class );
			} );

			/**
			 * On page load
			 */
			fcms_was.swatches_on_load();
		},

		/**
		 * Selected values from array
		 *
		 * @param element $form
		 * @param array values
		 */
		select_values: function( $form, values ) {
			var $selects = $form.find( 'select' );

			if ( $selects.length <= 0 ) {
				return false;
			}

			$selects.each( function( index, select ) {
				var $select = $( this ),
					name = $select.attr( 'name' );

				if ( typeof values[ name ] === "undefined" ) {
					return;
				}

				var $option = $select.find( 'option[value="' + fcms_was.esc_double_quotes( values[ name ] ) + '"]' );

				if ( $option.length <= 0 ) {
					return;
				}

				if ( !$option.hasClass( 'enabled' ) ) {
					return;
				}

				fcms_was.select_value( $select, values[ name ] );

				$form.find( fcms_was.vars.swatch_class + '[data-attribute-value="' + fcms_was.esc_double_quotes( values[ name ] ) + '"]' ).click();
			} );
		},

		/**
		 * Reset variations form
		 *
		 * @param obj event
		 * @param element $form
		 */
		reset_form: function( event, $form ) {
			event.preventDefault();

			$form
				.find( fcms_was.vars.attribute_selects_selector )
				.find( "option" ).prop( "selected", false ).end()
				.change().end()
				.find( fcms_was.vars.swatch_class ).removeClass( fcms_was.vars.selected_class ).end()
				.find( fcms_was.vars.attribute_labels_class + ' ' + fcms_was.vars.chosen_attribute_class ).html( fcms_was.vars.no_selection ).end()
				.trigger( 'reset_data' );

			fcms_was.deselect_all_swatch_groups( $form );
		},

		/**
		 * Get currently selected values
		 *
		 * @param element $form
		 */
		get_current_values: function( $form ) {
			var values = {},
				$selects = $form.find( 'select' );

			if ( $selects.length <= 0 ) {
				return false;
			}

			$selects.each( function() {

				var $select = $( this ),
					name = $select.attr( 'name' ),
					value = $select.val();

				if ( !value || value === "" ) {
					return;
				}

				values[ name ] = value;

			} );

			return values;
		},

		/**
		 * Deselect a group of swatches
		 *
		 * @param element $form
		 * @param str attribute
		 */
		deselect_swatch_group: function( $form, attribute ) {
			$form.find( '[data-attribute="' + fcms_was.esc_double_quotes( attribute ) + '"] ' + fcms_was.vars.swatch_class ).removeClass( fcms_was.vars.selected_class );
		},

		/**
		 * Deselect all swatches
		 *
		 * @param element $form
		 */
		deselect_all_swatch_groups: function( $form ) {
			$form.find( fcms_was.vars.swatch_class ).removeClass( fcms_was.vars.selected_class );
		},

		/**
		 * Trigger swatch selections on load
		 */
		swatches_on_load: function() {
			var $selected_options = $( '.variations select' ).find( ':selected' );

			if ( $selected_options.length <= 0 ) {
				return;
			}

			$selected_options.each( function() {
				var $select         = $( this ).closest( 'select' );
				var $form           = $select.closest( 'form' );
				var attribute       = $select.data( 'attribute_name' );
				var selected_values = $( this ).val();

				fcms_was.change_label( $select );

				if ( ! attribute || ! selected_values ) {
					return;
				}

				var $swatch = $( '.fcms-was-swatches[data-attribute="' + attribute + '"] a.fcms-was-swatch[data-attribute-value="' + selected_values + '"]' );

				fcms_was.deselect_swatch_group( $form, attribute );
				$swatch.addClass( fcms_was.vars.selected_class );
			} );
		},

		/**
		 * Helper: Select value
		 *
		 * @param $select
		 * @param value
		 */
		select_value: function( $select, value ) {
			$select.val( value ).change();
		},

		/**
		 * Change selected label
		 */
		change_label: function( $select ) {
			var value = $select.val(),
				$cell = $select.closest( '.value' ),
				// JSON.stringify( value ) adds double quote marks around the value,
				// so no need to have them in the selector. It escapes quotes, etc.
				$swatch = $cell.find( fcms_was.vars.swatch_class + '[data-attribute-value=' + JSON.stringify( value ) + ']' ),
				$label_selected = $cell.prev( '.label' ).find( fcms_was.vars.chosen_attribute_class ),
				attribute_value_name = $swatch.length > 0 ? $swatch.data( 'attribute-value-name' ) : $select.find( 'option[value="' + fcms_was.esc_double_quotes( value ) + '"]' ).text();

			if ( value === "" || typeof attribute_value_name === "undefined" ) {
				return;
			}

			$label_selected.text( attribute_value_name );
		},

		/**
		 * Setup change image links
		 */
		setup_change_image_links: function() {
			$( document.body ).on( 'click', fcms_was.vars.change_image_links_class, function() {
				var $link = $( this ),
					src = $link.attr( 'href' ),
					srcset = $link.data( 'srcset' ),
					sizes = $link.data( 'sizes' ),
					$parent = $link.closest( '.product' ),
					$main_image = $parent.find( 'img:first' );

				$main_image
					.attr( 'src', src )
					.attr( 'srcset', srcset )
					.attr( 'sizes', sizes );

				return false;
			} );
		},

		/**
		 * Escape double quotes.
		 *
		 * @return string
		 */
		esc_double_quotes: function( string ) {
			return String( string ).replace( /"/g, '\\\"' );
		},

		/**
		 * Setup fees.
		 */
		setup_fees: function() {
			$( document.body ).on( 'change', 'input[name="variation_id"]', function() {
				var $field = $( this ),
					variation_id = $field.val();

				if ( variation_id === "" ) {
					return;
				}

				var $form = $field.closest( 'form' );

				if ( $form.length <= 0 ) {
					return;
				}

				var fee_values = fcms_was.get_selected_fee_value( $form );

				if ( ! fee_values || fee_values.default === 0 ) {
					return;
				}

				var $variation_price = $form.find( '.woocommerce-variation-price' ),
					$prices = $variation_price.find( '.amount' );

				if ( $prices.length <= 0 ) {
					return;
				}

				var $suffix = $form.find( '.woocommerce-price-suffix' ),
					suffix_map = $suffix.length > 0 ? fcms_was.get_suffix_map( $suffix ) : false,
					suffix_index = 0,
					fee = 0;

				$prices.each( function( index, price ) {
					var $price = $( price );

					// If this is a price located in the price suffix, determine
					// if tax is inc or ex, and then select the correct fee.
					if ( $price.closest( '.woocommerce-price-suffix' ).length > 0 ) {
						var suffix_price_type = suffix_map[ suffix_index ];

						fee = fee_values[ suffix_price_type ];

						suffix_index ++;
					} else {
						fee = fee_values[ 'default' ];
					}

					var	price_value = accounting.unformat( $price.text(), fcms_was_vars.currency.format_decimal_sep ),
						new_value = price_value + fee,
						regex = new RegExp( '\\d{1,3}(' + fcms_was_vars.currency.format_thousand_sep + '\\d{3})*(\\' + fcms_was_vars.currency.format_decimal_sep + '\\d+)?', 'gm' );

					new_value = accounting.formatMoney( new_value, {
						symbol: '',
						decimal: fcms_was_vars.currency.format_decimal_sep,
						thousand: fcms_was_vars.currency.format_thousand_sep,
						precision: fcms_was_vars.currency.format_num_decimals,
						format: ''
					} );

					$price.html( $price.html().toString().replace( regex, new_value ) );
				} );
			} );
		},

		/**
		 * Determine which type of price appears in the price suffix.
		 *
		 * @param $suffix
		 * @returns {[]}
		 */
		get_suffix_map: function( $suffix ) {
			var map = [],
				suffix = fcms_was_vars.currency.price_display_suffix,
				suffix_split = suffix.split( '{' );

			$.each( suffix_split, function( index, string ) {
				if ( -1 !== string.indexOf( 'price_including_tax' ) ) {
					map.push( 'price_including_tax' );
				} else if ( -1 !== string.indexOf( 'price_excluding_tax' ) ) {
					map.push( 'price_excluding_tax' );
				}
			} );

			return map;
		},

		/**
		 * Setup filterable groups
		 */
		setup_filter_groups: function () {
			// check to see if items with the group data attribute exist.
			if ( $( fcms_was.vars.widget_group_item ).length <= 0 ) {
				return;
			}

			// if so, loop through.
			$( fcms_was.vars.widget_group_item ).each( function () {
				if ( '' === $( this ).data( 'group' ) ) {
					return;
				}

				var $group_list_item = false;
				var $group_name = 'fcms-was-group-' + $(this).data( 'group' ).toLowerCase().replace( ' ', '-' );

				// check to make sure we only add one of each group label.
				if ( $( 'li.fcms-was-swatches__group.' + $group_name ).length === 0 ) {
					$group_list_item = document.createElement( 'li' );
					$group_list_item.classList = 'fcms-was-swatches__group ' + $group_name;

					var $group_list_item_text = document.createElement( 'div' );
					$group_list_item_text.classList = 'fcms-was-swatches__group-label';
					$group_list_item_text.innerHTML = $( this ).data( 'group' );

					$group_list_item.appendChild( $group_list_item_text );
				}

				// check to see if group list item isn't false and append if it is not.
				if ( false !== $group_list_item ) {
					// hide original li so we don't get any random spaces appearing.
					$( fcms_was.vars.widget_class ).append( $group_list_item );
				}

				$(this).parent().remove();
				$( 'li.fcms-was-swatches__group.' + $group_name ).append( $(this) );
			} );
		},

		/**
		 * Get total value of selected fees.
		 *
		 * @param $form
		 *
		 * @return float
		 */
		get_selected_fee_value: function( $form ) {
			var $fee_data = $form.find( '.fcms-was-fees' );

			if ( $fee_data.length <= 0 ) {
				return false;
			}

			var fees_value = { default: 0, price_including_tax: 0, price_excluding_tax: 0 },
				fee_data = JSON.parse( $fee_data.text() ),
				attributes = fcms_was.get_attributes_from_form( $form );

			$.each( attributes, function( attribute, value ) {
				if ( typeof fee_data[ attribute ] === 'undefined' || typeof fee_data[ attribute ][ value ] === 'undefined' ) {
					return;
				}

				fees_value = {
					default: fees_value.default + fee_data[ attribute ][ value ].default,
					price_including_tax: fees_value.price_including_tax + fee_data[ attribute ][ value ].price_including_tax,
					price_excluding_tax: fees_value.price_excluding_tax + fee_data[ attribute ][ value ].price_excluding_tax
				};
			} );

			return fees_value;
		},

		/**
		 * Get attributes from form.
		 *
		 * @param $form
		 * @return {{}}
		 */
		get_attributes_from_form: function( $form ) {
			var attributes = {},
				data = $form.serializeArray();

			if ( data.length <= 0 ) {
				return attributes;
			}

			$.each( data, function( index, item ) {
				if ( item.value === "" || !item.name.startsWith( 'attribute_' ) ) {
					return;
				}

				var name = item.name.replace( 'attribute_', '' );

				attributes[ name ] = item.value;
			} );

			return attributes;
		},

		/**
		 * Run function only once in the certain period.
		 * @param function func      function to call
		 * @param int      wait      Time interval.
		 * @param bool     immediate Immediate.
		 */
		debounce: function( func, wait, immediate ) {
			var timeout;
			return function() {
				var context = this, args = arguments;
				var later = function() {
					timeout = null;
					if ( !immediate ) {
						func.apply( context, args );
					}
				};
				var callNow = immediate && !timeout;
				clearTimeout( timeout );
				timeout = setTimeout( later, wait );
				if ( callNow ) {
					func.apply( context, args );
				}
			};
		}
	};

	var fcms_was_accordion = {
		/**
		 * On Ready.
		 */
		on_ready: function() {
			if ( $( ".fcms-was-accordion.product" ).length && $( ".fcms-was-accordion.product" ).find( ".fcms-was-swatches" ).length ) {
				fcms_was_accordion.cache();
				fcms_was_accordion.setup_accordion_html();
				fcms_was_accordion.click_handler();
				//fcms_was_accordion.open_first_swatch();
			}

			var debounced_resize = fcms_was.debounce( fcms_was_accordion.reset_accordion_height, 250 );

			$( window ).resize( debounced_resize );
		},

		/**
		 * setup cache variables.
		 */
		cache: function() {
			fcms_was_accordion.els = {};
			fcms_was_accordion.els.$product = $( ".fcms-was-accordion.product" );
			fcms_was_accordion.els.$table = fcms_was_accordion.els.$product.find( "table.variations" );
			fcms_was_accordion.els.$trs = fcms_was_accordion.els.$table.find( ">tbody>tr" );
			fcms_was_accordion.els.$td_labels = fcms_was_accordion.els.$table.find( ">tbody>tr>.label" );

		},

		/**
		 * Add markup for accordion header.
		 */
		setup_accordion_html: function() {
			fcms_was_accordion.els.$td_labels.each( function() {
				var $label = $( this ),
					$row = $label.closest( 'tr' ),
					$variation_wrap = $row.find( '.single_variation_wrap' );

				if ( $variation_wrap.length > 0 || $label.find( ".fcms-was-accordion__handle" ).length > 0 ) {
					return;
				}

				$row.addClass( 'fcms-was-accordion__row' );
				$label.append( "<span class='fcms-was-accordion__handle'></span>" );
			} );
		},

		/**
		 * Handle open and close of accordion.
		 */
		click_handler: function() {
			fcms_was_accordion.els.$td_labels.click( function() {
				var $tr = $( this ).parent(),
					$handle = $( this ).find( ".fcms-was-accordion__handle" ),
					$value = $( this ).siblings( ".value" );

				if ( !$tr.hasClass( 'fcms-was-accordion--active' ) ) {
					var height = fcms_was_accordion.get_hidden_elements_height( $value );
					$value.show();
					$value.height( height );
					$tr.addClass( 'fcms-was-accordion--active' );
					//we need set overflow:hidden only during the animation 
					$value.css( "overflow", "hidden" );
					window.setTimeout( function() {
						$value.css( "overflow", "visible" );
					}, 150 );
				} else {
					$tr.removeClass( 'fcms-was-accordion--active' );
					//we need set overflow:hidden only during the animation 
					$value.css( "overflow", "hidden" );
					$value.height( 0 );
					window.setTimeout( function() {
						$value.css( "overflow", "visible" );
						$value.hide();
					}, 150 );
				}
			} );
		},

		/**
		 * Reset the accordion height.
		 */
		reset_accordion_height: function() {
			var $active_accordions = $( '.fcms-was-accordion--active' );

			if ( $active_accordions.length <= 0 ) {
				return;
			}

			$active_accordions.each( function( index, accordion ) {
				var $value = $( accordion ).find( 'td.value' ),
					$children = $value.children(),
					height = 0;

				if ( $children.length > 0 ) {
					$children.each( function( index, child ) {
						var $child = $( child );

						if ( !$child.is( ':visible' ) ) {
							return;
						}

						height += $( child ).outerHeight( true );
					} );
				}

				$value.height( height );
			} );
		},

		/**
		 * Returns the height of element which is hidden.
		 * @param {Object} $element
		 */
		get_hidden_elements_height: function( $element ) {
			$element.height( "auto" ).addClass( 'test' );
			var height = $element.height();
			$element.height( 0 );
			return height;
		},

		/**
		 * Open the first item in accordion on page load.
		 */
		open_first_swatch: function() {
			var $first_label = fcms_was_accordion.els.$trs && fcms_was_accordion.els.$trs.first() && fcms_was_accordion.els.$trs.first().find( ".label" );
			if ( $first_label ) {
				$first_label.trigger( "click" );
			}
		}

	};

	/**
	 * Functions responsible for 'single-line' and 'slider' Overflow behaviour.
	 */
	var fcms_was_adaptive = {
		/**
		 * On Ready.
		 */
		on_ready: function() {
			$( "ul.fcms-was-swatches.fcms-was-swatches--loop" ).not( ".fcms-was-swatches--stacked" ).each( function() {
				// Avoid race condition, by adding a slight delay.
				var $self = $( this );
				window.setTimeout( function() {
					fcms_was_adaptive.handle_adaptive_for_single_ul( $self );
				}, 1 );
			} );

			$( document.body ).on( 'click', '.fcms-was-swatches__item--dummy', fcms_was_adaptive.show_all_attributes );

			var debounced_resize = fcms_was.debounce( fcms_was_adaptive.on_window_resize, 250 );

			$( window ).resize( debounced_resize );
		},

		/**
		 * On window resize.
		 */
		on_window_resize: function() {
			$( ".fcms-was-swatches" ).each( function() {
				var $ul = $( this );
				fcms_was_adaptive.handle_adaptive_for_single_ul( $ul );
			} );
		},

		/**
		 * If the inner content of $ul is more then the width of inner content then it make it work
		 * @param {jQuery Object} $ul .fcms-was-swatches
		 */
		handle_adaptive_for_single_ul: function( $ul ) {
			var overflow_behaviour = $ul.data( "overflow" );

			if ( !overflow_behaviour || 'stacked' === overflow_behaviour ) {
				return;
			}

			// fcms-was-swatches--loading class is added by PHP to non-stacked ULs during 
			// HTML generation. Need to remove this class before calculations,
			// else "white-space: nowrap" property will interfere in the calculations below.
			$ul.removeClass( 'fcms-was-swatches--loading' );
			$ul.css( 'height', 'auto' );

			if ( "slider" === overflow_behaviour ) {
				var wrapper_class = 'fcms-was-swatches--slider-wrapper',
					has_wrapper = $ul.parent( '.' + wrapper_class ).length > 0;

				if ( ! has_wrapper ) {
					$ul.wrap( '<div class="' + wrapper_class + '"></div>' );
					has_wrapper = true;
				}

				var $row = $ul.parent(),
					row_inner_width = $row.width(),
					$ul_children = $ul.find('li'),
					$li = $ul_children.not('.fcms-was-swatches__label').first(),
					li_width = $li.outerWidth( true ),
					li_margin_right = parseInt( $li.css( 'margin-right' ) ),
					visible_swatches = Math.floor( row_inner_width / li_width ),
					number_of_swatches = $ul_children.length,
					max_width = (visible_swatches * li_width) - li_margin_right,
					needs_slider = number_of_swatches > visible_swatches;

				if ( ! needs_slider && has_wrapper ) {
					$ul.unwrap();
				}

				if ( $ul.hasClass( 'flickity-enabled' ) ) {
					if ( needs_slider ) {
						$ul.width( max_width );
						$ul.flickity( 'resize' );
					} else {
						$ul.flickity( 'destroy' );
						$ul.width( '' );
					}
				} else {
					if ( needs_slider ) {
						$ul.width( max_width );
						$ul.flickity( {
							resize: false,
							percentPosition: false,
							selectedAttraction: 0.075,
							friction: 0.42,
							pageDots: false,
							cellAlign: 'left',
							groupCells: true,
							contain: true,
							rightToLeft: false,
							arrowShape: {
								x0: 20,
								x1: 55, y1: 35,
								x2: 60, y2: 30,
								x3: 30
							}
						} );
					} else {
						$ul.width( '' );
					}
				}
			} else { // Single Line.
				var ul_width = $ul.width();
				var include_margin = true;
				var total_inner_width = 0;
				var visible_items = 0;

				$ul.find( ".fcms-was-swatches__item" ).each( function() {
					total_inner_width += $( this ).outerWidth( include_margin );
					if ( total_inner_width < ul_width ) {
						visible_items ++;
					}
				} );

				fcms_was_adaptive.adapt( $ul, visible_items );
			}
		},

		/**
		 * Returns the total width of inner li's of $ul
		 * @param {jQuery Object} $ul
		 */
		get_inner_elements_total_width: function( $ul ) {
			var total_width = 0;
			var include_margin = true;
			jQuery( $ul ).find( ">li" ).each( function() {
				if ( jQuery( this ).is( ":visible" ) ) {
					total_width += jQuery( this ).outerWidth( include_margin );
				}
			} );
			return total_width;
		},

		/**
		 * Only show items_to_show items and hide the rest.
		 *
		 * @param {jQuery Object} $ul
		 * @param {int} items_to_show
		 */
		adapt: function( $ul, items_to_show ) {
			var $all_items = $ul.find( ">li" ).not( ".fcms-was-swatches__item--dummy" );
			$all_items.removeClass( "fcms-was-swatches__item--hidden" );

			var $items_to_hide = $all_items.length <= items_to_show ? false : $all_items.slice( items_to_show - 1 );

			if ( $items_to_hide ) {
				$items_to_hide.addClass( "fcms-was-swatches__item--hidden" );
				$ul.find( ">.fcms-was-swatches__item--dummy a" ).text( "+" + $items_to_hide.length );
				$ul.find( ">.fcms-was-swatches__item--dummy" ).removeClass( "fcms-was-swatches__item--hidden" );
				$ul.addClass( 'fcms-was-swatches--single-line' );
			} else {
				$ul.find( ">.fcms-was-swatches__item--dummy" ).addClass( "fcms-was-swatches__item--hidden" );
				$ul.removeClass( 'fcms-was-swatches--single-line' );
			}
		},

		/**
		 * Show all attributes when clicked on the '+n' button.
		 */
		show_all_attributes: function( e ) {
			e.preventDefault();

			var $ul = $( this ).parent();

			if ( $ul.hasClass( 'fcms-was-swatches--loop' ) ) {
				window.location.href = $( this ).find( 'a' ).attr( 'href' );
				return;
			}

			$ul.find( '.fcms-was-swatches__item--hidden' ).removeClass( 'fcms-was-swatches__item--hidden' );
			$( this ).addClass( 'fcms-was-swatches__item--hidden' );
			$ul.removeClass( 'fcms-was-swatches--single-line' );
			$ul.data( 'overflow', 'stacked' );
			$ul.find( '.fcms-was-swatches__item--dummy' ).hide();
			$( document ).trigger( 'fcms_was_reset_accordion_height' );
		}
	};

	var fcms_was_tooltip = {
		cache: function() {
			fcms_was_tooltip.cache.vars = {
				tooltip: '.fcms-was-tooltip',
				arrow: '.fcms-was-tooltip__arrow',
				anchor: '.fcms-was-swatches--tooltips .fcms-was-swatch:not(.fcms-was-swatch--dummy)',
			};
		},

		on_ready: function() {
			fcms_was_tooltip.cache();

			if ( 0 === $( '.fcms-was-tooltip' ).length ) {
				$( 'body' ).append( '<div class="fcms-was-tooltip"><div class="fcms-was-tooltip__inner_wrap"></div><div class="fcms-was-tooltip__arrow"></div></div>' );
				$( '.fcms-was-tooltip' ).hide();
			}

			$( window ).resize( fcms_was_tooltip.on_resize );

			var debounced_mouseenter = fcms_was.debounce( fcms_was_tooltip.handle_mouseenter, 200 );
			var debounced_mouseleave = fcms_was.debounce( fcms_was_tooltip.handle_mouseleave, 200 );

			// Mobile needs different handling than desktop.
			if ( fcms_was_vars.is_mobile ) {
				fcms_was_tooltip.mobile_click_handler();
				$( window ).scroll( fcms_was_tooltip.handle_mouseleave );
				$( window ).on( 'resize_threshold', fcms_was_tooltip.handle_mouseleave );
			} else {
				$( document.body ).on( 'mouseenter', fcms_was_tooltip.cache.vars.anchor, debounced_mouseenter );
				$( document.body ).on( 'mouseleave', fcms_was_tooltip.cache.vars.anchor, debounced_mouseleave );

				$( window )
					.scroll( fcms_was_tooltip.handle_mouseleave )
					.resize( fcms_was_tooltip.handle_mouseleave );
			}
		},

		handle_mouseenter: function() {
			// determine the coordinates of element and place the tooltip accordingly.
			var $a = $( this ),
				text = $a.find( '.fcms-was-swatch__text' ).html(),
				$tooltip = $( fcms_was_tooltip.cache.vars.tooltip );

			// Update the text before calculation for accuracy.
			$tooltip.find( ".fcms-was-tooltip__inner_wrap" ).html( text );
			$tooltip.css( {
				'left': '',
				'right': ''
			} );

			var $arrow = $tooltip.find( fcms_was_tooltip.cache.vars.arrow ),
				tooltip_width = $tooltip.outerWidth( true ),
				rect = $a.get( 0 ).getBoundingClientRect(),
				a_center_point_x = rect.left + (rect.width / 2),
				tootltip_left = a_center_point_x - (tooltip_width / 2);

			tootltip_left = tootltip_left < 0 ? 0 : tootltip_left;

			var window_width = $( window ).width();

			// If we're off the right side of the screen, use right offset instead.
			if ( ( tooltip_width + tootltip_left ) > window_width ) {
				$tooltip.css( {
					'left': '',
					'right': 0
				} );
			} else {
				$tooltip.css( {
					'left': tootltip_left,
					'right': ''
				} );
			}

			var tooltip_height = $tooltip.outerHeight( true ),
				tooltip_top = rect.top - tooltip_height;

			$tooltip
			.css( 'top', tooltip_top )
			.show()
			.addClass( 'fcms-was-tooltip--animate-opacity fcms-was-tooltip--animate-top fcms-was-tooltip--active' );

			var tooltip_offset = $tooltip.offset();

			$arrow.css( 'left', a_center_point_x - tooltip_offset.left );
		},

		handle_mouseleave: function() {
			$( fcms_was_tooltip.cache.vars.tooltip ).removeClass( 'fcms-was-tooltip--animate-top fcms-was-tooltip--animate-opacity fcms-was-tooltip--active' );
		},

		/**
		 * Click behaviour is slightly different for mobile.
		 * We will "show" the preview on tap/click event instead of hover.
		 * And "hide" the preview when clicked outside.
		 */
		mobile_click_handler: function() {
			var swatches = document.querySelectorAll( fcms_was_tooltip.cache.vars.anchor );

			document.addEventListener( 'click', function ( event ) {
				var composedPath   = event.composedPath();
				var swatch_clicked = false;

				// Determine which swatch was clicked.
				for ( var swatch_idx in swatches ) {
					var swatch = swatches[swatch_idx];
					if ( composedPath.includes( swatch ) ) {
						swatch_clicked = swatch;
						break;
					}
				}

				if ( ! swatch_clicked ) {
					// if swatch_clicked is empty then user clicked outside of a swatch
					// trigger a mouseleave event.
					fcms_was_tooltip.handle_mouseleave();
				} else {
					// Clicked on a swatch. Call handle_mouseenter and pass the swatch element
					// which was cicked as `this`.
					fcms_was_tooltip.handle_mouseenter.call( swatch_clicked );
				}
			} );
		},

		/**
		 * Trigger 'resize_threshold' event if the difference of screen width is more than 10px.
		 */
		on_resize: function () {
			var old_size = fcms_was.vars.window_size;
			var new_size = $( window ).width();
			var diff     = old_size - new_size;
			
			if ( Math.abs( diff ) > 10 ) {
				fcms_was.vars.window_size = new_size;
				$( window ).trigger( 'resize_threshold' );
			}
		},
	};

	$( document ).ready( fcms_was.on_ready );
	$( document ).ready( fcms_was_accordion.on_ready );
	$( document ).ready( fcms_was_tooltip.on_ready );
	$( document ).ready( fcms_was_adaptive.on_ready );
	$( document ).on( 'fcms_was_reset_accordion_height', fcms_was_accordion.reset_accordion_height );

}( jQuery, document ));
