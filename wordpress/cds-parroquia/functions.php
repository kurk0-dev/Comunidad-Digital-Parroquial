<?php
/**
 * Comunidad Digital y Solidaria — tema de bloques del Grupo de Oración Divino Niño Jesús (RCC).
 *
 * Todo el contenido se edita desde WordPress:
 *   - Páginas ............ Páginas (editor de bloques)
 *   - Datos repetidos .... Ajustes → Datos del sitio
 *   - Bot ................ Preguntas del bot
 *   - Colores ............ Apariencia → Editor → Estilos
 *   - Instagram .......... Ajustes → Datos del sitio → Instagram en la web
 *   - Primer arranque .... Herramientas → Contenido inicial
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

define( 'CDS_VERSION', '1.7.0' );

/*
 * Módulos del tema, cada uno con una sola responsabilidad. El orden importa:
 * primero lo que otros usan (validación y ajustes), después lo que se apoya en ello.
 */
foreach ( array(
	'inc/seguridad.php',          // Validación pura: URLs, dígitos.
	'inc/endurecimiento.php',     // Cabeceras HTTP y protecciones de WordPress.
	'inc/ajustes.php',            // Ajustes → Datos del sitio.
	'inc/enlaces.php',            // Mapa, botones, formularios, QR, calendario.
	'inc/redes.php',              // Íconos y bloques de redes sociales.
	'inc/donaciones.php',         // Contador de la campaña.
	'inc/instagram/almacen.php',  // Estado de Instagram y token cifrado.
	'inc/instagram/api.php',      // Conexión con Meta y tarea programada.
	'inc/instagram/panel.php',    // Sección del panel.
	'inc/instagram/vista.php',    // [cds_instagram].
	'inc/bot.php',                // Preguntas del bot.
	'inc/contenido-inicial.php',  // Herramientas → Contenido inicial.
) as $cds_modulo ) {
	require_once get_theme_file_path( $cds_modulo );
}
unset( $cds_modulo );

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
		// La versión es la fecha del archivo: al cambiar el CSS, los celulares no se quedan con el viejo.
		wp_enqueue_style( 'cds', get_stylesheet_uri(), array(), (string) filemtime( get_theme_file_path( 'style.css' ) ) );
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

		register_block_pattern_category( 'cds-paginas', array( 'label' => 'Páginas del sitio' ) );
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
