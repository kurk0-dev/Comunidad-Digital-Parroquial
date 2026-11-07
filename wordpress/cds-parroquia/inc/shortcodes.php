<?php
/**
 * Atajos ([shortcodes]) para las partes dinámicas de las páginas.
 *
 * Se insertan con el bloque "Código corto". Sus datos salen de
 * Ajustes → Datos del sitio, así un cambio se ve en todo el sitio.
 *
 *   [cds_mapa]                                   Mapa clicable del templo
 *   [cds_como_llegar texto="Cómo llegar"]        Botón a Google Maps
 *   [cds_whatsapp texto="..." mensaje="..."]     Botón de WhatsApp
 *   [cds_formulario tipo="contacto|ayuda|donacion" texto="..."]
 *   [cds_donaciones]                             Contador de la campaña
 *   [cds_qr]                                     QR que abre la página actual
 *   [cds_calendario titulo="Misa dominical" dia="domingo" hora="10:00"]
 *   [cds_privacidad]                             Aviso de datos en letra pequeña
 *   [cds_redes]                                  Facebook, Instagram, Cómo llegar
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
	static function () {
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
			. '<span class="cds-mapa__boton" aria-hidden="true">Abrir en Google Maps</span></a></div>';
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
	'cds_redes',
	static function ( $atts = array() ) {
		// [cds_redes mapa="no"] quita "Cómo llegar" donde ya hay un botón que lleva al mapa.
		$a       = shortcode_atts( array( 'mapa' => 'si' ), $atts );
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
		$dia  = $dias[ strtolower( $a['dia'] ) ] ?? 7;
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

/* -----------------------------------------------------------------------------
   CONTADOR DE DONACIONES
   Lee la pestaña "Avance" publicada como CSV desde el servidor (sin problemas
   de CORS ni de CSP) y la guarda 15 minutos. Si no hay hoja o falla, usa la
   lista de Ajustes → Datos del sitio.
----------------------------------------------------------------------------- */

/**
 * Convierte "50", "12.5" o "12,5" en número; vacío = 0; texto = null.
 *
 * @param string $valor Celda.
 * @return float|null
 */
function cds_numero( $valor ) {
	$v = preg_replace( '/\s+/', '', (string) $valor );
	if ( '' === $v ) {
		return 0.0;
	}
	return preg_match( '/^\d+([.,]\d+)?$/', $v ) ? (float) str_replace( ',', '.', $v ) : null;
}

/** Lee el CSV publicado. Devuelve [] si no hay hoja o si falla. */
function cds_leer_csv_donaciones() {
	$url = cds_ajuste( 'csv_donaciones' );
	if ( ! $url ) {
		return array();
	}
	$guardado = get_transient( 'cds_csv_donaciones' );
	if ( is_array( $guardado ) ) {
		return $guardado;
	}

	$respuesta = wp_safe_remote_get(
		$url,
		array(
			'timeout'             => 8,
			'limit_response_size' => 100000,
			'redirection'         => 3,
		)
	);
	if ( is_wp_error( $respuesta ) || 200 !== (int) wp_remote_retrieve_response_code( $respuesta ) ) {
		return array();
	}

	$lineas = preg_split( '/\r\n|\r|\n/', trim( wp_remote_retrieve_body( $respuesta ) ) );
	$filas  = array_map( 'str_getcsv', array_filter( $lineas ) );
	if ( count( $filas ) < 2 ) {
		return array();
	}
	$cabecera = array_map(
		static function ( $c ) {
			return strtolower( remove_accents( trim( $c ) ) );
		},
		array_shift( $filas )
	);
	$col = array_flip( $cabecera );
	if ( ! isset( $col['item'], $col['meta'], $col['actual'] ) ) {
		return array();
	}

	$items = array();
	foreach ( array_slice( $filas, 0, 30 ) as $f ) {
		$item   = sanitize_text_field( $f[ $col['item'] ] ?? '' );
		$meta   = cds_numero( $f[ $col['meta'] ] ?? '' );
		$actual = cds_numero( $f[ $col['actual'] ] ?? '' );
		if ( '' === $item || null === $meta || null === $actual || $meta <= 0 ) {
			continue;
		}
		$items[] = array(
			'item'   => mb_substr( $item, 0, 60 ),
			'meta'   => $meta,
			'actual' => $actual,
			'unidad' => isset( $col['unidad'] ) ? sanitize_text_field( $f[ $col['unidad'] ] ?? '' ) : '',
		);
	}
	set_transient( 'cds_csv_donaciones', $items, 15 * MINUTE_IN_SECONDS );
	return $items;
}

/** Lista escrita a mano en Ajustes: "Arroz | 50 | 32 | kg" por línea. */
function cds_necesidades_manual() {
	$items = array();
	foreach ( preg_split( '/\r\n|\r|\n/', cds_ajuste( 'necesidades' ) ) as $linea ) {
		$p = array_map( 'trim', explode( '|', $linea ) );
		if ( count( $p ) < 3 ) {
			continue;
		}
		$meta   = cds_numero( $p[1] );
		$actual = cds_numero( $p[2] );
		if ( '' === $p[0] || null === $meta || null === $actual || $meta <= 0 ) {
			continue;
		}
		$items[] = array(
			'item'   => $p[0],
			'meta'   => $meta,
			'actual' => $actual,
			'unidad' => $p[3] ?? '',
		);
	}
	return $items;
}

add_shortcode(
	'cds_donaciones',
	static function () {
		$items   = cds_leer_csv_donaciones();
		$de_hoja = ! empty( $items );
		if ( ! $de_hoja ) {
			$items = cds_necesidades_manual();
		}
		if ( ! $items ) {
			return '<p>No hay campañas abiertas en este momento.</p>';
		}

		$numero = static function ( $n ) {
			return rtrim( rtrim( number_format( $n, 1, ',', '' ), '0' ), ',' );
		};

		$html = '<ul class="cds-campanas">';
		foreach ( $items as $it ) {
			$pct      = (int) max( 0, min( 100, round( $it['actual'] / $it['meta'] * 100 ) ) );
			$completa = $pct >= 100;
			$cifra    = $numero( $it['actual'] ) . ' / ' . $numero( $it['meta'] ) . ' ' . $it['unidad'];
			$falta    = max( 0, $it['meta'] - $it['actual'] );

			$html .= '<li class="cds-campana' . ( $completa ? ' cds-campana--completa' : '' ) . '">';
			$html .= '<div class="cds-campana__cabecera"><h3 class="cds-campana__item">' . esc_html( $it['item'] ) . '</h3>';
			$html .= '<span class="cds-campana__cifra">' . esc_html( $cifra ) . '</span></div>';
			$html .= '<div class="cds-campana__barra" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( $pct ) . '" aria-label="' . esc_attr( 'Avance de ' . $it['item'] . ': ' . $cifra . ', ' . $pct . ' por ciento' ) . '">';
			$html .= '<div class="cds-campana__relleno" style="width:' . esc_attr( $pct ) . '%"></div></div>';
			$html .= '<div class="cds-campana__pie"><span>' . esc_html( $pct . '% completado' ) . '</span>';
			if ( ! $completa ) {
				$html .= '<span>' . esc_html( 'Faltan ' . $numero( $falta ) . ' ' . $it['unidad'] ) . '</span>';
			}
			$html .= '</div>';
			// Texto, no solo color: un daltónico no distingue el verde del rojo.
			if ( $completa ) {
				$html .= '<span class="cds-campana__completa">Meta cumplida, ¡gracias!</span>';
			}
			$html .= '</li>';
		}
		$html .= '</ul>';

		if ( $de_hoja ) {
			$html .= '<p class="cds-campanas__fuente">Avance actualizado desde la hoja de la campaña.</p>';
		} elseif ( '1' !== cds_ajuste( 'necesidades_confirmadas' ) ) {
			$html .= '<p class="cds-campanas__fuente is-style-por-confirmar">Lista de ejemplo: la confirma pastoral social.</p>';
		}
		return $html;
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
		$numero = cds_solo_digitos( cds_ajuste( 'whatsapp' ) );
		if ( $numero ) {
			$url     = 'https://wa.me/' . $numero . '?text=' . rawurlencode( 'Hola, escribo desde la página del Grupo de Oración Divino Niño Jesús.' );
			$redes[] = array( 'whatsapp', $url, 'WhatsApp', 'Escríbele al coordinador', 'Para preguntar algo rápido o pedir oración.', true );
		}
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
