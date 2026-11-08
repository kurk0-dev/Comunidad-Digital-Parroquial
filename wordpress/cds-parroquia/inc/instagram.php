<?php
/**
 * Instagram en la web: "Lo último que publicamos".
 *
 * Cómo funciona
 *   1. Quien administra el sitio pega UNA vez el token de Instagram en
 *      Ajustes → Datos del sitio → Instagram en la web.
 *   2. Cada 3 horas WordPress pide las últimas publicaciones a la API oficial
 *      (graph.instagram.com), descarga una copia pequeña de cada foto en
 *      wp-content/uploads/cds-instagram/ y guarda la lista.
 *   3. Una vez por semana renueva el token solo (dura 60 días), así nadie tiene
 *      que acordarse de renovarlo.
 *
 * Dos clases de token
 *   - "IGAA…" (API con inicio de sesión de Instagram): se lee graph.instagram.com.
 *   - "EAA…" (inicio de sesión con Facebook o el unificado): se busca la página de
 *     Facebook conectada a la cuenta profesional de Instagram, se guarda el token
 *     de esa página (no vence si viene de un token de 60 días) y se lee
 *     graph.facebook.com/{id de Instagram}/media. El token personal no se guarda.
 *
 * Seguridad
 *   - El token vive en la base de datos de WordPress, nunca en el código ni en
 *     el repositorio (que es público). Nunca se imprime: ni en la página de
 *     ajustes, ni en los errores, ni en el HTML.
 *   - El visitante nunca se conecta con Instagram: ve fotos servidas por el
 *     propio sitio. Las fotos se validan (id numérico, origen en el CDN de
 *     Instagram, archivo de imagen real) antes de guardarse.
 *   - Si Instagram falla o el token vence, la web sigue mostrando las últimas
 *     publicaciones guardadas y el error aparece solo en el panel.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

const CDS_IG_OPCION   = 'cds_instagram';
const CDS_IG_CARPETA  = 'cds-instagram';
const CDS_IG_CANTIDAD = 12;
const CDS_IG_API      = 'https://graph.instagram.com/';
const CDS_IG_API_FB   = 'https://graph.facebook.com/';

/** Estado guardado, con valores por defecto. */
function cds_ig_datos() {
	$d = get_option( CDS_IG_OPCION, array() );
	return wp_parse_args(
		is_array( $d ) ? $d : array(),
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
}

/** Guarda el estado sin cargarlo en cada página (autoload desactivado). */
function cds_ig_guardar( $d ) {
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

/**
 * Llama a la API de Instagram. Los errores nunca incluyen la URL (lleva el token).
 *
 * @param string $ruta   Ruta después del dominio de la API.
 * @param array  $params Parámetros de la consulta.
 * @param string $token  Token de acceso.
 * @param string $api    CDS_IG_API (Instagram) o CDS_IG_API_FB (Facebook).
 * @return array|WP_Error
 */
function cds_ig_pedir( $ruta, $params, $token, $api = CDS_IG_API ) {
	$url       = add_query_arg( array_merge( $params, array( 'access_token' => $token ) ), $api . $ruta );
	$respuesta = wp_safe_remote_get( $url, array( 'timeout' => 15 ) );
	if ( is_wp_error( $respuesta ) ) {
		return new WP_Error( 'cds_ig_red', 'No se pudo conectar con Instagram. Se intentará de nuevo más tarde.' );
	}
	$codigo = (int) wp_remote_retrieve_response_code( $respuesta );
	$json   = json_decode( (string) wp_remote_retrieve_body( $respuesta ), true );
	if ( 200 !== $codigo || ! is_array( $json ) ) {
		$codigo_api = is_array( $json ) ? (int) ( $json['error']['code'] ?? 0 ) : 0;
		if ( 190 === $codigo_api || 401 === $codigo ) {
			return new WP_Error( 'cds_ig_token', 'El token de Instagram venció o no es válido. Genera uno nuevo y pégalo aquí.' );
		}
		return new WP_Error( 'cds_ig_api', 'Instagram respondió con un error (' . $codigo . '). Se intentará de nuevo más tarde.' );
	}
	return $json;
}

/**
 * Con un token de Facebook ("EAA…"): busca la página conectada a la cuenta
 * profesional de Instagram y devuelve el token de esa página, el id y el
 * usuario de Instagram. El token personal se usa solo aquí y no se guarda.
 *
 * @param string $token Token personal de Facebook.
 * @return array|WP_Error
 */
function cds_ig_conectar_facebook( $token ) {
	// ¿Es un token que dura una o dos horas? Entonces dejaría de funcionar hoy mismo.
	$info = cds_ig_pedir( 'debug_token', array( 'input_token' => $token ), $token, CDS_IG_API_FB );
	if ( ! is_wp_error( $info ) ) {
		$vence = (int) ( $info['data']['expires_at'] ?? 0 );
		if ( $vence && $vence - time() < 2 * DAY_IN_SECONDS ) {
			return new WP_Error( 'cds_ig_corto', 'Ese token dura solo unas horas. Extiéndelo primero en el Depurador de tokens de Meta (botón "Extender token de acceso") y pega el nuevo.' );
		}
	}

	$paginas = cds_ig_pedir(
		'me/accounts',
		array(
			'fields' => 'name,access_token,instagram_business_account{id,username}',
			'limit'  => 50,
		),
		$token,
		CDS_IG_API_FB
	);
	if ( is_wp_error( $paginas ) ) {
		return $paginas;
	}
	$lista = (array) ( $paginas['data'] ?? array() );

	// Páginas que pertenecen a un portafolio comercial (Meta Business Suite):
	// a veces no salen en me/accounts, pero sí dentro del negocio.
	$negocios = cds_ig_pedir(
		'me/businesses',
		array( 'fields' => 'owned_pages{name,access_token,instagram_business_account{id,username}}' ),
		$token,
		CDS_IG_API_FB
	);
	if ( ! is_wp_error( $negocios ) ) {
		foreach ( (array) ( $negocios['data'] ?? array() ) as $negocio ) {
			$lista = array_merge( $lista, (array) ( $negocio['owned_pages']['data'] ?? array() ) );
		}
	}

	foreach ( $lista as $pagina ) {
		$ig = $pagina['instagram_business_account'] ?? array();
		if ( empty( $pagina['access_token'] ) || ! ctype_digit( (string) ( $ig['id'] ?? '' ) ) ) {
			continue;
		}
		$token_pagina = (string) $pagina['access_token'];
		$vence        = 0;
		$info         = cds_ig_pedir( 'debug_token', array( 'input_token' => $token_pagina ), $token, CDS_IG_API_FB );
		if ( ! is_wp_error( $info ) ) {
			$vence = (int) ( $info['data']['expires_at'] ?? 0 );
		}
		return array(
			'token'   => $token_pagina,
			'ig_id'   => (string) $ig['id'],
			'usuario' => sanitize_user( (string) ( $ig['username'] ?? '' ), true ),
			'vence'   => $vence,
		);
	}
	// Inicio de sesión unificado: el token puede ser directamente de la cuenta de
	// Instagram (graph.facebook.com/me devuelve su id 1784… y su usuario).
	$yo = cds_ig_pedir( 'me', array( 'fields' => 'id,username' ), $token, CDS_IG_API_FB );
	if ( ! is_wp_error( $yo ) && ctype_digit( (string) ( $yo['id'] ?? '' ) ) && ! empty( $yo['username'] ) ) {
		return array(
			'token'   => $token,
			'ig_id'   => (string) $yo['id'],
			'usuario' => sanitize_user( (string) $yo['username'], true ),
			'vence'   => 0,
		);
	}

	return new WP_Error( 'cds_ig_sin_pagina', 'No encontré una página de Facebook conectada a la cuenta profesional de Instagram. Revisa que el Facebook del grupo sea una página (no un perfil), que esté conectada al Instagram y que al generar el token hayas marcado esa página.' );
}

/** ¿La dirección de la imagen viene del CDN de Instagram/Meta? */
function cds_ig_origen_valido( $url ) {
	$partes = wp_parse_url( (string) $url );
	if ( empty( $partes['host'] ) || 'https' !== ( $partes['scheme'] ?? '' ) ) {
		return false;
	}
	$host = strtolower( $partes['host'] );
	foreach ( array( '.cdninstagram.com', '.fbcdn.net' ) as $dominio ) {
		if ( substr( $host, -strlen( $dominio ) ) === $dominio ) {
			return true;
		}
	}
	return false;
}

/**
 * Descarga una foto, comprueba que sea una imagen real y guarda una copia de
 * 800 px como máximo (sin datos EXIF: el editor de imágenes la vuelve a codificar).
 *
 * @param string $id  Id numérico de la publicación (es el nombre del archivo).
 * @param string $url Dirección de la imagen en el CDN de Instagram.
 * @return string|false Nombre del archivo guardado.
 */
function cds_ig_descargar( $id, $url ) {
	$carpeta = cds_ig_carpeta();
	$archivo = $id . '.jpg';
	$destino = $carpeta['ruta'] . '/' . $archivo;
	if ( file_exists( $destino ) ) {
		return $archivo;
	}
	if ( ! cds_ig_origen_valido( $url ) || ! wp_mkdir_p( $carpeta['ruta'] ) ) {
		return false;
	}
	$respuesta = wp_safe_remote_get( $url, array( 'timeout' => 20, 'limit_response_size' => 8 * MB_IN_BYTES ) );
	$cuerpo    = is_wp_error( $respuesta ) ? '' : (string) wp_remote_retrieve_body( $respuesta );
	$info      = '' === $cuerpo ? false : @getimagesizefromstring( $cuerpo ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	if ( ! $info || ! in_array( $info['mime'] ?? '', array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
		return false;
	}

	$temporal = wp_tempnam( 'cds-ig' );
	file_put_contents( $temporal, $cuerpo ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$editor = wp_get_image_editor( $temporal );
	if ( is_wp_error( $editor ) ) {
		// Servidor sin GD ni Imagick: se guarda el JPEG tal como lo entrega
		// Instagram (ya viene sin datos EXIF). Otros formatos se descartan.
		wp_delete_file( $temporal );
		if ( 'image/jpeg' !== $info['mime'] ) {
			return false;
		}
		return false !== file_put_contents( $destino, $cuerpo ) ? $archivo : false; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	$editor->resize( 800, 800, false );
	$editor->set_quality( 82 );
	$guardado = $editor->save( $destino, 'image/jpeg' );
	wp_delete_file( $temporal );
	return is_wp_error( $guardado ) ? false : $archivo;
}

/**
 * Trae las últimas publicaciones y renueva el token si toca.
 *
 * @return true|WP_Error
 */
function cds_ig_sincronizar() {
	$d = cds_ig_datos();
	if ( '' === $d['token'] ) {
		return new WP_Error( 'cds_ig_sin_token', 'Todavía no se ha pegado el token de Instagram.' );
	}

	$es_fb = 'facebook' === $d['modo'];

	// Renovación semanal (solo tokens de Instagram; el de página de Facebook no
	// se renueva así). Instagram exige que el token tenga al menos 24 horas.
	if ( ! $es_fb && time() - (int) $d['renovado'] > WEEK_IN_SECONDS ) {
		$nuevo = cds_ig_pedir( 'refresh_access_token', array( 'grant_type' => 'ig_refresh_token' ), $d['token'] );
		if ( ! is_wp_error( $nuevo ) && ! empty( $nuevo['access_token'] ) ) {
			$d['token']    = (string) $nuevo['access_token'];
			$d['vence']    = time() + (int) ( $nuevo['expires_in'] ?? 60 * DAY_IN_SECONDS );
			$d['renovado'] = time();
		}
	}

	$campos = array(
		'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
		'limit'  => CDS_IG_CANTIDAD,
	);
	if ( $es_fb ) {
		$perfil = array( 'username' => $d['usuario'] );
		$medios = ctype_digit( (string) $d['ig_id'] )
			? cds_ig_pedir( $d['ig_id'] . '/media', $campos, $d['token'], CDS_IG_API_FB )
			: new WP_Error( 'cds_ig_sin_id', 'Falta el id de la cuenta de Instagram. Pega el token de nuevo.' );
	} else {
		$perfil = cds_ig_pedir( 'me', array( 'fields' => 'username' ), $d['token'] );
		$medios = is_wp_error( $perfil ) ? $perfil : cds_ig_pedir( 'me/media', $campos, $d['token'] );
	}
	if ( is_wp_error( $medios ) ) {
		$d['error'] = $medios->get_error_message();
		cds_ig_guardar( $d );
		return $medios;
	}

	$lista = array();
	foreach ( (array) ( $medios['data'] ?? array() ) as $m ) {
		$id     = (string) ( $m['id'] ?? '' );
		$enlace = (string) ( $m['permalink'] ?? '' );
		$tipo   = (string) ( $m['media_type'] ?? '' );
		$imagen = 'VIDEO' === $tipo ? ( $m['thumbnail_url'] ?? '' ) : ( $m['media_url'] ?? '' );
		if ( ! ctype_digit( $id ) || 0 !== strpos( $enlace, 'https://www.instagram.com/' ) || ! $imagen ) {
			continue;
		}
		$archivo = cds_ig_descargar( $id, $imagen );
		if ( ! $archivo ) {
			continue;
		}
		$lista[] = array(
			'id'      => $id,
			'archivo' => $archivo,
			'enlace'  => esc_url_raw( $enlace ),
			'tipo'    => in_array( $tipo, array( 'IMAGE', 'VIDEO', 'CAROUSEL_ALBUM' ), true ) ? $tipo : 'IMAGE',
			'texto'   => wp_html_excerpt( sanitize_text_field( (string) ( $m['caption'] ?? '' ) ), 160, '…' ),
			'fecha'   => (string) ( $m['timestamp'] ?? '' ),
		);
	}

	// Se borran las copias de publicaciones que ya no están entre las últimas.
	$carpeta  = cds_ig_carpeta();
	$vigentes = wp_list_pluck( $lista, 'archivo' );
	foreach ( (array) glob( $carpeta['ruta'] . '/*.jpg' ) as $viejo ) {
		if ( ! in_array( basename( $viejo ), $vigentes, true ) ) {
			wp_delete_file( $viejo );
		}
	}

	$d['usuario']       = sanitize_user( (string) ( $perfil['username'] ?? '' ), true );
	$d['publicaciones'] = $lista;
	$d['actualizado']   = time();
	$d['error']         = '';
	cds_ig_guardar( $d );
	return true;
}

/* --- Tarea programada: cada 3 horas ----------------------------------------- */
add_filter(
	'cron_schedules',
	static function ( $horarios ) {
		$horarios['cds_cada_3_horas'] = array(
			'interval' => 3 * HOUR_IN_SECONDS,
			'display'  => 'Cada 3 horas',
		);
		return $horarios;
	}
);
add_action( 'cds_ig_tarea', 'cds_ig_sincronizar' );
add_action(
	'init',
	static function () {
		if ( ! wp_next_scheduled( 'cds_ig_tarea' ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, 'cds_cada_3_horas', 'cds_ig_tarea' );
		}
	}
);
add_action(
	'switch_theme',
	static function () {
		wp_clear_scheduled_hook( 'cds_ig_tarea' );
	}
);

/* --- Formulario del panel (Ajustes → Datos del sitio) ------------------------ */

/** Dibuja la sección "Instagram en la web". El token nunca se muestra. */
function cds_ig_panel() {
	$d = cds_ig_datos();
	echo '<hr><h2 id="instagram">Instagram en la web</h2>';
	echo '<p>Las publicaciones de Instagram aparecen solas en la web, en "Lo último que publicamos". Se revisa cada 3 horas. Sirve un token de Instagram (empieza con IGAA) o uno de Facebook (empieza con EAA).</p>';

	if ( isset( $_GET['cds_ig'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$estado = sanitize_key( wp_unslash( $_GET['cds_ig'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$textos = array(
			'ok'           => array( 'success', 'Instagram conectado y actualizado.' ),
			'desconectado' => array( 'success', 'Instagram desconectado. Se borraron el token y las fotos guardadas.' ),
			'error'        => array( 'error', $d['error'] ? $d['error'] : 'No se pudo actualizar.' ),
			'vacio'        => array( 'error', 'El token tiene un formato que no es válido. Cópialo de nuevo, completo.' ),
		);
		if ( isset( $textos[ $estado ] ) ) {
			echo '<div class="notice notice-' . esc_attr( $textos[ $estado ][0] ) . '"><p>' . esc_html( $textos[ $estado ][1] ) . '</p></div>';
		}
	}

	echo '<table class="form-table" role="presentation">';
	if ( $d['token'] ) {
		echo '<tr><th scope="row">Estado</th><td><strong>' . ( $d['error'] ? 'Conectado, pero con un problema (ver abajo)' : 'Conectado' ) . '</strong>';
		if ( $d['usuario'] ) {
			echo ' como @' . esc_html( $d['usuario'] );
		}
		echo '<br>Publicaciones guardadas: ' . (int) count( $d['publicaciones'] );
		if ( $d['actualizado'] ) {
			echo '<br>Última actualización: ' . esc_html( wp_date( 'j \d\e F, g:i a', (int) $d['actualizado'] ) );
		}
		if ( 'facebook' === $d['modo'] ) {
			echo '<br>Conectado a través de la página de Facebook. ' . ( $d['vence'] ? 'El token vence el ' . esc_html( wp_date( 'j \d\e F \d\e Y', (int) $d['vence'] ) ) . '.' : 'El token no vence.' );
		} elseif ( $d['vence'] ) {
			echo '<br>El token vence el ' . esc_html( wp_date( 'j \d\e F \d\e Y', (int) $d['vence'] ) ) . ' (se renueva solo antes).';
		}
		if ( $d['error'] ) {
			echo '<br><span style="color:#A3020B"><strong>Último error:</strong> ' . esc_html( $d['error'] ) . '</span>';
		}
		echo '</td></tr>';
	}
	echo '</table>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="cds_instagram">';
	wp_nonce_field( 'cds_instagram' );
	echo '<table class="form-table" role="presentation"><tr><th scope="row"><label for="cds-ig-token">' . ( $d['token'] ? 'Reemplazar token' : 'Token de Instagram' ) . '</label></th><td>';
	echo '<input class="regular-text" type="password" id="cds-ig-token" name="cds_ig_token" value="" autocomplete="off" spellcheck="false">';
	echo '<p class="description">Se genera en developers.facebook.com (guía paso a paso en el repositorio: docs/guia-instagram.md). Pégalo solo aquí: no lo compartas por chat ni correo.</p>';
	echo '</td></tr></table>';
	echo '<p>';
	submit_button( $d['token'] ? 'Guardar y actualizar' : 'Conectar Instagram', 'primary', 'cds_ig_guardar', false );
	if ( $d['token'] ) {
		echo ' ';
		submit_button( 'Actualizar ahora', 'secondary', 'cds_ig_actualizar', false );
		echo ' ';
		submit_button( 'Desconectar', 'delete', 'cds_ig_desconectar', false );
	}
	echo '</p></form>';
}

add_action(
	'admin_post_cds_instagram',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No tienes permiso para hacer esto.' );
		}
		check_admin_referer( 'cds_instagram' );
		$volver = admin_url( 'options-general.php?page=cds-ajustes' );

		if ( isset( $_POST['cds_ig_desconectar'] ) ) {
			$carpeta = cds_ig_carpeta();
			foreach ( (array) glob( $carpeta['ruta'] . '/*.jpg' ) as $foto ) {
				wp_delete_file( $foto );
			}
			delete_option( CDS_IG_OPCION );
			wp_safe_redirect( add_query_arg( 'cds_ig', 'desconectado', $volver ) . '#instagram' );
			exit;
		}

		$token = trim( (string) wp_unslash( $_POST['cds_ig_token'] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( '' !== $token ) {
			// Los tokens de Meta solo tienen letras, números y unos pocos signos.
			if ( ! preg_match( '/^[A-Za-z0-9_\-.|]{20,600}$/', $token ) ) {
				wp_safe_redirect( add_query_arg( 'cds_ig', 'vacio', $volver ) . '#instagram' );
				exit;
			}
			$d = cds_ig_datos();
			if ( 0 === strpos( $token, 'EA' ) ) {
				$conexion = cds_ig_conectar_facebook( $token );
				if ( is_wp_error( $conexion ) ) {
					$d['error'] = $conexion->get_error_message();
					cds_ig_guardar( $d );
					wp_safe_redirect( add_query_arg( 'cds_ig', 'error', $volver ) . '#instagram' );
					exit;
				}
				$d = array_merge( $d, $conexion, array( 'modo' => 'facebook', 'renovado' => time() ) );
			} else {
				$d['token']    = $token;
				$d['modo']     = 'instagram';
				$d['ig_id']    = '';
				$d['renovado'] = time();
				$d['vence']    = time() + 60 * DAY_IN_SECONDS;
			}
			cds_ig_guardar( $d );
		}

		$resultado = cds_ig_sincronizar();
		wp_safe_redirect( add_query_arg( 'cds_ig', is_wp_error( $resultado ) ? 'error' : 'ok', $volver ) . '#instagram' );
		exit;
	}
);

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
