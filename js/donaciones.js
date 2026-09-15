/* =============================================================================
   donaciones.js — Barras de avance de las campañas de donación
   -----------------------------------------------------------------------------
   Lee CONTENT.campanasDonacion de content.js y dibuja una tarjeta por campaña
   con su barra de progreso.

   Para cambiar una meta o el avance, edita content.js. Nunca este archivo.

   Aclaración importante: esto es informativo. La web no cobra ni recibe dinero.
   ============================================================================= */

/**
 * Calcula el porcentaje de avance, protegido contra datos mal escritos:
 * si la meta es 0, negativa o no es un número, devuelve 0 en vez de romperse.
 */
function calcularPorcentaje(actual, meta) {
  const a = Number(actual);
  const m = Number(meta);

  if (!isFinite(a) || !isFinite(m) || m <= 0) return 0;

  const porcentaje = (a / m) * 100;
  return Math.max(0, Math.min(100, Math.round(porcentaje)));
}

function renderCampanas() {
  const lista = id("campanas");
  if (!lista) return;

  const campanas = CONTENT.campanasDonacion || [];

  if (campanas.length === 0) {
    lista.appendChild(el("li", "campana", "No hay campañas abiertas en este momento."));
  }

  campanas.forEach(function (campana) {
    const porcentaje = calcularPorcentaje(campana.actual, campana.meta);
    const completa = porcentaje >= 100;

    const li = el("li", completa ? "campana campana--completa" : "campana");

    /* --- Cabecera: nombre del artículo + cifra --- */
    const cabecera = el("div", "campana__cabecera");

    const titulo = el("h3", "campana__item", campana.item);
    marcarSiEsPlaceholder(titulo, campana);
    cabecera.appendChild(titulo);

    const cifra = el(
      "span",
      "campana__cifra",
      campana.actual + " / " + campana.meta + " " + (campana.unidad || "")
    );
    cabecera.appendChild(cifra);
    li.appendChild(cabecera);

    /* --- Barra de progreso ---
       Lleva role="progressbar" para que los lectores de pantalla anuncien
       el avance, y un aria-label que describe la campaña completa. */
    const barra = el("div", "campana__barra");
    barra.setAttribute("role", "progressbar");
    barra.setAttribute("aria-valuenow", String(porcentaje));
    barra.setAttribute("aria-valuemin", "0");
    barra.setAttribute("aria-valuemax", "100");
    barra.setAttribute(
      "aria-label",
      "Avance de la campaña de " + (campana.item || "donación") + ": " +
      campana.actual + " de " + campana.meta + " " + (campana.unidad || "") +
      ", " + porcentaje + " por ciento"
    );

    const relleno = el("div", "campana__relleno");
    barra.appendChild(relleno);
    li.appendChild(barra);

    /* --- Pie: porcentaje y cuánto falta --- */
    const pie = el("div", "campana__pie");
    pie.appendChild(el("span", null, porcentaje + "% completado"));

    const falta = Math.max(0, Number(campana.meta) - Number(campana.actual));
    if (!completa && isFinite(falta)) {
      pie.appendChild(el("span", null, "Faltan " + falta + " " + (campana.unidad || "")));
    }
    li.appendChild(pie);

    if (completa) {
      li.appendChild(el("span", "campana__completa", "Meta cumplida"));
    }

    if (campana.nota) {
      li.appendChild(el("p", "campana__nota", campana.nota));
    }

    lista.appendChild(li);

    /* El ancho se asigna de inmediato, no dentro de requestAnimationFrame:
       si la página se abre en una pestaña de segundo plano, el navegador no
       ejecuta requestAnimationFrame y las barras se quedarían en cero.
       El crecimiento suave lo da la transición del CSS, que además se anula
       sola si la persona pidió menos movimiento en su sistema. */
    relleno.style.width = porcentaje + "%";
  });

  /* --- Datos del punto de entrega --- */
  const info = CONTENT.donacionesInfo || {};

  const punto = id("puntoEntrega");
  if (punto) {
    punto.textContent = info.puntoEntrega || "";
    marcarSiEsPlaceholder(punto, info);
  }

  const contacto = id("contactoDonaciones");
  if (contacto && info.contactoNombre) {
    contacto.textContent = "Coordina con: " + info.contactoNombre;
  }
}
