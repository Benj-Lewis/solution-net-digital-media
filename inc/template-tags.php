<?php
/**
 * Petits éléments réutilisés dans les gabarits.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Identifiants des articles déjà affichés sur la page (pour éviter les doublons).
 */
function snd_shown( $add = null ) {
	static $ids = array();
	if ( null !== $add ) {
		$ids = array_merge( $ids, (array) $add );
	}
	return $ids;
}

function snd_brand( $context = 'header' ) {
	$name = get_bloginfo( 'name' );
	$logo = (int) get_theme_mod( 'snd_logo' );
	echo '<a class="snd-brand snd-brand--' . esc_attr( $context ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( $name . ' — accueil' ) . '">';
	if ( $logo ) {
		echo wp_get_attachment_image( $logo, 'medium', false, array( 'class' => 'snd-brand-img', 'alt' => '' ) );
	} else {
		echo '<img class="snd-brand-img" src="' . esc_url( get_template_directory_uri() . '/assets/logo-mark.png' ) . '" alt="" width="351" height="240">';
	}
	$words = preg_split( '/\s+/', trim( $name ) );
	$last  = count( $words ) > 1 ? array_pop( $words ) : '';
	echo '<span class="snd-brand-text"><span class="snd-brand-name">' . esc_html( implode( ' ', $words ) );
	if ( $last ) {
		echo ' <b>' . esc_html( $last ) . '</b>';
	}
	echo '</span>';
	$tag = snd_opt( 'snd_tagline' );
	if ( $tag && 'mini' !== $context ) {
		echo '<span class="snd-brand-tag">' . esc_html( $tag ) . '</span>';
	}
	echo '</span></a>';
}

function snd_primary_cat( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	$cats = get_the_category( $post->ID );
	if ( ! $cats ) {
		return null;
	}
	$sections = snd_media_sections();
	foreach ( $cats as $c ) {
		if ( isset( $sections[ $c->slug ] ) ) {
			return $c;
		}
	}
	return $cats[0];
}

function snd_cat_class( $post = null ) {
	$cat = snd_primary_cat( $post );
	return $cat ? 'snd-c-' . sanitize_html_class( $cat->slug ) : '';
}

function snd_badge( $post = null, $link = true ) {
	$cat = snd_primary_cat( $post );
	if ( ! $cat ) {
		return;
	}
	$cls = 'snd-badge snd-c-' . sanitize_html_class( $cat->slug );
	if ( $link ) {
		echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
	} else {
		echo '<span class="' . esc_attr( $cls ) . '">' . esc_html( $cat->name ) . '</span>';
	}
}

/**
 * Image d'un article ; visuel de secours aux couleurs de la rubrique.
 */
function snd_img( $size = 'snd-card', $post = null, $link = true, $eager = false ) {
	$post  = get_post( $post );
	$attrs = array( 'alt' => '', 'loading' => $eager ? 'eager' : 'lazy', 'decoding' => 'async' );
	if ( $eager ) {
		$attrs['fetchpriority'] = 'high';
	}
	$inner = has_post_thumbnail( $post )
		? get_the_post_thumbnail( $post, $size, $attrs )
		: '<span class="snd-noimg"><img src="' . esc_url( get_template_directory_uri() . '/assets/logo-mark.png' ) . '" alt="" width="351" height="240" loading="lazy"></span>';
	$cls   = 'snd-media ' . snd_cat_class( $post );
	if ( $link ) {
		echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( get_permalink( $post ) ) . '" tabindex="-1" aria-hidden="true">' . $inner . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo '<div class="' . esc_attr( $cls ) . '">' . $inner . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

function snd_title( $tag = 'h3', $post = null ) {
	$post = get_post( $post );
	echo '<' . tag_escape( $tag ) . ' class="snd-title"><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></' . tag_escape( $tag ) . '>';
}

/**
 * Date relative pour les 24 dernières heures, date courte ensuite.
 */
function snd_date_label( $post = null ) {
	$post = get_post( $post );
	$time = (int) get_post_time( 'U', true, $post );
	$diff = time() - $time;
	if ( $diff >= 0 && $diff < HOUR_IN_SECONDS ) {
		$m = max( 1, (int) round( $diff / 60 ) );
		return sprintf( 'Il y a %d min', $m );
	}
	if ( $diff >= 0 && $diff < DAY_IN_SECONDS ) {
		$h = (int) floor( $diff / HOUR_IN_SECONDS );
		return sprintf( 'Il y a %d h', $h );
	}
	return get_the_date( 'j M Y', $post );
}

function snd_meta( $post = null, $author = false ) {
	$post = get_post( $post );
	echo '<div class="snd-meta">';
	if ( $author ) {
		echo '<span class="snd-meta-author">' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</span>';
	}
	echo '<time datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . snd_icon( 'clock', 13 ) . esc_html( snd_date_label( $post ) ) . '</time>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

function snd_reading_time( $post = null ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_post( $post ) ) ) );
	return max( 1, (int) ceil( $words / 220 ) );
}

function snd_is_demo( $post = null ) {
	$post = get_post( $post );
	return $post && (bool) get_post_meta( $post->ID, '_snd_demo', true );
}

/**
 * Boutons de partage.
 */
function snd_share( $variant = '' ) {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	$links = array(
		array( 'facebook', 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
		array( 'whatsapp', 'WhatsApp', 'https://wa.me/?text=' . $title . '%20' . $url ),
		array( 'x', 'X', 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title ),
		array( 'telegram', 'Telegram', 'https://t.me/share/url?url=' . $url . '&text=' . $title ),
		array( 'linkedin', 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url ),
	);
	echo '<div class="snd-share ' . esc_attr( $variant ) . '"><span class="snd-share-label">Partager</span>';
	foreach ( $links as $l ) {
		echo '<a class="snd-share-' . esc_attr( $l[0] ) . '" href="' . esc_url( $l[2] ) . '" target="_blank" rel="noopener" aria-label="Partager sur ' . esc_attr( $l[1] ) . '">' . snd_icon( $l[0], 17 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '<button type="button" class="snd-share-copy" data-snd-copy="' . esc_attr( get_permalink() ) . '" aria-label="Copier le lien">' . snd_icon( 'link', 17 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

function snd_social_links( $class = 'snd-social' ) {
	$socials = snd_media_socials();
	if ( ! $socials ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $socials as $s ) {
		echo '<li><a class="snd-s-' . esc_attr( $s['icon'] ) . '" href="' . esc_url( $s['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $s['label'] ) . '">' . snd_icon( $s['icon'], 16 ) . '<span>' . esc_html( $s['label'] ) . '</span></a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</ul>';
}

/**
 * Menu de secours : les rubriques.
 */
function snd_menu_fallback( $args = array() ) {
	$class = ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'snd-menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( snd_media_sections() as $slug => $s ) {
		$term = get_category_by_slug( $slug );
		if ( $term ) {
			$cur = is_category( $term->term_id ) ? ' current-menu-item' : '';
			echo '<li class="snd-c-' . esc_attr( $slug ) . $cur . '"><a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/**
 * Ajoute la couleur de rubrique aux entrées du menu qui pointent vers une rubrique.
 */
add_filter( 'nav_menu_css_class', 'snd_menu_item_class', 10, 2 );
function snd_menu_item_class( $classes, $item ) {
	if ( 'taxonomy' === $item->type && 'category' === $item->object ) {
		$term = get_term( (int) $item->object_id, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$classes[] = 'snd-c-' . sanitize_html_class( $term->slug );
		}
	}
	return $classes;
}

/**
 * Liste compacte : vignette + titre + date.
 */
function snd_list_item( $thumb = true ) {
	echo '<article class="snd-li ' . esc_attr( snd_cat_class() ) . '">';
	if ( $thumb ) {
		snd_img( 'snd-thumb' );
	}
	echo '<div class="snd-li-body">';
	snd_title( 'h4' );
	snd_meta();
	echo '</div></article>';
}

/**
 * Carte : image + étiquette + titre + extrait + date.
 */
function snd_card( $excerpt = true, $size = 'snd-card', $htag = 'h3' ) {
	echo '<article class="snd-card ' . esc_attr( snd_cat_class() ) . '">';
	snd_img( $size );
	echo '<div class="snd-card-body">';
	snd_badge();
	snd_title( $htag );
	if ( $excerpt ) {
		echo '<p class="snd-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';
	}
	snd_meta();
	echo '</div></article>';
}

/**
 * Tuile à texte sur image.
 */
function snd_overlay( $size = 'snd-card', $htag = 'h3', $eager = false, $extra = '' ) {
	echo '<article class="snd-ov ' . esc_attr( snd_cat_class() . ' ' . $extra ) . '">';
	snd_img( $size, null, true, $eager );
	echo '<div class="snd-ov-body">';
	snd_badge();
	snd_title( $htag );
	snd_meta();
	echo '</div></article>';
}

function snd_section_head( $title, $url = '', $slug = '', $tag = 'h2' ) {
	echo '<header class="snd-sh ' . ( $slug ? 'snd-c-' . esc_attr( $slug ) : '' ) . '">';
	echo '<' . tag_escape( $tag ) . ' class="snd-sh-title">';
	echo $url ? '<a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>' : esc_html( $title );
	echo '</' . tag_escape( $tag ) . '>';
	if ( $url ) {
		echo '<a class="snd-sh-more" href="' . esc_url( $url ) . '">Voir tout ' . snd_icon( 'arrow', 14 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</header>';
}
