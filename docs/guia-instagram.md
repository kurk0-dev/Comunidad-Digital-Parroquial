# Conectar Instagram con la web

Cuando esté listo, el grupo solo sube una foto a Instagram y, como mucho
6 horas después, aparece sola en la web, en la sección **"Lo último que
publicamos"** de Inicio y Comunidad.

## Cómo funciona (y por qué el token está a salvo)

```
Instagram ──(token secreto)──▶ GitHub Actions ──▶ data/instagram.json + fotos ──▶ la web
                                (cada 6 horas)        (en el repositorio)          (solo lee el JSON)
```

- El **token** es una contraseña que da acceso a la cuenta de Instagram.
  **Nunca va en el código**, porque el repositorio es público y cualquiera
  podría copiarlo.
- Vive como **secreto de GitHub** (`IG_TOKEN`). Está cifrado, no se ve ni
  siquiera desde la web de GitHub y, si alguna vez aparece en un registro,
  GitHub lo tapa con `***`.
- Una tarea automática (`.github/workflows/instagram.yml`) lo usa cada 6 horas
  para descargar las últimas 9 publicaciones y guardarlas en el sitio.
- La web solo lee `data/instagram.json`. **El navegador del visitante nunca ve
  el token ni se conecta con Instagram.**

---

## Paso 1 — Crear la cuenta de Instagram

1. Crea la cuenta del grupo, de preferencia con el **Gmail del grupo**.
2. Pásala a **cuenta profesional** (es gratis): *Configuración → Tipo de
   cuenta y herramientas → Cambiar a cuenta profesional* → **Creador** o
   **Empresa**. Sin esto, la API de Instagram no funciona.

## Paso 2 — Crear la app en Meta para desarrolladores

> Las pantallas de Meta cambian a menudo. Si algún botón tiene otro nombre,
> busca la opción equivalente: el flujo es el mismo.

1. Entra a <https://developers.facebook.com/apps> con una cuenta de Facebook
   y pulsa **Crear app**.
2. En el caso de uso elige **Administrar mensajes y contenido en Instagram**
   (*Instagram API with Instagram Login*).
3. Dentro de la app, ve a **Roles de la app → Roles → Evaluadores de
   Instagram** y agrega la cuenta del grupo.
4. Acepta la invitación **desde Instagram**: *Configuración → Permisos de
   sitios web → Apps y sitios web → Invitaciones de evaluador*.
5. Vuelve a la app: **Configuración de la API con inicio de sesión de
   Instagram → Generar tokens de acceso → Agregar cuenta**. Inicia sesión con
   la cuenta del grupo y acepta el permiso `instagram_business_basic`.
6. Copia el **token** que aparece. Es un token de larga duración: dura **60 días**.

## Paso 3 — Guardar el token como secreto en GitHub

1. Abre el repositorio en GitHub → **Settings → Secrets and variables →
   Actions → New repository secret**.
2. **Name:** `IG_TOKEN`
3. **Secret:** pega el token.
4. **Add secret**.

> ⚠️ Pega el token **solo ahí**. Nunca en `content.js`, en un commit, en un
> chat del equipo ni en un documento compartido.

## Paso 4 — Escribir el usuario en content.js

En `content.js`, dentro de `redes`:

```js
redes: {
  instagram: "nombre_de_usuario",   // sin @
  facebook: ""
},
```

Con eso aparece el enlace "Instagram" en el pie de todas las páginas.

## Paso 5 — Probarlo

1. En GitHub: pestaña **Actions → Actualizar Instagram → Run workflow**.
2. A los pocos segundos debe salir en verde y decir
   `Guardadas N publicaciones de @usuario`.
3. La tarea crea un commit, Vercel publica solo, y en 1 o 2 minutos las fotos
   aparecen en la web.

Si la cuenta todavía no tiene publicaciones, la sección queda oculta: nunca se
ve un recuadro vacío.

---

## Cada 60 días: renovar el token

Cuando el token vence, la tarea falla y **GitHub te avisa por correo**. La web
**no se rompe**: sigue mostrando las últimas publicaciones que sí se guardaron.

Para renovarlo:

1. Repite el **paso 2.5** (Generar tokens de acceso) y copia el token nuevo.
2. En GitHub: **Settings → Secrets and variables → Actions → IG_TOKEN →
   Update secret**, pega el nuevo y guarda.
3. **Actions → Actualizar Instagram → Run workflow** para comprobar que está
   en verde.

Consejo: pon un recordatorio en el calendario cada 55 días.

## Problemas comunes

| Mensaje en Actions | Qué hacer |
|---|---|
| `El token de Instagram venció o no es válido` | Renueva el token (arriba). |
| `IG_TOKEN no está configurado` | Falta el paso 3. |
| La tarea ya no se ejecuta sola | GitHub desactiva las tareas programadas tras **60 días sin actividad** en un repositorio público. Entra a **Actions → Actualizar Instagram → Enable workflow**. |
| Un video no aparece | Se muestra su miniatura. Si es un carrusel que empieza con un video, se omite: no hay foto que mostrar. |
