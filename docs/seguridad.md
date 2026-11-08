# Seguridad del tema de WordPress (auditoría 08/11/2026)

Revisión de QA / DevSecOps del tema `wordpress/cds-parroquia/` antes del despliegue.
Marco: OWASP Top 10 (2021) y buenas prácticas de WordPress.

## Resultado

| # | Hallazgo | Riesgo | Estado |
|---|---|---|---|
| 1 | El token de Instagram/Meta se guardaba **en claro** en la base de datos | Alto: una copia filtrada de la BD lo exponía | **Corregido**: cifrado AES-256-GCM con clave derivada de las sales de `wp-config.php`; los tokens antiguos se migran solos |
| 2 | El token viajaba **en la URL** de las llamadas a Facebook | Medio: las URL quedan en registros intermedios | **Corregido**: va en la cabecera `Authorization: Bearer` (graph.instagram.com sigue usando parámetro: es lo único que documenta) |
| 3 | `xmlrpc.php` abierto | Alto: puerta típica de fuerza bruta | **Corregido**: responde 403 |
| 4 | Enumeración de usuarios por `?author=1` y `/wp-json/wp/v2/users` | Medio: revela el usuario administrador | **Corregido**: redirige a Inicio y el endpoint no existe para visitantes |
| 5 | Sin política de seguridad de contenido (CSP) ni HSTS | Medio | **Corregido**: CSP (`object-src 'none'`, `frame-ancestors 'self'`, `base-uri`/`form-action 'self'`, sin comodines), HSTS con https, COOP |
| 6 | Editor de temas/plugins del panel activo | Alto si roban una sesión de admin | **Corregido**: desactivado desde el tema |
| 7 | Mensaje de error de inicio de sesión revelaba si el usuario existe | Bajo | **Corregido**: mensaje único |
| 8 | Emojis cargados desde s.w.org, cabeceras `X-Pingback`/`X-Powered-By`, versión de WordPress en el HTML | Bajo (privacidad / huella) | **Corregido** |
| 9 | Carpeta de fotos de Instagram sin `index.php` | Bajo: listado de archivos | **Corregido** |
| 10 | Llamadas a la API sin límite de tamaño ni bloqueo de redirecciones | Bajo | **Corregido**: 2 MB máximo, sin redirecciones |
| 11 | Fotos con rostros de posibles menores | Alto (Ley 29733, consentimiento) | **Corregido** en las fotos publicadas; **pendiente** limpiar el historial de Git (ver abajo) |

Ya estaban bien y se mantienen: toda salida escapada (`esc_html`, `esc_attr`, `esc_url`),
JavaScript solo con `textContent`, URLs de ajustes filtradas por `cds_url_segura()`, nonce +
`manage_options` en cada acción del panel, `wp_safe_remote_get` (bloquea IP internas, SSRF),
imágenes de Instagram validadas por dominio, firma y nombre numérico, CSV de donaciones solo
desde `docs.google.com/spreadsheets`, ninguna librería de terceros cargada desde CDN.

Historial de Git revisado: **ningún token, contraseña ni archivo sensible** fue versionado.

## Diseño del código (SOLID)

Cada archivo de `inc/` tiene una sola responsabilidad y `functions.php` solo los carga en orden:

```
inc/seguridad.php          Validación pura (URLs, dígitos)
inc/endurecimiento.php     Cabeceras HTTP y protecciones de WordPress
inc/ajustes.php            Ajustes → Datos del sitio
inc/enlaces.php            Mapa, botones, formularios, QR, calendario
inc/redes.php              Íconos y bloques de redes
inc/donaciones.php         Contador de la campaña
inc/instagram/almacen.php  Estado y token cifrado (única capa que toca la opción)
inc/instagram/api.php      Conexión con Meta y tarea programada
inc/instagram/panel.php    Sección del panel
inc/instagram/vista.php    [cds_instagram]
inc/bot.php                Preguntas del bot
inc/contenido-inicial.php  Herramientas → Contenido inicial
```

## Integración continua

`.github/workflows/calidad.yml` corre en cada cambio: sintaxis PHP y JS, banco de pruebas
(`wordpress/tests/prueba_tema.php`, 42 comprobaciones) y búsqueda de secretos
(`scripts/revisar-secretos.sh`). Permisos de solo lectura y `actions/checkout` fijado por SHA.

## Riesgos aceptados

- **Formulario de donación en la web ([cds_donar], 08/11/2026)**: envía a
  `docs.google.com/forms/.../formResponse`, por eso la CSP permite ese dominio solo en
  `connect-src` y `form-action` (no en scripts ni marcos). WordPress no guarda nada; los
  datos van a la misma hoja privada de siempre. El envío usa `no-cors`: Google no deja
  leer su respuesta, así que la web no puede confirmar el guardado. Se mitiga validando
  en el navegador con las mismas reglas que el formulario de Google.

- **CSP con `'unsafe-inline'` en scripts y estilos**: WordPress imprime scripts en línea
  (mapa de módulos de los bloques, datos del bot). Quitarlo rompería el sitio. Lo mitiga que
  no hay ningún dato de usuario sin escapar.
- **`debug_token`** recibe el token como parámetro (así lo exige la API de Meta). Solo se usa
  al conectar, por https.
- **Historial de Git**: 7 fotos del seminario existen sin difuminar en commits anteriores del
  repositorio público. Para borrarlas hay que reescribir el historial (decisión del equipo).

## Lista para el despliegue

- [ ] Hosting con **https** activo antes de pegar el token.
- [ ] Usuario administrador **distinto de "admin"** y contraseña larga y única.
- [ ] Ajustes → Generales: desmarcar "Cualquiera puede registrarse".
- [ ] Mantener WordPress actualizado; no instalar plugins que no se usen.
- [ ] Pegar el token solo en el panel; nunca en chats, correos ni capturas.
