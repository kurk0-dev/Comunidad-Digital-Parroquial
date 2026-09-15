/* =============================================================================
   qr.js — Códigos QR
   -----------------------------------------------------------------------------
   Genera códigos QR en el navegador (no se guardan como imagen ni se generan
   en ningún servicio externo). Cada QR apunta a una pestaña del sitio:
     · el de la página de misas abre misas.html
     · el de la página de donaciones abre donaciones.html

   Usa la librería qrcode.js que se carga por CDN desde cada página.

   OJO: los QR apuntan a la dirección donde esté publicada la página. Mientras
   la abras desde tu computadora (archivo local), no hay una dirección pública
   que compartir, así que en su lugar se muestra un aviso. Una vez publicada en
   Vercel, los QR funcionan solos, sin tocar nada.
   ============================================================================= */

/** Arma la dirección completa de una pestaña, por ejemplo ".../donaciones.html" */
function urlDePagina(archivo) {
  const base = window.location.href.replace(/[^/]*$/, "");
  return base + archivo;
}

/**
 * Dibuja un QR dentro de un contenedor.
 * Si la página no está publicada todavía, muestra un aviso en lugar del código.
 */
function dibujarQR(contenedor, archivo) {
  if (!contenedor) return;

  const textoAviso = "El código QR aparece cuando la página esté publicada en internet.";

  // Página abierta como archivo local: no hay URL pública que codificar.
  if (window.location.protocol === "file:") {
    contenedor.textContent = "";
    contenedor.removeAttribute("role");
    contenedor.removeAttribute("aria-label");
    const aviso = el("p", "tarjeta-qr__nota", textoAviso);
    aviso.style.margin = "0";
    aviso.style.textAlign = "center";
    contenedor.appendChild(aviso);
    return;
  }

  // La librería no llegó a cargar (por ejemplo, sin internet).
  if (typeof QRCode === "undefined") {
    console.warn("No se pudo cargar la librería de códigos QR.");
    contenedor.textContent = "";
    contenedor.appendChild(el("p", "tarjeta-qr__nota", "Código QR no disponible."));
    return;
  }

  contenedor.textContent = "";

  new QRCode(contenedor, {
    text: urlDePagina(archivo),
    width: 132,
    height: 132,
    colorDark: "#14304D",
    colorLight: "#FFFFFF",
    correctLevel: QRCode.CorrectLevel.M
  });

  /* La librería mete un <img> con alt vacío. El contenedor ya tiene
     role="img" y aria-label en el HTML, así que ocultamos lo de adentro
     para que el lector de pantalla no lo lea dos veces. */
  const imagen = contenedor.querySelector("img");
  if (imagen) imagen.setAttribute("aria-hidden", "true");

  const canvas = contenedor.querySelector("canvas");
  if (canvas) canvas.setAttribute("aria-hidden", "true");
}

/** Lo llama main.js al cargar la página. */
function generarCodigosQR() {
  /* Cada contenedor declara a qué pestaña apunta con data-qr="archivo.html",
     así se pueden añadir más QR sin tocar este archivo. */
  document.querySelectorAll("[data-qr]").forEach(function (contenedor) {
    dibujarQR(contenedor, contenedor.getAttribute("data-qr"));
  });
}
