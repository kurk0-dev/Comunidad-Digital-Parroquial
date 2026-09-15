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
| QR | `qrcode.js` por CDN, generados en el navegador |
| Hosting | Vercel (plan gratuito) |
| Formularios | Google Forms + Google Sheets (respuestas caen solas en un Sheet) |

---

## Estructura de archivos

```
index.html          Página única, secciones con anclas de navegación
content.js          TODO el contenido editable
styles.css          Estilos globales
js/main.js          Nav, scroll, animaciones
js/donaciones.js    Renderiza barras de progreso de campañas
js/bot.js           Lógica del bot FAQ
js/qr.js            Genera los códigos QR
assets/img/         Logo, directiva, galería, testimonios
assets/favicon.ico
README.md           Instrucciones de edición para no-programadores
```

---

## Secciones (en este orden)

1. **Inicio** — logo, misión, enlaces a Instagram/Facebook
2. **Sobre nosotros** — historia, directiva con fotos, mapa embebido (iframe de Google Maps)
3. **Misas y horarios** — domingos, Semana Santa/Cuaresma, misas de difuntos + botón "Agregar a Google Calendar"
4. **Servicios parroquiales** — bautizos, catequesis, confesiones, consejería (requisitos y horarios)
5. **Solicitud de donaciones** — necesidades vigentes + barra de progreso por campaña (ej. "32/50 kg de arroz") + QR
6. **Eventos y actividades** — calendario simple, descripción, galería de eventos pasados
7. **Comunidades en acción** — testimonios (solo con consentimiento)
8. **Contacto / Solicitud de ayuda** — dos botones que abren en pestaña nueva sus Google Forms respectivos
9. **Bot FAQ flotante** — botón fijo abajo a la derecha, visible en todas las secciones

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

---

## Despliegue

Claude **no** despliega directo a Vercel. El flujo es:

1. Claude Code genera/actualiza los archivos localmente.
2. El equipo sube el proyecto a un repo de GitHub (uno lo crea, los demás hacen `git push`).
3. En Vercel: *Add New Project* → importar ese repo → deploy automático.
4. Desde ahí, cada `git push` a la rama principal despliega solo.

El historial de Git además sirve como evidencia de control de versiones para el curso.

---

## Pendientes del equipo (no bloquean el desarrollo)

- [ ] Inicializar el repositorio Git y subirlo a GitHub — **aún no está inicializado**.
- [ ] Número de WhatsApp real de la parroquia (por ahora, placeholder).
- [ ] Crear los 2 Google Forms (contacto general y solicitud de ayuda), cada uno con su
      Sheet de respuestas vinculado, y pegar las URLs en `content.js`.
- [ ] Corregir la inconsistencia **Google Sites vs. Vercel** en el Business Case: debe decir
      Vercel en ambas secciones.
- [ ] Recopilar contenido real con el párroco (fotos, horarios confirmados, textos) y quitar
      progresivamente las banderas `esPlaceholder`.
