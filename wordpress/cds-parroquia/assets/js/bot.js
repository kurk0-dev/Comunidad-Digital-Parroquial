/* =============================================================================
   bot.js — Bot de preguntas frecuentes (100 % en el navegador)
   -----------------------------------------------------------------------------
   Las preguntas vienen de WordPress (menú "Preguntas del bot") en window.CDS_BOT.
   El bot compara lo que escribe la persona con las palabras clave de cada
   pregunta. Si no encuentra nada, ofrece escribir a la secretaría por WhatsApp.
   Todo el texto se pinta con textContent: nunca con innerHTML.
   ============================================================================= */
(function () {
  "use strict";

  var datos = window.CDS_BOT;
  if (!datos || !Array.isArray(datos.preguntas)) return;

  function el(tag, clase, texto) {
    var n = document.createElement(tag);
    if (clase) n.className = clase;
    if (texto) n.textContent = texto;
    return n;
  }

  /* "¿A QUÉ hora?" y "a que hora" se consideran iguales */
  function normalizar(t) {
    return String(t || "").toLowerCase().normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .replace(/[¿?¡!.,;:()"']/g, " ")
      .replace(/\s+/g, " ").trim();
  }

  /* Gana la clave más larga: "primera comunion" le gana a "comunion" */
  function buscar(pregunta) {
    var texto = normalizar(pregunta), mejor = null, puntaje = 0;
    if (!texto) return null;
    datos.preguntas.forEach(function (p) {
      (p.claves || []).forEach(function (c) {
        var clave = normalizar(c);
        if (clave && texto.indexOf(clave) !== -1 && clave.length > puntaje) {
          puntaje = clave.length;
          mejor = p;
        }
      });
    });
    return mejor;
  }

  function icono() {
    var NS = "http://www.w3.org/2000/svg";
    var svg = document.createElementNS(NS, "svg");
    svg.setAttribute("viewBox", "0 0 24 24");
    svg.setAttribute("width", "22");
    svg.setAttribute("height", "22");
    svg.setAttribute("fill", "none");
    svg.setAttribute("stroke", "currentColor");
    svg.setAttribute("stroke-width", "2");
    svg.setAttribute("aria-hidden", "true");
    var path = document.createElementNS(NS, "path");
    path.setAttribute("d", "M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z");
    svg.appendChild(path);
    return svg;
  }

  function iniciar() {
    var lanzador = el("button", "cds-bot-lanzador");
    lanzador.type = "button";
    lanzador.setAttribute("aria-expanded", "false");
    lanzador.setAttribute("aria-controls", "cdsBotPanel");
    lanzador.appendChild(icono());
    lanzador.appendChild(el("span", "cds-bot-lanzador__texto", "Preguntas frecuentes"));
    lanzador.appendChild(el("span", "cds-sr", "Abrir el chat de preguntas frecuentes"));

    var panel = el("div", "cds-bot-panel");
    panel.id = "cdsBotPanel";
    panel.setAttribute("role", "dialog");
    panel.setAttribute("aria-labelledby", "cdsBotTitulo");
    panel.hidden = true;

    var cab = el("div", "cds-bot-panel__cabecera");
    var titulo = el("h2", null, "Preguntas frecuentes");
    titulo.id = "cdsBotTitulo";
    cab.appendChild(titulo);
    var cerrar = el("button", "cds-bot-panel__cerrar");
    cerrar.type = "button";
    var x = el("span", null, "×");
    x.setAttribute("aria-hidden", "true");
    cerrar.appendChild(x);
    cerrar.appendChild(el("span", "cds-sr", "Cerrar el chat"));
    cab.appendChild(cerrar);
    panel.appendChild(cab);

    var mensajes = el("div", "cds-bot-panel__mensajes");
    mensajes.setAttribute("role", "log");
    mensajes.setAttribute("aria-live", "polite");
    panel.appendChild(mensajes);

    var sugerencias = el("div", "cds-bot-panel__sugerencias");
    sugerencias.setAttribute("aria-label", "Preguntas sugeridas");
    panel.appendChild(sugerencias);

    var form = el("form", "cds-bot-panel__form");
    var etiqueta = el("label", "cds-sr", "Escribe tu pregunta");
    etiqueta.setAttribute("for", "cdsBotEntrada");
    form.appendChild(etiqueta);
    var entrada = el("input");
    entrada.type = "text";
    entrada.id = "cdsBotEntrada";
    entrada.placeholder = "Escribe tu pregunta...";
    entrada.autocomplete = "off";
    form.appendChild(entrada);
    var enviar = el("button", "cds-bot-panel__enviar");
    enviar.type = "submit";
    var flecha = el("span", null, "→");
    flecha.setAttribute("aria-hidden", "true");
    enviar.appendChild(flecha);
    enviar.appendChild(el("span", "cds-sr", "Enviar pregunta"));
    form.appendChild(enviar);
    panel.appendChild(form);

    document.body.appendChild(lanzador);
    document.body.appendChild(panel);

    function mensaje(texto, quien, conWhatsApp) {
      var m = el("div", "cds-bot-msg " + (quien === "usuario" ? "cds-bot-msg--usuario" : "cds-bot-msg--bot"), texto);
      if (conWhatsApp && /^https:\/\/wa\.me\/\d+/.test(datos.whatsapp || "")) {
        m.appendChild(document.createElement("br"));
        var a = el("a", null, "Escribir por WhatsApp");
        a.href = datos.whatsapp;
        a.target = "_blank";
        a.rel = "noopener";
        m.appendChild(a);
      }
      mensajes.appendChild(m);
      mensajes.scrollTop = mensajes.scrollHeight;
    }

    datos.preguntas.forEach(function (p) {
      if (!p.sugerida) return;
      var b = el("button", "cds-bot-sugerencia", p.sugerida);
      b.type = "button";
      b.addEventListener("click", function () {
        mensaje(p.sugerida, "usuario");
        mensaje(p.respuesta, "bot");
      });
      sugerencias.appendChild(b);
    });

    mensaje(datos.saludo, "bot");

    function abrir() { panel.hidden = false; lanzador.setAttribute("aria-expanded", "true"); entrada.focus(); }
    function cerrarPanel() { panel.hidden = true; lanzador.setAttribute("aria-expanded", "false"); lanzador.focus(); }

    lanzador.addEventListener("click", function () { if (panel.hidden) abrir(); else cerrarPanel(); });
    cerrar.addEventListener("click", cerrarPanel);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !panel.hidden) cerrarPanel(); });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var pregunta = entrada.value.trim();
      if (!pregunta) return;
      mensaje(pregunta, "usuario");
      entrada.value = "";
      var r = buscar(pregunta);
      if (r) mensaje(r.respuesta, "bot");
      else mensaje(datos.fallback || "No encontré una respuesta para eso.", "bot", true);
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", iniciar);
  else iniciar();
})();
