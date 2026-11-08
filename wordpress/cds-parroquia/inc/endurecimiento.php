<?php
/**
 * Endurecimiento de WordPress desde el tema.
 *
 * El sitio irá en un hosting gratuito donde no siempre se puede tocar
 * wp-config.php ni el servidor, así que las protecciones que dependen de
 * WordPress viven aquí:
 *
 *   1. Cabeceras HTTP: CSP, HSTS (solo con https), anti-clickjacking, nosniff…
 *   2. Superficie de ataque: sin XML-RPC, sin pingbacks, sin emojis externos.
 *   3. Enumeración de usuarios: ?author=N y /wp-json/wp/v2/users cerrados al público.
 *   4. Inicio de sesión: un único mensaje de error (no confirma si el usuario existe).
 *   5. Editor de archivos del panel desactivado (aunque roben una sesión de
 *      administrador, no se puede escribir PHP desde el navegador).
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Política de seguridad de contenido de las páginas públicas.
 *
 * script-src lleva 'unsafe-inline' porque WordPress imprime scripts en línea
 * (mapa de módulos de los bloques, datos del bot); el resto sí queda cerrado:
 * nada se carga desde otros dominios salvo el mapa de Google (iframe) y los
 * avatares de Gravatar en la barra de administración.
 *
 * @return string
 */
function cds_politica_csp() {
	$directivas = array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline'",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: https://secure.gravatar.com",
		"font-src 'self' data:",
		"connect-src 'self'",
		'frame-src https://www.google.com https://maps.google.com',
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self'",
		"frame-ancestors 'self'",
	);
	if ( is_ssl() ) {
		$directivas[] = 'upgrade-insecure-requests';
	}
	return implode( '; ', $directivas );
}

add_action(
	'send_headers',
	static function () {
		if ( is_admin() || headers_sent() ) {
			return;
		}
		header( 'Content-Security-Policy: ' . cds_politica_csp() );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()' );
		header( 'Cross-Origin-Opener-Policy: same-origin' );
		if ( is_ssl() ) {
			// Seis meses: el navegador recordará usar siempre https. Sin "preload" a propósito.
			header( 'Strict-Transport-Security: max-age=15552000' );
		}
		header_remove( 'X-Pingback' );
		header_remove( 'X-Powered-By' );
	}
);

/* --- 2. Superficie de ataque ----------------------------------------------------- */
add_action(
	'init',
	static function () {
		// xmlrpc.php es la puerta favorita de los ataques de fuerza bruta: se cierra entera.
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			status_header( 403 );
			exit;
		}
	},
	0
);
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter(
	'xmlrpc_methods',
	static function () {
		return array();
	}
);
add_filter(
	'wp_headers',
	static function ( $cabeceras ) {
		unset( $cabeceras['X-Pingback'] );
		return $cabeceras;
	}
);
remove_action( 'wp_head', 'wp_generator' );        // Versión exacta de WordPress.
remove_action( 'wp_head', 'rsd_link' );            // Descubrimiento de XML-RPC.
remove_action( 'wp_head', 'wlwmanifest_link' );    // Windows Live Writer.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Script en línea + imágenes de s.w.org.
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

/* --- 3. Enumeración de usuarios --------------------------------------------------- */
add_action(
	'template_redirect',
	static function () {
		// /?author=1 redirige a /author/nombre-de-usuario/ y delata el usuario.
		if ( ! is_user_logged_in() && ( isset( $_GET['author'] ) || is_author() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	},
	1 // Antes que redirect_canonical (prioridad 10), que redirige a /author/usuario/.
);
add_filter(
	'rest_endpoints',
	static function ( $rutas ) {
		if ( ! is_user_logged_in() ) {
			foreach ( array_keys( $rutas ) as $ruta ) {
				if ( 0 === strpos( $ruta, '/wp/v2/users' ) ) {
					unset( $rutas[ $ruta ] );
				}
			}
		}
		return $rutas;
	}
);
add_filter(
	'oembed_response_data',
	static function ( $datos ) {
		unset( $datos['author_name'], $datos['author_url'] );
		return $datos;
	}
);

/* --- 4. Inicio de sesión ------------------------------------------------------------ */
add_filter(
	'login_errors',
	static function () {
		return 'Usuario o contraseña incorrectos.';
	}
);

/* --- 5. Sin editor de archivos en el panel ------------------------------------------- */
add_filter(
	'map_meta_cap',
	static function ( $permisos, $permiso ) {
		if ( in_array( $permiso, array( 'edit_themes', 'edit_plugins', 'edit_files' ), true ) ) {
			return array( 'do_not_allow' );
		}
		return $permisos;
	},
	10,
	2
);
