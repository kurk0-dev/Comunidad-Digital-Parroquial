<?php
/**
 * Herramientas → Contenido inicial.
 *
 * Un solo botón crea lo necesario para que el sitio quede listo:
 *   - las 6 páginas (Inicio, Misas, Servicios, Donaciones, Comunidad, Contacto)
 *     a partir de los patrones del tema, editables desde el editor;
 *   - las fotos del tema copiadas a la Biblioteca de medios;
 *   - el escudo como logo del sitio;
 *   - las preguntas iniciales del bot;
 *   - "Inicio" como página principal.
 *
 * No duplica nada: si una página o pregunta ya existe, la deja como está.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

/** Páginas: slug => [título, patrón, orden en el menú]. */
function cds_paginas_iniciales() {
	return array(
		'inicio'     => array( 'Inicio', 'cds/pagina-inicio', 1 ),
		'misas'      => array( 'Misas', 'cds/pagina-misas', 2 ),
		'servicios'  => array( 'Servicios', 'cds/pagina-servicios', 3 ),
		'donaciones' => array( 'Donaciones', 'cds/pagina-donaciones', 4 ),
		'comunidad'  => array( 'Comunidad', 'cds/pagina-comunidad', 5 ),
		'contacto'   => array( 'Contacto', 'cds/pagina-contacto', 6 ),
	);
}

/** Preguntas del bot alineadas al Backlog (HU-15, HU-16, HU-17, HU-19). */
function cds_preguntas_iniciales() {
	return array(
		array( '¿A qué hora son las misas?', 'Puedes ver todos los horarios de misa, incluidas las fechas especiales, en la página Misas. Desde ahí también puedes agregar la misa dominical a tu Google Calendar.', array( 'horario', 'horarios', 'misa', 'misas', 'a que hora', 'cuando hay misa' ) ),
		array( '¿Qué necesito para un bautizo?', 'Los requisitos de bautizo están en la página Servicios: partida de nacimiento, copia del DNI de los padres, padrinos confirmados y charla prebautismal. Confirma fechas en la secretaría.', array( 'bautizo', 'bautismo', 'bautizar', 'requisitos' ) ),
		array( '¿Cómo puedo donar?', 'En la página Donaciones verás qué se necesita y cuánto falta para cada meta. Solo recibimos donaciones en especie (víveres y ropa), nunca dinero.', array( 'donar', 'donacion', 'donaciones', 'campana', 'ayudar', 'colaborar', 'viveres', 'ropa' ) ),
		array( '¿Cuándo es la catequesis?', 'Inscripciones abiertas para la catequesis de iniciación cristiana. Informes de martes a viernes, de 7:00 p.m. a 9:00 p.m. Niños de 8 a 13 años (Bautismo y Eucaristía) y jóvenes de 15 a 19 años (Bautismo, Eucaristía y Confirmación).', array( 'catequesis', 'primera comunion', 'comunion', 'confirmacion', 'inscripcion' ) ),
		array( 'Necesito pedir ayuda', 'En la página Contacto hay un formulario de solicitud de ayuda que puedes enviar de forma anónima. Alguien de la parroquia lo revisará.', array( 'ayuda', 'necesito ayuda', 'apoyo', 'pedir ayuda' ) ),
		array( '¿Dónde quedan?', 'Estamos en Ate, Lima. En la página Inicio hay un mapa: tócalo para abrir la ruta en Google Maps.', array( 'donde', 'direccion', 'ubicacion', 'como llego', 'mapa' ) ),
	);
}

/**
 * Copia una imagen del tema a la Biblioteca de medios (una sola vez).
 *
 * @param string $relativa Ruta dentro de assets/img/.
 * @return array{id:int,url:string}|null
 */
function cds_importar_imagen( $relativa ) {
	$mapa = get_option( 'cds_medios', array() );
	if ( isset( $mapa[ $relativa ] ) && get_post( $mapa[ $relativa ] ) ) {
		return array( 'id' => (int) $mapa[ $relativa ], 'url' => wp_get_attachment_url( $mapa[ $relativa ] ) );
	}
	$origen = get_theme_file_path( 'assets/img/' . $relativa );
	if ( ! is_readable( $origen ) ) {
		return null;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$subida = wp_upload_bits( wp_basename( $origen ), null, file_get_contents( $origen ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $subida['error'] ) ) {
		return null;
	}
	$tipo = wp_check_filetype( $subida['file'] );
	$id   = wp_insert_attachment(
		array(
			'post_mime_type' => $tipo['type'],
			'post_title'     => sanitize_text_field( pathinfo( $origen, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$subida['file']
	);
	if ( ! $id || is_wp_error( $id ) ) {
		return null;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $subida['file'] ) );
	$mapa[ $relativa ] = $id;
	update_option( 'cds_medios', $mapa, false );
	return array( 'id' => $id, 'url' => $subida['url'] );
}

/**
 * El contenido de un patrón del tema, con sus imágenes ya en la Biblioteca.
 *
 * @param string $nombre Nombre registrado del patrón.
 * @return string
 */
function cds_contenido_de_patron( $nombre ) {
	$patron = WP_Block_Patterns_Registry::get_instance()->get_registered( $nombre );
	if ( ! $patron || empty( $patron['content'] ) ) {
		return '';
	}
	$contenido = $patron['content'];
	$base      = trailingslashit( get_theme_file_uri( 'assets/img' ) );
	if ( preg_match_all( '#' . preg_quote( $base, '#' ) . '([A-Za-z0-9_\-/]+\.(?:webp|png|jpe?g|svg))#', $contenido, $m ) ) {
		foreach ( array_unique( $m[1] ) as $relativa ) {
			$medio = cds_importar_imagen( $relativa );
			if ( $medio ) {
				$contenido = str_replace( $base . $relativa, $medio['url'], $contenido );
			}
		}
	}
	return $contenido;
}

/** Crea todo lo que falte. Devuelve un resumen legible. */
function cds_crear_contenido_inicial() {
	$hechos = array();

	// Los botones enlazan a /misas/, /donaciones/…: hacen falta enlaces permanentes.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
		$hechos[] = 'Enlaces permanentes activados (/nombre-de-la-pagina/)';
	}

	foreach ( cds_paginas_iniciales() as $slug => list( $titulo, $patron, $orden ) ) {
		$existente = get_page_by_path( $slug );
		if ( $existente ) {
			$id = $existente->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $titulo,
					'post_name'    => $slug,
					'menu_order'   => $orden,
					'post_content' => cds_contenido_de_patron( $patron ),
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				$hechos[] = 'Página creada: ' . $titulo;
			}
		}
		if ( 'inicio' === $slug && ! is_wp_error( $id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $id );
		}
	}

	if ( ! get_posts( array( 'post_type' => 'cds_pregunta', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
		foreach ( cds_preguntas_iniciales() as $orden => list( $titulo, $respuesta, $claves ) ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'cds_pregunta',
					'post_status'  => 'publish',
					'post_title'   => $titulo,
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $respuesta ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $orden,
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				wp_set_object_terms( $id, $claves, 'cds_clave' );
			}
		}
		$hechos[] = 'Preguntas del bot creadas: ' . count( cds_preguntas_iniciales() );
	}

	$escudo = cds_importar_imagen( 'escudo-parroquia.webp' );
	if ( $escudo && ! get_theme_mod( 'custom_logo' ) ) {
		set_theme_mod( 'custom_logo', $escudo['id'] );
		update_option( 'site_logo', $escudo['id'] );
		$hechos[] = 'Escudo de la parroquia puesto como logo';
	}

	// Nombre del sitio solo si sigue el de fábrica.
	$nombre = get_option( 'blogname' );
	if ( '' === $nombre || in_array( $nombre, array( 'Mi blog', 'My Blog', 'Mi sitio', 'My WordPress Website', 'WordPress' ), true ) ) {
		update_option( 'blogname', 'Parroquia San Francisco Javier' );
		update_option( 'blogdescription', 'Diócesis de Chosica · Vicaría II · Ate, Lima' );
		$hechos[] = 'Nombre y descripción del sitio';
	}

	return $hechos ? $hechos : array( 'Todo ya existía: no se cambió nada.' );
}

add_action(
	'admin_menu',
	static function () {
		add_management_page( 'Contenido inicial', 'Contenido inicial', 'manage_options', 'cds-contenido', 'cds_pagina_contenido' );
	}
);

/** Página Herramientas → Contenido inicial. */
function cds_pagina_contenido() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>Contenido inicial de la parroquia</h1>';
	$resumen = get_transient( 'cds_resumen_contenido' );
	if ( $resumen ) {
		delete_transient( 'cds_resumen_contenido' );
		echo '<div class="notice notice-success"><ul>';
		foreach ( (array) $resumen as $linea ) {
			echo '<li>' . esc_html( $linea ) . '</li>';
		}
		echo '</ul></div>';
	}
	echo '<p>Crea las 6 páginas del sitio, sube las fotos a la Biblioteca de medios, pone el escudo como logo y carga las preguntas iniciales del bot. Lo que ya exista no se modifica.</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="cds_contenido">';
	wp_nonce_field( 'cds_contenido' );
	submit_button( 'Crear contenido inicial' );
	echo '</form></div>';
}

add_action(
	'admin_post_cds_contenido',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No tienes permiso para hacer esto.', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'cds_contenido' );
		set_transient( 'cds_resumen_contenido', cds_crear_contenido_inicial(), MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'tools.php?page=cds-contenido' ) );
		exit;
	}
);
