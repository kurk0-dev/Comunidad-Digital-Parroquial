<?php
/**
 * Botones y enlaces que salen de Ajustes → Datos del sitio.
 *
 * [cds_mapa boton="si|no"]                      Mapa clicable de la capilla
 * [cds_como_llegar texto="..."]                 Botón a Google Maps
 * [cds_whatsapp texto="..." mensaje="..."]      Botón de WhatsApp
 * [cds_correo texto="..."]                      Botón de correo
 * [cds_formulario tipo="contacto|ayuda|donacion"]
 * [cds_privacidad]                              Aviso de datos en letra pequeña
 * [cds_qr]                                      QR que abre la página actual
 * [cds_calendario titulo dia hora duracion]     Evento en Google Calendar
 *
 * Toda URL pasa por cds_url_segura() o se construye aquí con datos validados.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/** URL de Google Maps hacia la capilla, construida desde las coordenadas. */
function cds_url_maps() {
	$coords = cds_ajuste( 'coordenadas' );
	return $coords ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $coords ) : '';
}

/** Ícono de candado (decorativo) para el aviso de privacidad. */
function cds_icono_candado() {
	return '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><path d="M5 11h14v10H5z"/></svg>';
}

/** Botón apagado con un texto que explica por qué. */
function cds_boton_apagado( $texto ) {
	return '<span class="cds-boton cds-boton--apagado" role="link" aria-disabled="true">' . esc_html( $texto ) . '</span>';
}

add_shortcode(
	'cds_mapa',
	static function ( $atts = array() ) {
		// [cds_mapa boton="no"] quita la etiqueta "Abrir en Google Maps" encima del mapa
		// (para cuando al lado ya hay un botón con ese texto). El mapa sigue siendo un enlace.
		$a      = shortcode_atts( array( 'boton' => 'si' ), $atts );
		$coords = cds_ajuste( 'coordenadas' );
		if ( ! $coords ) {
			return '';
		}
		$etiqueta = cds_ajuste( 'etiqueta_mapa' );
		$embed    = 'https://www.google.com/maps?q=' . rawurlencode( $coords )
			. ( $etiqueta ? '(' . rawurlencode( $etiqueta ) . ')' : '' ) . '&z=17&hl=es&output=embed';

		// El mapa entero es un enlace: tocar cualquier punto (incluido el pin) abre Google Maps.
		return '<div class="cds-mapa">'
			. '<iframe src="' . esc_url( $embed ) . '" loading="lazy" tabindex="-1" aria-hidden="true" referrerpolicy="no-referrer-when-downgrade"></iframe>'
			. '<a class="cds-mapa__capa" href="' . esc_url( cds_url_maps() ) . '" target="_blank" rel="noopener">'
			. '<span class="cds-sr">Abrir la ubicación de la capilla en Google Maps</span>'
			. ( 'no' === $a['boton'] ? '' : '<span class="cds-mapa__boton" aria-hidden="true">Abrir en Google Maps</span>' )
			. '</a></div>';
	}
);

add_shortcode(
	'cds_como_llegar',
	static function ( $atts ) {
		$a   = shortcode_atts( array( 'texto' => 'Cómo llegar' ), $atts );
		$url = cds_url_maps();
		return $url ? '<a class="cds-boton" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $a['texto'] ) . '</a>' : '';
	}
);

add_shortcode(
	'cds_whatsapp',
	static function ( $atts ) {
		$a = shortcode_atts(
			array(
				'texto'    => 'Escribir por WhatsApp',
				'mensaje'  => 'Hola, escribo desde la página del Grupo de Oración Divino Niño Jesús.',
				'opcional' => 'no',
			),
			$atts
		);
		$numero = cds_solo_digitos( cds_ajuste( 'whatsapp' ) );
		if ( ! $numero ) {
			return 'si' === $a['opcional'] ? '' : cds_boton_apagado( 'WhatsApp por confirmar' );
		}
		$url = 'https://wa.me/' . $numero . '?text=' . rawurlencode( $a['mensaje'] );
		return '<a class="cds-boton cds-boton--whatsapp" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $a['texto'] ) . '</a>';
	}
);

add_shortcode(
	'cds_formulario',
	static function ( $atts ) {
		$a = shortcode_atts(
			array(
				'tipo'  => 'contacto',
				'texto' => 'Abrir formulario',
			),
			$atts
		);
		$claves = array(
			'contacto' => 'form_contacto',
			'ayuda'    => 'form_ayuda',
			'donacion' => 'form_donacion',
		);
		$url = isset( $claves[ $a['tipo'] ] ) ? cds_url_segura( cds_ajuste( $claves[ $a['tipo'] ] ) ) : '';
		if ( ! $url ) {
			return cds_boton_apagado( 'Formulario aún no configurado' );
		}
		return '<a class="cds-boton" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $a['texto'] ) . '</a>';
	}
);

add_shortcode(
	'cds_privacidad',
	static function () {
		$texto = cds_ajuste( 'privacidad' );
		return $texto ? '<p class="cds-privacidad">' . cds_icono_candado() . '<span>' . esc_html( $texto ) . '</span></p>' : '';
	}
);

add_shortcode(
	'cds_qr',
	static function () {
		wp_enqueue_script( 'cds-qrcode', get_theme_file_uri( 'assets/js/vendor/qrcode.min.js' ), array(), '1.0.0', true );
		wp_enqueue_script( 'cds-qr', get_theme_file_uri( 'assets/js/qr.js' ), array( 'cds-qrcode' ), CDS_VERSION, true );
		$url = get_permalink();
		return '<div class="cds-qr" data-qr="' . esc_url( $url ) . '" role="img" aria-label="' . esc_attr__( 'Código QR que abre esta página', 'cds' ) . '"></div>';
	}
);

add_shortcode(
	'cds_calendario',
	static function ( $atts ) {
		$a = shortcode_atts(
			array(
				'titulo'   => 'Misa dominical',
				'dia'      => 'domingo',
				'hora'     => '10:00',
				'duracion' => '60',
				'texto'    => 'Agregar a Google Calendar',
			),
			$atts
		);
		$dias = array( 'lunes' => 1, 'martes' => 2, 'miercoles' => 3, 'miércoles' => 3, 'jueves' => 4, 'viernes' => 5, 'sabado' => 6, 'sábado' => 6, 'domingo' => 7 );
		$dia  = $dias[ mb_strtolower( (string) $a['dia'] ) ] ?? 7; // mb_: "Sábado" con tilde.
		if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', $a['hora'], $m ) ) {
			return '';
		}
		$zona   = wp_timezone();
		$inicio = new DateTimeImmutable( 'now', $zona );
		$faltan = ( $dia - (int) $inicio->format( 'N' ) + 7 ) % 7;
		$inicio = $inicio->modify( '+' . ( $faltan ? $faltan : 7 ) . ' days' )->setTime( (int) $m[1], (int) $m[2] );
		$fin    = $inicio->modify( '+' . max( 15, (int) $a['duracion'] ) . ' minutes' );

		$parametros = array(
			'action'   => 'TEMPLATE',
			'text'     => $a['titulo'] . ' — ' . get_bloginfo( 'name' ),
			'dates'    => $inicio->format( 'Ymd\THis' ) . '/' . $fin->format( 'Ymd\THis' ),
			'ctz'      => wp_timezone_string(),
			'details'  => $a['titulo'] . ' en ' . get_bloginfo( 'name' ) . '.',
			'location' => cds_ajuste( 'direccion' ),
		);
		$url = 'https://calendar.google.com/calendar/render?' . http_build_query( $parametros, '', '&', PHP_QUERY_RFC3986 );
		return '<a class="cds-boton" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $a['texto'] ) . '</a>';
	}
);

/**
 * [cds_correo texto="Escribir un correo"]: botón que abre el correo del grupo.
 * Sin correo configurado, el botón sale apagado con una explicación.
 */
add_shortcode(
	'cds_correo',
	static function ( $atts = array() ) {
		$a      = shortcode_atts( array( 'texto' => 'Escribir un correo' ), $atts );
		$correo = sanitize_email( cds_ajuste( 'correo' ) );
		if ( ! $correo || ! is_email( $correo ) ) {
			return cds_boton_apagado( 'Correo por confirmar' );
		}
		return '<a class="cds-boton" href="' . esc_url( 'mailto:' . $correo, array( 'mailto' ) ) . '">' . esc_html( $a['texto'] ) . '</a>'
			. '<p class="cds-correo-texto">' . esc_html( $correo ) . '</p>';
	}
);
