/* =============================================================================
   main.js — Lógica compartida por todas las páginas
   -----------------------------------------------------------------------------
   Este archivo NO contiene textos del grupo: los lee de content.js.
   Si quieres cambiar contenido, edita content.js, no este archivo.

   La cabecera y el pie se generan aquí, una sola vez, y se insertan en las 6
   páginas. Para agregar o quitar una pestaña del menú, se edita solo la lista
   PAGINAS de aquí abajo y el cambio aparece en todo el sitio.
   ============================================================================= */

/* -----------------------------------------------------------------------------
   LAS PESTAÑAS DEL SITIO
----------------------------------------------------------------------------- */
const PAGINAS = [
  { id: "inicio",      texto: "Inicio",           archivo: "index.html" },
  { id: "grupo",       texto: "Grupo de oración", archivo: "grupo.html" },
  { id: "capilla",     texto: "Capilla",          archivo: "capilla.html" },
  { id: "comunidad",   texto: "Comunidad",        archivo: "comunidad.html" },
  { id: "solidaridad", texto: "Solidaridad",      archivo: "solidaridad.html" },
  { id: "contacto",    texto: "Contacto",         archivo: "contacto.html" }
];

/* -----------------------------------------------------------------------------
   AYUDANTES (se usan también desde donaciones.js, bot.js, qr.js e instagram.js)
----------------------------------------------------------------------------- */

/**
 * Crea un elemento con clase y texto.
 * Usamos textContent (y no innerHTML) para que un texto con símbolos como
 * & o < no rompa la página ni pueda inyectar código.
 */
function el(tag, clase, texto) {
  const nodo = document.createElement(tag);
  if (clase) nodo.className = clase;
  if (texto !== undefined && texto !== null && texto !== "") nodo.textContent = texto;
  return nodo;
}

/** Devuelve la etiqueta "Ejemplo / Por confirmar" si el objeto la necesita. */
function etiquetaPlaceholder(obj) {
  if (!obj || obj.esPlaceholder !== true) return null;
  const tag = el("span", "placeholder-tag", "Ejemplo / Por confirmar");
  tag.setAttribute("title", "Este dato todavía no ha sido confirmado por el grupo.");
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

/**
 * SEGURIDAD: filtra las direcciones antes de ponerlas en un enlace o imagen.
 *
 * Solo deja pasar http:// y https:// y rutas del propio sitio. Cualquier otra
 * cosa devuelve "" y no se muestra.
 *
 * Por qué existe: content.js lo edita gente del grupo y el archivo vive en un
 * repositorio público. Si alguien escribiera ahí una dirección que empiece por
 * "javascript:", el navegador ejecutaría ese código al hacer clic. Esto lo
 * impide de raíz.
 *
 * No borres esta función ni la rodees: si una dirección legítima no aparece,
 * revisa que empiece por https://
 */
function urlSegura(url) {
  if (typeof url !== "string") return "";

  const limpia = url.trim();
  if (!limpia) return "";

  // Rutas del propio sitio: "capilla.html", "assets/img/foto.webp"
  if (/^[\w.\-/]+$/.test(limpia) && !limpia.includes(":")) return limpia;

  try {
    const analizada = new URL(limpia, window.location.href);
    if (analizada.protocol === "http:" || analizada.protocol === "https:") {
      return analizada.href;
    }
  } catch (e) {
    return "";                          // dirección mal escrita
  }

  console.warn("Dirección descartada por seguridad en content.js:", limpia);
  return "";
}

/**
 * Dirección de Google Maps hacia la capilla, construida desde las coordenadas
 * de content.js. La usan el botón, la capa sobre el mapa, la tarjeta de la
 * reunión y el pie, para que todos lleven exactamente al mismo sitio.
 */
function urlGoogleMaps() {
  const coords = ((CONTENT.capilla || {}).coordenadas || "").replace(/\s/g, "");
  if (!coords) return "";
  return "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(coords);
}

/* --- WhatsApp ---
   El número de ejemplo (51900000000) tiene formato de celular peruano válido:
   si dejáramos los botones activos, alguien podría escribirle a un
   desconocido. Por eso se trata como "sin configurar" y los botones se apagan. */
const WHATSAPP_DE_EJEMPLO = "51900000000";

function numeroWhatsapp() {
  // Solo dígitos: evita que un valor mal escrito altere el destino del enlace.
  const numero = String((CONTENT.contacto || {}).whatsapp || "").replace(/\D/g, "");
  if (!numero || numero === WHATSAPP_DE_EJEMPLO) return "";
  return numero;
}

function urlWhatsapp(mensaje) {
  const numero = numeroWhatsapp();
  if (!numero) return "";
  return "https://wa.me/" + numero + "?text=" + encodeURIComponent(mensaje || "Hola, escribo desde la página del grupo de oración.");
}

/** Deja un botón visible pero apagado, con un texto que explica por qué. */
function apagarBoton(boton, texto) {
  boton.removeAttribute("href");
  boton.removeAttribute("target");
  boton.setAttribute("role", "link");
  boton.setAttribute("aria-disabled", "true");
  boton.classList.remove("boton--primario", "boton--whatsapp");
  boton.classList.add("boton--secundario");
  boton.textContent = texto;
}

/** Ícono SVG simple, construido sin innerHTML. */
function icono(trazos, tam) {
  const NS = "http://www.w3.org/2000/svg";
  const svg = document.createElementNS(NS, "svg");
  svg.setAttribute("viewBox", "0 0 24 24");
  svg.setAttribute("width", String(tam || 16));
  svg.setAttribute("height", String(tam || 16));
  svg.setAttribute("fill", "none");
  svg.setAttribute("stroke", "currentColor");
  svg.setAttribute("stroke-width", "2");
  svg.setAttribute("stroke-linecap", "round");
  svg.setAttribute("stroke-linejoin", "round");
  svg.setAttribute("aria-hidden", "true");
  trazos.forEach(function (d) {
    const path = document.createElementNS(NS, "path");
    path.setAttribute("d", d);
    svg.appendChild(path);
  });
  return svg;
}

const ICONO_CANDADO = ["M7 11V7a5 5 0 0 1 10 0v4", "M5 11h14v10H5z"];

/** Qué página se está viendo: lo declara el atributo data-pagina del <body>. */
function paginaActual() {
  return document.body.getAttribute("data-pagina") || "inicio";
}

/* -----------------------------------------------------------------------------
   EL ESCUDO
   Si el archivo no está, en vez de un ícono roto se muestra un aviso que dice
   exactamente qué archivo falta.
----------------------------------------------------------------------------- */
function crearLogo(clase, alto, mostrarAviso) {
  const g = CONTENT.grupo || {};
  const img = el("img", clase);
  img.src = urlSegura(g.logo);
  img.alt = "Escudo del " + (g.nombre || "grupo de oración");
  img.width = alto;
  img.height = alto;

  img.addEventListener("error", function () {
    if (!img.parentNode) return;
    if (mostrarAviso) {
      const aviso = el("div", "logo-faltante");
      aviso.appendChild(el("strong", null, "Falta el escudo"));
      aviso.appendChild(el("span", null, "Guarda el escudo del grupo como:"));
      aviso.appendChild(el("code", null, g.logo || "assets/img/escudo.webp"));
      img.parentNode.replaceChild(aviso, img);
    } else {
      img.remove();
    }
  });

  return img;
}

/* -----------------------------------------------------------------------------
   CABECERA
----------------------------------------------------------------------------- */
function renderCabecera() {
  const destino = id("cabecera");
  if (!destino) return;

  const g = CONTENT.grupo || {};
  const actual = paginaActual();

  const cabecera = el("header", "cabecera");
  const fila = el("div", "contenedor cabecera__fila");

  /* --- Marca: escudo + nombre --- */
  const marca = el("a", "marca");
  marca.href = "index.html";
  marca.appendChild(crearLogo("marca__logo", 54, false));

  const textoMarca = el("span", "marca__texto");
  textoMarca.appendChild(document.createTextNode(g.nombreCorto || g.nombre || ""));
  if (g.diocesis) textoMarca.appendChild(el("small", null, "RCC · " + g.diocesis));
  marca.appendChild(textoMarca);
  fila.appendChild(marca);

  /* --- Botón de menú (solo en celular) --- */
  const boton = el("button", "nav-toggle");
  boton.id = "navToggle";
  boton.type = "button";
  boton.setAttribute("aria-expanded", "false");
  boton.setAttribute("aria-controls", "navPrincipal");
  const barras = el("span", "nav-toggle__barras");
  barras.setAttribute("aria-hidden", "true");
  boton.appendChild(barras);
  boton.appendChild(el("span", "sr-only", "Abrir menú de navegación"));
  fila.appendChild(boton);

  /* --- Menú --- */
  const nav = el("nav", "nav");
  nav.id = "navPrincipal";
  nav.setAttribute("aria-label", "Navegación principal");

  const lista = el("ul", "nav__lista");
  PAGINAS.forEach(function (pagina) {
    const li = el("li");
    const a = el("a", null, pagina.texto);
    a.href = pagina.archivo;
    if (pagina.id === actual) a.setAttribute("aria-current", "page");
    li.appendChild(a);
    lista.appendChild(li);
  });
  nav.appendChild(lista);
  fila.appendChild(nav);

  cabecera.appendChild(fila);
  destino.replaceWith(cabecera);

  /* --- Comportamiento del menú de las tres rayas (solo en celular) --- */
  function ponerMenu(abierto) {
    nav.classList.toggle("esta-abierto", abierto);
    boton.setAttribute("aria-expanded", abierto ? "true" : "false");
    boton.querySelector(".sr-only").textContent =
      abierto ? "Cerrar menú de navegación" : "Abrir menú de navegación";
  }

  boton.addEventListener("click", function () {
    ponerMenu(!nav.classList.contains("esta-abierto"));
  });

  /* IMPORTANTE: al tocar un enlace NO cerramos el menú.
     Cerrarlo le pone display:none al enlace en mitad del clic, y hay
     navegadores de celular que cancelan la navegación cuando el elemento que
     pulsaste desaparece antes de que termine el evento. No hace falta cerrarlo:
     la página siguiente ya se carga con el menú cerrado. */
  nav.addEventListener("click", function (evento) {
    const enlace = evento.target.closest("a");
    if (!enlace) return;

    // Única excepción: la pestaña en la que ya estamos. Ahí no hay navegación.
    if (enlace.getAttribute("aria-current") === "page") {
      evento.preventDefault();
      ponerMenu(false);
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });

  document.addEventListener("click", function (evento) {
    if (!nav.classList.contains("esta-abierto")) return;
    if (nav.contains(evento.target) || boton.contains(evento.target)) return;
    ponerMenu(false);
  });

  document.addEventListener("keydown", function (evento) {
    if (evento.key === "Escape" && nav.classList.contains("esta-abierto")) {
      ponerMenu(false);
      boton.focus();
    }
  });
}

/* -----------------------------------------------------------------------------
   PIE DE PÁGINA
----------------------------------------------------------------------------- */
function enlaceExterno(texto, url) {
  const a = el("a", null, texto);
  a.href = urlSegura(url);
  a.target = "_blank";
  a.rel = "noopener";
  return a;
}

function urlInstagram() {
  const usuario = String((CONTENT.redes || {}).instagram || "").replace(/^@/, "").trim();
  if (!/^[A-Za-z0-9._]{1,30}$/.test(usuario)) return "";
  return "https://www.instagram.com/" + usuario + "/";
}

function renderPie() {
  const destino = id("pie");
  if (!destino) return;

  const g = CONTENT.grupo || {};
  const redes = CONTENT.redes || {};

  const pie = el("footer", "pie");
  const fila = el("div", "contenedor pie__fila");

  const izquierda = el("div");
  const marca = el("div", "pie__marca");
  marca.appendChild(crearLogo(null, 52, false));
  const nombres = el("div");
  nombres.appendChild(el("p", "pie__nombre", g.nombre));
  nombres.appendChild(el("p", "pie__meta", (g.movimiento || "") + " · " + (g.diocesis || "")));
  marca.appendChild(nombres);
  izquierda.appendChild(marca);

  const capilla = CONTENT.capilla || {};
  if (capilla.nombre) izquierda.appendChild(el("p", "pie__meta", "Nos reunimos en la " + capilla.nombre));

  const enlaces = el("div", "pie__enlaces");
  const ig = urlInstagram();
  if (ig) enlaces.appendChild(enlaceExterno("Instagram", ig));
  if (redes.facebook) enlaces.appendChild(enlaceExterno("Facebook", redes.facebook));
  const mapa = urlGoogleMaps();
  if (mapa) enlaces.appendChild(enlaceExterno("Cómo llegar", mapa));
  const wa = urlWhatsapp();
  if (wa) enlaces.appendChild(enlaceExterno("WhatsApp", wa));
  izquierda.appendChild(enlaces);
  fila.appendChild(izquierda);

  const derecha = el("div");
  const navPie = el("nav", "pie__enlaces");
  navPie.setAttribute("aria-label", "Pestañas del sitio");
  PAGINAS.forEach(function (pagina) {
    const a = el("a", null, pagina.texto);
    a.href = pagina.archivo;
    navPie.appendChild(a);
  });
  derecha.appendChild(navPie);
  fila.appendChild(derecha);

  pie.appendChild(fila);
  destino.replaceWith(pie);
}

/* -----------------------------------------------------------------------------
   PORTADA
----------------------------------------------------------------------------- */
function renderPortada() {
  const caja = id("logoPortada");
  if (caja) caja.appendChild(crearLogo(null, 300, true));

  const g = CONTENT.grupo || {};
  const p = CONTENT.portada || {};

  const sobretitulo = id("portadaSobretitulo");
  if (sobretitulo) sobretitulo.textContent = (g.movimiento || "") + " · " + (g.diocesis || "");

  const titulo = id("portadaTitulo");
  if (titulo) titulo.textContent = g.nombre || "";

  const mision = id("portadaMision");
  if (mision) {
    mision.textContent = p.mision || "";
    marcarSiEsPlaceholder(mision, p);
  }

  const bajada = id("portadaBajada");
  if (bajada) bajada.textContent = p.bajada || "";
}

/* Tarjeta amarilla con el día, la hora y el lugar de la reunión semanal. */
function renderReunion() {
  const caja = id("reunion");
  if (!caja) return;

  const r = CONTENT.reunion || {};

  const etiqueta = el("p", "reunion__etiqueta", "Nos reunimos");
  marcarSiEsPlaceholder(etiqueta, r);
  caja.appendChild(etiqueta);

  caja.appendChild(el("p", "reunion__cuando", [r.dia, r.hora].filter(Boolean).join(" · ")));
  caja.appendChild(el("p", "reunion__donde", r.lugar || ""));

  const acciones = el("div", "reunion__acciones");

  const mapa = urlGoogleMaps();
  if (mapa) {
    const llegar = el("a", "boton boton--oscuro", "Cómo llegar");
    llegar.href = urlSegura(mapa); llegar.target = "_blank"; llegar.rel = "noopener";
    acciones.appendChild(llegar);
  }

  const wa = el("a", "boton boton--secundario", "Escríbenos");
  wa.setAttribute("data-whatsapp", "Hola, quiero ir a la reunión del grupo de oración.");
  acciones.appendChild(wa);

  caja.appendChild(acciones);
}

function renderPrimeraVez() {
  const lista = id("primeraVez");
  if (!lista) return;

  (CONTENT.primeraVez || []).forEach(function (punto, i) {
    const li = el("li", "punto");
    const marca = el("span", "punto__marca", String(i + 1));
    marca.setAttribute("aria-hidden", "true");
    li.appendChild(marca);
    const titulo = el("h3", "punto__titulo", punto.titulo);
    marcarSiEsPlaceholder(titulo, punto);
    li.appendChild(titulo);
    li.appendChild(el("p", "punto__texto", punto.texto));
    lista.appendChild(li);
  });
}

/* Tarjetas de acceso a las otras pestañas. */
function renderAccesos() {
  const lista = id("accesos");
  if (!lista) return;

  const accesos = [
    { titulo: "Grupo de oración", texto: "Quiénes somos, cómo es una reunión y el Seminario de Vida en el Espíritu.", archivo: "grupo.html" },
    { titulo: "Capilla Divino Niño", texto: "Horarios de misa, confesiones, catequesis y servicios de la capilla.", archivo: "capilla.html" },
    { titulo: "Comunidad", texto: "Nuestras publicaciones, fotos de los encuentros, próximos eventos y testimonios.", archivo: "comunidad.html" },
    { titulo: "Solidaridad", texto: "La campaña de Navidad, qué falta y cómo pedir ayuda si la necesitas.", archivo: "solidaridad.html" },
    { titulo: "Contacto", texto: "Escríbele al coordinador del grupo o déjanos un mensaje.", archivo: "contacto.html" }
  ];

  accesos.forEach(function (acceso) {
    const a = el("a", "acceso");
    a.href = acceso.archivo;
    a.appendChild(el("h3", "acceso__titulo", acceso.titulo));
    a.appendChild(el("p", "acceso__texto", acceso.texto));
    a.appendChild(el("span", "acceso__flecha", "Ver más →"));
    lista.appendChild(a);
  });
}

/* -----------------------------------------------------------------------------
   SEMINARIO DE VIDA EN EL ESPÍRITU
   data-seminario="resumen"  → portada (sin la lista de temas)
   data-seminario="completo" → página del grupo
----------------------------------------------------------------------------- */
function renderSeminario() {
  const s = CONTENT.seminario || {};

  document.querySelectorAll("[data-seminario]").forEach(function (caja) {
    const completo = caja.getAttribute("data-seminario") === "completo";

    if (s.imagen) {
      const img = el("img", "seminario__imagen");
      img.src = urlSegura(s.imagen);
      img.alt = "";                       // decorativa: la paloma del Espíritu Santo
      img.width = 220; img.height = 293;
      img.loading = "lazy";
      caja.appendChild(img);
    }

    const cuerpo = el("div");
    cuerpo.appendChild(el(completo ? "h2" : "h3", "seminario__titulo", s.titulo));
    cuerpo.appendChild(el("p", "seminario__texto", s.descripcion));

    if (completo) {
      const temas = Array.isArray(s.temas) ? s.temas.filter(Boolean) : [];
      const lista = el("ol", "temas");
      const cantidad = temas.length || 7;
      for (let i = 0; i < cantidad; i++) {
        const li = el("li", "tema");
        const numero = el("span", "tema__numero", String(i + 1));
        numero.setAttribute("aria-hidden", "true");
        li.appendChild(numero);
        li.appendChild(el("span", null, temas[i] || ("Viernes " + (i + 1))));
        lista.appendChild(li);
      }
      cuerpo.appendChild(lista);

      if (!temas.length) {
        const nota = el("p", "temas__nota", "Los nombres de los 7 temas se publicarán aquí.");
        marcarSiEsPlaceholder(nota, { esPlaceholder: true });
        cuerpo.appendChild(nota);
      }
    }

    const acciones = el("div", "reunion__acciones");
    if (s.proximo) {
      cuerpo.appendChild(el("p", "seminario__texto", "Próximo seminario: " + s.proximo));
    }
    const aviso = el("a", "boton boton--primario", s.proximo ? "Quiero participar" : "Avísame del próximo seminario");
    aviso.setAttribute("data-whatsapp", "Hola, quiero participar en el próximo Seminario de Vida en el Espíritu.");
    acciones.appendChild(aviso);

    if (!completo) {
      const ver = el("a", "boton boton--secundario", "Ver el seminario");
      ver.href = "grupo.html#seminario";
      acciones.appendChild(ver);
    }
    cuerpo.appendChild(acciones);
    caja.appendChild(cuerpo);
  });
}

/* -----------------------------------------------------------------------------
   PÁGINA: GRUPO DE ORACIÓN
----------------------------------------------------------------------------- */
function renderQuienesSomos() {
  const q = CONTENT.quienesSomos || {};

  const rcc = id("textoRcc");
  if (rcc) rcc.textContent = q.rcc || "";

  const historia = id("textoHistoria");
  if (historia) {
    historia.textContent = q.historia || "";
    marcarSiEsPlaceholder(historia, q);
  }
}

function renderServidores() {
  const s = CONTENT.servidores || {};

  const foto = id("servidoresFoto");
  if (foto && s.foto) {
    const img = el("img");
    img.src = urlSegura(s.foto);
    img.alt = s.alt || "Equipo de servidores del grupo";
    img.width = 1200; img.height = 900;
    img.loading = "lazy";
    foto.appendChild(img);
  }

  const texto = id("servidoresTexto");
  if (texto) {
    texto.textContent = s.texto || "";
    marcarSiEsPlaceholder(texto, s);
  }
}

/* Galería de fotos. data-limite="6" muestra solo las primeras 6. */
function renderGaleria() {
  const lista = id("galeria");
  if (!lista) return;

  const limite = parseInt(lista.getAttribute("data-limite"), 10) || Infinity;

  (CONTENT.galeria || []).slice(0, limite).forEach(function (item) {
    const src = urlSegura(item.foto);
    if (!src) return;
    const figura = el("figure", "foto");
    const img = el("img");
    img.src = src;
    img.alt = item.alt || "";
    img.width = 1200; img.height = 900;
    img.loading = "lazy";
    img.decoding = "async";
    figura.appendChild(img);
    if (item.pie) figura.appendChild(el("figcaption", null, item.pie));
    const li = el("li");
    li.appendChild(figura);
    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   UBICACIÓN (mapa en la portada)
----------------------------------------------------------------------------- */
function renderUbicacion() {
  const c = CONTENT.capilla || {};

  const lugar = id("ubicacionLugar");
  if (lugar) lugar.textContent = c.nombre || "";

  const direccion = id("ubicacionDireccion");
  if (direccion) direccion.textContent = c.direccion || "";

  const coords = (c.coordenadas || "").replace(/\s/g, "");
  if (!coords) return;

  const etiqueta = c.etiquetaMapa || c.nombre || "";
  const urlEmbed = "https://www.google.com/maps?q=" +
    encodeURIComponent(coords) +
    (etiqueta ? "(" + encodeURIComponent(etiqueta) + ")" : "") +
    "&z=17&hl=es&output=embed";

  const mapa = id("mapa");
  if (mapa) mapa.src = urlSegura(urlEmbed);

  document.querySelectorAll("[data-maps]").forEach(function (enlace) {
    enlace.href = urlSegura(urlGoogleMaps());
    enlace.target = "_blank";
    enlace.rel = "noopener";
  });
}

/* -----------------------------------------------------------------------------
   PÁGINA: CAPILLA
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

/* --- Botón "Agregar a Google Calendar" ---
   Toma la primera hora del bloque "Domingo" y arma el evento del próximo
   domingo. Si no hay domingo en content.js, el botón no se muestra. */
function convertirHoraATexto(horaTexto) {
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
  if (!hora) { boton.hidden = true; return; }

  const inicio = new Date();
  const diasFaltantes = (7 - inicio.getDay()) % 7 || 7;
  inicio.setDate(inicio.getDate() + diasFaltantes);
  inicio.setHours(hora.horas, hora.minutos, 0, 0);
  const fin = new Date(inicio.getTime() + 60 * 60 * 1000);

  const capilla = CONTENT.capilla || {};
  const parametros = new URLSearchParams({
    action: "TEMPLATE",
    text: "Misa dominical — " + (capilla.nombre || "Capilla"),
    dates: formatoGoogleCalendar(inicio) + "/" + formatoGoogleCalendar(fin),
    details: "Misa dominical en la " + (capilla.nombre || "capilla") + ".",
    location: [capilla.nombre, capilla.direccion].filter(Boolean).join(", ")
  });

  boton.href = "https://calendar.google.com/calendar/render?" + parametros.toString();
}

function renderCatequesis() {
  const caja = id("catequesis");
  if (!caja) return;

  const c = CONTENT.catequesis || {};
  if (!c.inscripcionesAbiertas) { caja.hidden = true; return; }

  if (c.anuncio) caja.appendChild(el("span", "catequesis__anuncio", c.anuncio));
  const titulo = el("h3", "catequesis__titulo", c.titulo);
  marcarSiEsPlaceholder(titulo, c);
  caja.appendChild(titulo);

  const grupos = el("div", "catequesis__grupos");
  (c.grupos || []).forEach(function (grupo) {
    const div = el("div", "grupo");
    div.appendChild(el("p", "grupo__publico", grupo.publico));
    div.appendChild(el("p", "grupo__edades", grupo.edades));
    const sacramentos = el("div", "grupo__sacramentos");
    (grupo.sacramentos || []).forEach(function (s) {
      sacramentos.appendChild(el("span", "grupo__sacramento", s));
    });
    div.appendChild(sacramentos);
    grupos.appendChild(div);
  });
  caja.appendChild(grupos);

  const informes = c.informes || {};
  const bloque = el("div", "catequesis__informes");
  bloque.appendChild(el("span", "catequesis__informes-titulo", "Informes e inscripciones"));
  const cuando = el("p");
  cuando.appendChild(el("strong", null, informes.dias || ""));
  cuando.appendChild(document.createTextNode(" · " + (informes.horario || "")));
  bloque.appendChild(cuando);
  if (informes.lugar) bloque.appendChild(el("p", "catequesis__lugar", informes.lugar));
  caja.appendChild(bloque);
}

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
      servicio.requisitos.forEach(function (req) { ul.appendChild(el("li", null, req)); });
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
   PÁGINA: COMUNIDAD
----------------------------------------------------------------------------- */
function renderEventos() {
  const lista = id("eventos");
  if (!lista) return;

  const eventos = CONTENT.eventos || [];
  if (eventos.length === 0) {
    lista.appendChild(el("li", "evento", "No hay eventos programados por ahora."));
  }
  eventos.forEach(function (evento) {
    const li = el("li", "evento");
    li.appendChild(el("p", "evento__fecha", evento.fecha));
    const titulo = el("h3", "evento__titulo", evento.titulo);
    marcarSiEsPlaceholder(titulo, evento);
    li.appendChild(titulo);
    li.appendChild(el("p", "evento__descripcion", evento.descripcion));
    lista.appendChild(li);
  });
}

function renderTestimonios() {
  const lista = id("testimonios");
  if (!lista) return;

  (CONTENT.testimonios || []).forEach(function (t) {
    const li = el("li", "testimonio");
    li.appendChild(el("blockquote", "testimonio__texto", t.texto));
    const autor = el("p", "testimonio__autor", t.autor);
    marcarSiEsPlaceholder(autor, t);
    li.appendChild(autor);
    lista.appendChild(li);
  });
}

/* -----------------------------------------------------------------------------
   FORMULARIOS, WHATSAPP Y AVISO DE PRIVACIDAD
   Se conectan por atributos, así cualquier página puede tener uno:
     data-form="contacto|ayuda|donacion"   → abre el Google Form
     data-whatsapp="mensaje"               → abre WhatsApp con ese mensaje
     data-privacidad                       → aviso pequeño de protección de datos
----------------------------------------------------------------------------- */
function renderContacto() {
  const c = CONTENT.contacto || {};
  const d = CONTENT.donaciones || {};

  const formularios = {
    contacto: c.googleFormContacto,
    ayuda: c.googleFormAyuda,
    donacion: d.formularioDonacion
  };

  document.querySelectorAll("[data-form]").forEach(function (boton) {
    const url = formularios[boton.getAttribute("data-form")];
    if (esEnlaceSinConfigurar(url) || !urlSegura(url)) {
      apagarBoton(boton, "Formulario aún no configurado");
      return;
    }
    boton.href = urlSegura(url);
    boton.target = "_blank";
    boton.rel = "noopener";
  });

  document.querySelectorAll("[data-whatsapp]").forEach(function (boton) {
    const url = urlWhatsapp(boton.getAttribute("data-whatsapp"));
    if (!url) {
      apagarBoton(boton, "WhatsApp por confirmar");
      return;
    }
    boton.href = url;
    boton.target = "_blank";
    boton.rel = "noopener";
  });

  const responsable = id("contactoResponsable");
  if (responsable) {
    responsable.textContent = c.responsable || "";
    marcarSiEsPlaceholder(responsable, c);
  }

  const horario = id("contactoHorario");
  if (horario) horario.textContent = c.horarioAtencion || "";
}

function renderPrivacidad() {
  const texto = CONTENT.privacidad || "";
  if (!texto) return;

  document.querySelectorAll("[data-privacidad]").forEach(function (caja) {
    caja.classList.add("aviso-privacidad");
    caja.appendChild(icono(ICONO_CANDADO, 14));
    caja.appendChild(el("span", null, texto));
  });
}

/* -----------------------------------------------------------------------------
   ARRANQUE
   Cada función revisa si su contenedor existe en la página actual; si no está,
   no hace nada. Por eso el mismo main.js sirve para las 6 páginas.
----------------------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", function () {
  if (typeof CONTENT === "undefined") {
    console.error("No se pudo leer content.js. Revisa que el archivo no tenga errores de sintaxis.");
    return;
  }

  renderCabecera();
  renderPie();

  renderPortada();
  renderReunion();
  renderPrimeraVez();
  renderAccesos();
  renderSeminario();
  renderUbicacion();

  renderQuienesSomos();
  renderServidores();
  renderGaleria();

  renderHorarios();
  renderEspeciales();
  renderBotonCalendario();
  renderCatequesis();
  renderServicios();

  renderEventos();
  renderTestimonios();

  // Va después de todo lo anterior: conecta los botones que esas funciones crearon.
  renderContacto();
  renderPrivacidad();

  // Estos archivos solo se cargan en las páginas que los usan.
  if (typeof renderCampanas === "function") renderCampanas();       // js/donaciones.js
  if (typeof renderInstagram === "function") renderInstagram();     // js/instagram.js
  if (typeof generarCodigosQR === "function") generarCodigosQR();   // js/qr.js
  iniciarBot();                                                      // js/bot.js
});
