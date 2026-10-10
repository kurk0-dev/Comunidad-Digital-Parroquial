# Comunidad Digital y Solidaria

Página web del **Grupo de Oración Divino Niño Jesús** — Renovación Carismática
Católica (RCC), Diócesis de Chosica, Zona 3. El grupo se reúne en la **Capilla
Divino Niño**.

Incluye la reunión semanal, el Seminario de Vida en el Espíritu, los horarios
de la capilla, las últimas publicaciones de Instagram, la campaña de donaciones
con su avance, la solicitud de ayuda anónima y un bot de preguntas frecuentes.

---

## Para quien va a editar el contenido (no hace falta saber programar)

### La regla principal

**Todo lo que se puede cambiar está en un solo archivo: `content.js`.**

Horarios, textos, fotos, la campaña de donaciones, eventos, testimonios y las
respuestas del bot. No necesitas abrir ningún otro archivo.

Hay dos excepciones, que **se actualizan solas** y no se escriben a mano:

- **Instagram**: basta con publicar en la cuenta del grupo.
  → [`docs/guia-instagram.md`](docs/guia-instagram.md)
- **El avance de las donaciones**, si se conecta la hoja de Google.
  → [`docs/guia-donaciones-sheets.md`](docs/guia-donaciones-sheets.md)

### Cómo editarlo

1. Abre `content.js` con [Visual Studio Code](https://code.visualstudio.com/) (gratis).
2. Busca el bloque que quieres cambiar. Cada uno tiene un comentario arriba que
   explica qué hace.
3. Cambia el texto **dentro de las comillas**.
4. Guarda el archivo, haz commit y push. Vercel publica los cambios solo.

### Tres reglas para no romper la página

1. Todo texto va entre comillas dobles: `nombre: "Hna. María"`
2. Cada línea termina en coma, menos la última de cada bloque.
3. Si el texto lleva comillas dobles por dentro, usa comillas simples por fuera.

### La etiqueta amarilla "Ejemplo / Por confirmar"

Cada bloque que todavía no ha sido confirmado por el grupo lleva esta línea:

```js
esPlaceholder: true
```

Eso muestra en la página una etiqueta amarilla que avisa que el dato aún no es
oficial. **Cuando confirmes un dato** con el coordinador o con el padre:
corrige el texto y **borra esa línea**. La etiqueta desaparece sola.

### Fotos

- Las fotos van en `assets/img/` en formato `.webp`, de máximo 1200 px de ancho.
- **Solo se publican fotos con permiso** de quienes aparecen.
- Mientras el grupo no defina un criterio sobre fotos de niños, **no se publican
  fotos donde aparezcan menores de edad**.
- La carpeta `images/` (fotos originales sin seleccionar) **no se sube a
  GitHub**: el repositorio es público.

### Si algo se rompe

La página se queda en blanco casi siempre por una coma o una comilla que falta.
Abre la página, presiona `F12`, entra a **Console** y busca la línea en rojo.

---

## Lo que falta configurar

| Qué | Dónde | Cómo |
|---|---|---|
| Número de WhatsApp del coordinador | `contacto.whatsapp` | Código de país + número, sin `+` ni espacios: `51987654321`. Mientras siga el de ejemplo, los botones de WhatsApp salen apagados |
| Formulario de contacto | `contacto.googleFormContacto` | Crear en [Google Forms](https://forms.google.com) con el Gmail del grupo |
| Formulario de ayuda | `contacto.googleFormAyuda` | El nombre debe ser **opcional** y debe estar **desactivado** "Recopilar direcciones de correo electrónico". Si no, la promesa de anonimato de la página es falsa |
| Formulario de donaciones + avance | `donaciones.formularioDonacion`, `donaciones.csvAvance` | [`docs/guia-donaciones-sheets.md`](docs/guia-donaciones-sheets.md) |
| Instagram | `redes.instagram` + secreto `IG_TOKEN` | [`docs/guia-instagram.md`](docs/guia-instagram.md) |

Los botones de formularios sin configurar salen apagados con el texto
"Formulario aún no configurado", en lugar de llevar a un enlace roto.

---

## Para el equipo de desarrollo

### Estructura

```
index.html        Inicio          Escudo, reunión semanal, ¿primera vez?, seminario, Instagram, mapa
grupo.html        Grupo           RCC, historia, Seminario de Vida en el Espíritu, servidores
capilla.html      Capilla         Misas, fechas especiales, QR, catequesis, servicios
comunidad.html    Comunidad       Instagram, galería del seminario, eventos, testimonios
solidaridad.html  Solidaridad     Campaña de Navidad con avance, cómo donar, QR, pedir ayuda
contacto.html     Contacto        WhatsApp, formulario, cómo llegar

content.js        Todo el contenido editable
styles.css        Estilos (colores en :root)
js/main.js        Cabecera, pie y renderizado de cada página
js/donaciones.js  Barras de avance (lee el CSV de Google Sheets si está configurado)
js/instagram.js   "Lo último que publicamos" (lee data/instagram.json)
js/bot.js         Bot de preguntas frecuentes
js/qr.js          Códigos QR (librería local en js/vendor/)

data/instagram.json             Lo escribe la tarea automática, no se edita a mano
assets/instagram/               Fotos de Instagram descargadas por la tarea
scripts/instagram_feed.py       Script de la tarea (solo biblioteca estándar)
.github/workflows/instagram.yml Tarea programada cada 6 horas
docs/                           Guías de Instagram y de Google Sheets
```

El **menú, el pie y el bot no están escritos en los HTML**: los genera
`js/main.js`. Para cambiar el menú se edita solo la lista `PAGINAS`.

### Decisiones técnicas

- **Sin framework, sin build step, sin npm.** Se despliega en Vercel sin compilar.
- **Sin backend ni base de datos.** Formularios en Google Forms, respuestas en
  Google Sheets. Instagram llega por una tarea de GitHub Actions, no por un servidor.
- **Ningún secreto en el código.** El token de Instagram vive como secreto de
  GitHub y el navegador nunca lo ve.
- **No se procesan pagos.** Solo donaciones físicas.
- **`textContent`, nunca `innerHTML`**, para pintar contenido.
- **Accesibilidad:** contraste AA medido en todos los pares de texto (el más
  bajo, 5.02:1), `alt` en todas las imágenes, objetivos táctiles de 44 px o
  más, foco visible y respeto a `prefers-reduced-motion`.
- **Tipografía:** Lexend, alojada en el propio sitio (39 KB), pensada para
  leer con poco esfuerzo.

### Cómo se despliega

En [vercel.com](https://vercel.com) → **Add New Project** → importar este
repositorio → Framework Preset **Other**, sin comando de build → Deploy.
Desde ahí, cada `git push` a `main` publica solo.

Para probar en local, levanta un servidor (`python -m http.server`): el feed de
Instagram, el CSV y los QR no funcionan abriendo el archivo con doble clic.

---

## Créditos

Proyecto del curso de Gestión de Proyectos, **Universidad de Lima**, desarrollado
para el Grupo de Oración Divino Niño Jesús.
