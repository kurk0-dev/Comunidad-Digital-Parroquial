<?php
/**
 * Seguridad: validación de direcciones y cabeceras HTTP.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Devuelve la URL solo si es http(s); si no, cadena vacía.
 *
 * Mismo criterio que urlSegura() de la versión estática: una dirección
 * "javascript:" escrita por error en los ajustes nunca llega a un enlace.
 *
 * @param string $url Dirección a validar.
 * @return string
 */
function cds_url_segura( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	$limpia = esc_url_raw( $url, array( 'https', 'http' ) );
	return ( $limpia && wp_http_validate_url( $limpia ) ) ? $limpia : '';
}

/**
 * Solo dígitos: evita que un valor mal escrito altere el destino de wa.me.
 *
 * @param string $numero Número tal como lo escribió la secretaría.
 * @return string
 */
function cds_solo_digitos( $numero ) {
	return preg_replace( '/\D+/', '', (string) $numero );
}

/**
 * Cabeceras de seguridad para las páginas públicas.
 *
 * No se envía una CSP estricta porque el propio WordPress usa scripts en línea
 * en el editor y en algunos bloques; sí se protege contra el "clickjacking"
 * y la detección errónea de tipos de archivo.
 */
add_action(
	'send_headers',
	static function () {
		if ( is_admin() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
	}
);

/* No anunciar la versión exacta de WordPress en el HTML público. */
remove_action( 'wp_head', 'wp_generator' );
