<?php
/**
 * Instagram — dónde y cómo se guarda el estado (token cifrado, publicaciones).
 *
 * Única capa que lee o escribe la opción cds_instagram. El token se guarda
 * cifrado (AES-256-GCM con una clave derivada de las sales de wp-config) y
 * nunca se imprime.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

const CDS_IG_OPCION   = 'cds_instagram';
const CDS_IG_CARPETA  = 'cds-instagram';
const CDS_IG_CANTIDAD = 12;
const CDS_IG_API      = 'https://graph.instagram.com/';
const CDS_IG_API_FB   = 'https://graph.facebook.com/';

const CDS_IG_PREFIJO_CIFRADO = 'cds1:';

/*
 * Cifrado del token en la base de datos.
 *
 * Si alguien obtiene una copia de la base de datos (respaldo filtrado, otro
 * plugin con una inyección SQL), no se lleva el token: está cifrado con una
 * clave que vive en wp-config.php (las "sales"), fuera de la base de datos.
 * Si las sales cambian, el token ya no se puede leer y basta con pegarlo de nuevo.
 */

/** Clave de 32 bytes derivada de las sales de WordPress. */
function cds_ig_clave() {
	return hash( 'sha256', wp_salt( 'secure_auth' ) . '|cds-instagram', true );
}

/**
 * Cifra con AES-256-GCM (confidencialidad e integridad).
 *
 * @param string $texto Token en claro.
 * @return string "cds1:" + base64(iv|tag|cifrado), o el texto si el servidor no tiene OpenSSL.
 */
function cds_ig_cifrar( $texto ) {
	if ( '' === $texto || ! function_exists( 'openssl_encrypt' ) ) {
		return $texto;
	}
	$iv      = random_bytes( 12 );
	$tag     = '';
	$cifrado = openssl_encrypt( $texto, 'aes-256-gcm', cds_ig_clave(), OPENSSL_RAW_DATA, $iv, $tag );
	return false === $cifrado ? $texto : CDS_IG_PREFIJO_CIFRADO . base64_encode( $iv . $tag . $cifrado ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
}

/**
 * Descifra lo que guardó cds_ig_cifrar(). Un valor sin prefijo es de una
 * versión anterior (en claro) y se devuelve tal cual; se cifrará al guardar.
 *
 * @param string $valor Valor guardado.
 * @return string Token en claro, o '' si no se pudo descifrar.
 */
function cds_ig_descifrar( $valor ) {
	if ( 0 !== strpos( $valor, CDS_IG_PREFIJO_CIFRADO ) ) {
		return $valor;
	}
	$crudo = base64_decode( substr( $valor, strlen( CDS_IG_PREFIJO_CIFRADO ) ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	if ( false === $crudo || strlen( $crudo ) < 29 || ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}
	$claro = openssl_decrypt( substr( $crudo, 28 ), 'aes-256-gcm', cds_ig_clave(), OPENSSL_RAW_DATA, substr( $crudo, 0, 12 ), substr( $crudo, 12, 16 ) );
	return false === $claro ? '' : $claro;
}

/** Estado guardado, con valores por defecto y el token ya descifrado. */
function cds_ig_datos() {
	$guardado = get_option( CDS_IG_OPCION, array() );
	$d        = wp_parse_args(
		is_array( $guardado ) ? $guardado : array(),
		array(
			'token'         => '',
			'modo'          => 'instagram',
			'ig_id'         => '',
			'vence'         => 0,
			'renovado'      => 0,
			'usuario'       => '',
			'publicaciones' => array(),
			'actualizado'   => 0,
			'error'         => '',
		)
	);
	$en_claro   = '' !== $d['token'] && 0 !== strpos( $d['token'], CDS_IG_PREFIJO_CIFRADO );
	$d['token'] = cds_ig_descifrar( (string) $d['token'] );
	if ( $en_claro ) {
		cds_ig_guardar( $d ); // Migra un token de una versión anterior a cifrado.
	}
	return $d;
}

/** Guarda el estado cifrando el token, sin cargarlo en cada página (autoload desactivado). */
function cds_ig_guardar( $d ) {
	$d['token'] = cds_ig_cifrar( (string) ( $d['token'] ?? '' ) );
	update_option( CDS_IG_OPCION, $d, false );
}

/** Carpeta y dirección pública donde se guardan las copias de las fotos. */
function cds_ig_carpeta() {
	$subidas = wp_upload_dir( null, false );
	return array(
		'ruta' => trailingslashit( $subidas['basedir'] ) . CDS_IG_CARPETA,
		'url'  => trailingslashit( $subidas['baseurl'] ) . CDS_IG_CARPETA,
	);
}
