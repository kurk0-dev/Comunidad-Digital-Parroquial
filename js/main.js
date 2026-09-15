/* =============================================================================
   main.js — Lógica general y renderizado de la página
   -----------------------------------------------------------------------------
   Este archivo NO contiene textos de la parroquia: los lee de content.js.
   Si quieres cambiar contenido, edita content.js, no este archivo.
   ============================================================================= */

/* -----------------------------------------------------------------------------
   AYUDANTES
   Se usan también desde donaciones.js, bot.js y qr.js.
----------------------------------------------------------------------------- */

/**
 * Crea un elemento con clase y texto.
 * Usamos textContent (y no innerHTML) para que un texto con símbolos como
 * & o < no rompa la página. Así la parroquia puede escribir con libertad.
 */
function el(tag, clase, texto) {
  const nodo = document.createElement(tag);
  if (clase) nodo.className = clase;
  if (texto !== undefined && texto !== null && texto !== "") nodo.textContent = texto;
  return nodo;
}

/**
 * Devuelve la etiqueta "Ejemplo / Por confirmar" si el objeto tiene
 * esPlaceholder: true. Si no, devuelve null y no se pinta nada.
 */
function etiquetaPlaceholder(obj) {
  if (!obj || obj.esPlaceholder !== true) return null;
  const tag = el("span", "placeholder-tag", "Ejemplo / Por confirmar");
  tag.setAttribute("title", "Este dato todavía no ha sido confirmado por la parroquia.");
  return tag;
}

/** Añade la etiqueta de placeholder al final de un elemento, si corresponde. */
function marcarSiEsPlaceholder(contenedor, obj) {
  const tag = etiquetaPlaceholder(obj);
  if (tag) contenedor.appendChild(tag);
}

/** Atajo para document.getElementById. */
function id(nombre) {
  return document.getElementById(nombre);
}

/** ¿El enlace sigue siendo el de ejemplo, sin configurar? */
function esEnlaceSinConfigurar(url) {
  if (!url) return true;
  return url.includes("XXXXXXXXXXXX") || url.includes("YYYYYYYYYYYY");
}

/* -----------------------------------------------------------------------------
   NAVEGACIÓN
----------------------------------------------------------------------------- */
function iniciarNavegacion() {
  const boton = id("navToggle");
  const nav = id("navPrincipal");
  if (!boton || !nav) return;

  boton.addEventListener("click", function () {
    const abierto = nav.classList.toggle("esta-abierto");
    boton.setAttribute("aria-expanded", abierto ? "true" : "false");
    boton.querySelector(".sr-only").textContent =
      abierto ? "Cerrar menú de navegación" : "Abrir menú de navegación";
  });

  // Al tocar un enlace del menú en celular, el menú se cierra solo.
  nav.addEventListener("click", function (evento) {
    if (evento.target.tagName === "A") {
      nav.classList.remove("esta-abierto");
      boton.setAttribute("aria-expanded", "false");
    }
  });
}

/* -----------------------------------------------------------------------------
   1. PORTADA
----------------------------------------------------------------------------- */
function renderPortada() {
  const p = CONTENT.parroquia || {};

  document.querySelectorAll("[data-parroquia-nombre]").forEach(function (nodo) {
    nodo.textContent = p.nombre || "";
  });

  const distrito = document.querySelector("[data-parroquia-distrito]");
  if (distrito) distrito.textContent = p.distrito || "";

  const mision = document.querySelector("[data-parroquia-mision]");
  if (mision) {
    mision.textContent = p.mision || "";
    marcarSiEsPlaceholder(mision, p);
  }

  const bajada = document.querySelector("[data-parroquia-bajada]");
  if (bajada) bajada.textContent = p.bajada || "";
}

function renderRedes() {
  const lista = id("redes");
  const redes = CONTENT.redes || {};
  if (!lista) return;

  const items = [
    { nombre: "Instagram", url: redes.instagram },
    { nombre: "Facebook", url: redes.facebook }
  ];

  items.forEach(function (red) {
    if (!red.url) return;                 // si está vacío, no se muestra
    const li = el("li");
    const a = el("a", null, red.nombre);
    a.href = red.url;
    a.target = "_blank";
    a.rel = "noopener";
    li.appendChild(a);
    lista.appendChild(li);
  });

  marcarSiEsPlaceholder(lista, redes);
}

/* -----------------------------------------------------------------------------
   2. SOBRE NOSOTROS
----------------------------------------------------------------------------- */
function renderSobreNosotros() {
  const datos = CONTENT.sobreNosotros || {};

  const historia = id("historia");
  if (historia) {
    historia.textContent = datos.historia || "";
    marcarSiEsPlaceholder(historia, datos);
  }

  const direccion = id("direccion");
  if (direccion) direccion.textContent = datos.direccion || "";

  const mapa = id("mapa");
  if (mapa && datos.mapaEmbedUrl) mapa.src = datos.mapaEmbedUrl;
}

function renderDirectiva() {
  const lista = id("directiva");
  if (!lista) return;

  (CONTENT.directiva || []).forEach(function (persona) {
    const li = el("li", "directiva__item");

    if (persona.foto) {
      const img = el("img", "directiva__foto");
      img.src = persona.foto;
      img.alt = "Fotografía de " + (persona.nombre || "integrante del equipo pastoral");
      img.loading = "lazy";
      li.appendChild(img);
    } else {
      // Sin foto: círculo con la inicial. aria-hidden porque el nombre ya se lee debajo.
      const inicial = el("div", "directiva__inicial", (persona.nombre || "?").trim().charAt(0));
      inicial.setAttribute("aria-hidden", "true");
      li.appendChild(inicial);
    }

    const nombre = el("p", "directiva__nombre", persona.nombre);
    marcarSiEsPlaceholder(nombre, persona);
    li.appendChild(nombre);
    li.appendChild(el("p", "directiva__cargo", persona.cargo));

    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   3. MISAS Y HORARIOS
----------------------------------------------------------------------------- */
function renderHorarios() {
  const lista = id("horarios");
  if (!lista) return;

  (CONTENT.horariosMisa || []).forEach(function (bloque) {
    const li = el("li", "horarios__item");

    const dia = el("p", "horarios__dia", bloque.dia);
    marcarSiEsPlaceholder(dia, bloque);
    li.appendChild(dia);

    const horas = el("div", "horarios__horas");
    (bloque.horas || []).forEach(function (hora) {
      horas.appendChild(el("span", "horarios__hora", hora));
    });
    li.appendChild(horas);

    if (bloque.nota) li.appendChild(el("p", "horarios__nota", bloque.nota));

    lista.appendChild(li);
  });
}

function renderEspeciales() {
  const lista = id("especiales");
  if (!lista) return;

  (CONTENT.horariosEspeciales || []).forEach(function (item) {
    const li = el("li", "especiales__item");
    const titulo = el("span", "especiales__titulo", item.titulo);
    marcarSiEsPlaceholder(titulo, item);
    li.appendChild(titulo);
    li.appendChild(el("p", "especiales__detalle", item.detalle));
    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   BOTÓN "AGREGAR A GOOGLE CALENDAR"
   Toma la primera hora del bloque que diga "Domingo" y arma un evento para el
   próximo domingo. No inventa nada: si no hay domingo en content.js, el botón
   no se muestra.
----------------------------------------------------------------------------- */
function convertirHoraATexto(horaTexto) {
  // Acepta "7:00 am", "6:00 pm", "12:00 m."
  const limpio = (horaTexto || "").toLowerCase().trim();
  const partes = limpio.match(/(\d{1,2})[:.](\d{2})/);
  if (!partes) return null;

  let horas = parseInt(partes[1], 10);
  const minutos = parseInt(partes[2], 10);

  if (limpio.includes("pm") && horas < 12) horas += 12;
  if (limpio.includes("am") && horas === 12) horas = 0;

  return { horas: horas, minutos: minutos };
}

function formatoGoogleCalendar(fecha) {
  function dosDigitos(n) { return String(n).padStart(2, "0"); }
  return fecha.getFullYear() +
    dosDigitos(fecha.getMonth() + 1) +
    dosDigitos(fecha.getDate()) + "T" +
    dosDigitos(fecha.getHours()) +
    dosDigitos(fecha.getMinutes()) + "00";
}

function renderBotonCalendario() {
  const boton = id("btnCalendario");
  if (!boton) return;

  const bloqueDomingo = (CONTENT.horariosMisa || []).find(function (b) {
    return (b.dia || "").toLowerCase().includes("domingo");
  });

  const hora = bloqueDomingo ? convertirHoraATexto((bloqueDomingo.horas || [])[0]) : null;

  if (!hora) {
    boton.hidden = true;               // sin dato confiable, no mostramos el botón
    return;
  }

  // Próximo domingo (si hoy es domingo, toma el de la próxima semana).
  const inicio = new Date();
  const diasFaltantes = (7 - inicio.getDay()) % 7 || 7;
  inicio.setDate(inicio.getDate() + diasFaltantes);
  inicio.setHours(hora.horas, hora.minutos, 0, 0);

  const fin = new Date(inicio.getTime() + 60 * 60 * 1000); // dura 1 hora

  const parametros = new URLSearchParams({
    action: "TEMPLATE",
    text: "Misa dominical — " + (CONTENT.parroquia.nombre || "Parroquia"),
    dates: formatoGoogleCalendar(inicio) + "/" + formatoGoogleCalendar(fin),
    details: "Misa dominical en " + (CONTENT.parroquia.nombre || "la parroquia") + ".",
    location: (CONTENT.sobreNosotros && CONTENT.sobreNosotros.direccion) || ""
  });

  boton.href = "https://calendar.google.com/calendar/render?" + parametros.toString();
}

/* -----------------------------------------------------------------------------
   4. SERVICIOS PARROQUIALES
----------------------------------------------------------------------------- */
function renderServicios() {
  const lista = id("servicios-lista");
  if (!lista) return;

  (CONTENT.servicios || []).forEach(function (servicio) {
    const li = el("li", "servicios__item");

    const titulo = el("h3", "servicios__nombre", servicio.nombre);
    marcarSiEsPlaceholder(titulo, servicio);
    li.appendChild(titulo);

    if (servicio.requisitos && servicio.requisitos.length > 0) {
      li.appendChild(el("span", "servicios__etiqueta", "Requisitos"));
      const ul = el("ul", "servicios__requisitos");
      servicio.requisitos.forEach(function (req) {
        ul.appendChild(el("li", null, req));
      });
      li.appendChild(ul);
    }

    if (servicio.horario) {
      li.appendChild(el("span", "servicios__etiqueta", "Horario"));
      li.appendChild(el("p", "servicios__horario", servicio.horario));
    }

    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   6. EVENTOS
----------------------------------------------------------------------------- */
function renderEventos() {
  const proximos = id("eventos-proximos");
  const pasados = id("eventos-pasados");
  const lista = CONTENT.eventos || [];

  if (proximos) {
    const futuros = lista.filter(function (e) { return !e.pasado; });

    if (futuros.length === 0) {
      proximos.appendChild(el("li", "evento", "No hay eventos programados por ahora."));
    }

    futuros.forEach(function (evento) {
      const li = el("li", "evento");
      li.appendChild(el("p", "evento__fecha", evento.fecha));

      const titulo = el("h4", "evento__titulo", evento.titulo);
      marcarSiEsPlaceholder(titulo, evento);
      li.appendChild(titulo);

      li.appendChild(el("p", "evento__descripcion", evento.descripcion));
      proximos.appendChild(li);
    });
  }

  if (pasados) {
    const anteriores = lista.filter(function (e) { return e.pasado; });

    anteriores.forEach(function (evento) {
      const li = el("li", "galeria__item");

      if (evento.foto) {
        const img = el("img", "galeria__foto");
        img.src = evento.foto;
        img.alt = "Fotografía del evento: " + (evento.titulo || "");
        img.loading = "lazy";
        li.appendChild(img);
      } else {
        const vacio = el("div", "galeria__sinfoto", "Foto pendiente");
        vacio.setAttribute("aria-hidden", "true");
        li.appendChild(vacio);
      }

      const cuerpo = el("div", "galeria__cuerpo");
      const titulo = el("h4", "galeria__titulo", evento.titulo);
      marcarSiEsPlaceholder(titulo, evento);
      cuerpo.appendChild(titulo);
      cuerpo.appendChild(el("p", "galeria__fecha", evento.fecha));
      li.appendChild(cuerpo);

      pasados.appendChild(li);
    });
  }
}

/* -----------------------------------------------------------------------------
   7. TESTIMONIOS
----------------------------------------------------------------------------- */
function renderTestimonios() {
  const lista = id("testimonios");
  if (!lista) return;

  (CONTENT.testimonios || []).forEach(function (t) {
    const li = el("li", "testimonio");

    const blockquote = el("blockquote", "testimonio__texto", t.texto);
    li.appendChild(blockquote);

    const autor = el("p", "testimonio__autor", t.autor);
    marcarSiEsPlaceholder(autor, t);
    li.appendChild(autor);

    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   8. CONTACTO
   Si un formulario todavía tiene el enlace de ejemplo, el botón se desactiva
   en lugar de llevar a una página rota.
----------------------------------------------------------------------------- */
function configurarBotonFormulario(boton, url, textoPendiente) {
  if (!boton) return;

  if (esEnlaceSinConfigurar(url)) {
    boton.removeAttribute("href");
    boton.setAttribute("role", "link");
    boton.setAttribute("aria-disabled", "true");
    boton.classList.remove("boton--primario");
    boton.classList.add("boton--secundario");
    boton.textContent = textoPendiente;
    boton.style.pointerEvents = "none";
    boton.style.opacity = ".7";
    return;
  }

  boton.href = url;
}

function renderContacto() {
  const c = CONTENT.contacto || {};

  configurarBotonFormulario(id("formContacto"), c.googleFormContacto, "Formulario aún no configurado");
  configurarBotonFormulario(id("formAyuda"), c.googleFormAyuda, "Formulario aún no configurado");

  const whatsapp = id("whatsappLink");
  if (whatsapp && c.whatsapp) {
    const mensaje = encodeURIComponent("Hola, escribo desde la página de la parroquia.");
    whatsapp.href = "https://wa.me/" + c.whatsapp + "?text=" + mensaje;
  }
}

/* -----------------------------------------------------------------------------
   ARRANQUE
   Se ejecuta cuando la página terminó de cargar.
----------------------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", function () {
  if (typeof CONTENT === "undefined") {
    console.error("No se pudo leer content.js. Revisa que el archivo no tenga errores de sintaxis.");
    return;
  }

  iniciarNavegacion();

  renderPortada();
  renderRedes();
  renderSobreNosotros();
  renderDirectiva();
  renderHorarios();
  renderEspeciales();
  renderBotonCalendario();
  renderServicios();
  renderEventos();
  renderTestimonios();
  renderContacto();

  // Módulos en sus propios archivos
  renderCampanas();      // js/donaciones.js
  iniciarBot();          // js/bot.js
  generarCodigosQR();    // js/qr.js
});
