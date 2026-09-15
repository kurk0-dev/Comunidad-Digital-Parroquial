/* =============================================================================
   main.js — Lógica compartida por todas las páginas
   -----------------------------------------------------------------------------
   Este archivo NO contiene textos de la parroquia: los lee de content.js.
   Si quieres cambiar contenido, edita content.js, no este archivo.

   La cabecera y el pie se generan aquí, una sola vez, y se insertan en las 6
   páginas. Así, para agregar o quitar una pestaña del menú, se edita solo la
   lista PAGINAS de aquí abajo y el cambio aparece en todo el sitio.
   ============================================================================= */

/* -----------------------------------------------------------------------------
   LAS PESTAÑAS DEL SITIO
   Para agregar una pestaña: crea el archivo .html, copia la estructura de otra
   página, y añade una línea a esta lista.
----------------------------------------------------------------------------- */
const PAGINAS = [
  { id: "inicio",     texto: "Inicio",      archivo: "index.html" },
  { id: "misas",      texto: "Misas",       archivo: "misas.html" },
  { id: "servicios",  texto: "Servicios",   archivo: "servicios.html" },
  { id: "donaciones", texto: "Donaciones",  archivo: "donaciones.html" },
  { id: "comunidad",  texto: "Comunidad",   archivo: "comunidad.html" },
  { id: "contacto",   texto: "Contacto",    archivo: "contacto.html" }
];

/* -----------------------------------------------------------------------------
   AYUDANTES (se usan también desde donaciones.js, bot.js y qr.js)
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

/** Devuelve la etiqueta "Ejemplo / Por confirmar" si el objeto la necesita. */
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

/** Qué página se está viendo: lo declara el atributo data-pagina del <body>. */
function paginaActual() {
  return document.body.getAttribute("data-pagina") || "inicio";
}

/* -----------------------------------------------------------------------------
   EL LOGO
   Si el archivo todavía no está en assets/img/, en vez de un ícono roto se
   muestra un aviso que dice exactamente qué archivo falta.
----------------------------------------------------------------------------- */
function crearLogo(clase, alto, mostrarAviso) {
  const p = CONTENT.parroquia || {};
  const img = el("img", clase);
  img.src = p.logo || "";
  img.alt = "Escudo de la " + (p.nombre || "parroquia");
  img.width = alto;
  img.height = alto;

  img.addEventListener("error", function () {
    if (!img.parentNode) return;

    /* En la portada mostramos un aviso visible que dice qué archivo falta.
       En la cabecera no: ahí ocuparía toda la barra, así que el logo
       simplemente se oculta y queda el nombre de la parroquia. */
    if (mostrarAviso) {
      const aviso = el("div", "logo-faltante");
      aviso.appendChild(el("strong", null, "Falta el logo"));
      aviso.appendChild(el("span", null, "Guarda el escudo de la parroquia como:"));
      aviso.appendChild(el("code", null, p.logo || "assets/img/logo-parroquia.png"));
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

  const p = CONTENT.parroquia || {};
  const actual = paginaActual();

  const cabecera = el("header", "cabecera");
  const fila = el("div", "contenedor cabecera__fila");

  /* --- Marca: logo + nombre --- */
  const marca = el("a", "marca");
  marca.href = "index.html";

  const logo = crearLogo("marca__logo", 56, false);
  marca.appendChild(logo);

  const textoMarca = el("span", "marca__texto");
  textoMarca.appendChild(document.createTextNode(p.nombre || ""));
  if (p.diocesis) textoMarca.appendChild(el("small", null, p.diocesis));
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
    // La pestaña en la que estamos se marca para lectores de pantalla y CSS.
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
     navegadores (sobre todo de celular) que cancelan la navegación cuando el
     elemento que pulsaste desaparece antes de que termine el evento. Como
     resultado, el menú se cerraba y la página no cambiaba.
     No hace falta cerrarlo: la página siguiente ya se carga con el menú
     cerrado, porque la cabecera se genera de nuevo en cada página. */
  nav.addEventListener("click", function (evento) {
    const enlace = evento.target.closest("a");
    if (!enlace) return;

    /* Única excepción: el enlace de la página en la que ya estamos. Ahí no
       hay navegación que esperar, así que lo cerramos nosotros y subimos
       al inicio. */
    if (enlace.getAttribute("aria-current") === "page") {
      evento.preventDefault();
      ponerMenu(false);
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });

  /* Cerrar el menú al tocar fuera de él o con la tecla Escape. */
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
function renderPie() {
  const destino = id("pie");
  if (!destino) return;

  const p = CONTENT.parroquia || {};
  const redes = CONTENT.redes || {};

  const pie = el("footer", "pie");
  const fila = el("div", "contenedor pie__fila");

  const izquierda = el("div");
  const cruz = el("p", "pie__cruz", "✝");
  cruz.setAttribute("aria-hidden", "true");
  izquierda.appendChild(cruz);
  izquierda.appendChild(el("p", "pie__nombre", p.nombre));
  izquierda.appendChild(el("p", "pie__meta", (p.diocesis || "") + " · " + (p.distrito || "")));

  const enlaces = el("div", "pie__enlaces");
  if (redes.facebook) {
    const fb = el("a", null, "Facebook");
    fb.href = redes.facebook; fb.target = "_blank"; fb.rel = "noopener";
    enlaces.appendChild(fb);
  }
  if (redes.instagram) {
    const ig = el("a", null, "Instagram");
    ig.href = redes.instagram; ig.target = "_blank"; ig.rel = "noopener";
    enlaces.appendChild(ig);
  }
  const mapa = (CONTENT.sobreNosotros || {}).mapaEnlace;
  if (mapa) {
    const m = el("a", null, "Cómo llegar");
    m.href = mapa; m.target = "_blank"; m.rel = "noopener";
    enlaces.appendChild(m);
  }
  izquierda.appendChild(enlaces);
  fila.appendChild(izquierda);

  const derecha = el("div");
  const navPie = el("div", "pie__enlaces");
  PAGINAS.forEach(function (pagina) {
    const a = el("a", null, pagina.texto);
    a.href = pagina.archivo;
    navPie.appendChild(a);
  });
  derecha.appendChild(navPie);
  derecha.appendChild(el(
    "p",
    "pie__meta",
    "Comunidad Digital y Solidaria · Proyecto de estudiantes de la Universidad de Lima"
  ));
  fila.appendChild(derecha);

  pie.appendChild(fila);
  destino.replaceWith(pie);
}

/* -----------------------------------------------------------------------------
   PÁGINA: INICIO
----------------------------------------------------------------------------- */
function renderPortada() {
  const caja = id("logoPortada");
  if (caja) caja.appendChild(crearLogo(null, 290, true));

  const p = CONTENT.parroquia || {};

  const distrito = id("portadaDistrito");
  if (distrito) distrito.textContent = (p.diocesis || "") + " · " + (p.distrito || "");

  const mision = id("portadaMision");
  if (mision) {
    mision.textContent = p.mision || "";
    marcarSiEsPlaceholder(mision, p);
  }

  const bajada = id("portadaBajada");
  if (bajada) bajada.textContent = p.bajada || "";
}

function renderRedes() {
  const lista = id("redes");
  if (!lista) return;

  const redes = CONTENT.redes || {};
  const items = [
    { nombre: "Facebook", url: redes.facebook },
    { nombre: "Instagram", url: redes.instagram }
  ];

  items.forEach(function (red) {
    if (!red.url) return;               // si está vacío, no se muestra
    const li = el("li");
    const a = el("a", null, red.nombre);
    a.href = red.url; a.target = "_blank"; a.rel = "noopener";
    li.appendChild(a);
    lista.appendChild(li);
  });
}

/* Tarjetas de acceso a las otras pestañas, en la página de inicio. */
function renderAccesos() {
  const lista = id("accesos");
  if (!lista) return;

  const accesos = [
    { titulo: "Misas y horarios", texto: "Horarios de cada día, fechas especiales y cómo guardar la misa dominical en tu calendario.", archivo: "misas.html" },
    { titulo: "Servicios y catequesis", texto: "Bautizos, confesiones, consejería y las inscripciones abiertas de catequesis.", archivo: "servicios.html" },
    { titulo: "Donaciones", texto: "Qué necesita la parroquia ahora mismo, cuánto falta para cada meta y dónde entregar.", archivo: "donaciones.html" },
    { titulo: "Comunidad", texto: "Eventos, actividades y testimonios de quienes forman parte de la parroquia.", archivo: "comunidad.html" },
    { titulo: "Contacto y ayuda", texto: "Escríbenos, o pide apoyo de forma anónima si tú o una familia cercana lo necesitan.", archivo: "contacto.html" }
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
   SOBRE NOSOTROS (en la página de inicio)
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

  const enlaceMapa = id("enlaceMapa");
  if (enlaceMapa && datos.mapaEnlace) enlaceMapa.href = datos.mapaEnlace;
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
   PÁGINA: MISAS
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
   Toma la primera hora del bloque que diga "Domingo" y arma el evento del
   próximo domingo. Si no hay domingo en content.js, el botón no se muestra. */
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
   PÁGINA: SERVICIOS Y CATEQUESIS
----------------------------------------------------------------------------- */
function renderCatequesis() {
  const caja = id("catequesis");
  if (!caja) return;

  const c = CONTENT.catequesis || {};

  // Inscripciones cerradas: el bloque entero desaparece.
  if (!c.inscripcionesAbiertas) { caja.hidden = true; return; }

  if (c.anuncio) caja.appendChild(el("span", "catequesis__anuncio", "¡" + c.anuncio + "!"));
  caja.appendChild(el("h2", "catequesis__titulo", c.titulo));

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
   PÁGINA: COMUNIDAD (eventos + testimonios)
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
      const titulo = el("h3", "evento__titulo", evento.titulo);
      marcarSiEsPlaceholder(titulo, evento);
      li.appendChild(titulo);
      li.appendChild(el("p", "evento__descripcion", evento.descripcion));
      proximos.appendChild(li);
    });
  }

  if (pasados) {
    lista.filter(function (e) { return e.pasado; }).forEach(function (evento) {
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
      const titulo = el("h3", "galeria__titulo", evento.titulo);
      marcarSiEsPlaceholder(titulo, evento);
      cuerpo.appendChild(titulo);
      cuerpo.appendChild(el("p", "galeria__fecha", evento.fecha));
      li.appendChild(cuerpo);
      pasados.appendChild(li);
    });
  }
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
   PÁGINA: CONTACTO
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
  renderRedes();
  renderAccesos();
  renderSobreNosotros();
  renderDirectiva();

  renderHorarios();
  renderEspeciales();
  renderBotonCalendario();

  renderCatequesis();
  renderServicios();

  renderEventos();
  renderTestimonios();
  renderContacto();

  renderCampanas();      // js/donaciones.js
  iniciarBot();          // js/bot.js
  generarCodigosQR();    // js/qr.js
});
