<?php
/**
 * [cds_donar]: formulario de donación en la propia web.
 *
 * Google Forms no puede mostrar una pregunta solo cuando se marca una casilla.
 * Este formulario sí: al marcar "Arroz" aparece "¿Cuántos kilos de arroz?" y al
 * desmarcarlo la pregunta se oculta y su número se borra.
 *
 * Lo enviado va al MISMO formulario de Google (formResponse), así que la hoja de
 * respuestas, la casilla "¿Llegó?" y la barra de la campaña no cambian. La web no
 * guarda nada: no hay base de datos ni datos personales en WordPress.
 *
 * Si el formulario de Google cambia (preguntas nuevas o borradas), hay que
 * actualizar los códigos "entry.N" de abajo. Se sacan de la página pública del
 * formulario (FB_PUBLIC_LOAD_DATA_). Ver docs/guia-formularios.md.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/** Dirección que recibe las respuestas del formulario de donación de Google. */
const CDS_DONAR_ACCION = 'https://docs.google.com/forms/d/e/1FAIpQLSeCasx8EjHjEMiekt599pSm0l0vt3N3v12bORUt6OuHa2qYCg/formResponse';

/**
 * Preguntas del formulario de Google y su código "entry".
 *
 * Los productos van en el mismo orden y con el mismo nombre que la pestaña
 * "Avance" de la hoja: [nombre, pregunta, unidad, entry].
 *
 * @return array
 */
function cds_donar_config() {
	return array(
		'nombre'    => 'entry.704505623',
		'celular'   => 'entry.1463871008',
		'productos' => array(
			array( 'Arroz', '¿Cuántos kilos de arroz?', 'kilos', 'entry.1693030593' ),
			array( 'Azúcar', '¿Cuántos kilos de azúcar?', 'kilos', 'entry.767869928' ),
			array( 'Aceite', '¿Cuántos litros de aceite?', 'litros', 'entry.1992210627' ),
			array( 'Menestras', '¿Cuántos kilos de menestras?', 'kilos', 'entry.513239771' ),
			array( 'Fideos', '¿Cuántos kilos de fideos?', 'kilos', 'entry.429784270' ),
			array( 'Leche', '¿Cuántos tarros de leche?', 'tarros', 'entry.989491035' ),
			array( 'Conservas', '¿Cuántas latas de conserva?', 'latas', 'entry.660784818' ),
			array( 'Panetón', '¿Cuántos panetones?', 'panetones', 'entry.116573957' ),
			array( 'Chocolate para taza', '¿Cuántas tabletas de chocolate?', 'tabletas', 'entry.722185669' ),
			array( 'Juguetes nuevos', '¿Cuántos juguetes?', 'juguetes', 'entry.835302079' ),
			array( 'Ropa de abrigo', '¿Cuántas prendas de abrigo?', 'prendas', 'entry.430377683' ),
			array( 'Útiles de aseo', '¿Cuántos útiles de aseo?', 'unidades', 'entry.742999474' ),
		),
		'otro'      => 'entry.1471238944',
		'cuando'    => array(
			'entry.808286941',
			array(
				'El viernes, después de la reunión en la capilla',
				'Prefiero que me escriban para ponernos de acuerdo',
			),
		),
		'mas'       => 'entry.9214441',
		'datos'     => array(
			'entry.1424234691',
			'Acepto que el grupo use mi nombre y mi celular solo para coordinar esta donación. No se los damos a nadie (Ley N.° 29733).',
		),
	);
}

/**
 * Marcado del formulario. Sin JavaScript se ven todas las cantidades y se envía
 * igual (directo a Google); con JavaScript solo aparecen las de lo marcado.
 *
 * @return string
 */
function cds_donar_html() {
	$c  = cds_donar_config();
	$h  = '<form class="cds-donar" id="donar" method="post" action="' . esc_url( CDS_DONAR_ACCION ) . '" accept-charset="UTF-8">';
	$h .= '<p class="cds-donar__nota">Los datos marcados con * son obligatorios.</p>';

	$h .= '<div class="cds-donar__campo"><label for="cds-donar-nombre">Nombre y apellido <span aria-hidden="true">*</span></label>';
	$h .= '<input id="cds-donar-nombre" name="' . esc_attr( $c['nombre'] ) . '" type="text" required maxlength="80" autocomplete="name"></div>';

	$h .= '<div class="cds-donar__campo"><label for="cds-donar-celular">Número de celular o WhatsApp <span aria-hidden="true">*</span></label>';
	$h .= '<p class="cds-donar__ayuda" id="cds-donar-celular-ayuda">A este número le escribiremos para coordinar. Son 9 números y empieza con 9.</p>';
	$h .= '<input id="cds-donar-celular" name="' . esc_attr( $c['celular'] ) . '" type="tel" inputmode="numeric" required maxlength="13" autocomplete="tel-national" pattern=" *9[0-9]{2} ?[0-9]{3} ?[0-9]{3} *" aria-describedby="cds-donar-celular-ayuda" data-mensaje="Escriba su celular: son 9 números y empieza con 9. Por ejemplo: 987654321"></div>';

	$h .= '<fieldset class="cds-donar__productos"><legend>¿Qué va a traer? <span aria-hidden="true">*</span></legend>';
	$h .= '<p class="cds-donar__ayuda">Marque lo que va a traer y le preguntaremos cuánto. Los juguetes, por favor nuevos y sin envolver.</p>';
	foreach ( $c['productos'] as $i => list( $nombre, $pregunta, $unidad, $entry ) ) {
		$id  = 'cds-donar-p' . $i;
		$h  .= '<div class="cds-donar__producto">';
		$h  .= '<label class="cds-donar__opcion"><input type="checkbox" data-cantidad="' . esc_attr( $id . '-cantidad' ) . '"> <span>' . esc_html( $nombre ) . '</span></label>';
		$h  .= '<div class="cds-donar__cantidad" id="' . esc_attr( $id . '-cantidad' ) . '">';
		$h  .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $pregunta ) . '</label>';
		$h  .= '<span class="cds-donar__numero"><input id="' . esc_attr( $id ) . '" name="' . esc_attr( $entry ) . '" type="number" inputmode="numeric" min="1" max="9999" step="1" data-mensaje="Escriba solo el número, sin letras. Por ejemplo: 5"> <span>' . esc_html( $unidad ) . '</span></span>';
		$h  .= '</div></div>';
	}
	$h .= '<div class="cds-donar__campo"><label for="cds-donar-otro">¿Trae otra cosa? Escriba qué y cuánto</label>';
	$h .= '<input id="cds-donar-otro" name="' . esc_attr( $c['otro'] ) . '" type="text" maxlength="200" placeholder="Por ejemplo: 3 paquetes de galletas"></div>';
	$h .= '</fieldset>';

	$h .= '<fieldset class="cds-donar__grupo"><legend>¿Cuándo lo podría traer? <span aria-hidden="true">*</span></legend>';
	foreach ( $c['cuando'][1] as $i => $opcion ) {
		$h .= '<label class="cds-donar__opcion"><input type="radio" name="' . esc_attr( $c['cuando'][0] ) . '" value="' . esc_attr( $opcion ) . '"' . ( 0 === $i ? ' required' : '' ) . '> <span>' . esc_html( $opcion ) . '</span></label>';
	}
	$h .= '</fieldset>';

	$h .= '<div class="cds-donar__campo"><label for="cds-donar-mas">¿Algo más que quiera contarnos?</label>';
	$h .= '<textarea id="cds-donar-mas" name="' . esc_attr( $c['mas'] ) . '" rows="3" maxlength="1000"></textarea></div>';

	$h .= '<label class="cds-donar__opcion cds-donar__permiso"><input type="checkbox" name="' . esc_attr( $c['datos'][0] ) . '" value="' . esc_attr( $c['datos'][1] ) . '" required> <span>' . esc_html( $c['datos'][1] ) . '</span></label>';

	$h .= '<p class="cds-donar__estado" role="status" aria-live="polite"></p>';
	$h .= '<button type="submit" class="wp-element-button cds-donar__enviar">Enviar mi donación</button>';
	$h .= '</form>';
	return $h;
}

add_shortcode(
	'cds_donar',
	static function () {
		wp_enqueue_script( 'cds-donar', get_theme_file_uri( 'assets/js/donar.js' ), array(), CDS_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		return cds_donar_html();
	}
);
