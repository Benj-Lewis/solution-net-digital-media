<?php
/**
 * SND Média — thème magazine d'actualité pour Solution Net Digital.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SND_MEDIA_VERSION', '2.0.0' );

require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/demo-data.php';
require get_template_directory() . '/inc/setup-content.php';

/**
 * Rubriques du site : slug => [nom, couleur].
 */
function snd_media_sections() {
	return array(
		'actualite' => array( 'Actualité', '#1b6fb3' ),
		'politique' => array( 'Politique', '#c62828' ),
		'securite'  => array( 'Sécurité', '#37474f' ),
		'economie'  => array( 'Économie', '#2e7d32' ),
		'sport'     => array( 'Sport', '#ef6c00' ),
		'societe'   => array( 'Société', '#6a1b9a' ),
		'culture'   => array( 'Culture', '#ad1457' ),
		'femme'     => array( 'Femme', '#d81b60' ),
		'justice'   => array( 'Justice', '#5d4037' ),
		'sante'     => array( 'Santé', '#00897b' ),
		'afrique'   => array( 'Afrique', '#b8860b' ),
	);
}

function snd_cat_color( $term ) {
	$sections = snd_media_sections();
	$slug     = is_object( $term ) ? $term->slug : (string) $term;
	return isset( $sections[ $slug ] ) ? $sections[ $slug ][1] : '#1b6fb3';
}

add_action( 'after_setup_theme', 'snd_media_setup' );
function snd_media_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	add_image_size( 'snd-xl', 1280, 720, true );
	add_image_size( 'snd-card', 720, 405, true );
	add_image_size( 'snd-thumb', 300, 200, true );

	register_nav_menus(
		array(
			'primary' => 'Menu principal (rubriques)',
			'footer'  => 'Pied de page (liens utiles)',
		)
	);
}

add_action( 'widgets_init', 'snd_media_widgets' );
function snd_media_widgets() {
	register_sidebar(
		array(
			'name'          => 'Bannière publicitaire (en-tête, 728×90)',
			'id'            => 'snd-ad-header',
			'before_widget' => '<div class="snd-ad-widget">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => 'Colonne latérale',
			'id'            => 'snd-sidebar',
			'description'   => 'Sous « Les plus lus » et « Suivez-nous ». Idéal pour une publicité 300×250.',
			'before_widget' => '<section class="snd-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="snd-widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

/**
 * Polices hébergées dans le thème (aucun appel à Google).
 */
function snd_media_font_css() {
	$base  = get_template_directory_uri() . '/assets/fonts/';
	$faces = array(
		array( 'Archivo', 700, 'normal', 'archivo-latin-700-normal' ),
		array( 'Archivo', 800, 'normal', 'archivo-latin-800-normal' ),
		array( 'Inter', 400, 'normal', 'inter-latin-400-normal' ),
		array( 'Inter', 600, 'normal', 'inter-latin-600-normal' ),
		array( 'Inter', 700, 'normal', 'inter-latin-700-normal' ),
		array( 'Source Serif 4', 400, 'normal', 'source-serif-4-latin-400-normal' ),
		array( 'Source Serif 4', 400, 'italic', 'source-serif-4-latin-400-italic' ),
		array( 'Source Serif 4', 700, 'normal', 'source-serif-4-latin-700-normal' ),
	);
	$css = '';
	foreach ( $faces as $f ) {
		$css .= "@font-face{font-family:'{$f[0]}';font-style:{$f[2]};font-weight:{$f[1]};font-display:swap;src:url('{$base}{$f[3]}.woff2') format('woff2');}";
	}
	return $css;
}

add_action( 'wp_enqueue_scripts', 'snd_media_assets' );
function snd_media_assets() {
	wp_enqueue_style( 'snd-media', get_stylesheet_uri(), array(), SND_MEDIA_VERSION );
	wp_add_inline_style( 'snd-media', snd_media_font_css() . snd_media_cat_css() );
	wp_enqueue_script( 'snd-media', get_template_directory_uri() . '/assets/main.js', array(), SND_MEDIA_VERSION, true );
	wp_localize_script(
		'snd-media',
		'SND',
		array(
			'ajax'   => admin_url( 'admin-ajax.php' ),
			'postId' => is_singular( 'post' ) ? get_queried_object_id() : 0,
		)
	);
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_head', 'snd_media_preload', 1 );
function snd_media_preload() {
	$base = get_template_directory_uri() . '/assets/fonts/';
	foreach ( array( 'archivo-latin-800-normal', 'inter-latin-400-normal' ) as $f ) {
		echo '<link rel="preload" href="' . esc_url( $base . $f . '.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
	echo '<meta name="theme-color" content="#0b1b33">' . "\n";
}

/**
 * Une classe CSS par rubrique, pour colorer étiquettes et titres de section.
 */
function snd_media_cat_css() {
	$css = '';
	foreach ( snd_media_sections() as $slug => $s ) {
		$css .= ".snd-c-{$slug}{--cat:{$s[1]};}";
	}
	return $css;
}

add_action( 'wp_head', 'snd_media_default_icon' );
function snd_media_default_icon() {
	if ( has_site_icon() ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/';
	echo '<link rel="icon" href="' . esc_url( $base . 'icon-192.png' ) . '" sizes="192x192">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $base . 'icon-512.png' ) . '">' . "\n";
}

add_filter( 'excerpt_length', function () {
	return 24;
} );
add_filter( 'excerpt_more', function () {
	return '…';
} );
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Compteur de lectures pour « Les plus lus » (compatible avec le cache de page).
 */
add_action( 'wp_ajax_snd_view', 'snd_media_count_view' );
add_action( 'wp_ajax_nopriv_snd_view', 'snd_media_count_view' );
function snd_media_count_view() {
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $id && 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
		update_post_meta( $id, '_snd_views', (int) get_post_meta( $id, '_snd_views', true ) + 1 );
	}
	wp_die( '', '', array( 'response' => 204 ) );
}

/**
 * Réglages dans Apparence > Personnaliser.
 */
add_action( 'customize_register', 'snd_media_customize' );
function snd_media_customize( $wp_customize ) {
	$wp_customize->add_section( 'snd_media', array( 'title' => 'SND Média — identité, contact, réseaux', 'priority' => 25 ) );

	$wp_customize->add_setting( 'snd_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'snd_logo',
			array(
				'label'     => 'Logo (laisser vide pour le logo Solution Net Digital fourni)',
				'section'   => 'snd_media',
				'mime_type' => 'image',
			)
		)
	);

	$fields = array(
		'snd_tagline'  => array( 'Slogan sous le nom', "L'info en continu", 'text' ),
		'snd_about'    => array( 'Présentation (pied de page)', "Solution Net Digital est un média d'information en ligne : politique, économie, société, sport et culture, avec des repères clairs pour comprendre l'actualité.", 'textarea' ),
		'snd_address'  => array( 'Adresse', 'Kinshasa, RD Congo', 'text' ),
		'snd_phone'    => array( 'Téléphone', '(+243) 833 803 102', 'text' ),
		'snd_email'    => array( 'E-mail', 'solutionnet102@gmail.com', 'text' ),
		'snd_facebook' => array( 'Lien Facebook', '', 'url' ),
		'snd_instagram' => array( 'Lien Instagram', '', 'url' ),
		'snd_tiktok'   => array( 'Lien TikTok', '', 'url' ),
		'snd_whatsapp' => array( 'Lien WhatsApp (chaîne ou numéro wa.me)', '', 'url' ),
		'snd_x'        => array( 'Lien X (Twitter)', '', 'url' ),
		'snd_youtube'  => array( 'Lien YouTube', '', 'url' ),
		'snd_telegram' => array( 'Lien Telegram', '', 'url' ),
	);
	foreach ( $fields as $id => $f ) {
		$sanitize = 'url' === $f[2] ? 'esc_url_raw' : ( 'textarea' === $f[2] ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $f[1], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'snd_media', 'type' => $f[2] ) );
	}

	$wp_customize->add_setting( 'snd_ticker', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control( 'snd_ticker', array( 'label' => 'Afficher le bandeau « Flash info »', 'section' => 'snd_media', 'type' => 'checkbox' ) );
}

function snd_opt( $key ) {
	$defaults = array(
		'snd_tagline' => "L'info en continu",
		'snd_about'   => "Solution Net Digital est un média d'information en ligne : politique, économie, société, sport et culture, avec des repères clairs pour comprendre l'actualité.",
		'snd_address' => 'Kinshasa, RD Congo',
		'snd_phone'   => '(+243) 833 803 102',
		'snd_email'   => 'solutionnet102@gmail.com',
		'snd_ticker'  => true,
	);
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function snd_media_socials() {
	$map = array(
		'snd_facebook'  => array( 'facebook', 'Facebook' ),
		'snd_instagram' => array( 'instagram', 'Instagram' ),
		'snd_tiktok'    => array( 'tiktok', 'TikTok' ),
		'snd_whatsapp'  => array( 'whatsapp', 'WhatsApp' ),
		'snd_x'         => array( 'x', 'X' ),
		'snd_youtube'   => array( 'youtube', 'YouTube' ),
		'snd_telegram'  => array( 'telegram', 'Telegram' ),
	);
	$out = array();
	foreach ( $map as $key => $m ) {
		$url = get_theme_mod( $key );
		if ( $url ) {
			$out[] = array( 'icon' => $m[0], 'label' => $m[1], 'url' => $url );
		}
	}
	return $out;
}
