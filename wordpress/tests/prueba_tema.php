<?php
/**
 * Banco de pruebas del tema SIN WordPress: simula las funciones mínimas que
 * usan los patrones y los atajos, y verifica su salida.
 * Uso: php wordpress/tests/prueba_tema.php wordpress/cds-parroquia <carpeta-de-salida>
 *
 * Lo corre GitHub Actions en cada cambio (.github/workflows/calidad.yml).
 */
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );
set_error_handler( static function ( $n, $m, $f, $l ) { throw new ErrorException( "$m ($f:$l)", 0, $n ); } );

$TEMA   = rtrim( $argv[1], '/\\' );
$SALIDA = rtrim( $argv[2], '/\\' );
define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'MB_IN_BYTES', 1048576 );

/* ---- Simulación mínima de WordPress ---- */
$GLOBALS['opciones']  = array();
$GLOBALS['atajos']    = array();
$GLOBALS['transient'] = array();
function esc_url( $u ) { return htmlspecialchars( (string) $u, ENT_QUOTES ); }
function esc_url_raw( $u, $p = null ) {
	$u = trim( (string) $u );
	$esquema = strtolower( (string) parse_url( $u, PHP_URL_SCHEME ) );
	return ( $p && ! in_array( $esquema, $p, true ) ) ? '' : $u;
}
function wp_http_validate_url( $u ) { return filter_var( $u, FILTER_VALIDATE_URL ) ? $u : false; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr__( $s, $d = '' ) { return esc_attr( $s ); }
function esc_textarea( $s ) { return esc_html( $s ); }
function get_theme_file_uri( $f = '' ) { return 'https://parroquia.test/wp-content/themes/cds-parroquia/' . ltrim( $f, '/' ); }
function get_theme_file_path( $f = '' ) { global $TEMA; return $TEMA . '/' . ltrim( $f, '/' ); }
function home_url( $p = '' ) { return 'https://parroquia.test' . $p; }
function get_permalink() { return 'https://parroquia.test/misas/'; }
function get_bloginfo( $k ) { return 'Parroquia San Francisco Javier'; }
function add_action() {}
function add_filter() {}
function remove_action() {}
function add_shortcode( $n, $f ) { $GLOBALS['atajos'][ $n ] = $f; }
function shortcode_atts( $def, $atts ) { return array_merge( $def, array_intersect_key( (array) $atts, $def ) ); }
function get_option( $k, $d = false ) { return $GLOBALS['opciones'][ $k ] ?? $d; }
function wp_parse_args( $a, $d ) { return array_merge( $d, $a ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function add_settings_error( $a, $b, $m ) { $GLOBALS['errores'][] = $m; }
function delete_transient( $k ) {}
function get_transient( $k ) { return false; }
function set_transient( $k, $v, $t ) {}
function wp_enqueue_script() {}
function wp_timezone() { return new DateTimeZone( 'America/Lima' ); }
function wp_timezone_string() { return 'America/Lima'; }
function sanitize_email( $s ) { return filter_var( trim( (string) $s ), FILTER_SANITIZE_EMAIL ); }
function is_email( $s ) { return (bool) filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
if ( ! class_exists( "WP_Error" ) ) { class WP_Error { public function __construct( $c = "", $m = "" ) {} public function get_error_message() { return ""; } } }
if ( ! function_exists( "is_wp_error" ) ) { function is_wp_error( $x ) { return $x instanceof WP_Error; } }
function wp_safe_remote_get( $u, $a = array() ) { $GLOBALS['ultima_peticion'] = array( $u, $a ); return new WP_Error( "sin_red", "sin red en las pruebas" ); }
function add_query_arg( $p, $u ) { return $p ? $u . ( false === strpos( $u, '?' ) ? '?' : '&' ) . http_build_query( $p ) : $u; }
function wp_salt( $s = '' ) { return 'sal-de-prueba-' . $s; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opciones'][ $k ] = $v; return true; }
function wp_upload_dir() { return array( 'basedir' => sys_get_temp_dir(), 'baseurl' => 'https://parroquia.test/wp-content/uploads' ); }
function trailingslashit( $s ) { return rtrim( $s, "/" ) . "/"; }
function wp_next_scheduled() { return true; }
function remove_accents( $s ) { return iconv( 'UTF-8', 'ASCII//TRANSLIT', $s ); }

define( 'CDS_VERSION', 'prueba' );
foreach ( array( 'seguridad', 'endurecimiento', 'ajustes', 'enlaces', 'redes', 'donaciones', 'donar', 'instagram/almacen', 'instagram/api', 'instagram/panel', 'instagram/vista' ) as $modulo ) {
	require $TEMA . '/inc/' . $modulo . '.php';
}

$ok = true;
function comprobar( $nombre, $cond ) { global $ok; $ok = $ok && $cond; echo ( $cond ? "PASA " : "FALLA" ) . "  $nombre\n"; }

/* ---- 1. Patrones: se ejecutan y su salida se guarda para revisarla ---- */
foreach ( glob( $TEMA . '/patterns/*.php' ) as $patron ) {
	ob_start();
	include $patron;
	file_put_contents( $SALIDA . '/' . basename( $patron, '.php' ) . '.html', ob_get_clean() );
}
echo "Patrones renderizados: " . count( glob( $SALIDA . '/*.html' ) ) . "\n";

/* ---- 2. Validación de ajustes (seguridad) ---- */
$s = cds_sanear_ajustes( array(
	'whatsapp'       => '+51 987-654-321',
	'facebook'       => 'javascript:alert(1)',
	'instagram'      => 'https://instagram.com/parroquia',
	'form_ayuda'     => 'data:text/html,<script>alert(1)</script>',
	'coordenadas'    => 'esto no son coordenadas',
	'csv_donaciones' => 'https://evil.example.com/hoja.csv',
	'necesidades'    => "Arroz | 50 | 32 | kg\n<script>x</script> | 10 | 1 | u",
) );
comprobar( 'WhatsApp queda solo en dígitos', '51987654321' === $s['whatsapp'] );
comprobar( 'Facebook "javascript:" rechazado', '' === $s['facebook'] );
comprobar( 'Formulario "data:" rechazado', '' === $s['form_ayuda'] );
comprobar( 'Instagram https aceptado', 'https://instagram.com/parroquia' === $s['instagram'] );
comprobar( 'Coordenadas inválidas: se conserva el valor anterior', '-12.0405377,-76.925658' === $s['coordenadas'] );
comprobar( 'CSV que no es de Google rechazado', '' === $s['csv_donaciones'] );
comprobar( 'Etiquetas HTML quitadas de la lista de necesidades', false === strpos( $s['necesidades'], '<script>' ) );

/* ---- 3. Atajos ---- */
$A = $GLOBALS['atajos'];
$GLOBALS['opciones'][ CDS_OPCION ] = array( 'whatsapp' => '', 'form_contacto' => '' );
comprobar( 'WhatsApp sin número: botón apagado', false !== strpos( $A['cds_whatsapp']( array() ), 'WhatsApp por confirmar' ) );
comprobar( 'WhatsApp opcional sin número: no aparece', '' === $A['cds_whatsapp']( array( 'opcional' => 'si' ) ) );
comprobar( 'Formulario sin enlace: apagado', false !== strpos( $A['cds_formulario']( array( 'tipo' => 'contacto' ) ), 'aún no configurado' ) );
comprobar( 'Tipo de formulario inexistente: apagado', false !== strpos( $A['cds_formulario']( array( 'tipo' => 'otro' ) ), 'aún no configurado' ) );

$GLOBALS['opciones'][ CDS_OPCION ] = array( 'whatsapp' => '51987654321' );
comprobar( 'WhatsApp con número: enlace a wa.me', false !== strpos( $A['cds_whatsapp']( array() ), 'https://wa.me/51987654321?text=' ) );

$mapa = $A['cds_mapa']();
comprobar( 'Mapa: iframe de Google y capa que abre Maps', false !== strpos( $mapa, 'output=embed' ) && false !== strpos( $mapa, 'maps/search/?api=1' ) );

$cal = $A['cds_calendario']( array( 'hora' => '10:00', 'dia' => 'domingo' ) );
comprobar( 'Calendario: evento con zona horaria de Lima', false !== strpos( $cal, 'calendar.google.com' ) && false !== strpos( $cal, 'America%2FLima' ) );
comprobar( 'Calendario: hora mal escrita no genera botón', '' === $A['cds_calendario']( array( 'hora' => 'diez' ) ) );

$GLOBALS['opciones'][ CDS_OPCION ] = array( 'necesidades' => "Arroz | 50 | 32 | kg\nAceite | 30 | 30 | litros\nMal | abc | 1 | u\nCero | 0 | 1 | u" );
$don = $A['cds_donaciones']();
comprobar( 'Donaciones: 64 % de arroz', false !== strpos( $don, 'aria-valuenow="64"' ) );
comprobar( 'Donaciones: meta cumplida con texto (no solo color)', false !== strpos( $don, 'Meta cumplida' ) );
comprobar( 'Donaciones: descarta filas inválidas y metas en cero', 2 === substr_count( $don, '<li class="cds-campana' ) );
comprobar( 'Donaciones: lista sin confirmar lleva etiqueta', false !== strpos( $don, 'is-style-por-confirmar' ) );

$GLOBALS['opciones'][ CDS_OPCION ] = array( 'facebook' => 'https://www.facebook.com/grupo.divinonino/', 'instagram' => '' );
$redes = $A['cds_redes']();
comprobar( 'Redes: Facebook aparece, Instagram vacío no', false !== strpos( $redes, 'Facebook' ) && false === strpos( $redes, 'Instagram' ) );
comprobar( 'Redes con mapa="no": sin enlace Cómo llegar', false === strpos( $A['cds_redes']( array( 'mapa' => 'no' ) ), 'Cómo llegar' ) && false !== strpos( $redes, 'Cómo llegar' ) );
comprobar( 'Ubicación por defecto: Capilla Divino Niño', 'Capilla Divino Niño' === cds_ajustes_por_defecto()['etiqueta_mapa'] );


/* ---- 4. Redes e Instagram ---- */
comprobar( 'Datos por defecto: redes y correo del grupo', 'https://www.instagram.com/grupo.divinonino/' === cds_ajustes_por_defecto()['instagram'] && 'diocesisdechosica.zona3@gmail.com' === cds_ajustes_por_defecto()['correo'] );
$GLOBALS['opciones'] = array( CDS_OPCION => array( 'instagram' => 'https://www.instagram.com/grupo.divinonino/', 'facebook' => 'javascript:alert(1)', 'correo' => 'diocesisdechosica.zona3@gmail.com', 'whatsapp' => '' ) );
$sig = $A['cds_siguenos']();
comprobar( 'Síguenos: Instagram con @usuario sacado de la URL', false !== strpos( $sig, '@grupo.divinonino' ) );
comprobar( 'Síguenos: Facebook "javascript:" no aparece', false === strpos( $sig, 'javascript' ) && false === strpos( $sig, 'cds-red--facebook' ) );
comprobar( 'Síguenos: correo como mailto y sin WhatsApp vacío', false !== strpos( $sig, 'mailto:diocesisdechosica.zona3@gmail.com' ) && false === strpos( $sig, 'cds-red--whatsapp' ) );
comprobar( 'Instagram sin publicaciones: la sección no aparece', '' === $A['cds_instagram']( array() ) );
$GLOBALS['opciones'][ CDS_IG_OPCION ] = array(
	'publicaciones' => array(
		array( 'id' => '1', 'archivo' => '1.jpg', 'enlace' => 'https://www.instagram.com/p/x/', 'tipo' => 'VIDEO', 'texto' => 'Hola <b>', 'fecha' => '' ),
		array( 'id' => '2', 'archivo' => '../../wp-config.php', 'enlace' => 'https://www.instagram.com/p/y/', 'tipo' => 'IMAGE', 'texto' => '', 'fecha' => '' ),
		array( 'id' => '3', 'archivo' => '3.jpg', 'enlace' => 'https://evil.example/p/', 'tipo' => 'IMAGE', 'texto' => '', 'fecha' => '' ),
	),
);
$feed = $A['cds_instagram']( array( 'limite' => 6 ) );
comprobar( 'Instagram: solo publicaciones válidas (descarta rutas raras y enlaces ajenos)', 1 === substr_count( $feed, '<li>' ) && false === strpos( $feed, 'wp-config' ) && false === strpos( $feed, 'evil' ) );
comprobar( 'Instagram: el texto se escapa', false === strpos( $feed, '<b>' ) );
$GLOBALS['opciones'][ CDS_OPCION ]['instagram'] = 'https://www.instagram.com/nombre_viejo/';
$GLOBALS['opciones'][ CDS_IG_OPCION ]            = array( 'token' => 'x', 'usuario' => 'grupo.divinonino' );
comprobar( 'Instagram: si la cuenta cambió de nombre, el enlace usa el usuario conectado', 'https://www.instagram.com/grupo.divinonino/' === cds_ig_perfil() );
$GLOBALS['opciones'][ CDS_IG_OPCION ] = array( 'token' => 'x', 'usuario' => 'malo"><script>' );
comprobar( 'Instagram: un usuario raro no se usa (queda el de los ajustes)', 'https://www.instagram.com/nombre_viejo/' === cds_ig_perfil() );
$GLOBALS['opciones'][ CDS_OPCION ]['instagram'] = '';
$GLOBALS['opciones'][ CDS_IG_OPCION ]            = array( 'token' => 'x', 'usuario' => 'grupo.divinonino' );
comprobar( 'Instagram: con el campo vacío no hay enlace aunque esté conectado', '' === cds_ig_perfil() );
comprobar( 'Instagram: imágenes solo del CDN de Meta y por https', cds_ig_origen_valido( 'https://scontent.cdninstagram.com/a.jpg' ) && ! cds_ig_origen_valido( 'https://cdninstagram.com.evil.net/a.jpg' ) && ! cds_ig_origen_valido( 'http://x.fbcdn.net/a.jpg' ) );
/* ---- 5. Seguridad del token y de las cabeceras ---- */
$cifrado = cds_ig_cifrar( 'EAApruebaSecreta123' );
comprobar( 'Token: se guarda cifrado (no aparece en claro)', 0 === strpos( $cifrado, CDS_IG_PREFIJO_CIFRADO ) && false === strpos( $cifrado, 'EAApruebaSecreta123' ) );
comprobar( 'Token: se descifra igual', 'EAApruebaSecreta123' === cds_ig_descifrar( $cifrado ) );
$alterado = substr( $cifrado, 0, -4 ) . ( 'AAAA' === substr( $cifrado, -4 ) ? 'BBBB' : 'AAAA' );
comprobar( 'Token: si alguien lo altera en la base de datos, no se acepta', '' === cds_ig_descifrar( $alterado ) );
comprobar( 'Token: dos cifrados del mismo token son distintos (IV aleatorio)', cds_ig_cifrar( 'x-token' ) !== cds_ig_cifrar( 'x-token' ) );
$GLOBALS['opciones'][ CDS_IG_OPCION ] = array( 'token' => 'EAAtokenViejoEnClaro' );
$datos = cds_ig_datos();
comprobar( 'Token: el de una versión anterior (en claro) se migra a cifrado', 'EAAtokenViejoEnClaro' === $datos['token'] && 0 === strpos( $GLOBALS['opciones'][ CDS_IG_OPCION ]['token'], CDS_IG_PREFIJO_CIFRADO ) );
cds_ig_pedir( 'me', array( 'fields' => 'id' ), 'TOKENSECRETO', CDS_IG_API_FB );
list( $url_pedida, $args_pedidos ) = $GLOBALS['ultima_peticion'];
comprobar( 'API de Facebook: el token va en la cabecera, no en la URL', false === strpos( $url_pedida, 'TOKENSECRETO' ) && 'Bearer TOKENSECRETO' === ( $args_pedidos['headers']['Authorization'] ?? '' ) );
comprobar( 'API: sin redirecciones y con tamaño máximo de respuesta', 0 === $args_pedidos['redirection'] && $args_pedidos['limit_response_size'] > 0 );
function is_ssl() { return true; }
$csp = cds_politica_csp();
comprobar( 'CSP: sin objetos, sin marcos ajenos, base y formularios propios', false !== strpos( $csp, "object-src 'none'" ) && false !== strpos( $csp, "frame-ancestors 'self'" ) && false !== strpos( $csp, "base-uri 'self'" ) && false !== strpos( $csp, "form-action 'self'" ) );
comprobar( 'CSP: ningún comodín (*) en los orígenes', ! preg_match( '/(^|\s)\*(\s|;|$)/', $csp ) );
comprobar( 'CSP: con https, obliga a pedir todo por https', false !== strpos( $csp, 'upgrade-insecure-requests' ) );
$cal = html_entity_decode( $A['cds_calendario']( array( 'dia' => 'Sábado', 'hora' => '19:30' ) ) );
preg_match( '/dates=(\d{8})T/', $cal, $fecha_cal );
comprobar( 'Calendario: "Sábado" con mayúscula y tilde cae en sábado', isset( $fecha_cal[1] ) && '6' === DateTimeImmutable::createFromFormat( 'Ymd', $fecha_cal[1] )->format( 'N' ) );

$donar = $A['cds_donar']();
preg_match_all( '/<input id="cds-donar-p[0-9]+" name="([^"]+)" type="number"[^>]*min="1"/', $donar, $cantidades );
comprobar( 'Donar: envía al formulario de Google por https (formResponse)', 0 === strpos( CDS_DONAR_ACCION, 'https://docs.google.com/forms/d/e/' ) && false !== strpos( $donar, 'action="' . CDS_DONAR_ACCION . '"' ) );
comprobar( 'Donar: una cantidad numérica (mínimo 1) por producto, con su código entry', 12 === count( $cantidades[1] ) && 12 === count( array_filter( $cantidades[1], static function ( $n ) { return (bool) preg_match( '/^entry\.[0-9]+$/', $n ); } ) ) );
$nombres_avance = array_map( static function ( $l ) { return trim( explode( '|', $l )[0] ); }, explode( "\n", cds_ajustes_por_defecto()['necesidades'] ) );
comprobar( 'Donar: los productos se llaman igual que en la hoja de avance', array_column( cds_donar_config()['productos'], 0 ) === $nombres_avance );
comprobar( 'Donar: sin código en línea ni innerHTML en el script', false === strpos( file_get_contents( $TEMA . '/assets/js/donar.js' ), '.innerHTML' ) && false === strpos( $donar, '<script' ) );
comprobar( 'CSP: Google Forms solo para enviar (connect-src y form-action)', false !== strpos( $csp, "connect-src 'self' https://docs.google.com" ) && false !== strpos( $csp, "form-action 'self' https://docs.google.com" ) && false === strpos( $csp, 'frame-src https://docs.google.com' ) );
echo "\nRESULTADO: " . ( $ok ? "TODO PASA" : "HAY FALLOS" ) . "\n";
exit( $ok ? 0 : 1 );
