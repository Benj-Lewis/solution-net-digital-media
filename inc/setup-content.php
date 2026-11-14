<?php
/**
 * Mise en place automatique (une fois par version) : rubriques, menus, réglages,
 * articles et photos de démonstration. Tout le contenu de démonstration est marqué
 * (_snd_demo) et se supprime en un clic dans Apparence > Articles de démonstration.
 * Un journal est écrit dans wp-content/uploads/snd-media-setup.log.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function snd_log( $msg ) {
	$up = wp_upload_dir( null, false );
	if ( empty( $up['basedir'] ) || ! wp_mkdir_p( $up['basedir'] ) ) {
		return;
	}
	@file_put_contents( trailingslashit( $up['basedir'] ) . 'snd-media-setup.log', gmdate( 'Y-m-d H:i:s' ) . ' ' . $msg . "\n", FILE_APPEND ); // phpcs:ignore
}

add_action( 'init', 'snd_media_maybe_setup', 20 );
function snd_media_maybe_setup() {
	$done_setup  = get_option( 'snd_media_setup_done' ) === SND_MEDIA_VERSION;
	$done_images = (bool) get_option( 'snd_media_images_done' );
	if ( $done_setup && $done_images ) {
		return;
	}
	if ( get_transient( 'snd_media_setup_lock' ) ) {
		return;
	}
	set_transient( 'snd_media_setup_lock', 1, 3 * MINUTE_IN_SECONDS );

	if ( ! $done_setup ) {
		snd_media_run_setup();
	}
	snd_media_import_images( defined( 'WP_CLI' ) && WP_CLI ? 100 : 6 );

	delete_transient( 'snd_media_setup_lock' );
}

function snd_media_run_setup() {
	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	snd_log( 'Mise en place v' . SND_MEDIA_VERSION );

	// 1. Rubriques.
	$term_ids = array();
	foreach ( snd_media_sections() as $slug => $s ) {
		$term = get_category_by_slug( $slug );
		if ( $term ) {
			$term_ids[ $slug ] = (int) $term->term_id;
			continue;
		}
		$res = wp_insert_term( $s[0], 'category', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $res ) ) {
			$term_ids[ $slug ] = (int) $res['term_id'];
		}
	}

	// 2. Menus.
	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) || ! wp_get_nav_menu_object( $locations['primary'] ) ) {
		$menu    = wp_get_nav_menu_object( 'Menu principal SND' );
		$menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu( 'Menu principal SND' );
		if ( ! is_wp_error( $menu_id ) ) {
			if ( ! $menu ) {
				foreach ( $term_ids as $term_id ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-object-id' => $term_id,
						'menu-item-object'    => 'category',
						'menu-item-type'      => 'taxonomy',
						'menu-item-status'    => 'publish',
					) );
				}
			}
			$locations['primary'] = $menu_id;
		}
	} else {
		// Le v1 avait un lien « Accueil » en tête : l'icône maison le remplace.
		foreach ( (array) wp_get_nav_menu_items( $locations['primary'] ) as $item ) {
			if ( 'custom' === $item->type && untrailingslashit( $item->url ) === untrailingslashit( home_url( '/' ) ) ) {
				wp_delete_post( $item->ID, true );
			}
		}
	}
	if ( empty( $locations['footer'] ) ) {
		$menu    = wp_get_nav_menu_object( 'Liens utiles SND' );
		$menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu( 'Liens utiles SND' );
		if ( ! is_wp_error( $menu_id ) ) {
			if ( ! $menu ) {
				$pages = get_pages( array( 'post_status' => 'publish' ) );
				foreach ( $pages as $p ) {
					if ( preg_match( '/propos|contact|confidentialit|privacy|mentions|charte|publicit/i', $p->post_name . ' ' . $p->post_title ) ) {
						wp_update_nav_menu_item( $menu_id, 0, array(
							'menu-item-object-id' => $p->ID,
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						) );
					}
				}
			}
			$locations['footer'] = $menu_id;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	// Widgets hérités de l'ancien thème : mis de côté (Apparence > Widgets > inactifs), pas supprimés.
	$sw = wp_get_sidebars_widgets();
	foreach ( array( 'snd-sidebar', 'snd-ad-header' ) as $sb ) {
		if ( ! empty( $sw[ $sb ] ) ) {
			$sw['wp_inactive_widgets'] = array_merge( isset( $sw['wp_inactive_widgets'] ) ? (array) $sw['wp_inactive_widgets'] : array(), (array) $sw[ $sb ] );
			$sw[ $sb ] = array();
		}
	}
	wp_set_sidebars_widgets( $sw );

	// 3. Accueil = page d'actualités du thème (les anciens réglages restent enregistrés).
	if ( false === get_option( 'snd_media_previous_reading' ) ) {
		add_option( 'snd_media_previous_reading', array(
			'show_on_front'  => get_option( 'show_on_front' ),
			'page_on_front'  => get_option( 'page_on_front' ),
			'page_for_posts' => get_option( 'page_for_posts' ),
		), '', false );
	}
	update_option( 'show_on_front', 'posts' );
	update_option( 'posts_per_page', 12 );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}

	// 4. Article d'exemple de WordPress.
	foreach ( array( 'hello-world', 'bonjour-tout-le-monde' ) as $slug ) {
		$p = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}

	// 5. Articles de démonstration : on remplace ceux de la v1.
	$old = get_posts( array( 'post_type' => 'post', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_snd_demo', 'meta_value' => 1 ) );
	$keep_v2 = 0;
	foreach ( $old as $id ) {
		if ( '2' === (string) get_post_meta( $id, '_snd_demo_v', true ) ) {
			$keep_v2++;
			continue;
		}
		wp_delete_post( $id, true );
	}
	if ( ! $keep_v2 && ! get_option( 'snd_media_demo_removed' ) ) {
		$n = snd_media_create_demo_posts( $term_ids );
		snd_log( "$n articles de démonstration créés" );
	}

	update_option( 'snd_media_setup_done', SND_MEDIA_VERSION, false );
	do_action( 'litespeed_purge_all' );
}

function snd_media_create_demo_posts( $term_ids ) {
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$author = $admins ? (int) $admins[0] : 0;
	$titles = snd_demo_titles();
	$names  = snd_media_sections();
	$count  = 0;
	$order  = 0;
	// Les articles alternent entre rubriques pour que la une soit variée.
	for ( $k = 0; $k < 5; $k++ ) {
		foreach ( $titles as $slug => $list ) {
			if ( empty( $term_ids[ $slug ] ) || empty( $list[ $k ] ) ) {
				continue;
			}
			$stamp   = time() - ( $order * 53 * MINUTE_IN_SECONDS ) - 5 * MINUTE_IN_SECONDS;
			$post_id = wp_insert_post( array(
				'post_title'    => $list[ $k ],
				'post_content'  => snd_demo_body( $names[ $slug ][0] ),
				'post_excerpt'  => 'Article de démonstration : ce chapô de deux lignes montre comment s\'affiche le résumé d\'un article dans la rubrique ' . $names[ $slug ][0] . '.',
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_author'   => $author,
				'post_category' => array( $term_ids[ $slug ] ),
				'post_date'     => wp_date( 'Y-m-d H:i:s', $stamp ),
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $stamp ),
				'meta_input'    => array(
					'_snd_demo'   => 1,
					'_snd_demo_v' => 2,
					'_snd_views'  => wp_rand( 40, 900 ),
				),
			) );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$count++;
			}
			$order++;
		}
	}
	return $count;
}

/**
 * Télécharge les photos de démonstration par petits lots, puis les associe aux articles.
 */
function snd_media_import_images( $batch ) {
	if ( get_option( 'snd_media_images_done' ) ) {
		return;
	}
	$images = snd_demo_images();
	$done   = (array) get_option( 'snd_media_images_imported', array() );
	if ( ! $images ) {
		update_option( 'snd_media_images_done', 1, false );
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore
	}

	$failed = (array) get_option( 'snd_media_images_failed', array() );
	$n      = 0;
	foreach ( $images as $i => $img ) {
		$key = 'img' . $i;
		if ( isset( $done[ $key ] ) || ( isset( $failed[ $key ] ) && $failed[ $key ] >= 3 ) ) {
			continue;
		}
		if ( $n >= $batch ) {
			break;
		}
		$n++;
		$tmp = download_url( $img['url'], 60 );
		if ( is_wp_error( $tmp ) ) {
			$failed[ $key ] = ( isset( $failed[ $key ] ) ? $failed[ $key ] : 0 ) + 1;
			snd_log( "Photo $key : échec du téléchargement (" . $tmp->get_error_message() . ')' );
			continue;
		}
		$file = array(
			'name'     => 'snd-demo-' . $img['cat'] . '-' . $i . '.webp',
			'tmp_name' => $tmp,
		);
		$att = media_handle_sideload( $file, 0, 'Photo de démonstration — ' . $img['cat'] );
		if ( is_wp_error( $att ) ) {
			@unlink( $tmp ); // phpcs:ignore
			$failed[ $key ] = ( isset( $failed[ $key ] ) ? $failed[ $key ] : 0 ) + 1;
			snd_log( "Photo $key : échec de l'import (" . $att->get_error_message() . ')' );
			continue;
		}
		update_post_meta( $att, '_snd_demo', 1 );
		update_post_meta( $att, '_wp_attachment_image_alt', '' );
		$done[ $key ] = array( 'id' => (int) $att, 'cat' => $img['cat'] );
		snd_log( "Photo $key importée (#$att)" );
	}
	update_option( 'snd_media_images_imported', $done, false );
	update_option( 'snd_media_images_failed', $failed, false );

	$remaining = 0;
	foreach ( $images as $i => $img ) {
		$key = 'img' . $i;
		if ( ! isset( $done[ $key ] ) && ( ! isset( $failed[ $key ] ) || $failed[ $key ] < 3 ) ) {
			$remaining++;
		}
	}
	snd_media_assign_images( $done );
	if ( ! $remaining ) {
		update_option( 'snd_media_images_done', 1, false );
		snd_log( 'Photos terminées : ' . count( $done ) . ' importées' );
		do_action( 'litespeed_purge_all' );
	}
}

/**
 * Associe une photo de la bonne rubrique à chaque article de démonstration sans image.
 */
function snd_media_assign_images( $done ) {
	$by_cat = array();
	$all    = array();
	foreach ( $done as $d ) {
		$by_cat[ $d['cat'] ][] = $d['id'];
		$all[]                 = $d['id'];
	}
	if ( ! $all ) {
		return;
	}
	$posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1, 'meta_key' => '_snd_demo', 'meta_value' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
	$turn  = array();
	foreach ( $posts as $p ) {
		if ( has_post_thumbnail( $p ) ) {
			continue;
		}
		$cat  = snd_primary_cat( $p );
		$slug = $cat ? $cat->slug : '';
		$pool = ! empty( $by_cat[ $slug ] ) ? $by_cat[ $slug ] : null;
		if ( ! $pool ) {
			continue; // la photo de cette rubrique arrivera au lot suivant.
		}
		$t            = isset( $turn[ $slug ] ) ? $turn[ $slug ] : 0;
		$turn[ $slug ] = $t + 1;
		set_post_thumbnail( $p->ID, $pool[ $t % count( $pool ) ] );
	}
}

/**
 * Apparence > Articles de démonstration : suppression en un clic.
 */
add_action( 'admin_menu', 'snd_media_demo_menu' );
function snd_media_demo_menu() {
	add_theme_page( 'Articles de démonstration', 'Articles de démonstration', 'manage_options', 'snd-media-demo', 'snd_media_demo_page' );
}

function snd_media_demo_ids( $type = 'post' ) {
	return get_posts( array(
		'post_type'      => $type,
		'post_status'    => 'any',
		'meta_key'       => '_snd_demo',
		'meta_value'     => 1,
		'fields'         => 'ids',
		'posts_per_page' => -1,
	) );
}

function snd_media_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$deleted = null;
	if ( isset( $_POST['snd_delete_demo'] ) && check_admin_referer( 'snd_delete_demo' ) ) {
		$deleted = 0;
		foreach ( snd_media_demo_ids() as $id ) {
			if ( wp_delete_post( $id, true ) ) {
				$deleted++;
			}
		}
		foreach ( snd_media_demo_ids( 'attachment' ) as $id ) {
			wp_delete_attachment( $id, true );
		}
		update_option( 'snd_media_demo_removed', 1, false );
		update_option( 'snd_media_images_done', 1, false );
		do_action( 'litespeed_purge_all' );
	}
	$count  = count( snd_media_demo_ids() );
	$photos = count( snd_media_demo_ids( 'attachment' ) );
	echo '<div class="wrap"><h1>Articles de démonstration</h1>';
	if ( null !== $deleted ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $deleted ) . ' article(s) de démonstration et leurs photos ont été supprimés.</p></div>';
	}
	echo '<p>Le site contient <strong>' . esc_html( $count ) . '</strong> article(s) de démonstration et <strong>' . esc_html( $photos ) . '</strong> photo(s) de démonstration. Vos propres articles et images ne sont pas concernés.</p>';
	if ( $count || $photos ) {
		echo '<form method="post">';
		wp_nonce_field( 'snd_delete_demo' );
		echo '<p><button class="button button-primary" name="snd_delete_demo" value="1" onclick="return confirm(\'Supprimer définitivement les articles et photos de démonstration ?\');">Supprimer le contenu de démonstration</button></p>';
		echo '</form>';
	}
	echo '</div>';
}

/**
 * Signale les articles de démonstration dans la liste des articles.
 */
add_filter( 'display_post_states', function ( $states, $post ) {
	if ( snd_is_demo( $post ) ) {
		$states['snd_demo'] = 'Démo';
	}
	return $states;
}, 10, 2 );
