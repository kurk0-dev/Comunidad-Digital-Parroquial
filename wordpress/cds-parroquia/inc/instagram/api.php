<?php
/**
 * Instagram — conexión con la API de Meta y sincronización programada.
 *
 * Pide las publicaciones, valida cada foto (id numérico, CDN de Meta, imagen
 * real) y guarda una copia de 800 px en uploads/cds-instagram/. Si falla, la web
 * sigue mostrando lo último que guardó y el error queda solo en el panel.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

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
	$opciones = array(
		'timeout'             => 15,
		'redirection'         => 0,
		'limit_response_size' => 2 * MB_IN_BYTES,
	);
	if ( CDS_IG_API_FB === $api ) {
		// La API de Facebook acepta el token en la cabecera: así no viaja en la URL
		// (las URL quedan en registros de servidores intermedios).
		$opciones['headers'] = array( 'Authorization' => 'Bearer ' . $token );
	} else {
		// graph.instagram.com solo documenta el token como parámetro.
		$params['access_token'] = $token;
	}
	$respuesta = wp_safe_remote_get( add_query_arg( $params, $api . $ruta ), $opciones );
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
 * @param string $ig_id Id de la cuenta de Instagram (opcional; necesario con
 *                      tokens de "System User", que no ven páginas).
 * @return array|WP_Error
 */
function cds_ig_conectar_facebook( $token, $ig_id = '' ) {
	// Camino directo: el token lee la cuenta de Instagram indicada.
	if ( ctype_digit( (string) $ig_id ) ) {
		$cuenta = cds_ig_pedir( $ig_id, array( 'fields' => 'id,username' ), $token, CDS_IG_API_FB );
		if ( is_wp_error( $cuenta ) ) {
			return $cuenta;
		}
		if ( (string) ( $cuenta['id'] ?? '' ) === (string) $ig_id ) {
			return array(
				'token'   => $token,
				'ig_id'   => (string) $ig_id,
				'usuario' => sanitize_user( (string) ( $cuenta['username'] ?? '' ), true ),
				'vence'   => 0,
			);
		}
	}

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
 * Crea la carpeta de fotos con un index.php vacío, para que el servidor no
 * liste su contenido aunque tenga el listado de directorios activado.
 *
 * @return bool
 */
function cds_ig_preparar_carpeta() {
	$ruta = cds_ig_carpeta()['ruta'];
	if ( ! wp_mkdir_p( $ruta ) ) {
		return false;
	}
	if ( ! file_exists( $ruta . '/index.php' ) ) {
		file_put_contents( $ruta . '/index.php', "<?php\n// Silencio.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	return true;
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
	if ( ! cds_ig_origen_valido( $url ) || ! cds_ig_preparar_carpeta() ) {
		return false;
	}
	$respuesta = wp_safe_remote_get( $url, array( 'timeout' => 20, 'limit_response_size' => 8 * MB_IN_BYTES ) );
	$cuerpo    = is_wp_error( $respuesta ) ? '' : (string) wp_remote_retrieve_body( $respuesta );
	$info      = '' === $cuerpo ? false : @getimagesizefromstring( $cuerpo ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	if ( ! $info || ! in_array( $info['mime'] ?? '', array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
		return false;
	}

	// wp_tempnam() vive en wp-admin: en la tarea programada y en la web pública no
	// está cargada, y sin esto ninguna foto se guardaba.
	if ( ! function_exists( 'wp_tempnam' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
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
		// Se vuelve a leer el usuario: si la cuenta cambió de nombre, la web lo toma.
		$perfil = ctype_digit( (string) $d['ig_id'] ) ? cds_ig_pedir( $d['ig_id'], array( 'fields' => 'username' ), $d['token'], CDS_IG_API_FB ) : null;
		if ( ! is_array( $perfil ) || empty( $perfil['username'] ) ) {
			$perfil = array( 'username' => $d['usuario'] );
		}
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

	cds_ig_preparar_carpeta();
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
