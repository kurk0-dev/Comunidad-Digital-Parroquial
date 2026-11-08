<?php
/**
 * Contador de la campaña: [cds_donaciones].
 *
 * Lee la pestaña "Avance" publicada como CSV (solo totales, sin datos personales)
 * desde el servidor y la guarda 15 minutos. Si no hay hoja o falla, usa la lista
 * escrita a mano en Ajustes → Datos del sitio.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

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
