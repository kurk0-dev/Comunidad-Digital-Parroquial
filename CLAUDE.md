# Comunidad Digital y Solidaria

Sitio web informativo + bot FAQ para la **Parroquia San Francisco Javier** (Ate Vitarte, Lima).
Proyecto del curso de Gestión de Proyectos, Universidad de Lima. Cliente real: la parroquia.
Usuarios finales: feligreses, **la mayoría entra desde celular**.

---

## Reglas duras (no negociables)

1. **Sin backend, sin base de datos, sin login, sin procesamiento de pagos.**
   Las donaciones son solo informativas: se muestran necesidades y avance, nunca se cobra.
2. **Sin framework y sin build step.** HTML + CSS + JavaScript vanilla.
   Debe desplegarse en Vercel sin ningún paso de build.
3. **Gratuito de operar.** Nada que requiera plan de pago o servicio externo facturable.
4. **Mantenible sin conocimientos técnicos.** Este es el requisito que justifica toda la
   arquitectura: **todo** el contenido editable vive aislado en `content.js`. Si algo que la
   parroquia podría querer cambiar (un horario, un texto, una foto, una respuesta del bot)
   queda escrito en el HTML o el CSS, está mal hecho.
5. **No inventar datos de la parroquia como si fueran oficiales.** Los datos de ejemplo se
   marcan con `esPlaceholder: true` (ver abajo).

---

## Stack

| Capa | Elección |
|---|---|
| Frontend | HTML + CSS + JS vanilla |
| Estilos | CSS puro con variables en `:root` (Tailwind por CDN solo si hace falta acelerar) |
| Contenido | `content.js`, único archivo editable por no-programadores |
| Bot FAQ | Widget de chat 100% client-side que responde desde `content.js`, con fallback a WhatsApp (`wa.me`) |
| QR | `qrcode.js` con copia local en `js/vendor/`, generados en el navegador |
| Hosting | Vercel (plan gratuito) |
| Formularios | Google Forms + Google Sheets (respuestas caen solas en un Sheet) |

---

## Estructura de archivos

```
index.html            Inicio: escudo grande, accesos, sobre nosotros, mapa
misas.html            Horarios, fechas especiales, QR, Google Calendar
servicios.html        Catequesis + sacramentos y servicios
donaciones.html       Campañas con barra de avance, punto de entrega, QR
comunidad.html        Eventos próximos, galería de pasados, testimonios
contacto.html         Google Forms, WhatsApp

content.js            TODO el contenido editable
styles.css            Estilos globales (paleta en :root)
js/main.js            Cabecera, pie, ayudantes y render de cada página
js/donaciones.js      Barras de avance de campañas
js/bot.js             Bot FAQ (crea su propio marcado)
js/qr.js              Códigos QR
assets/patron-azulejo.svg   Fondo de azulejos de todas las páginas
assets/favicon.svg
assets/img/           Escudo de la parroquia, fotos
README.md             Instrucciones de edición para no-programadores
```

**Cabecera, pie y bot NO se repiten en el HTML.** Los genera JavaScript
(`renderCabecera`, `renderPie`, `construirMarcadoBot`) y se insertan en las 6
páginas. Para añadir o quitar una pestaña del menú se edita **solo** la lista
`PAGINAS` al inicio de `js/main.js`. Nunca dupliques el menú en los HTML: eso
obligaría a editar 6 archivos para cambiar un enlace.

Cada `<body>` declara en qué página está con `data-pagina="..."`. De ahí sale
el `aria-current="page"` que resalta la pestaña activa.

---

## Las 6 pestañas

1. **Inicio** (`index.html`) — escudo grande, misión, accesos a las demás pestañas, historia, mapa, equipo pastoral
2. **Misas** (`misas.html`) — horarios por día, fechas especiales, QR y botón de Google Calendar
3. **Servicios** (`servicios.html`) — catequesis de iniciación cristiana (inscripciones) + bautizos, confesiones, consejería
4. **Donaciones** (`donaciones.html`) — campañas con barra de avance, punto de entrega, QR
5. **Comunidad** (`comunidad.html`) — eventos próximos, galería de pasados, testimonios
6. **Contacto** (`contacto.html`) — Google Forms (contacto y ayuda anónima) y WhatsApp

El **bot FAQ flotante** aparece en las 6.

---

## Contrato de `content.js`

```js
const CONTENT = {
  mision: "...",
  redes: { instagram: "...", facebook: "..." },
  directiva:        [{ nombre, cargo, foto }],
  horariosMisa:     [{ dia, horas: [] }],
  servicios:        [{ nombre, requisitos: [], horario }],
  campanasDonacion: [{ item, meta, actual, unidad }],
  eventos:          [{ titulo, fecha, descripcion, foto }],
  testimonios:      [{ texto, autor }],
  faqBot:           [{ pregunta, respuesta }],
  contacto: {
    whatsapp: "51900000000",
    googleFormContacto: "https://forms.gle/...",
    googleFormAyuda:    "https://forms.gle/..."
  }
};
```

**Ubicación.** `sobreNosotros.coordenadas` (por ejemplo `"-12.0365691,-76.928581"`) es la
**única** fuente de la ubicación: de ahí salen el mapa incrustado, el botón, la capa clicable
sobre el mapa y el enlace del pie. No uses enlaces cortos `maps.app.goo.gl`: caducan y dejan
el botón roto.

**Bandera `esPlaceholder`.** Cualquier objeto de `content.js` acepta la propiedad opcional
`esPlaceholder: true`. Cuando está presente, la página debe mostrar junto a ese contenido una
etiqueta visual discreta **"Ejemplo / Por confirmar"**. Sirve para no presentar información
inventada como si fuera oficial mientras se recopilan los datos reales con el párroco.
Todo dato que no haya sido confirmado por la parroquia va con esta bandera.

---

## Requisitos no funcionales

- **Mobile-first**, 100% responsive. El celular es el caso principal, no el secundario.
- **Accesible**: contraste adecuado, `alt` en todas las imágenes, navegación por teclado,
  targets táctiles cómodos.
- Contenido neutral, claro e inclusivo.
- El formulario de solicitud de ayuda debe ofrecer **anonimato** (checkbox que oculta el campo nombre).
- Sin dependencias que requieran npm o compilación.

---

## Diseño — skills a usar por defecto

Para no repetirlo cada sesión: al crear o revisar cualquier cosa visual en este proyecto,
apóyate en estas skills instaladas.

| Cuándo | Skill |
|---|---|
| Paleta, tipografía, guías UX (usar `--stack html-tailwind` o `web`) | `ui-ux-pro-max` |
| Que la landing no quede con pinta de plantilla genérica | `design-taste-frontend` |
| Dirección visual sobria y editorial (encaja con una parroquia) | `minimalist-ui` |
| Revisar el HTML/CSS ya escrito contra buenas prácticas | `web-design-guidelines` |
| Verificar accesibilidad (es un NFR explícito, no un extra) | `accesslint-scan`, `accesslint-audit` |
| Desplegar a Vercel | `all-deploy` |

**Criterio estético**: cálido, sobrio y confiable. Nada de estética brutalista, dark-tech ni
animaciones pesadas — el público incluye adultos mayores y celulares de gama baja. La animación
debe ser mínima y respetar `prefers-reduced-motion`. Peso de página bajo: sin librerías grandes.

### Paleta (definida por el cliente)

Azul marino litúrgico + dorado + crema, tomada de la referencia visual que dio el equipo.
Está en `:root` de `styles.css`.

| Variable | Color | Uso |
|---|---|---|
| `--azul` | `#14304D` | Cabecera, pie, títulos, chips de hora |
| `--azul-oscuro` | `#0E2438` | Fondo del pie |
| `--dorado` | `#B8912F` | Filetes, bordes, **fondo** de botón primario |
| `--terracota` | `#9C4A2F` | Color de acento **para texto** |
| `--crema` | `#F7F1E3` | Fondo de página |
| `--crema-clara` | `#FCF8EF` | Fondo de tarjetas |

> **Nunca uses el dorado como color de texto sobre crema.** Da 2.62:1 y no cumple AA.
> El dorado va en bordes y como fondo de botón (azul sobre dorado = 4.57:1, sí cumple).
> El acento de texto es el terracota (5.43:1). Todos los demás pares están por encima de 5:1.

### Fondo

Todas las páginas llevan el patrón de azulejos `assets/patron-azulejo.svg` (3 KB, se repite
cada 160px). No hay fondos blancos lisos. Para cambiarlo por una foto real, se edita una sola
`url()` en la regla `body` de `styles.css`.

### Identidad

El escudo oficial de la parroquia va en `assets/img/logo.png` y se muestra **grande**
en la portada. Si el archivo falta, la portada muestra un aviso con el nombre exacto que se
espera, en vez de una imagen rota. **No redibujes ni sustituyas el escudo**: es la identidad
oficial de la parroquia.

---

## Seguridad (auditado 2026-09-15 — no revertir sin pensarlo)

El sitio pasó una auditoría de seguridad antes de publicarse. Estas cuatro
decisiones son deliberadas:

1. **Cero código de terceros en ejecución.** La librería de QR vive en
   `js/vendor/qrcode.min.js`, no en un CDN, y solo se carga en las 2 páginas que
   tienen QR. **No vuelvas a poner una etiqueta `<script src="https://...">`**:
   rompería la CSP y reintroduciría el riesgo de que un CDN comprometido ejecute
   código en la página.
2. **`urlSegura()` en `js/main.js`.** Toda dirección que venga de `content.js` y
   acabe en un `href` o un `src` pasa por ahí: solo admite `http:`, `https:` y
   rutas internas. Impide que una dirección `javascript:` escrita por error en
   `content.js` ejecute código. No la quites ni la rodees.
3. **`vercel.json` define las cabeceras**, incluida una CSP con
   `script-src 'self'` (sin `unsafe-inline` ni `unsafe-eval`),
   `frame-ancestors 'none'` y `form-action 'none'`. Si algún día hace falta un
   recurso externo nuevo, hay que añadirlo explícitamente a la CSP y volver a
   probar las 6 páginas.
4. **`textContent`, nunca `innerHTML`**, para pintar contenido de `content.js`.

**Pendiente de privacidad (no es código):** la página promete que la solicitud de
ayuda puede enviarse de forma anónima. Eso solo es cierto si el Google Form tiene
**desactivado** «Recopilar direcciones de correo electrónico» y no obliga a
iniciar sesión. Verificarlo antes de publicar los enlaces.

Informe completo: `cyber-neo-report-Comunidad-Digital-2026-09-15.md` (en el
Escritorio, fuera del repositorio).

---

## Despliegue

Claude **no** despliega directo a Vercel. El flujo es:

1. Claude Code genera/actualiza los archivos localmente.
2. El equipo sube el proyecto a un repo de GitHub (uno lo crea, los demás hacen `git push`).
3. En Vercel: *Add New Project* → importar ese repo → deploy automático.
4. Desde ahí, cada `git push` a la rama principal despliega solo.

El historial de Git además sirve como evidencia de control de versiones para el curso.

---

## Repositorio

`https://github.com/kurk0-dev/Comunidad-Digital-Parroquial` (privado, rama `main`).
Todos los cambios se guardan ahí: commit y `git push origin main`.

---

## Pendientes del equipo (no bloquean el desarrollo)

- [x] Inicializar el repositorio Git y subirlo a GitHub.
- [x] Ubicación real de la parroquia (mapa ya apunta a -12.0365691, -76.928581).
- [x] Facebook oficial: `facebook.com/parroquiasfjlima`.
- [x] Datos de catequesis (del afiche oficial; por eso no llevan `esPlaceholder`).
- [x] Escudo oficial guardado en `assets/img/logo.png`.
- [ ] Conectar el repo a Vercel (Add New Project → Import → Framework Preset: Other).
- [ ] Número de WhatsApp real de la parroquia (por ahora, placeholder).
- [ ] Dirección exacta (calle y número) e historia de la parroquia.
- [ ] Instagram, si la parroquia tiene (queda oculto mientras esté vacío).
- [ ] Crear los 2 Google Forms (contacto general y solicitud de ayuda), cada uno con su
      Sheet de respuestas vinculado, y pegar las URLs en `content.js`.
- [ ] Corregir la inconsistencia **Google Sites vs. Vercel** en el Business Case: debe decir
      Vercel en ambas secciones.
- [ ] Recopilar contenido real con el párroco (fotos, horarios confirmados, textos) y quitar
      progresivamente las banderas `esPlaceholder`.
