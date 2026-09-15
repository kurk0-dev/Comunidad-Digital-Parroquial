# Comunidad Digital y Solidaria

Página web de la **Parroquia San Francisco Javier** (Ate Vitarte, Lima).

Incluye horarios de misa, servicios parroquiales, campañas de donación con su
avance, eventos, testimonios, formularios de contacto y de solicitud de ayuda,
y un bot de preguntas frecuentes.

---

## Para quien va a editar el contenido (no hace falta saber programar)

### La regla principal

**Todo lo que se puede cambiar está en un solo archivo: `content.js`.**

Horarios, textos, fotos, campañas de donación, eventos, testimonios y las
respuestas del bot. No necesitas abrir ningún otro archivo.

### Cómo editarlo

1. Abre `content.js` con el Bloc de notas, o mejor con
   [Visual Studio Code](https://code.visualstudio.com/) (gratis).
2. Busca el bloque que quieres cambiar. Cada uno tiene un comentario arriba que
   explica qué hace.
3. Cambia el texto **dentro de las comillas**.
4. Guarda el archivo.
5. Abre `index.html` con doble clic para ver el resultado.

### Tres reglas para no romper la página

1. Todo texto va entre comillas dobles: `nombre: "Padre Juan"`
2. Cada línea termina en coma, menos la última de cada bloque.
3. Si el texto lleva comillas dobles por dentro, usa comillas simples por fuera.

### La etiqueta amarilla "Ejemplo / Por confirmar"

Los datos que vienen de fábrica son **de ejemplo**. Cada bloque que todavía no
ha sido confirmado por la parroquia lleva esta línea:

```js
esPlaceholder: true
```

Eso hace que en la página aparezca una etiqueta amarilla que avisa al visitante
que ese dato aún no es oficial.

**Cuando confirmes un dato con el párroco:** corrige el texto y **borra esa
línea**. La etiqueta desaparece sola.

Esto existe para que la página nunca muestre información inventada como si
fuera oficial mientras se recoge la información real.

### Si algo se rompe

La página se queda en blanco o vacía casi siempre por una coma o una comilla
que falta. Para encontrarla:

1. Abre la página en el navegador.
2. Presiona `F12` y entra a la pestaña **Console**.
3. El error aparece en rojo, con el número de la línea.

---

## Lo que hay que configurar una sola vez

Estos datos vienen en blanco o con ejemplos. Se ponen en `content.js`, en el
bloque `contacto` del final:

| Qué | Dónde | Cómo se consigue |
|---|---|---|
| **Escudo de la parroquia** | archivo `assets/img/logo-parroquia.png` | Guarda ahí la imagen del escudo oficial, con ese nombre exacto. Mientras falte, la portada muestra un aviso |
| Número de WhatsApp | `contacto.whatsapp` | Código de país + número, sin `+` ni espacios. Perú: `51987654321` |
| Formulario de contacto | `contacto.googleFormContacto` | Crear en [Google Forms](https://forms.google.com) y copiar el enlace de "Enviar" |
| Formulario de ayuda | `contacto.googleFormAyuda` | Igual que el anterior. **Debe incluir una casilla de "Enviar de forma anónima"** que oculte el campo del nombre |
| Dirección exacta | `sobreNosotros.direccion` | Calle y número. El mapa ya apunta a la ubicación correcta |

Mientras los formularios no estén configurados, los botones de la página se
muestran desactivados con el texto "Formulario aún no configurado", en lugar de
llevar a un enlace roto.

**Importante sobre los Google Forms:** al crear cada formulario, entra a la
pestaña *Respuestas* y pulsa el ícono verde para vincularlo a una hoja de
Google Sheets. Así las respuestas se guardan solas y se pueden descargar en Excel.

---

## Para el equipo de desarrollo

### Estructura

El sitio tiene **6 pestañas**, cada una en su propio archivo:

```
index.html        Inicio          Escudo, accesos, historia, mapa, equipo
misas.html        Misas           Horarios, fechas especiales, QR, calendario
servicios.html    Servicios       Catequesis + sacramentos
donaciones.html   Donaciones      Campañas con avance, punto de entrega, QR
comunidad.html    Comunidad       Eventos y testimonios
contacto.html     Contacto        Google Forms y WhatsApp

content.js        Todo el contenido editable
styles.css        Estilos (los colores están arriba, en :root)
js/main.js        Cabecera, pie y renderizado de cada página
js/donaciones.js  Barras de avance de las campañas
js/bot.js         Bot de preguntas frecuentes
js/qr.js          Códigos QR
assets/patron-azulejo.svg  Fondo de azulejos
assets/favicon.svg         Ícono de la pestaña
assets/img/                Escudo de la parroquia y fotos
```

El **menú, el pie y el bot no están escritos en los HTML**: los genera
`js/main.js` y aparecen igual en las 6 páginas. Para cambiar el menú se edita
solo la lista `PAGINAS`, al inicio de ese archivo.

### Decisiones técnicas

- **Sin framework, sin build step, sin npm.** Se abre con doble clic y se
  despliega en Vercel sin compilar nada.
- **Sin backend ni base de datos.** Los formularios son Google Forms y las
  respuestas quedan en Google Sheets.
- **No se procesan pagos.** Las campañas de donación son informativas: muestran
  qué se necesita y cuánto falta. Las entregas son presenciales.
- **El contenido se renderiza con `textContent`, no con `innerHTML`**, para que
  un texto con `&`, `<` o comillas escrito por una persona no técnica no rompa
  la página.
- **Accesibilidad:** contraste AA verificado (el par de texto más bajo en uso es
  5.43:1, el terracota sobre crema; el dorado quedó descartado como color de
  texto porque solo daba 2.62:1), `alt` en las imágenes, objetivos táctiles de 44 px o más, foco
  visible, navegación por teclado en el bot y respeto a `prefers-reduced-motion`.
- **Tipografía:** Lora solo para los títulos, vía Google Fonts. El texto usa la
  fuente del sistema para que la página cargue rápido en celulares de gama baja.

### Cómo se despliega

El proyecto se publica en Vercel conectado a este repositorio de GitHub:

1. En [vercel.com](https://vercel.com) → **Add New Project**.
2. Importar este repositorio.
3. Framework Preset: **Other**. No hay comando de build ni carpeta de salida.
4. Deploy.

A partir de ahí, **cada `git push` a la rama principal publica los cambios
automáticamente**.

Para trabajar en local basta con abrir `index.html` en el navegador. Los códigos
QR son la única parte que no funciona así: necesitan una dirección pública, por
lo que muestran un aviso hasta que la página esté publicada.

---

## Créditos

Proyecto del curso de Gestión de Proyectos, **Universidad de Lima**, desarrollado
para la Parroquia San Francisco Javier de Ate Vitarte.
