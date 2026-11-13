<?php
/**
 * SND Média — fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SND_MEDIA_VERSION', '1.0.0' );

require get_template_directory() . '/inc/setup-content.php';

/**
 * Rubriques du site, dans l'ordre du menu.
 */
function snd_media_sections() {
	return array(
		'actualite' => 'Actualité',
		'politique' => 'Politique',
		'securite'  => 'Sécurité',
		'economie'  => 'Économie',
		'sport'     => 'Sport',
		'societe'   => 'Société',
		'culture'   => 'Culture',
		'femme'     => 'Femme',
		'justice'   => 'Justice',
		'sante'     => 'Santé',
		'afrique'   => 'Afrique',
	);
}

add_action( 'after_setup_theme', 'snd_media_setup' );
function snd_media_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 160, 'width' => 400, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_image_size( 'snd-hero', 1200, 700, true );
	add_image_size( 'snd-card', 640, 360, true );
	add_image_size( 'snd-small', 240, 180, true );

	register_nav_menus( array( 'primary' => 'Menu principal' ) );
}

add_action( 'widgets_init', 'snd_media_widgets' );
function snd_media_widgets() {
	register_sidebar(
		array(
			'name'          => 'Bannière publicitaire (en-tête)',
			'id'            => 'snd-ad-header',
			'description'   => 'Emplacement 728×90 à droite du logo.',
			'before_widget' => '<div class="snd-ad-widget">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => 'Colonne latérale',
			'id'            => 'snd-sidebar',
			'before_widget' => '<section class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<div class="snd-section-title"><span class="snd-h">',
			'after_title'   => '</span></div>',
		)
	);
}

add_action( 'wp_enqueue_scripts', 'snd_media_assets' );
function snd_media_assets() {
	wp_enqueue_style( 'snd-media', get_stylesheet_uri(), array(), SND_MEDIA_VERSION );
	if ( is_front_page() || is_home() ) {
		wp_enqueue_script( 'snd-media-slider', get_template_directory_uri() . '/assets/slider.js', array(), SND_MEDIA_VERSION, true );
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Icône du site par défaut tant qu'aucune n'est définie dans Apparence > Personnaliser.
 */
add_action( 'wp_head', 'snd_media_default_icon' );
function snd_media_default_icon() {
	if ( has_site_icon() ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/';
	echo '<link rel="icon" href="' . esc_url( $base . 'icon-192.png' ) . '" sizes="192x192">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $base . 'icon-512.png' ) . '">' . "\n";
}

/**
 * Logo : celui du Personnaliseur s'il existe, sinon celui livré avec le thème.
 */
function snd_media_brand() {
	$name = get_bloginfo( 'name' );
	echo '<a class="snd-brand" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( has_custom_logo() ) {
		$logo = wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'custom-logo', 'alt' => $name ) );
		echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo '<img src="' . esc_url( get_template_directory_uri() . '/assets/logo-mark.png' ) . '" alt="" width="234" height="160">';
	}
	$tagline = get_theme_mod( 'snd_tagline', 'L\'info en continu' );
	echo '<span class="snd-brand-name">' . esc_html( $name );
	if ( $tagline ) {
		echo '<small>' . esc_html( $tagline ) . '</small>';
	}
	echo '</span></a>';
}

/**
 * Vignette d'un article, avec un visuel aux couleurs du site quand il n'y a pas d'image.
 */
function snd_media_thumb( $size = 'snd-card', $link = true, $post = null ) {
	$post  = get_post( $post );
	$open  = $link ? '<a class="snd-thumb%s" href="' . esc_url( get_permalink( $post ) ) . '" tabindex="-1" aria-hidden="true">' : '<div class="snd-thumb%s">';
	$close = $link ? '</a>' : '</div>';
	if ( has_post_thumbnail( $post ) ) {
		printf( $open, '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo get_the_post_thumbnail( $post, $size, array( 'loading' => 'lazy', 'alt' => '' ) );
	} else {
		printf( $open, ' snd-thumb--empty' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$cat = snd_media_primary_cat( $post );
		echo '<span>' . esc_html( $cat ? $cat->name : get_bloginfo( 'name' ) ) . '</span>';
	}
	echo $close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function snd_media_primary_cat( $post = null ) {
	$cats = get_the_category( get_post( $post )->ID );
	return $cats ? $cats[0] : null;
}

function snd_media_cat_badge( $post = null ) {
	$cat = snd_media_primary_cat( $post );
	if ( $cat ) {
		echo '<a class="snd-cat" href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
	}
}

/**
 * Date relative (« il y a 2 heures ») pour les articles récents, date complète ensuite.
 */
function snd_media_date( $post = null ) {
	$post = get_post( $post );
	$time = get_post_time( 'U', true, $post );
	$diff = time() - $time;
	if ( $diff >= 0 && $diff < DAY_IN_SECONDS ) {
		/* translators: %s: durée */
		$label = sprintf( 'il y a %s', human_time_diff( $time, time() ) );
	} else {
		$label = get_the_date( 'j F Y', $post );
	}
	echo '<time class="snd-meta" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( $label ) . '</time>';
}

function snd_media_is_demo( $post = null ) {
	return (bool) get_post_meta( get_post( $post )->ID, '_snd_demo', true );
}

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
add_filter( 'excerpt_length', function () {
	return 26;
} );
add_filter( 'excerpt_more', function () {
	return '…';
} );

/**
 * Menu de secours : les rubriques, si aucun menu n'est attribué.
 */
function snd_media_menu_fallback() {
	echo '<ul class="snd-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Accueil</a></li>';
	foreach ( snd_media_sections() as $slug => $name ) {
		$term = get_category_by_slug( $slug );
		if ( $term ) {
			echo '<li><a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/**
 * Réglages simples dans Apparence > Personnaliser.
 */
add_action( 'customize_register', 'snd_media_customize' );
function snd_media_customize( $wp_customize ) {
	$wp_customize->add_section( 'snd_media', array( 'title' => 'SND Média — coordonnées et réseaux', 'priority' => 30 ) );
	$fields = array(
		'snd_tagline'  => array( 'Slogan sous le nom', 'L\'info en continu' ),
		'snd_about'    => array( 'Texte de présentation (pied de page)', '' ),
		'snd_address'  => array( 'Adresse', '' ),
		'snd_phone'    => array( 'Téléphone', '' ),
		'snd_email'    => array( 'E-mail', '' ),
		'snd_facebook' => array( 'Lien Facebook', '' ),
		'snd_x'        => array( 'Lien X (Twitter)', '' ),
		'snd_youtube'  => array( 'Lien YouTube', '' ),
		'snd_whatsapp' => array( 'Lien WhatsApp', '' ),
		'snd_tiktok'   => array( 'Lien TikTok', '' ),
	);
	foreach ( $fields as $id => $f ) {
		$is_url = in_array( $id, array( 'snd_facebook', 'snd_x', 'snd_youtube', 'snd_whatsapp', 'snd_tiktok' ), true );
		$wp_customize->add_setting( $id, array( 'default' => $f[1], 'sanitize_callback' => $is_url ? 'esc_url_raw' : 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'snd_media', 'type' => $is_url ? 'url' : 'text' ) );
	}
}

function snd_media_socials() {
	$out = array();
	foreach ( array( 'snd_facebook' => 'Facebook', 'snd_x' => 'X', 'snd_youtube' => 'YouTube', 'snd_whatsapp' => 'WhatsApp', 'snd_tiktok' => 'TikTok' ) as $key => $label ) {
		$url = get_theme_mod( $key );
		if ( $url ) {
			$out[ $label ] = $url;
		}
	}
	return $out;
}

/**
 * Liste d'articles compacte (vignette + titre + date).
 */
function snd_media_list( $query ) {
	echo '<ul class="snd-list">';
	while ( $query->have_posts() ) {
		$query->the_post();
		echo '<li class="snd-item">';
		snd_media_thumb( 'snd-small' );
		echo '<div><h4><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h4>';
		snd_media_date();
		echo '</div></li>';
	}
	echo '</ul>';
	wp_reset_postdata();
}

/**
 * Carte d'article pour les archives.
 */
function snd_media_card() {
	echo '<article class="snd-card">';
	snd_media_thumb( 'snd-card' );
	echo '<div class="snd-card-body">';
	snd_media_cat_badge();
	echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
	echo '<p>' . esc_html( get_the_excerpt() ) . '</p>';
	snd_media_date();
	echo '</div></article>';
}
