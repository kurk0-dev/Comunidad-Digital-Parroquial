<?php
/**
 * Bot de preguntas frecuentes.
 *
 * Cada pregunta es una entrada del menú "Preguntas del bot":
 *   - Título             → el botón de acceso rápido ("¿A qué hora es la misa?")
 *   - Contenido          → la respuesta
 *   - Palabras clave     → formas de preguntarlo ("horario", "a que hora", "misa")
 *   - Orden (Atributos)  → posición del botón
 *
 * El bot responde en el navegador, sin servidores externos ni IA.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		register_post_type(
			'cds_pregunta',
			array(
				'labels'          => array(
					'name'          => 'Preguntas del bot',
					'singular_name' => 'Pregunta del bot',
					'add_new'       => 'Agregar pregunta',
					'add_new_item'  => 'Agregar pregunta al bot',
					'edit_item'     => 'Editar pregunta del bot',
					'all_items'     => 'Todas las preguntas',
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-format-chat',
				'menu_position'   => 21,
				'supports'        => array( 'title', 'editor', 'page-attributes' ),
				'capability_type' => 'post',
			)
		);
		register_taxonomy(
			'cds_clave',
			'cds_pregunta',
			array(
				'labels'       => array(
					'name'          => 'Palabras clave',
					'singular_name' => 'Palabra clave',
					'add_new_item'  => 'Agregar palabra clave',
					'separate_items_with_commas' => 'Escribe las formas de preguntar, separadas por comas (sin tildes ni signos).',
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'hierarchical' => false,
			)
		);
	}
);

/** Las preguntas publicadas, listas para el navegador. */
function cds_preguntas_bot() {
	$preguntas = get_posts(
		array(
			'post_type'      => 'cds_pregunta',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$salida = array();
	foreach ( $preguntas as $p ) {
		$claves   = wp_get_post_terms( $p->ID, 'cds_clave', array( 'fields' => 'names' ) );
		$salida[] = array(
			'sugerida'  => wp_strip_all_tags( get_the_title( $p ) ),
			'respuesta' => trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $p->post_content ) ) ),
			'claves'    => is_wp_error( $claves ) ? array() : array_values( $claves ),
		);
	}
	return $salida;
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( is_admin() ) {
			return;
		}
		$numero = cds_solo_digitos( cds_ajuste( 'whatsapp' ) );
		$datos  = array(
			'preguntas' => cds_preguntas_bot(),
			'fallback'  => cds_ajuste( 'bot_fallback' ),
			'whatsapp'  => $numero ? 'https://wa.me/' . $numero . '?text=' . rawurlencode( 'Hola, tengo una consulta sobre el grupo de oración.' ) : '',
			'imagen'    => get_theme_file_uri( 'assets/img/paloma-fuego.webp' ),
			'saludo'    => '¡Hola! Soy el asistente del grupo. Te ayudo con horarios, sacramentos, donaciones y pedidos de ayuda. Escribe tu pregunta o toca una opción.',
		);
		wp_enqueue_script( 'cds-bot', get_theme_file_uri( 'assets/js/bot.js' ), array(), CDS_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		// JSON_HEX_TAG impide que un "</script>" escrito en una respuesta cierre el script.
		wp_add_inline_script( 'cds-bot', 'window.CDS_BOT = ' . wp_json_encode( $datos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . ';', 'before' );
	}
);
