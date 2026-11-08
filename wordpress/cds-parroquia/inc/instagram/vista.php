<?php
/**
 * Instagram — lo que ve el visitante: [cds_instagram].
 *
 * Solo muestra fotos servidas por el propio sitio y revalida cada publicación
 * antes de pintarla (nombre de archivo numérico, enlace a instagram.com).
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/* --- Lo que ve el visitante --------------------------------------------------- */

/**
 * Publicaciones válidas para mostrar (se revalidan al pintar).
 *
 * @param int $limite Cantidad máxima.
 * @return array<int,array<string,string>>
 */
function cds_ig_publicaciones( $limite ) {
	$salida = array();
	foreach ( (array) cds_ig_datos()['publicaciones'] as $p ) {
		if ( ! is_array( $p ) || ! preg_match( '/^\d+\.jpg$/', (string) ( $p['archivo'] ?? '' ) ) || 0 !== strpos( (string) ( $p['enlace'] ?? '' ), 'https://www.instagram.com/' ) ) {
			continue;
		}
		$salida[] = $p;
		if ( count( $salida ) >= $limite ) {
			break;
		}
	}
	return $salida;
}

/** Usuario de Instagram (sin @) sacado de la dirección del perfil en los ajustes. */
function cds_ig_usuario_de_url( $url ) {
	$ruta = trim( (string) wp_parse_url( (string) $url, PHP_URL_PATH ), '/' );
	$uno  = explode( '/', $ruta )[0];
	return preg_match( '/^[A-Za-z0-9._]{1,30}$/', $uno ) ? $uno : '';
}

add_shortcode(
	'cds_instagram',
	static function ( $atts = array() ) {
		$a     = shortcode_atts( array( 'limite' => 6, 'titulo' => 'Lo último que publicamos' ), $atts );
		$lista = cds_ig_publicaciones( max( 1, min( CDS_IG_CANTIDAD, (int) $a['limite'] ) ) );
		if ( ! $lista ) {
			return ''; // Sin publicaciones la sección no aparece: nunca un recuadro vacío.
		}
		$carpeta = cds_ig_carpeta();
		$perfil  = cds_url_segura( cds_ajuste( 'instagram' ) );
		$usuario = cds_ig_usuario_de_url( $perfil );

		$html  = '<div class="cds-ig">';
		$html .= '<div class="cds-ig__cabecera"><h2 class="wp-block-heading is-style-con-filete">' . esc_html( $a['titulo'] ) . '</h2>';
		if ( $perfil && $usuario ) {
			$html .= '<a class="cds-ig__perfil" href="' . esc_url( $perfil ) . '" target="_blank" rel="noopener">@' . esc_html( $usuario ) . cds_icono( 'flecha', 16 ) . '<span class="cds-sr"> (abre Instagram)</span></a>';
		}
		$html .= '</div><ul class="cds-ig__lista">';
		foreach ( $lista as $p ) {
			$texto = (string) $p['texto'];
			$alt   = $texto ? 'Publicación de Instagram: ' . $texto : 'Publicación de Instagram del grupo';
			$html .= '<li><a class="cds-ig__item" href="' . esc_url( $p['enlace'] ) . '" target="_blank" rel="noopener">'
				. '<span class="cds-ig__foto"><img src="' . esc_url( $carpeta['url'] . '/' . $p['archivo'] ) . '" alt="' . esc_attr( $alt ) . '" width="800" height="800" loading="lazy" decoding="async">';
			if ( 'VIDEO' === $p['tipo'] || 'CAROUSEL_ALBUM' === $p['tipo'] ) {
				$html .= '<span class="cds-ig__tipo" aria-hidden="true">' . cds_icono( 'VIDEO' === $p['tipo'] ? 'video' : 'varias', 16 ) . '</span>';
			}
			$html .= '</span>';
			if ( $texto ) {
				$html .= '<span class="cds-ig__texto">' . esc_html( $texto ) . '</span>';
			}
			$html .= '</a></li>';
		}
		return $html . '</ul></div>';
	}
);
