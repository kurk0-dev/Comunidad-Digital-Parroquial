<?php
/**
 * Instagram — sección del panel (Ajustes → Datos del sitio → Instagram en la web).
 *
 * Formulario aparte del resto de ajustes: el token nunca viaja con ellos.
 * Cada acción exige manage_options y un nonce.
 *
 * @package cds
 */

defined( 'ABSPATH' ) || exit;

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
	echo '</td></tr>';
	echo '<tr><th scope="row"><label for="cds-ig-id">ID de la cuenta de Instagram</label></th><td>';
	echo '<input class="regular-text" type="text" inputmode="numeric" id="cds-ig-id" name="cds_ig_id" value="' . esc_attr( $d['ig_id'] ) . '" autocomplete="off">';
	echo '<p class="description">Solo con tokens que empiezan con EAA. Es el número que Meta muestra debajo del nombre de la cuenta, en "Generar tokens de acceso" (empieza con 1784). No es secreto.</p>';
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
				$ig_id    = substr( preg_replace( '/\D/', '', (string) wp_unslash( $_POST['cds_ig_id'] ?? '' ) ), 0, 30 ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$conexion = cds_ig_conectar_facebook( $token, $ig_id );
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
