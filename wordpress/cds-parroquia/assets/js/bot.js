/* =============================================================================
   bot.js — Bot de preguntas frecuentes (100 % en el navegador)
   -----------------------------------------------------------------------------
   Las preguntas vienen de WordPress (menú "Preguntas del bot") en window.CDS_BOT.
   El bot compara lo que escribe la persona con las palabras clave de cada
   pregunta. Si no encuentra nada, ofrece escribir al grupo por WhatsApp.
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

  /* La paloma con el fuego de Pentecostés (imagen del tema). Es decorativa:
     el botón ya tiene su texto para lectores de pantalla. */
  function paloma(clase) {
    var img = el("img", "cds-paloma " + (clase || ""));
    img.alt = "";
    img.decoding = "async";
    img.width = 171;
    img.height = 240;
    if (/^https?:\/\/[^"'<>\s]+\.webp(\?[^"'<>\s]*)?$/.test(datos.imagen || "")) img.src = datos.imagen;
    return img;
  }

  function iniciar() {
    var lanzador = el("button", "cds-bot-lanzador");
    lanzador.type = "button";
    lanzador.setAttribute("aria-expanded", "false");
    lanzador.setAttribute("aria-controls", "cdsBotPanel");
    var burbuja = el("span", "cds-bot-burbuja", "¿Te ayudo?");
    burbuja.setAttribute("aria-hidden", "true");
    lanzador.appendChild(burbuja);
    var ave = el("span", "cds-bot-lanzador__ave");
    ave.appendChild(paloma());
    lanzador.appendChild(ave);
    lanzador.appendChild(el("span", "cds-sr", "Abrir el asistente del grupo"));

    var panel = el("div", "cds-bot-panel");
    panel.id = "cdsBotPanel";
    panel.setAttribute("role", "dialog");
    panel.setAttribute("aria-labelledby", "cdsBotTitulo");
    panel.hidden = true;

    var cab = el("div", "cds-bot-panel__cabecera");
    var quien = el("div", "cds-bot-panel__quien");
    quien.appendChild(paloma("cds-paloma--mini"));
    var textos = el("div");
    var titulo = el("h2", null, "Asistente del grupo");
    titulo.id = "cdsBotTitulo";
    textos.appendChild(titulo);
    textos.appendChild(el("p", null, "Responde al instante"));
    quien.appendChild(textos);
    cab.appendChild(quien);
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

    /* Puntitos de "escribiendo" un momento antes de responder */
    function responder(texto, conWhatsApp) {
      var t = el("div", "cds-bot-escribiendo");
      t.setAttribute("aria-hidden", "true");
      for (var i = 0; i < 3; i++) t.appendChild(el("span"));
      mensajes.appendChild(t);
      mensajes.scrollTop = mensajes.scrollHeight;
      setTimeout(function () {
        if (t.parentNode) t.parentNode.removeChild(t);
        mensaje(texto, "bot", conWhatsApp);
      }, 650);
    }

    datos.preguntas.forEach(function (p) {
      if (!p.sugerida) return;
      var b = el("button", "cds-bot-sugerencia", p.sugerida);
      b.type = "button";
      b.addEventListener("click", function () {
        mensaje(p.sugerida, "usuario");
        responder(p.respuesta);
      });
      sugerencias.appendChild(b);
    });

    mensaje(datos.saludo, "bot");

    /* El globito "¿Te ayudo?" sale una vez por visita, a los 2 segundos */
    function yaSaludo() { try { return sessionStorage.getItem("cdsBotSaludo") === "1"; } catch (e) { return false; } }
    function marcarSaludo() { try { sessionStorage.setItem("cdsBotSaludo", "1"); } catch (e) { /* sin almacenamiento */ } }
    if (!yaSaludo()) {
      setTimeout(function () {
        if (!panel.hidden) return;
        lanzador.classList.add("is-saludando");
        setTimeout(function () { lanzador.classList.remove("is-saludando"); }, 9000);
      }, 2000);
    }

    function abrir() {
      panel.hidden = false;
      lanzador.hidden = true;
      lanzador.classList.remove("is-saludando");
      marcarSaludo();
      lanzador.setAttribute("aria-expanded", "true");
      entrada.focus();
    }
    function cerrarPanel() {
      panel.hidden = true;
      lanzador.hidden = false;
      lanzador.setAttribute("aria-expanded", "false");
      lanzador.focus();
    }

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
      if (r) responder(r.respuesta);
      else responder(datos.fallback || "No encontré una respuesta para eso.", true);
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", iniciar);
  else iniciar();
})();
