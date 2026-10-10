/* =============================================================================
   instagram.js — "Lo último que publicamos"
   -----------------------------------------------------------------------------
   Muestra las últimas publicaciones de Instagram del grupo.

   Cómo llegan aquí, SIN exponer el token de Instagram:
     1. Una tarea automática de GitHub (.github/workflows/instagram.yml) corre
        cada 6 horas. Ella sí tiene el token, guardado como secreto cifrado.
     2. Descarga las fotos a assets/instagram/ y escribe data/instagram.json.
     3. Esta página solo lee ese JSON del propio sitio. El navegador del
        visitante nunca ve el token ni se conecta con Instagram.

   Si todavía no hay publicaciones (o el JSON no se puede leer), la sección
   queda oculta: nunca se ve un cuadro vacío.

   Uso en el HTML:  <section class="instagram" data-instagram data-limite="3" hidden>
   ============================================================================= */

const TIPOS_INSTAGRAM = ["IMAGE", "VIDEO", "CAROUSEL_ALBUM"];

/** Solo imágenes que la propia tarea descargó al sitio. */
function imagenInstagramValida(ruta) {
  return typeof ruta === "string" && /^assets\/instagram\/\d{1,30}\.(jpg|webp|png)$/.test(ruta);
}

/** Solo enlaces a instagram.com. */
function enlaceInstagramValido(url) {
  try {
    const u = new URL(String(url));
    return u.protocol === "https:" && (u.hostname === "www.instagram.com" || u.hostname === "instagram.com") ? u.href : "";
  } catch (e) {
    return "";
  }
}

/** Primera línea del texto, recortada: la tarjeta muestra como mucho 2 líneas. */
function resumenPublicacion(texto) {
  const linea = String(texto || "").split("\n").map(function (l) { return l.trim(); }).filter(Boolean)[0] || "";
  return linea.length > 140 ? linea.slice(0, 137).trimEnd() + "…" : linea;
}

function iconoTipo(tipo) {
  if (tipo === "VIDEO") return icono(["M8 5v14l11-7z"], 14);
  if (tipo === "CAROUSEL_ALBUM") return icono(["M8 8h12v12H8z", "M4 16V4h12"], 14);
  return null;
}

function crearPublicacion(pub) {
  const a = el("a", "publicacion");
  a.href = pub.enlace;
  a.target = "_blank";
  a.rel = "noopener";

  const marco = el("div", "publicacion__marco");
  const img = el("img");
  img.src = pub.imagen;
  img.alt = pub.resumen ? "Publicación de Instagram: " + pub.resumen : "Publicación de Instagram del grupo";
  img.width = 1080; img.height = 1350;
  img.loading = "lazy";
  img.decoding = "async";
  marco.appendChild(img);

  const tipo = iconoTipo(pub.tipo);
  if (tipo) {
    const insignia = el("span", "publicacion__tipo");
    insignia.appendChild(tipo);
    insignia.appendChild(el("span", "sr-only", pub.tipo === "VIDEO" ? "Video" : "Varias fotos"));
    marco.appendChild(insignia);
  }
  a.appendChild(marco);

  if (pub.resumen) a.appendChild(el("p", "publicacion__texto", pub.resumen));
  return a;
}

function limpiarPublicaciones(datos) {
  const lista = datos && Array.isArray(datos.publicaciones) ? datos.publicaciones : [];
  return lista
    .map(function (p) {
      if (!p || TIPOS_INSTAGRAM.indexOf(p.tipo) < 0) return null;
      if (!imagenInstagramValida(p.imagen)) return null;
      const enlace = enlaceInstagramValido(p.enlace);
      if (!enlace) return null;
      return { tipo: p.tipo, imagen: p.imagen, enlace: enlace, resumen: resumenPublicacion(p.texto) };
    })
    .filter(Boolean);
}

function renderInstagram() {
  const secciones = document.querySelectorAll("[data-instagram]");
  if (secciones.length === 0) return;

  fetch("data/instagram.json", { cache: "no-cache", credentials: "omit" })
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (datos) {
      const publicaciones = limpiarPublicaciones(datos);
      if (publicaciones.length === 0) return;      // sin publicaciones: sección oculta

      const usuarioJson = datos && /^[A-Za-z0-9._]{1,30}$/.test(datos.usuario || "") ? datos.usuario : "";
      const perfil = usuarioJson ? "https://www.instagram.com/" + usuarioJson + "/" : urlInstagram();

      secciones.forEach(function (seccion) {
        const limite = parseInt(seccion.getAttribute("data-limite"), 10) || 9;
        const lista = seccion.querySelector(".instagram__lista");
        if (!lista) return;

        lista.textContent = "";
        publicaciones.slice(0, limite).forEach(function (pub) {
          const li = el("li");
          li.appendChild(crearPublicacion(pub));
          lista.appendChild(li);
        });

        const enlacePerfil = seccion.querySelector(".instagram__usuario");
        if (enlacePerfil) {
          if (perfil) {
            const nombre = perfil.replace(/^https:\/\/www\.instagram\.com\/|\/$/g, "");
            enlacePerfil.href = perfil;
            enlacePerfil.target = "_blank";
            enlacePerfil.rel = "noopener";
            enlacePerfil.textContent = "@" + nombre + " ↗";
          } else {
            enlacePerfil.remove();
          }
        }

        seccion.hidden = false;
      });
    })
    .catch(function () {
      // Abierta como archivo local o sin conexión: la sección sigue oculta.
    });
}
