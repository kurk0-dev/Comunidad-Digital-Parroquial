# Comunidad Digital y Solidaria

Sitio web informativo + bot FAQ para el **Grupo de Oración Divino Niño Jesús** — Renovación
Carismática Católica (RCC), **Diócesis de Chosica, Zona 3**. El grupo se reúne en la
**Capilla Divino Niño** (Lima). Proyecto del curso de Gestión de Proyectos, Universidad de Lima.

**Foco del sitio:** el grupo de oración es el protagonista. La capilla (misas, servicios,
catequesis) es secundaria. Usuarios finales: miembros del grupo y vecinos,
**la mayoría entra desde celular**; muchos son adultos mayores.

Lo mantiene Joaquín. El contenido lo aprobará el padre cuando el sitio exista.

---

## Reglas duras (no negociables)

1. **Sin backend, sin base de datos, sin login, sin procesamiento de pagos.**
   Las donaciones son solo físicas e informativas: la web nunca recibe ni mueve dinero.
2. **Sin framework y sin build step.** HTML + CSS + JavaScript vanilla.
   Debe desplegarse en Vercel sin ningún paso de build.
3. **Gratuito de operar.** Nada que requiera plan de pago o servicio externo facturable.
4. **Mantenible sin conocimientos técnicos.** **Todo** el contenido editable vive en
   `content.js`. Si algo que el grupo podría querer cambiar queda escrito en el HTML o el CSS,
   está mal hecho. Excepciones automáticas: Instagram (`data/instagram.json`, lo escribe una
   tarea) y el avance de donaciones (CSV de Google Sheets).
5. **No inventar datos como si fueran oficiales.** Lo no confirmado lleva `esPlaceholder: true`.
6. **Ningún secreto en el código.** El repositorio es **público**. Tokens y claves van como
   secretos de GitHub, nunca en archivos.

---

## Stack

| Capa | Elección |
|---|---|
| Frontend | HTML + CSS + JS vanilla |
| Estilos | CSS puro con variables en `:root` |
| Tipografía | Lexend variable, alojada en `assets/fonts/` (sin Google Fonts) |
| Contenido | `content.js`, único archivo editable por no-programadores |
| Bot FAQ | Widget 100% client-side que responde desde `content.js`, con fallback a WhatsApp |
| QR | `qrcode.js` con copia local en `js/vendor/` |
| Instagram | GitHub Actions cada 6 h → `data/instagram.json` + fotos en `assets/instagram/` |
| Donaciones | Google Form → Sheet → pestaña "Avance" publicada como CSV → `js/donaciones.js` |
| Hosting | Vercel (plan gratuito) |

---

## Estructura de archivos

```
index.html        Inicio: escudo grande, tarjeta "Nos reunimos", ¿primera vez?, seminario, Instagram, mapa
grupo.html        Grupo de oración: RCC, historia, Seminario de Vida en el Espíritu, servidores
capilla.html      Capilla: misas, fechas especiales, QR, catequesis, servicios
comunidad.html    Comunidad: Instagram, galería del seminario, eventos, testimonios
solidaridad.html  Solidaridad: campaña de Navidad con avance, cómo donar, QR, pedir ayuda
contacto.html     Contacto: WhatsApp, formulario, cómo llegar

content.js        TODO el contenido editable
styles.css        Estilos (paleta en :root)
js/main.js        Cabecera, pie, ayudantes (urlSegura, urlWhatsapp, icono…) y render
js/donaciones.js  Barras de avance; lee el CSV de Sheets si está configurado
js/instagram.js   "Lo último que publicamos"; valida cada publicación del JSON
js/bot.js         Bot FAQ (crea su propio marcado)
js/qr.js          Códigos QR
data/instagram.json              Generado por la tarea. No editar a mano
scripts/instagram_feed.py        Script de la tarea (solo stdlib)
.github/workflows/instagram.yml  Tarea programada
docs/             Guías de Instagram y de Google Sheets
images/           Fotos originales sin seleccionar — IGNORADO por Git (rostros, menores)
```

**Cabecera, pie y bot NO se repiten en el HTML.** Los genera JavaScript. Para cambiar el menú
se edita **solo** la lista `PAGINAS` al inicio de `js/main.js`.

Cada `<body>` declara su página con `data-pagina="..."` (de ahí sale `aria-current`).
Cada página carga solo los scripts que usa: `qrcode.min.js` + `qr.js` solo en capilla y
solidaridad, `instagram.js` en inicio y comunidad, `donaciones.js` en solidaridad.
`main.js` comprueba con `typeof` antes de llamarlos.

Los botones se conectan por atributos, no por id: `data-form="contacto|ayuda|donacion"`,
`data-whatsapp="mensaje"`, `data-maps`, `data-privacidad`, `data-qr="pagina.html"`,
`data-seminario="resumen|completo"`, `data-instagram data-limite="N"`.

Las URLs antiguas (`misas.html`, `servicios.html`, `donaciones.html`) redirigen en `vercel.json`.

---

## Contrato de `content.js`

Bloques: `grupo`, `portada`, `reunion`, `primeraVez[]`, `quienesSomos`, `seminario`
(`temas[]` vacío → se muestran "Viernes 1…7"), `galeria[]` (`foto`, `alt`, `pie`),
`servidores`, `redes`, `capilla`, `horariosMisa[]`, `horariosEspeciales[]`, `servicios[]`,
`catequesis`, `donaciones` (`csvAvance`, `formularioDonacion`, `articulos[]`), `eventos[]`,
`testimonios[]`, `faqBot[]`, `botFallback`, `contacto`, `privacidad`.

**Ubicación.** `capilla.coordenadas` (`"-12.0405377,-76.925658"`, Capilla Divino Niño) es la
**única** fuente: de ahí salen el mapa, los botones `data-maps` y el enlace del pie. No uses
enlaces cortos `maps.app.goo.gl`: caducan.

**WhatsApp.** `51900000000` es el número de ejemplo y tiene formato de celular peruano válido:
`urlWhatsapp()` lo trata como "sin configurar" y apaga los botones. No quites esa protección.

**`esPlaceholder: true`** muestra la etiqueta "Ejemplo / Por confirmar".

---

## Requisitos no funcionales

- **Mobile-first**, 100% responsive.
- **Accesible**: contraste AA medido, `alt` en todas las imágenes, un `<h1>` por página,
  navegación por teclado, objetivos táctiles ≥ 44 px, `prefers-reduced-motion`.
- El formulario de ayuda ofrece **anonimato**; el de donaciones **no** (se necesita contacto).
- Aviso de privacidad en **letra pequeña** (`data-privacidad`), presente pero sin estorbar.
- **Fotos:** solo con permiso. Mientras no haya criterio sobre niños, **ninguna foto con
  menores**. Se publican copias WebP ≤ 1200 px **sin EXIF** en `assets/img/`.

---

## Diseño — skills a usar por defecto

| Cuándo | Skill |
|---|---|
| Paleta, tipografía, guías UX | `ui-ux-pro-max` |
| Que no quede con pinta de plantilla genérica | `design-taste-frontend` |
| Dirección visual sobria y editorial | `minimalist-ui` |
| Revisar HTML/CSS contra buenas prácticas | `web-design-guidelines` |
| Accesibilidad (es un NFR explícito) | `accesslint-scan`, `accesslint-audit` |
| Desplegar a Vercel | `all-deploy` |

**Criterio estético**: cálido, alegre y confiable, sin estridencias. Público con adultos
mayores y celulares de gama baja: animación mínima, peso bajo, nada de `background-attachment: fixed`.

### Paleta (sacada del escudo del grupo, contraste medido)

| Variable | Color | Uso |
|---|---|---|
| `--rojo` | `#CF040D` | Cabecera, botones primarios (blanco encima 5.71:1), texto sobre crema (5.35:1) |
| `--rojo-oscuro` | `#A3020B` | Pie, acentos de texto, hover |
| `--azul` | `#1B4F7A` | Enlaces, franja de Instagram (blanco 8.59:1, amarillo 5.29:1) |
| `--azul-marca` | `#2078C0` | **Solo decorativo**: líneas, filetes (como texto 3.67:1, no pasa) |
| `--verde` | `#2F6229` | "Meta cumplida" y estados |
| `--verde-marca` | `#418239` | **Solo decorativo** (como texto 4.39:1, no pasa) |
| `--amarillo` | `#F6C526` | Tarjeta "Nos reunimos" con tinta encima (9.86:1). **Nunca texto sobre claro** (1.5:1) |
| `--crema` / `--tarjeta` | `#FBF7F0` / `#FFFDF9` | Fondo de página / tarjetas |
| `--tinta` / `--tinta-suave` | `#2A1F1D` / `#5C4F4B` | Texto |

> **Nunca distingas algo solo por rojo frente a verde** (daltonismo): acompaña con texto.

### Identidad

Escudo oficial del grupo: `assets/img/escudo.webp` (rojo, cruz verde, líneas azules, paloma
blanca con halo amarillo). Imagen del seminario: `assets/img/paloma-llamas.webp`.
**No redibujes ni sustituyas el escudo.** El de la Parroquia San Francisco Javier se retiró:
falta confirmar su relación con la capilla.

---

## Seguridad (auditado 2026-09-15 y 2026-10-10 — no revertir sin pensarlo)

1. **Cero código de terceros en ejecución.** Nada de `<script src="https://...">`.
2. **`urlSegura()`** filtra toda URL de `content.js` antes de un `href`/`src`.
3. **`vercel.json`**: CSP con `script-src 'self'`, `font-src 'self'`,
   `connect-src 'self' https://docs.google.com https://*.googleusercontent.com` (solo el CSV
   de Sheets), `frame-src https://www.google.com` (mapa), `frame-ancestors 'none'`,
   `form-action 'none'`. Recurso externo nuevo = añadirlo a la CSP y volver a probar las 6 páginas.
4. **`textContent`, nunca `innerHTML`.**
5. **Instagram sin exponer el token.** El token vive en el secreto de GitHub `IG_TOKEN`.
   `scripts/instagram_feed.py` nunca lo imprime (errores sin URL, `from None`), valida que el
   id sea numérico (nombre de archivo), que la imagen venga del CDN de Instagram y que el
   archivo sea una imagen real por su firma. `js/instagram.js` vuelve a validar en el
   navegador (rutas `assets/instagram/<dígitos>.ext`, enlaces solo a instagram.com).
   El workflow tiene `permissions: contents: write` y `actions/checkout` fijado por SHA.
   **Nunca** pidas, muestres ni escribas el token en un archivo o en el chat.
6. **CSV de donaciones**: solo se acepta `https://docs.google.com/spreadsheets/...`. Se
   publica **solo la pestaña "Avance"**: la de respuestas tiene nombres y teléfonos.
7. **`.gitignore`** excluye `images/` (fotos originales con rostros y menores) y secretos.
   **`.vercelignore`** evita publicar `scripts/`, `.github/`, `docs/`, `images/` y los `.md`.

**Pendiente de privacidad:** el formulario de ayuda debe tener desactivado «Recopilar
direcciones de correo electrónico» y el nombre como opcional; si no, la promesa de anonimato
es falsa.

---

## Despliegue

Claude **no** despliega directo a Vercel. Commit y `git push origin main`; Vercel publica solo
cuando el repo esté conectado (Add New Project → Import → Framework Preset: Other).

---

## Repositorio

`https://github.com/kurk0-dev/Comunidad-Digital-Parroquial` (**público**, rama `main`).

---

## Pendientes del equipo

- [x] Repositorio en GitHub.
- [x] Escudo del grupo y fotos del Seminario de Vida en el Espíritu.
- [x] Ubicación: Capilla Divino Niño (-12.0405377, -76.925658).
- [ ] Conectar el repo a Vercel.
- [ ] Crear el Gmail del grupo (dueño de Forms, Sheets e Instagram).
- [ ] Instagram del grupo + secreto `IG_TOKEN` (`docs/guia-instagram.md`).
- [ ] Formulario y hoja de donaciones (`docs/guia-donaciones-sheets.md`).
- [ ] Formularios de contacto y de ayuda (anónimo).
- [ ] WhatsApp del coordinador y su horario de atención.
- [ ] Día y hora de la reunión semanal.
- [ ] Los 7 temas del seminario (uno se ve en las fotos: "El pecado y sus consecuencias") y fecha del próximo.
- [ ] Relación Capilla Divino Niño ↔ Parroquia San Francisco Javier; ¿la catequesis aplica?
- [ ] Horarios de misa, confesiones y servicios de la capilla.
- [ ] Lista real de la campaña de Navidad (productos, metas, lo que no se recibe, horarios de entrega).
- [ ] Historia del grupo, misión oficial (la actual es provisional) y servidores con permiso.
- [ ] Criterio sobre fotos de niños.
- [ ] Corregir "Google Sites vs. Vercel" en el Business Case.
