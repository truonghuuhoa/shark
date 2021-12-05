<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FCMS_WooThumbs_Media.
 *
 * @class    FCMS_WooThumbs_Media
 * @version  1.0.0
 * @package  FCMS_WooThumbs
 * @category Class
 * @author   FCMS
 */
class FCMS_WooThumbs_Media {
	/**
	 * @var string
	 */
	private static $media_meta_key = 'fcms_woothumbs_media';

	/**
	 * @var string
	 */
	private static $media_aspect_ratio_meta_key = 'fcms_woothumbs_media_aspect_ratio';

	/**
	 * Run
	 */
	public static function run() {
		add_action( 'fcms_woothumbs_before_thumbnail', array( __CLASS__, 'thumbnail_play_icon' ), 10, 2 );

		add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'attachment_fields_to_edit' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( __CLASS__, 'attachment_fields_to_save' ), 10, 2 );
		add_filter( 'oembed_result', array( __CLASS__, 'oembed_result' ), 10, 3 );
		add_filter( 'fcms_woothumbs_single_image_data', array( __CLASS__, 'single_image_data' ), 10, 2 );
	}

	/**
	 * Add fields to the $form_fields array
	 *
	 * @param array  $form_fields
	 * @param object $post
	 *
	 * @return array
	 */
	public static function attachment_fields_to_edit( $form_fields, $post ) {
		if ( strpos( $post->post_mime_type, 'image/' ) !== 0 ) {
			return $form_fields;
		}

		$form_fields['fcms_woothumbs_media_title'] = array(
			'tr' => '<td colspan="2" id="fcms-woothumbs-attach-mp4-header-cell" >' . sprintf( '<h2>%s</h2>', __( 'WooThumbs Media Details', 'fcms-woothumbs' ) ) . '</td>',
		);

		$form_fields[ self::$media_meta_key ] = array(
			'label' => __( 'Media URL', 'fcms-woothumbs' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_' . self::$media_meta_key, true ),
		);

		/* Translators: %s: Link to wordpress.org article */
		$valid_media_link = sprintf( __( 'Enter a <a href="%s" target="_blank">valid media URL</a>, or click "Attach MP4" to upload your own MP4 video into the WordPress media library.', 'fcms-woothumbs' ), esc_url( 'https://wordpress.org/support/article/embeds/#okay-so-what-sites-can-i-embed-from' ) );

		$form_fields['fcms_woothumbs_media_upload'] = array(
			'tr' => '<th scope="row" class="label">&nbsp;</th>
			<td class="field fcms-woothumbs-attach-btn-cell">
			<span class="setting has-description"><a href="#" class="fcms-wt-upload-media button-secondary" data-image-id="' . esc_attr( $post->ID ) . '">' . __( 'Attach MP4', 'fcms-woothumbs' ) . '</a></span>
			<p class="description" style="width: 100%; padding-top: 4px;">' . $valid_media_link . '</p>			
			</td>',
		);

		$form_fields[ self::$media_aspect_ratio_meta_key ] = array(
			'label' => __( 'Aspect Ratio', 'fcms-woothumbs' ),
			'input' => 'text',
			'value' => self::get_aspect_ratio( $post->ID ),
		);

        $form_fields['fcms_woothumbs_media_description'] = array(
            'tr' => '<td colspan="2" style="display: block; padding-top: 8px;">
			    <p class="description">' . sprintf( '<strong>%s</strong>: %s', __( 'Note', 'fcms-woothumbs' ), __( 'Any changes made to the WooThumbs media settings are saved automatically.', 'fcms-woothumbs' ) ) . '</p>			
			</td>',
        );

		return $form_fields;
	}

	/**
	 * Get aspect ratio.
	 *
	 * @param int|null $attachment_id
	 * @param bool     $percentage
	 *
	 * @return str|int
	 */
	public static function get_aspect_ratio( $attachment_id = null, $percentage = false ) {
		$aspect_ratio = $attachment_id ? get_post_meta( $attachment_id, '_' . self::$media_aspect_ratio_meta_key, true ) : false;
		$aspect_ratio = ! empty( $aspect_ratio ) ? $aspect_ratio : '16:9';
		$aspect_ratio = apply_filters( 'fcms-woothumbs-aspect-ratio', $aspect_ratio, $attachment_id, $percentage );

		if ( ! $percentage ) {
			return $aspect_ratio;
		}

		$aspect_ratio_parts = explode( ':', $aspect_ratio );

		return ( $aspect_ratio_parts[1] / $aspect_ratio_parts[0] ) * 100;
	}

	/**
	 * Save attachment fields
	 *
	 * @param array $post
	 * @param array $attachment
	 *
	 * @return array
	 */
	public static function attachment_fields_to_save( $post, $attachment ) {
		global $fcms_woothumbs_class;

		if ( isset( $attachment[ self::$media_meta_key ] ) ) {
			update_post_meta( $post['ID'], '_' . self::$media_meta_key, $attachment[ self::$media_meta_key ] );
		}

		if ( isset( $attachment[ self::$media_aspect_ratio_meta_key ] ) ) {
			update_post_meta( $post['ID'], '_' . self::$media_aspect_ratio_meta_key, $attachment[ self::$media_aspect_ratio_meta_key ] );
		}

		if ( ! empty( $post['post_parent'] ) ) {
			$fcms_woothumbs_class->delete_transients( true, $post['post_parent'] );
		}

		return $post;
	}

	/**
	 * Oembed result.
	 */
	public static function oembed_result( $result, $url, $args ) {
		if ( empty( $args['fcms-woothumbs'] ) ) {
			return $result;
		}

		$embed = wp_oembed_get( $url );

		if ( strpos( $url, 'youtube' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
			$embed = self::modify_embed_src( $embed, $url, array( 'showinfo' => 0, 'rel' => 0, 'autoplay' => 0, 'iv_load_policy' => 3 ) );
		} elseif ( strpos( $url, 'vimeo' ) !== false ) {
			$embed = self::modify_embed_src( $embed, $url, array( 'byline' => 0, 'title' => 0, 'portrait' => 0 ) );
		}

		return $embed;
	}

	/**
	 * Modify embed src.
	 */
	public static function modify_embed_src( $html, $url, $args ) {
		$join         = strpos( $html, '?' ) !== false ? "&amp;" : "?";
		$query        = http_build_query( $args );
		$patterns     = '/src="(.*?)"/';
		$replacements = sprintf( 'src="${1}%s%s"', $join, $query );

		return preg_replace( $patterns, $replacements, $html );
	}

	/**
	 * Get media URL.
	 *
	 * @param $attachment_id
	 *
	 * @return bool|str
	 */
	public static function get_media_url( $attachment_id ) {
		return get_post_meta( $attachment_id, '_' . self::$media_meta_key, true );
	}

	/**
	 * Get media embed.
	 *
	 * @param int|string $attachment Attachment ID or media URL.
	 *
	 * @return bool|str
	 */
	public static function get_media_embed( $attachment ) {
		$attachment_id = is_numeric( $attachment ) ? absint( $attachment ) : false;
		$media_url = $attachment_id ? self::get_media_url( $attachment_id ) : trim( $attachment );

		if ( empty( $media_url ) ) {
			return false;
		}

		// Is the URL an MP4?
		preg_match( '#^(http|https)://.+\.(mp4|MP4|mpeg4)(?=\?|$)#i', $media_url, $matches );

		if ( ! empty( $matches[0] ) ) {
			$embed = self::get_mp4_embed( $media_url, $attachment_id );
		} else {
			$embed = wp_oembed_get( $media_url, array( 'fcms-woothumbs' => true ) );
			// Decode for uniformity. These special characters would be encoded again by Woo before output.
			$embed = html_entity_decode( $embed );
		}

		if ( ! $embed ) {
			$embed = '<iframe src="' . esc_url( $media_url ) . '" frameborder="0"></iframe>';
		}

		// Add classes to disable lazyloading for iframes.
		if ( false !== strpos( $embed, '<iframe' ) ) {
			$lazy_classes = 'no-lazyload skip-lazy';
			$has_class    = false !== strpos( $embed, 'class="' );
			$replace      = $has_class ? 'class="' : '<iframe';
			$with         = $has_class ? 'class="' . $lazy_classes . '"' : '<iframe class="' . $lazy_classes . '"';

			$embed = str_replace( $replace, $with, $embed );
		}

		return sprintf( '<div class="fcms-woothumbs-responsive-media" style="padding-bottom: %s%%;">%s</div>', self::get_aspect_ratio( $attachment_id, true ), $embed );
	}

	/**
	 * Add thumbnail play icon.
	 *
	 * @param array $image
	 * @param int   $i
	 */
	public static function thumbnail_play_icon( $image, $i ) {
		if ( empty( $image['media_embed'] ) ) {
			return;
		}

		echo '<div class="fcms-woothumbs-thumbnails__play-overlay"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-play"></i></div>';
	}

	/**
	 * Modify single image sizes.
	 *
	 * @param int $data
	 * @param int $attachment_id
	 *
	 * @return bool|array
	 */
	public static function single_image_data( $data, $attachment_id ) {
		if ( empty( $data ) ) {
			return $data;
		}

		if ( 'placeholder' === $attachment_id ) {
			return $data;
		}

		$data['media_embed'] = self::get_media_embed( $attachment_id );

		return $data;
	}

	/**
	 * Get formatted MP4 embed.
	 *
	 * @param null|string $media_url
	 * @param null|int    $attachment_id
	 *
	 * @return string
	 */
	public static function get_mp4_embed( $media_url = null, $attachment_id = null ) {
		if ( empty( $media_url ) ) {
			return '';
		}

		global $fcms_woothumbs_class;

		$controls       = boolval( $fcms_woothumbs_class->settings['media_mp4_controls'] );
		$loop           = boolval( $fcms_woothumbs_class->settings['media_mp4_loop'] );
		$autoplay       = boolval( $fcms_woothumbs_class->settings['media_mp4_autoplay'] );
		$lazyload_video = boolval( $fcms_woothumbs_class->settings['media_mp4_lazyload'] );
		$poster         = is_numeric( $attachment_id ) ? wp_get_attachment_url( $attachment_id ) : '';
		$preload        = $lazyload_video ? 'none' : 'metadata';

		$class   = array();
		$class[] = $controls ? 'fcms-woothumbs-responsive-media__manual-embed--controls' : '';
		$class   = array_filter( $class );

		$atts   = array();
		$atts[] = $loop ? 'loop' : '';
		$atts[] = $autoplay ? 'autoplay playsinline muted' : '';
		$atts   = array_filter( $atts );

		$return = '<video class="fcms-woothumbs-responsive-media__manual-embed intrinsic-ignore ' . esc_attr( implode( ' ', $class ) ) . '" ' . implode( ' ', $atts ) . ' poster="' . esc_url( $poster ) . '"><source src="' . esc_url( $media_url ) . '"  type="video/mp4" preload="' . esc_attr( $preload ) . '" ></video>';

		if ( $controls ) {
			$icon   = $autoplay ? 'pause' : 'play-alt';
			$return .= '<div class="fcms-woothumbs-responsive-media__controls fcms-woothumbs-responsive-media__controls--' . ( $autoplay ? 'pause' : 'play' ) . '"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-' . esc_attr( $icon ) . '"></i></div>';
		}

		return $return;
	}
}