<?php
/**
 * Comunidad Digital y Solidaria — tema de bloques de la Parroquia San Francisco Javier.
 *
 * Todo el contenido se edita desde WordPress:
 *   - Páginas ............ Páginas (editor de bloques)
 *   - Datos repetidos .... Ajustes → Datos de la parroquia
 *   - Bot ................ Preguntas del bot
 *   - Colores ............ Apariencia → Editor → Estilos (incluye la variante RCC)
 *   - Primer arranque .... Herramientas → Contenido inicial
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

define( 'CDS_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/seguridad.php' );
require_once get_theme_file_path( 'inc/ajustes.php' );
require_once get_theme_file_path( 'inc/shortcodes.php' );
require_once get_theme_file_path( 'inc/bot.php' );
require_once get_theme_file_path( 'inc/contenido-inicial.php' );

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'cds', get_stylesheet_uri(), array(), CDS_VERSION );
	}
);

/* Estilos de bloque: aparecen en el panel lateral del editor ("Estilos"). */
add_action(
	'init',
	static function () {
		foreach ( array( 'core/group', 'core/column' ) as $bloque ) {
			register_block_style( $bloque, array( 'name' => 'tarjeta', 'label' => 'Tarjeta' ) );
		}
		foreach ( array( 'core/paragraph', 'core/heading', 'core/group', 'core/list', 'core/columns', 'core/quote' ) as $bloque ) {
			register_block_style( $bloque, array( 'name' => 'por-confirmar', 'label' => 'Por confirmar' ) );
		}
		register_block_style( 'core/heading', array( 'name' => 'con-filete', 'label' => 'Con filete' ) );
		register_block_style( 'core/gallery', array( 'name' => 'carrusel', 'label' => 'Carrusel' ) );
		register_block_style( 'core/gallery', array( 'name' => 'viernes', 'label' => 'Viernes (miniaturas)' ) );

		register_block_pattern_category( 'cds-paginas', array( 'label' => 'Páginas de la parroquia' ) );
	}
);

/* El script del carrusel solo se carga en las páginas que tienen uno. */
add_filter(
	'render_block_core/gallery',
	static function ( $html, $bloque ) {
		if ( false !== strpos( $bloque['attrs']['className'] ?? '', 'is-style-carrusel' ) ) {
			wp_enqueue_script( 'cds-carrusel', get_theme_file_uri( 'assets/js/carrusel.js' ), array(), CDS_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		}
		return $html;
	},
	10,
	2
);
