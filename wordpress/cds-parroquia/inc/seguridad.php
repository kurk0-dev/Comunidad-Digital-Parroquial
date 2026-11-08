<?php
/**
 * Seguridad: funciones puras de validación que usa el resto del tema.
 *
 * Las protecciones que se enganchan a WordPress (cabeceras, XML-RPC, etc.)
 * están en inc/endurecimiento.php.
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
 * @param string $numero Número tal como lo escribió quien edita el sitio.
 * @return string
 */
function cds_solo_digitos( $numero ) {
	return preg_replace( '/\D+/', '', (string) $numero );
}
