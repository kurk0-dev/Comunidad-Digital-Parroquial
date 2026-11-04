/* qr.js — Dibuja en el navegador el QR de [cds_qr] (librería local, sin CDN). */
(function () {
  "use strict";

  function dibujar() {
    document.querySelectorAll(".cds-qr[data-qr]").forEach(function (caja) {
      var url = caja.getAttribute("data-qr");
      if (!/^https?:\/\//.test(url) || typeof window.QRCode === "undefined") {
        caja.textContent = "Código QR no disponible.";
        return;
      }
      caja.textContent = "";
      new window.QRCode(caja, {
        text: url,
        width: 132,
        height: 132,
        colorDark: "#14304D",
        colorLight: "#FFFFFF",
        correctLevel: window.QRCode.CorrectLevel.M
      });
      // El contenedor ya tiene role="img" y aria-label: el lector no debe leerlo dos veces.
      caja.querySelectorAll("img, canvas").forEach(function (n) {
        n.setAttribute("aria-hidden", "true");
        if (n.tagName === "IMG") n.setAttribute("alt", "");
      });
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", dibujar);
  else dibujar();
})();
