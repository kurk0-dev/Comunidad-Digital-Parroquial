<?php
/**
 * Ajustes → Datos del sitio.
 *
 * Aquí el equipo del grupo cambia, sin tocar código, los datos que se repiten en
 * varias páginas: WhatsApp, formularios, ubicación, redes y la hoja de
 * donaciones. Cada dato se valida al guardarlo.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

const CDS_OPCION = 'cds_ajustes';

/**
 * Valores por defecto. Los datos ya confirmados (ubicación de la
 * capilla) vienen llenos; lo pendiente queda vacío para que su botón no
 * aparezca o salga como "por configurar".
 *
 * @return array<string,string>
 */
function cds_ajustes_por_defecto() {
	return array(
		'whatsapp'               => '',
		'coordenadas'            => '-12.0405377,-76.925658',
		'etiqueta_mapa'          => 'Capilla Divino Niño',
		'direccion'              => 'Irlanda, Lima 15494 · Plus Code X35F+QPP',
		'facebook'               => '',
		'instagram'              => '',
		'form_contacto'          => '',
		'form_ayuda'             => '',
		'form_donacion'          => '',
		'csv_donaciones'         => '',
		'necesidades'            => "Arroz | 50 | 0 | kg\nAceite | 30 | 0 | litros\nMenestras | 40 | 0 | kg\nRopa de abrigo | 60 | 0 | prendas",
		'necesidades_confirmadas' => '',
		'privacidad'             => 'Tus datos solo los ve el equipo del grupo, se usan únicamente para responderte y no se comparten con nadie (Ley N.° 29733).',
		'bot_fallback'           => 'No estoy seguro de cómo responder eso. Si quieres, escríbenos por WhatsApp o desde la página Contacto.',
	);
}

/**
 * Un ajuste concreto, con su valor por defecto si nunca se guardó.
 *
 * @param string $clave Nombre del ajuste.
 * @return string
 */
function cds_ajuste( $clave ) {
	$guardados = get_option( CDS_OPCION, array() );
	$valores   = wp_parse_args( is_array( $guardados ) ? $guardados : array(), cds_ajustes_por_defecto() );
	return isset( $valores[ $clave ] ) ? (string) $valores[ $clave ] : '';
}

/**
 * Valida cada campo antes de guardarlo.
 *
 * @param mixed $entrada Datos enviados desde el formulario.
 * @return array<string,string>
 */
function cds_sanear_ajustes( $entrada ) {
	$entrada = is_array( $entrada ) ? $entrada : array();
	$salida  = cds_ajustes_por_defecto();

	$salida['whatsapp'] = cds_solo_digitos( $entrada['whatsapp'] ?? '' );

	$coords = preg_replace( '/\s+/', '', (string) ( $entrada['coordenadas'] ?? '' ) );
	if ( preg_match( '/^-?\d{1,2}(\.\d+)?,-?\d{1,3}(\.\d+)?$/', $coords ) ) {
		$salida['coordenadas'] = $coords;
	} elseif ( '' !== $coords ) {
		add_settings_error( CDS_OPCION, 'coords', 'Las coordenadas deben tener la forma -12.0365691,-76.928581. Se conservó el valor anterior.' );
		$salida['coordenadas'] = cds_ajuste( 'coordenadas' );
	}

	foreach ( array( 'etiqueta_mapa', 'direccion' ) as $clave ) {
		$salida[ $clave ] = sanitize_text_field( $entrada[ $clave ] ?? '' );
	}

	foreach ( array( 'facebook', 'instagram', 'form_contacto', 'form_ayuda', 'form_donacion' ) as $clave ) {
		$salida[ $clave ] = cds_url_segura( $entrada[ $clave ] ?? '' );
	}

	// Solo se aceptan hojas de Google publicadas: es lo que lee el contador.
	$csv = cds_url_segura( $entrada['csv_donaciones'] ?? '' );
	if ( $csv && 0 !== strpos( $csv, 'https://docs.google.com/spreadsheets/' ) ) {
		add_settings_error( CDS_OPCION, 'csv', 'El enlace del avance de donaciones debe ser una hoja de Google publicada como CSV (empieza por https://docs.google.com/spreadsheets/).' );
		$csv = '';
	}
	$salida['csv_donaciones'] = $csv;
	delete_transient( 'cds_csv_donaciones' );

	$salida['necesidades']             = sanitize_textarea_field( $entrada['necesidades'] ?? '' );
	$salida['necesidades_confirmadas'] = empty( $entrada['necesidades_confirmadas'] ) ? '' : '1';
	$salida['privacidad']              = sanitize_text_field( $entrada['privacidad'] ?? '' );
	$salida['bot_fallback']            = sanitize_text_field( $entrada['bot_fallback'] ?? '' );

	return $salida;
}

add_action(
	'admin_init',
	static function () {
		register_setting(
			'cds_grupo',
			CDS_OPCION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'cds_sanear_ajustes',
				'default'           => cds_ajustes_por_defecto(),
			)
		);
	}
);

add_action(
	'admin_menu',
	static function () {
		add_options_page( 'Datos del sitio', 'Datos del sitio', 'manage_options', 'cds-ajustes', 'cds_pagina_ajustes' );
	}
);

/**
 * Dibuja un campo de texto de la página de ajustes.
 *
 * @param string $clave  Nombre del ajuste.
 * @param string $titulo Etiqueta visible.
 * @param string $ayuda  Texto de ayuda debajo del campo.
 * @param string $tipo   text|url|textarea|checkbox.
 */
function cds_campo( $clave, $titulo, $ayuda = '', $tipo = 'text' ) {
	$id     = 'cds-' . $clave;
	$nombre = CDS_OPCION . '[' . $clave . ']';
	$valor  = cds_ajuste( $clave );
	echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $titulo ) . '</label></th><td>';
	if ( 'textarea' === $tipo ) {
		echo '<textarea class="large-text code" rows="5" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nombre ) . '">' . esc_textarea( $valor ) . '</textarea>';
	} elseif ( 'checkbox' === $tipo ) {
		echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nombre ) . '" value="1" ' . checked( '1', $valor, false ) . '>';
	} else {
		echo '<input class="regular-text" type="' . esc_attr( $tipo ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nombre ) . '" value="' . esc_attr( $valor ) . '">';
	}
	if ( $ayuda ) {
		echo '<p class="description">' . esc_html( $ayuda ) . '</p>';
	}
	echo '</td></tr>';
}

/** Página Ajustes → Datos del sitio. */
function cds_pagina_ajustes() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>Datos del sitio</h1>';
	echo '<p>Estos datos se usan en varias páginas a la vez. Si un campo queda vacío, su botón no aparece o se muestra como "por configurar".</p>';
	settings_errors( CDS_OPCION );
	echo '<form method="post" action="options.php">';
	settings_fields( 'cds_grupo' );

	echo '<h2>Contacto</h2><table class="form-table" role="presentation">';
	cds_campo( 'whatsapp', 'WhatsApp del coordinador', 'Código de país + número, sin + ni espacios. Perú: 51987654321.' );
	cds_campo( 'form_contacto', 'Formulario de contacto (Google Forms)', 'Enlace de "Enviar" del formulario.', 'url' );
	cds_campo( 'form_ayuda', 'Formulario de solicitud de ayuda', 'El nombre debe ser opcional y "Recopilar correos" debe estar desactivado: la página promete anonimato.', 'url' );
	cds_campo( 'privacidad', 'Aviso de privacidad (letra pequeña)' );
	echo '</table>';

	echo '<h2>Ubicación y redes</h2><table class="form-table" role="presentation">';
	cds_campo( 'coordenadas', 'Coordenadas del templo', 'En Google Maps: clic derecho sobre el templo y pulsar las dos cifras. No uses enlaces cortos maps.app.goo.gl: caducan.' );
	cds_campo( 'etiqueta_mapa', 'Nombre en el mapa' );
	cds_campo( 'direccion', 'Dirección' );
	cds_campo( 'facebook', 'Facebook', '', 'url' );
	cds_campo( 'instagram', 'Instagram', 'Déjalo vacío si el grupo no tiene: el enlace no aparecerá.', 'url' );
	echo '</table>';

	echo '<h2>Campaña de donaciones</h2><table class="form-table" role="presentation">';
	cds_campo( 'form_donacion', 'Formulario para registrar una donación', 'Este formulario NO es anónimo: se pide nombre y teléfono para coordinar la entrega.', 'url' );
	cds_campo( 'csv_donaciones', 'Hoja de avance publicada como CSV', 'Opcional. Si la llenas, el contador se actualiza solo desde Google Sheets (pestaña "Avance": item, meta, unidad, actual).', 'url' );
	cds_campo( 'necesidades', 'Lista de necesidades (si no hay hoja)', 'Una por línea: producto | meta | recibido | unidad. Ejemplo: Arroz | 50 | 32 | kg', 'textarea' );
	cds_campo( 'necesidades_confirmadas', 'Lista confirmada por pastoral social', 'Mientras no esté marcada, la página muestra la etiqueta "Ejemplo / Por confirmar".', 'checkbox' );
	echo '</table>';

	echo '<h2>Bot de preguntas frecuentes</h2><table class="form-table" role="presentation">';
	cds_campo( 'bot_fallback', 'Respuesta cuando el bot no entiende' );
	echo '</table><p>Las preguntas y respuestas del bot se editan en el menú <strong>Preguntas del bot</strong>.</p>';

	submit_button( 'Guardar cambios' );
	echo '</form></div>';
}
