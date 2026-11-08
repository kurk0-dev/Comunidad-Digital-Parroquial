<?php
/**
 * Redes sociales: íconos, [cds_redes], [cds_siguenos] y cuadros de la portada.
 *
 * Íconos de Tabler Icons (MIT) en línea: el sitio no carga librerías externas.
 * Cada red aparece solo si su dato existe en Ajustes → Datos del sitio.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

add_shortcode(
	'cds_redes',
	static function ( $atts = array() ) {
		// [cds_redes mapa="no"] quita "Cómo llegar" donde ya hay un botón que lleva al mapa.
		// [cds_redes estilo="cajas"] los muestra como cuadros con su ícono.
		$a = shortcode_atts(
			array(
				'mapa'   => 'si',
				'estilo' => 'enlaces',
			),
			$atts
		);
		if ( 'cajas' === $a['estilo'] ) {
			return cds_redes_cajas();
		}
		$enlaces = array(
			'Facebook'    => cds_url_segura( cds_ajuste( 'facebook' ) ),
			'Instagram'   => cds_url_segura( cds_ajuste( 'instagram' ) ),
			'Cómo llegar' => 'no' === $a['mapa'] ? '' : cds_url_maps(),
		);
		$correo = sanitize_email( cds_ajuste( 'correo' ) );
		if ( $correo && is_email( $correo ) ) {
			$enlaces['Correo'] = 'mailto:' . $correo;
		}
		$html = '';
		foreach ( $enlaces as $nombre => $url ) {
			if ( $url ) {
				$html .= '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $nombre ) . '</a></li>';
			}
		}
		return $html ? '<ul class="cds-redes">' . $html . '</ul>' : '';
	}
);

/* =============================================================================
   Íconos (Tabler Icons, licencia MIT, https://tabler.io/icons). Van en línea:
   el sitio no carga ninguna librería externa. Siempre decorativos.
   ============================================================================= */
function cds_icono( $nombre, $tam = 24 ) {
	$trazos = array(
		'instagram' => '<path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M16.5 7.5v.01"/>',
		'facebook'  => '<path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"/>',
		'whatsapp'  => '<path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"/><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"/>',
		'correo'    => '<path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 7l9 6l9 -6"/>',
		'flecha'    => '<path d="M17 7l-10 10"/><path d="M8 7l9 0l0 9"/>',
		'video'     => '<path d="M7 4v16l13 -8z"/>',
		'varias'    => '<path d="M8 10a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"/><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"/>',
	);
	if ( ! isset( $trazos[ $nombre ] ) ) {
		return '';
	}
	$tam = (int) $tam;
	return '<svg class="cds-icono" viewBox="0 0 24 24" width="' . $tam . '" height="' . $tam . '" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $trazos[ $nombre ] . '</svg>';
}

/* =============================================================================
   [cds_siguenos] Bloques táctiles de redes: Instagram (grande, con sus últimas
   fotos si ya está conectado), Facebook, WhatsApp y correo. Cada bloque sale
   solo si su dato existe en Ajustes → Datos del sitio.
   ============================================================================= */
add_shortcode(
	'cds_siguenos',
	static function () {
		$redes = array();

		$ig = cds_url_segura( cds_ajuste( 'instagram' ) );
		if ( $ig ) {
			$usuario = function_exists( 'cds_ig_usuario_de_url' ) ? cds_ig_usuario_de_url( $ig ) : '';
			$redes[] = array( 'instagram', $ig, 'Instagram', $usuario ? '@' . $usuario : 'Nuestro perfil', 'Fotos y videos de cada reunión y evento.', true );
		}
		$fb = cds_url_segura( cds_ajuste( 'facebook' ) );
		if ( $fb ) {
			$ruta    = trim( (string) wp_parse_url( $fb, PHP_URL_PATH ), '/' );
			$redes[] = array( 'facebook', $fb, 'Facebook', $ruta ? $ruta : 'Nuestra página', 'Avisos y fechas de los próximos encuentros.', true );
		}
		// Sin WhatsApp aquí: el grupo prefiere que le escriban al correo.
		$correo = sanitize_email( cds_ajuste( 'correo' ) );
		if ( $correo && is_email( $correo ) ) {
			$redes[] = array( 'correo', 'mailto:' . $correo, 'Correo', $correo, 'Para consultas con más calma.', false );
		}
		if ( ! $redes ) {
			return '';
		}

		// Miniaturas de las 3 últimas publicaciones dentro del bloque de Instagram.
		$fotos = '';
		if ( $ig && function_exists( 'cds_ig_publicaciones' ) ) {
			$carpeta = cds_ig_carpeta();
			foreach ( cds_ig_publicaciones( 3 ) as $p ) {
				$fotos .= '<img src="' . esc_url( $carpeta['url'] . '/' . $p['archivo'] ) . '" alt="" width="800" height="800" loading="lazy" decoding="async">';
			}
		}

		$html = '<ul class="cds-siguenos cds-siguenos--' . count( $redes ) . '">';
		foreach ( $redes as list( $clave, $url, $nombre, $detalle, $texto, $externa ) ) {
			$html .= '<li class="cds-red cds-red--' . esc_attr( $clave ) . '">'
				. '<a class="cds-red__enlace" href="' . esc_url( $url, array( 'https', 'mailto' ) ) . '"' . ( $externa ? ' target="_blank" rel="noopener"' : '' ) . '>'
				. '<span class="cds-red__icono">' . cds_icono( $clave, 26 ) . '</span>'
				. '<span class="cds-red__nombre">' . esc_html( $nombre ) . ( $externa ? '<span class="cds-sr"> (se abre en otra pestaña)</span>' : '' ) . '</span>'
				. '<span class="cds-red__detalle">' . esc_html( $detalle ) . '</span>'
				. '<span class="cds-red__texto">' . esc_html( $texto ) . '</span>'
				. ( 'instagram' === $clave && $fotos ? '<span class="cds-red__fotos" aria-hidden="true">' . $fotos . '</span>' : '' )
				. '<span class="cds-red__flecha">' . cds_icono( 'flecha', 22 ) . '</span>'
				. '</a></li>';
		}
		return $html . '</ul>';
	}
);

/**
 * Redes como cuadros con ícono: Facebook, Instagram y correo (sin WhatsApp:
 * el grupo prefiere el correo). Se usa en la portada, debajo de los botones.
 *
 * @return string
 */
function cds_redes_cajas() {
	$cajas  = array();
	$correo = sanitize_email( cds_ajuste( 'correo' ) );
	$lista  = array(
		array( 'facebook', 'Facebook', cds_url_segura( cds_ajuste( 'facebook' ) ) ),
		array( 'instagram', 'Instagram', cds_url_segura( cds_ajuste( 'instagram' ) ) ),
		array( 'correo', 'Correo', $correo && is_email( $correo ) ? 'mailto:' . $correo : '' ),
	);
	foreach ( $lista as list( $clave, $nombre, $url ) ) {
		if ( ! $url ) {
			continue;
		}
		$externo = 0 === strpos( $url, 'https://' );
		$cajas[] = '<li><a class="cds-caja-red cds-caja-red--' . esc_attr( $clave ) . '" href="' . esc_url( $url, array( 'https', 'mailto' ) ) . '"'
			. ( $externo ? ' target="_blank" rel="noopener"' : '' ) . '>'
			. '<span class="cds-caja-red__icono">' . cds_icono( $clave, 24 ) . '</span>'
			. '<span class="cds-caja-red__nombre">' . esc_html( $nombre ) . '</span>'
			. ( $externo ? '<span class="cds-sr"> (se abre en otra pestaña)</span>' : '' )
			. '</a></li>';
	}
	if ( ! $cajas ) {
		return '';
	}
	return '<div class="cds-cajas-redes"><p class="cds-cajas-redes__titulo">Síguenos y escríbenos</p><ul>' . implode( '', $cajas ) . '</ul></div>';
}
