/* =============================================================================
   bot.js — Bot de preguntas frecuentes (100% en el navegador)
   -----------------------------------------------------------------------------
   No hay servidor ni inteligencia artificial detrás: el bot compara lo que
   escribe la persona con las palabras de "claves" en CONTENT.faqBot y responde
   con el texto que ya escribió la parroquia.

   Para agregar o cambiar respuestas, edita la lista faqBot en content.js.
   Si el bot no encuentra nada, ofrece escribir por WhatsApp.
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
    .replace(/[̀-ͯ]/g, "")   // quita tildes
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
   PINTAR MENSAJES
----------------------------------------------------------------------------- */
function agregarMensaje(texto, quien) {
  const contenedor = id("botMensajes");
  if (!contenedor) return;

  const clase = quien === "usuario" ? "bot-msg bot-msg--usuario" : "bot-msg bot-msg--bot";
  const mensaje = el("div", clase, texto);
  contenedor.appendChild(mensaje);

  // Siempre mostrar lo último escrito.
  contenedor.scrollTop = contenedor.scrollHeight;
}

/** Mensaje del bot con un enlace a WhatsApp al final. */
function agregarMensajeConWhatsApp(texto) {
  const contenedor = id("botMensajes");
  if (!contenedor) return;

  const mensaje = el("div", "bot-msg bot-msg--bot", texto);

  const numero = (CONTENT.contacto && CONTENT.contacto.whatsapp) || "";
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
   ABRIR / CERRAR EL PANEL
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
  const lanzador = id("botLanzador");
  const cerrar = id("botCerrar");
  const formulario = id("botForm");
  const entrada = id("botInput");
  const panel = id("botPanel");

  if (!lanzador || !panel) return;

  // Saludo inicial
  agregarMensaje(
    "Hola. Puedo ayudarte con horarios de misa, sacramentos, donaciones y solicitudes de ayuda. " +
    "Escribe tu pregunta o toca una de las opciones de abajo.",
    "bot"
  );

  renderSugerencias();

  lanzador.addEventListener("click", function () {
    if (panel.hidden) abrirBot(); else cerrarBot();
  });

  if (cerrar) cerrar.addEventListener("click", cerrarBot);

  // La tecla Escape cierra el panel.
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
