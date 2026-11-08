# Instagram → Facebook → la web

Meta: **publicar una sola vez, en Instagram**, y que eso aparezca solo en Facebook y en
la web.

```
            ┌──(Meta lo comparte solo)──▶ Facebook
Instagram ──┤
            └──(WordPress lo trae cada 3 h)──▶ la web: "Lo último que publicamos"
```

Cuentas del grupo:

- Gmail: diocesisdechosica.zona3@gmail.com (dueño de todo)
- Instagram: https://www.instagram.com/diocesischosica_grupo3/
- Facebook: https://www.facebook.com/diocesis.de.chosica.grupo.3

> Las pantallas de Meta cambian seguido. Si un botón tiene otro nombre, busca la opción
> equivalente: el camino es el mismo.

---

## Parte A: Instagram en cuenta profesional (5 minutos)

Sin esto no funciona ni lo de Facebook ni lo de la web. Es gratis.

1. Instagram → tu perfil → **☰ → Configuración y actividad → Tipo de cuenta y
   herramientas → Cambiar a cuenta profesional**.
2. Categoría: **Organización religiosa** (o "Comunidad").
3. Tipo: **Empresa**. Puedes saltar los pasos de contacto.

## Parte B: que cada publicación de Instagram salga sola en Facebook

### B.1 Revisa que el Facebook sea una **página**, no un perfil personal

Abre facebook.com/diocesis.de.chosica.grupo.3 desde otra cuenta:

- Si ves **"Seguir" o "Me gusta"** → es una página. Sigue con B.2.
- Si ves **"Agregar a amigos"** → es un perfil personal. Facebook no permite perfiles
  para grupos u organizaciones. Desde ese perfil: **☰ → Páginas → Crear página
  nueva**, con el nombre del grupo y la categoría "Organización religiosa". Usa esa
  página desde ahora.

### B.2 Conectar Instagram con la página

1. Instagram → **☰ → Centro de cuentas → Cuentas → Agregar cuentas → Facebook**.
   Entra con la cuenta que administra la página.
2. Centro de cuentas → **Compartir en varios perfiles** (o "Compartir entre perfiles")
   → elige la cuenta de Instagram → destino: **la página de Facebook**.
3. Activa **Compartir automáticamente**: publicaciones ✅ y reels ✅
   (historias, si quieren).
4. Prueba: publica algo en Instagram. Al momento de publicar debe aparecer activado
   **"Compartir en Facebook"**. En un par de minutos sale en la página.

Un cuidado: los reels con música de la biblioteca de Instagram a veces no se comparten
en Facebook por derechos de autor. Si alguno no aparece, súbelo a mano.

## Parte C: que las publicaciones salgan solas en la web

La web (el tema de WordPress) pide las últimas 12 publicaciones a la API oficial de
Instagram cada 3 horas, guarda una copia pequeña de cada foto en el propio sitio y las
muestra en **Inicio** y en **Comunidad**. Para eso necesita un **token**: una llave que
da permiso de **solo lectura** sobre la cuenta.

### C.1 Crear la app en Meta para desarrolladores

1. Entra a <https://developers.facebook.com/apps> con la cuenta de Facebook que
   administra la página → **Crear app**.
2. Caso de uso: **Administrar mensajes y contenido en Instagram** (*Instagram API
   with Instagram Login*). Tipo: **Empresa**. Nombre: "Web Divino Niño".
3. Dentro de la app: **Roles de la app → Roles → Evaluadores de Instagram → Agregar**
   y escribe `diocesischosica_grupo3`.
4. Acepta la invitación **desde Instagram**: Configuración → **Permisos de sitios
   web → Apps y sitios web → Invitaciones de evaluador → Aceptar**.
5. Vuelve a la app: **Configuración de la API con inicio de sesión de Instagram →
   Generar tokens de acceso → Agregar cuenta**. Inicia sesión con la cuenta del
   grupo y acepta el permiso `instagram_business_basic`.
6. Pulsa **Generar token** y cópialo. Dura 60 días, **pero el tema lo renueva solo
   cada semana**: no hay que volver a generarlo mientras la web esté funcionando.

#### Si Meta solo te da un token que empieza con `EAA` (inicio de sesión con Facebook o "Unified login")

También sirve: el tema busca la página de Facebook conectada al Instagram y usa el
token de esa página. Para que funcione:

1. El Facebook del grupo tiene que ser una **página** (parte B.1) y estar conectado al
   Instagram profesional.
2. Al generar el token, en la ventana "Elige las páginas", **marca la página del grupo**
   (y el negocio, si lo pregunta).
3. Permisos que deben estar aceptados: `instagram_basic`, `pages_show_list`,
   `pages_read_engagement` y `business_management`.
4. Si el token dura solo 1 o 2 horas, el panel lo rechaza. Ve a
   <https://developers.facebook.com/tools/debug/accesstoken/>, pégalo, pulsa
   **Depurar** y abajo **Extender token de acceso**. Pega ese nuevo token en WordPress.
   Así el token de la página que guarda el tema **no vence**.

La app puede quedarse en "modo desarrollo": como solo lee la cuenta propia del grupo,
no necesita la revisión de Meta.

### C.2 Pegar el token en WordPress

1. WordPress → **Ajustes → Datos del sitio** → baja hasta **Instagram en la web**.
2. Pega el token → **Conectar Instagram**.
3. Debe decir **"Conectado como @diocesischosica_grupo3"** y cuántas publicaciones
   guardó. Abre Inicio: aparece "Lo último que publicamos" y las fotos dentro del
   bloque rojo de Instagram en "Síguenos en redes".

> ⚠️ El token se pega **solo ahí**. Nunca en un chat, un correo, un documento ni en
> GitHub: el repositorio es público. El panel nunca lo vuelve a mostrar.

### Cómo se cuida el token y la web

- Vive en la base de datos de WordPress, no en el código.
- El visitante nunca se conecta con Instagram: ve fotos servidas por la propia web.
- Cada foto se revisa antes de guardarse: que venga del servidor de Instagram, que sea
  una imagen de verdad y que el enlace lleve a instagram.com.
- Si Instagram falla, la web sigue mostrando lo último que guardó y el error aparece
  solo en el panel.
- Si no hay publicaciones, la sección no aparece: nunca se ve un recuadro vacío.

## Problemas comunes

| En el panel dice | Qué hacer |
|---|---|
| "El token de Instagram venció o no es válido" | Repite C.1 paso 6 y pégalo de nuevo. Pasa si la web estuvo más de 60 días sin visitas o si se cambió la contraseña de Instagram. |
| "No se pudo conectar con Instagram" | El hosting no dejó salir la conexión. Prueba **Actualizar ahora** más tarde. Si sigue, el hosting bloquea conexiones salientes: hay que cambiar de plan u hosting. |
| Publicaste y no aparece | WordPress revisa cada 3 horas y solo cuando alguien visita la web. Para verlo ya: **Actualizar ahora**. |
| Un video sale como foto | Es normal: se muestra su portada con un ícono de "play" y al tocarla se abre en Instagram. |

---

## Versión estática anterior (sin WordPress)

La versión HTML de la raíz del repositorio usaba otra ruta: el token como secreto de
GitHub `IG_TOKEN` y la tarea `.github/workflows/instagram.yml` cada 6 horas. Solo
aplica si se vuelve a publicar esa versión en Vercel; con WordPress no hace falta.
