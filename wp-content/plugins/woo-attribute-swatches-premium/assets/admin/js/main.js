(function( $, document ) {

	var fcms_was = {

		cache: function() {
			fcms_was.els = {};
			fcms_was.vars = {};

			// common vars
			fcms_was.vars.u_hide_class = 'fcms-was-u-hide';

			// common elements
			fcms_was.els.attribute_form = $( ".product_page_product_attributes #attribute_label" ).closest( 'form' );
			fcms_was.els.attribute_form_submit = fcms_was.els.attribute_form.find( '.submit' );
			fcms_was.els.product_attribute_swatch_types = $( '.fcms-was-attributes__swatch-type select' );
			fcms_was.els.attribute_type_field = $( 'select[name="attribute_type"]' );
		},

		on_ready: function() {
			// on ready stuff here
			fcms_was.cache();
			fcms_was.setup_attribute_fields();
			fcms_was.setup_product_swatch_fields();
		},

		/**
		 * Dynamically insert the attribute fields
		 */
		setup_attribute_fields: function() {

			fcms_was.get_attribute_fields( function( fields ) {

				var fields_formatted = fcms_was.format_attribute_fields( fields );

				fcms_was.els.attribute_form_submit.before( fields_formatted );

				$( '[data-conditional]' ).fcmsConditional();

			} );

			fcms_was.els.attribute_type_field.on( 'change', function() {

				var attribute_type = $( this ).val(),
					$attribute_fields = $( '.fcms-was-attribute-fields' );

				if ( attribute_type === "select" ) {

					$attribute_fields.removeClass( fcms_was.vars.u_hide_class );

				} else {

					$attribute_fields.addClass( fcms_was.vars.u_hide_class );

				}

			} );

			fcms_was.setup_colour_pickers();
			fcms_was.setup_image_swatch_fields();

		},

		/**
		 * Setup colour picker fields
		 */
		setup_colour_pickers: function() {

			$( '.colour-swatch-picker' ).wpColorPicker();

		},

		/**
		 * Setup image swatch fields
		 */
		setup_image_swatch_fields: function() {

			// Uploading files
			var file_frame;

			$( '.fcms-was-image-picker__upload' ).on( 'click', function( event ) {

				event.preventDefault();

				var $image_swatch_upload = $( this ),
					$image_swatch_wrapper = $image_swatch_upload.closest( '.fcms-was-image-picker' ),
					$image_swatch_field = $image_swatch_wrapper.find( '.fcms-was-image-picker__field' ),
					$image_swatch_preview = $image_swatch_wrapper.find( '.fcms-was-image-picker__preview' ),
					$image_swatch_remove = $image_swatch_wrapper.find( '.fcms-was-image-picker__remove' );

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media( {
					title: $( this ).data( 'title' ),
					button: {
						text: $( this ).data( 'button-text' ),
					},
					multiple: false  // Set to true to allow multiple files to be selected
				} );

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {

					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get( 'selection' ).first().toJSON();
					attachment_url = typeof attachment.sizes.thumbnail !== "undefined" ? attachment.sizes.thumbnail.url : attachment.url;

					$image_swatch_field.val( attachment.id );
					$image_swatch_preview.html( '<img src="' + attachment_url + '" class="attachment-thumbnail size-thumbnail">' );
					$image_swatch_upload.addClass( 'fcms-was-image-picker__upload--edit' );
					$image_swatch_remove.show();

				} );

				// Finally, open the modal
				file_frame.open();

			} );

			$( '.fcms-was-image-picker__remove' ).on( 'click', function( event ) {

				event.preventDefault();

				var $image_swatch_wrapper = $( this ).closest( '.fcms-was-image-picker' ),
					$image_swatch_field = $image_swatch_wrapper.find( '.fcms-was-image-picker__field' ),
					$image_swatch_preview = $image_swatch_wrapper.find( '.fcms-was-image-picker__preview' ),
					$image_swatch_upload = $image_swatch_wrapper.find( '.fcms-was-image-picker__upload' );

				$image_swatch_field.val( '' );
				$image_swatch_preview.html( '' );
				$image_swatch_upload.removeClass( 'fcms-was-image-picker__upload--edit' );
				$( this ).hide();

			} );

		},

		/**
		 * Helper: Get admin page
		 */
		get_admin_page: function() {

			if ( fcms_was.els.attribute_form.length <= 0 ) {
				return false;
			}

			if ( fcms_was.els.attribute_form.find( 'table' ).length > 0 ) {

				return 'update';

			} else {

				return 'add';

			}

		},

		/**
		 * Helper: Get attribute fields
		 *
		 * @param func callback
		 */
		get_attribute_fields: function( callback ) {

			var fields = false,
				attribute_id = typeof fcms_was_vars.url_params.edit !== "undefined" ? fcms_was_vars.url_params.edit : false,
				data = {
					'action': 'fcms_was_get_attribute_fields',
					'attribute_id': attribute_id
				};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( ajaxurl, data, function( response ) {

				if ( response.success !== true ) {
					return false;
				}

				if ( callback && typeof(callback) === "function" ) {
					callback( response.fields );
				}

			} );

		},

		/**
		 * Helper: Format attribute fields
		 *
		 * @param obj fields
		 */
		format_attribute_fields: function( fields ) {

			var formatted_fields = "",
				attribute_type = fcms_was.els.attribute_type_field.val(),
				hidden_class = attribute_type === "text" ? fcms_was.vars.u_hide_class : "";

			if ( fcms_was.get_admin_page() === "update" ) {
				var $table = $( '<table />' ),
					$tbody = $( '<tbody />' );

				$table.attr( 'class', 'form-table fcms-was-attribute-fields ' + hidden_class );

				$.each( fields, function( field_id, field_data ) {
					var condition = field_data.condition ? ( $.isArray( field_data.condition ) ? JSON.stringify( field_data.condition ) : field_data.condition ) : false,
						match = JSON.stringify( field_data.match ),
						$row = $( '<tr />' ),
						$row_th = $( '<th scope="row" valign="top" />' ),
						$row_td = $( '<td />' );

					$row.attr( 'class', 'fcms-was-attribute-row form-field ' + field_data.class.join( ' ' ) );

					if ( condition ) {
						$row.attr( 'data-condition', condition );
						$row.attr( 'data-match', match );
					}

					$row_th.append( '<label for="' + field_id + '">' + field_data.label + '</label>' );
					$row_td.append( field_data.field + '<p class="description">' + field_data.description + '</p>' );

					$row.append( $row_th, $row_td );

					$tbody.append( $row );
				} );

				formatted_fields = $table.append( $tbody );
			} else {

				var $div = $( '<div />' );

				$div.attr( 'class', 'fcms-was-attribute-fields ' + hidden_class );

				$.each( fields, function( field_id, field_data ) {
					var condition = field_data.condition ? ( $.isArray( field_data.condition ) ? JSON.stringify( field_data.condition ) : field_data.condition ) : false,
						match = JSON.stringify( field_data.match ),
						$inner_div = $( '<div />' );

					$inner_div.attr( 'class', 'fcms-was-attribute-row form-field ' + field_data.class.join( ' ' ) );

					if ( condition ) {
						$inner_div.attr( 'data-condition', condition );
						$inner_div.attr( 'data-match', match );
					}

					$inner_div.append( '<label for="' + field_id + '">' + field_data.label + '</label>' );
					$inner_div.append( field_data.field + '<p class="description">' + field_data.description + '</p>' );

					$div.append( $inner_div );
				} );

				formatted_fields = $div;
			}

			return formatted_fields;

		},

		/**
		 * Escape
		 *
		 * @param str string
		 */
		escape: function( string ) {

			return string
				.replace( /[\\]/g, '\\\\' )
				.replace( /[\"]/g, '\&quot;' )
				.replace( /[\/]/g, '\\/' )
				.replace( /[\b]/g, '\\b' )
				.replace( /[\f]/g, '\\f' )
				.replace( /[\n]/g, '\\n' )
				.replace( /[\r]/g, '\\r' )
				.replace( /[\t]/g, '\\t' );

		},

		/**
		 * Setup the swatch fields for products
		 */
		setup_product_swatch_fields: function() {
			fcms_was.els.product_attribute_swatch_types.on( 'change', function() {

				var $select = $( this ),
					swatch_type = $select.val(),
					swatch_type_text = $select.find( ':selected' ).text(),
					$attribute_wrapper = $select.closest( '.fcms-was-attribute-wrapper' ),
					$swatch_type = $attribute_wrapper.find( '.fcms-was-swatch-type' ),
					$swatch_options = $attribute_wrapper.find( '.fcms-was-attributes__swatch-options td' ),
					product_id = parseInt( $attribute_wrapper.data( 'product-id' ) ),
					attribute_slug = $attribute_wrapper.data( 'taxonomy' ),
					$wc_metabox_content = $attribute_wrapper.find( '.wc-metabox-content' );

				$swatch_type.text( swatch_type_text );
				$swatch_options.html( '' );
				fcms_was.reset_height( $wc_metabox_content );

				if ( fcms_was.is_swatch_visual( swatch_type ) ) {
					var data = {
						action: 'fcms_was_get_product_attribute_fields',
						swatch_type: swatch_type,
						product_id: product_id,
						attribute_slug: attribute_slug
					};

					$.post( ajaxurl, data, function( response ) {
						if ( response.success !== true ) {
							return false;
						}

						if ( response.fields === false ) {
							return false;
						}

						$swatch_options.html( response.fields );
						fcms_was.setup_image_swatch_fields();
						fcms_was.setup_colour_pickers();
						fcms_was.reset_height( $wc_metabox_content );
					} );
				}
			} );

		},

		/**
		 * Helper: Is swatch type visual
		 *
		 * @param str swatch_type
		 */
		is_swatch_visual: function( swatch_type ) {

			return swatch_type === "colour-swatch" || swatch_type === "image-swatch";

		},

		/**
		 * Reset height of element
		 *
		 * @param obj $el
		 */
		reset_height: function( $el ) {

			$el.height( '' );

		}

	};

	$( document ).ready( fcms_was.on_ready() );

}( jQuery, document ));