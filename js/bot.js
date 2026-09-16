/* =============================================================================
   bot.js — Bot de preguntas frecuentes (100% en el navegador)
   -----------------------------------------------------------------------------
   No hay servidor ni inteligencia artificial detrás: el bot compara lo que
   escribe la persona con las palabras de "claves" en CONTENT.faqBot y responde
   con el texto que ya escribió la parroquia.

   Para agregar o cambiar respuestas, edita la lista faqBot en content.js.
   Si el bot no encuentra nada, ofrece escribir por WhatsApp.

   El botón y el panel se crean desde aquí y se añaden solos a cada página,
   así el mismo bot aparece en las 6 pestañas sin repetir el HTML.
   ============================================================================= */

/**
 * Normaliza un texto para poder compararlo:
 * pasa a minúsculas, quita tildes y signos de puntuación.
 * Así "¿A qué hora?" y "a que hora" se consideran iguales.
 */
function normalizarTexto(texto) {
  return (texto || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")   // quita tildes
    .replace(/[¿?¡!.,;:()"']/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

/**
 * Busca la mejor respuesta para la pregunta.
 * Gana la entrada que coincide con la clave más larga: así "primera comunion"
 * le gana a "comunion" y la respuesta es más precisa.
 */
function buscarRespuesta(pregunta) {
  const texto = normalizarTexto(pregunta);
  if (!texto) return null;

  let mejor = null;
  let mejorPuntaje = 0;

  (CONTENT.faqBot || []).forEach(function (entrada) {
    (entrada.claves || []).forEach(function (clave) {
      const claveNormalizada = normalizarTexto(clave);
      if (claveNormalizada && texto.includes(claveNormalizada)) {
        if (claveNormalizada.length > mejorPuntaje) {
          mejorPuntaje = claveNormalizada.length;
          mejor = entrada;
        }
      }
    });
  });

  return mejor;
}

/* -----------------------------------------------------------------------------
   CONSTRUIR EL BOTÓN Y EL PANEL
----------------------------------------------------------------------------- */
function construirMarcadoBot() {
  /* --- Botón flotante --- */
  const lanzador = el("button", "bot-lanzador");
  lanzador.id = "botLanzador";
  lanzador.type = "button";
  lanzador.setAttribute("aria-expanded", "false");
  lanzador.setAttribute("aria-controls", "botPanel");

  const icono = document.createElementNS("http://www.w3.org/2000/svg", "svg");
  icono.setAttribute("viewBox", "0 0 24 24");
  icono.setAttribute("width", "22");
  icono.setAttribute("height", "22");
  icono.setAttribute("fill", "none");
  icono.setAttribute("stroke", "currentColor");
  icono.setAttribute("stroke-width", "2");
  icono.setAttribute("stroke-linecap", "round");
  icono.setAttribute("aria-hidden", "true");
  const trazo = document.createElementNS("http://www.w3.org/2000/svg", "path");
  trazo.setAttribute("d", "M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z");
  icono.appendChild(trazo);
  lanzador.appendChild(icono);
  lanzador.appendChild(el("span", "bot-lanzador__texto", "Preguntas frecuentes"));
  lanzador.appendChild(el("span", "sr-only", "Abrir el chat de preguntas frecuentes"));

  /* --- Panel --- */
  const panel = el("div", "bot-panel");
  panel.id = "botPanel";
  panel.setAttribute("role", "dialog");
  panel.setAttribute("aria-modal", "false");
  panel.setAttribute("aria-labelledby", "botTitulo");
  panel.hidden = true;

  const cabecera = el("header", "bot-panel__cabecera");
  const titulo = el("h2", null, "Preguntas frecuentes");
  titulo.id = "botTitulo";
  cabecera.appendChild(titulo);

  const cerrar = el("button", "bot-panel__cerrar");
  cerrar.id = "botCerrar";
  cerrar.type = "button";
  const equis = el("span", null, "×");
  equis.setAttribute("aria-hidden", "true");
  cerrar.appendChild(equis);
  cerrar.appendChild(el("span", "sr-only", "Cerrar el chat de preguntas frecuentes"));
  cabecera.appendChild(cerrar);
  panel.appendChild(cabecera);

  const mensajes = el("div", "bot-panel__mensajes");
  mensajes.id = "botMensajes";
  mensajes.setAttribute("role", "log");
  mensajes.setAttribute("aria-live", "polite");
  mensajes.setAttribute("aria-relevant", "additions");
  panel.appendChild(mensajes);

  const sugerencias = el("div", "bot-panel__sugerencias");
  sugerencias.id = "botSugerencias";
  sugerencias.setAttribute("aria-label", "Preguntas sugeridas");
  panel.appendChild(sugerencias);

  const formulario = el("form", "bot-panel__form");
  formulario.id = "botForm";
  const etiqueta = el("label", "sr-only", "Escribe tu pregunta");
  etiqueta.setAttribute("for", "botInput");
  formulario.appendChild(etiqueta);

  const entrada = el("input", null);
  entrada.type = "text";
  entrada.id = "botInput";
  entrada.name = "pregunta";
  entrada.placeholder = "Escribe tu pregunta...";
  entrada.autocomplete = "off";
  formulario.appendChild(entrada);

  const enviar = el("button", "bot-panel__enviar");
  enviar.type = "submit";
  const flecha = el("span", null, "→");
  flecha.setAttribute("aria-hidden", "true");
  enviar.appendChild(flecha);
  enviar.appendChild(el("span", "sr-only", "Enviar pregunta"));
  formulario.appendChild(enviar);

  panel.appendChild(formulario);

  document.body.appendChild(lanzador);
  document.body.appendChild(panel);
}

/* -----------------------------------------------------------------------------
   PINTAR MENSAJES
----------------------------------------------------------------------------- */
function agregarMensaje(texto, quien) {
  const contenedor = id("botMensajes");
  if (!contenedor) return;

  const clase = quien === "usuario" ? "bot-msg bot-msg--usuario" : "bot-msg bot-msg--bot";
  contenedor.appendChild(el("div", clase, texto));
  contenedor.scrollTop = contenedor.scrollHeight;
}

/** Mensaje del bot con un enlace a WhatsApp al final. */
function agregarMensajeConWhatsApp(texto) {
  const contenedor = id("botMensajes");
  if (!contenedor) return;

  const mensaje = el("div", "bot-msg bot-msg--bot", texto);

  // Solo dígitos, por la misma razón que en main.js.
  const numero = String((CONTENT.contacto && CONTENT.contacto.whatsapp) || "").replace(/\D/g, "");
  if (numero) {
    mensaje.appendChild(document.createElement("br"));
    const enlace = el("a", null, "Escribir por WhatsApp");
    enlace.href = "https://wa.me/" + numero +
      "?text=" + encodeURIComponent("Hola, tengo una consulta sobre la parroquia.");
    enlace.target = "_blank";
    enlace.rel = "noopener";
    mensaje.appendChild(enlace);
  }

  contenedor.appendChild(mensaje);
  contenedor.scrollTop = contenedor.scrollHeight;
}

/* -----------------------------------------------------------------------------
   BOTONES DE PREGUNTAS SUGERIDAS
----------------------------------------------------------------------------- */
function renderSugerencias() {
  const zona = id("botSugerencias");
  if (!zona) return;

  zona.textContent = "";

  (CONTENT.faqBot || []).forEach(function (entrada) {
    if (!entrada.sugerida) return;      // sin texto de botón, no se muestra

    const boton = el("button", "bot-sugerencia", entrada.sugerida);
    boton.type = "button";
    boton.addEventListener("click", function () {
      agregarMensaje(entrada.sugerida, "usuario");
      agregarMensaje(entrada.respuesta, "bot");
    });
    zona.appendChild(boton);
  });
}

/* -----------------------------------------------------------------------------
   ABRIR / CERRAR
----------------------------------------------------------------------------- */
function abrirBot() {
  const panel = id("botPanel");
  const lanzador = id("botLanzador");
  if (!panel || !lanzador) return;

  panel.hidden = false;
  lanzador.setAttribute("aria-expanded", "true");

  const entrada = id("botInput");
  if (entrada) entrada.focus();
}

function cerrarBot() {
  const panel = id("botPanel");
  const lanzador = id("botLanzador");
  if (!panel || !lanzador) return;

  panel.hidden = true;
  lanzador.setAttribute("aria-expanded", "false");
  lanzador.focus();                     // devuelve el foco a quien abrió el panel
}

/* -----------------------------------------------------------------------------
   ARRANQUE DEL BOT (lo llama main.js)
----------------------------------------------------------------------------- */
function iniciarBot() {
  construirMarcadoBot();

  const lanzador = id("botLanzador");
  const cerrar = id("botCerrar");
  const formulario = id("botForm");
  const entrada = id("botInput");
  const panel = id("botPanel");

  if (!lanzador || !panel) return;

  agregarMensaje(
    "Hola. Puedo ayudarte con horarios de misa, sacramentos, catequesis, donaciones " +
    "y solicitudes de ayuda. Escribe tu pregunta o toca una de las opciones de abajo.",
    "bot"
  );

  renderSugerencias();

  lanzador.addEventListener("click", function () {
    if (panel.hidden) abrirBot(); else cerrarBot();
  });

  if (cerrar) cerrar.addEventListener("click", cerrarBot);

  document.addEventListener("keydown", function (evento) {
    if (evento.key === "Escape" && !panel.hidden) cerrarBot();
  });

  if (formulario && entrada) {
    /* Enviar con la tecla Enter.
       El navegador ya lo hace solo al haber un botón de tipo submit, pero lo
       manejamos explícitamente para que funcione igual en todos los teclados,
       incluidos los de celular. preventDefault evita que se envíe dos veces. */
    entrada.addEventListener("keydown", function (evento) {
      if (evento.key === "Enter") {
        evento.preventDefault();
        formulario.requestSubmit();
      }
    });

    formulario.addEventListener("submit", function (evento) {
      evento.preventDefault();

      const pregunta = entrada.value.trim();
      if (!pregunta) return;

      agregarMensaje(pregunta, "usuario");
      entrada.value = "";

      const encontrada = buscarRespuesta(pregunta);

      if (encontrada) {
        agregarMensaje(encontrada.respuesta, "bot");
      } else {
        agregarMensajeConWhatsApp(
          CONTENT.botFallback || "No encontré una respuesta para eso."
        );
      }
    });
  }
}
