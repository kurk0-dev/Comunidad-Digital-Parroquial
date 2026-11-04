/* =============================================================================
   carrusel.js — Flechas y contador para las galerías con estilo "Carrusel"
   -----------------------------------------------------------------------------
   La galería ya se desliza con el dedo (CSS). Esto agrega botones grandes de
   "anterior / siguiente" para quien no sabe deslizar. Al tocar una foto se
   abre el visor nativo de WordPress (lightbox).

   Lecciones de la versión anterior que se mantienen:
   - Cada flecha apunta a una foto concreta (no "desplazar X píxeles"), así
     dos clics rápidos avanzan dos fotos en vez de pisarse.
   - "instant" y no "auto" con movimiento reducido: "auto" obedece al CSS.
   - Si la animación no llega (celular cargado), salta directo, salvo que la
     persona esté deslizando con el dedo.
   ============================================================================= */
(function () {
  "use strict";

  function reducido() {
    return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  }

  function icono(d) {
    var NS = "http://www.w3.org/2000/svg";
    var svg = document.createElementNS(NS, "svg");
    svg.setAttribute("viewBox", "0 0 24 24");
    svg.setAttribute("width", "22");
    svg.setAttribute("height", "22");
    svg.setAttribute("fill", "none");
    svg.setAttribute("stroke", "currentColor");
    svg.setAttribute("stroke-width", "2");
    svg.setAttribute("aria-hidden", "true");
    var p = document.createElementNS(NS, "path");
    p.setAttribute("d", d);
    svg.appendChild(p);
    return svg;
  }

  function preparar(pista, n) {
    var items = Array.prototype.slice.call(pista.querySelectorAll(":scope > figure.wp-block-image"));
    if (items.length < 2) return;

    pista.setAttribute("role", "region");
    pista.setAttribute("aria-roledescription", "carrusel");
    if (!pista.getAttribute("aria-label")) pista.setAttribute("aria-label", "Galería de fotos " + n);
    pista.tabIndex = 0;

    var controles = document.createElement("div");
    controles.className = "cds-carrusel__controles";
    function flecha(d, texto, dir) {
      var b = document.createElement("button");
      b.type = "button";
      b.className = "cds-carrusel__flecha";
      b.appendChild(icono(d));
      var s = document.createElement("span");
      s.className = "cds-sr";
      s.textContent = texto;
      b.appendChild(s);
      b.addEventListener("click", function () { mover(dir); });
      return b;
    }
    var anterior = flecha("M15 18l-6-6 6-6", "Fotos anteriores", -1);
    var contador = document.createElement("p");
    contador.className = "cds-carrusel__contador";
    var siguiente = flecha("M9 18l6-6-6-6", "Fotos siguientes", 1);
    controles.appendChild(anterior);
    controles.appendChild(contador);
    controles.appendChild(siguiente);
    pista.parentNode.insertBefore(controles, pista.nextSibling);

    function pos(i) { return items[i].offsetLeft - items[0].offsetLeft; }
    function visible() {
      var mejor = 0;
      items.forEach(function (_, i) {
        if (Math.abs(pos(i) - pista.scrollLeft) < Math.abs(pos(mejor) - pista.scrollLeft)) mejor = i;
      });
      return mejor;
    }
    function ultimo() {
      var max = pista.scrollWidth - pista.clientWidth;
      for (var i = 0; i < items.length; i++) if (pos(i) >= max - 4) return i;
      return items.length - 1;
    }

    var destino = null, hasta = 0, toque = 0;
    ["pointerdown", "wheel", "touchstart"].forEach(function (t) {
      pista.addEventListener(t, function () { toque = Date.now(); }, { passive: true });
    });

    function actualizar() {
      var actual = (destino !== null && Date.now() < hasta) ? destino : visible();
      contador.textContent = (actual + 1) + " de " + items.length;
      anterior.disabled = actual <= 0;
      siguiente.disabled = actual >= ultimo();
    }

    function mover(dir) {
      var desde = (destino !== null && Date.now() < hasta) ? destino : visible();
      var obj = Math.max(0, Math.min(ultimo(), desde + dir));
      var clic = Date.now();
      destino = obj;
      hasta = clic + 700;
      pista.scrollTo({ left: pos(obj), behavior: reducido() ? "instant" : "smooth" });
      actualizar();
      setTimeout(function () {
        if (Math.abs(pista.scrollLeft - pos(obj)) > 4 && destino === obj && toque < clic) {
          pista.scrollTo({ left: pos(obj), behavior: "instant" });
        }
        actualizar();
      }, 750);
    }

    pista.addEventListener("scroll", actualizar, { passive: true });
    window.addEventListener("resize", actualizar);
    pista.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") { e.preventDefault(); mover(-1); }
      if (e.key === "ArrowRight") { e.preventDefault(); mover(1); }
    });
    setTimeout(actualizar, 0);
  }

  function iniciar() {
    document.querySelectorAll(".wp-block-gallery.is-style-carrusel").forEach(function (g, i) { preparar(g, i + 1); });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", iniciar);
  else iniciar();
})();
