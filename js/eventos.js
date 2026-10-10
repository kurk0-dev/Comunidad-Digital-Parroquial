/* =============================================================================
   eventos.js — Eventos del grupo, carrusel de fotos y visor de fotos
   -----------------------------------------------------------------------------
   Lee CONTENT.eventos (content.js) y dibuja:
     · Próximos eventos: tarjetas sencillas.
     · Eventos pasados: tarjeta completa con los encuentros (por ejemplo, cada
       viernes del seminario: al tocarlo se abre su foto en grande) y un
       carrusel con todas las fotos del evento.

   Sin librerías: el carrusel usa el desplazamiento nativo del navegador
   (se desliza con el dedo) y el visor usa <dialog>, que ya trae el foco
   atrapado y el cierre con la tecla Escape.
   ============================================================================= */

const ICONO_ANTERIOR = ["M15 18l-6-6 6-6"];
const ICONO_SIGUIENTE = ["M9 18l6-6-6-6"];
const ICONO_CERRAR = ["M18 6L6 18", "M6 6l12 12"];
const ICONO_AMPLIAR = ["M15 3h6v6", "M9 21H3v-6", "M21 3l-7 7", "M3 21l7-7"];

/** Deja solo las fotos con una dirección válida. */
function fotosValidas(lista) {
  return (lista || [])
    .map(function (f) {
      const src = urlSegura(f && f.foto);
      return src ? { src: src, alt: f.alt || "", pie: f.pie || "" } : null;
    })
    .filter(Boolean);
}

function movimientoReducido() {
  return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

/* -----------------------------------------------------------------------------
   VISOR DE FOTOS (uno solo para toda la página)
----------------------------------------------------------------------------- */
const visor = { dialogo: null, fotos: [], indice: 0, origen: null };

function crearVisor() {
  const d = el("dialog", "visor");
  d.setAttribute("aria-label", "Foto ampliada");

  const figura = el("figure", "visor__figura");
  const img = el("img", "visor__img");
  img.alt = "";
  figura.appendChild(img);
  const pie = el("figcaption", "visor__pie");
  figura.appendChild(pie);
  d.appendChild(figura);

  function boton(clase, trazos, etiqueta, accion) {
    const b = el("button", "visor__boton " + clase);
    b.type = "button";
    b.appendChild(icono(trazos, 26));
    b.appendChild(el("span", "sr-only", etiqueta));
    b.addEventListener("click", accion);
    d.appendChild(b);
    return b;
  }

  boton("visor__cerrar", ICONO_CERRAR, "Cerrar", cerrarVisor);
  boton("visor__anterior", ICONO_ANTERIOR, "Foto anterior", function () { moverVisor(-1); });
  boton("visor__siguiente", ICONO_SIGUIENTE, "Foto siguiente", function () { moverVisor(1); });

  const contador = el("p", "visor__contador");
  contador.setAttribute("aria-live", "polite");
  d.appendChild(contador);

  // Deslizar con el dedo hacia un lado cambia de foto.
  let inicioX = null;
  let ultimoDeslizamiento = 0;
  d.addEventListener("pointerdown", function (e) { inicioX = e.clientX; });
  d.addEventListener("pointerup", function (e) {
    if (inicioX === null) return;
    const delta = e.clientX - inicioX;
    inicioX = null;
    if (Math.abs(delta) > 50) {
      ultimoDeslizamiento = Date.now();
      moverVisor(delta < 0 ? 1 : -1);
    }
  });

  // Tocar el fondo oscuro (fuera de la foto) también cierra. Se ignora el
  // "clic" que el navegador genera al terminar un deslizamiento sobre el fondo.
  d.addEventListener("click", function (evento) {
    if (evento.target === d && Date.now() - ultimoDeslizamiento > 400) cerrarVisor();
  });

  d.addEventListener("keydown", function (evento) {
    if (evento.key === "ArrowLeft") moverVisor(-1);
    if (evento.key === "ArrowRight") moverVisor(1);
  });

  // Al cerrarse (también con Escape): devolver el scroll y el foco.
  d.addEventListener("close", function () {
    document.documentElement.style.overflow = "";
    if (visor.origen && document.contains(visor.origen)) visor.origen.focus();
  });

  document.body.appendChild(d);
  return d;
}

function pintarVisor() {
  const d = visor.dialogo;
  const foto = visor.fotos[visor.indice];
  const img = d.querySelector(".visor__img");
  img.src = foto.src;
  img.alt = foto.alt;
  d.querySelector(".visor__pie").textContent = foto.pie;
  d.querySelector(".visor__contador").textContent = (visor.indice + 1) + " de " + visor.fotos.length;

  const varias = visor.fotos.length > 1;
  d.querySelector(".visor__anterior").hidden = !varias;
  d.querySelector(".visor__siguiente").hidden = !varias;
}

function abrirVisor(fotos, indice, origen) {
  if (!fotos.length) return;
  if (!visor.dialogo) visor.dialogo = crearVisor();
  visor.fotos = fotos;
  visor.indice = indice || 0;
  visor.origen = origen || null;
  pintarVisor();
  document.documentElement.style.overflow = "hidden";   // que no se mueva la página de fondo
  visor.dialogo.showModal();
  visor.dialogo.querySelector(".visor__cerrar").focus();
}

function moverVisor(paso) {
  const total = visor.fotos.length;
  if (total < 2) return;
  visor.indice = (visor.indice + paso + total) % total;   // da la vuelta al llegar al final
  pintarVisor();
}

function cerrarVisor() {
  if (visor.dialogo && visor.dialogo.open) visor.dialogo.close();
}

/* -----------------------------------------------------------------------------
   CARRUSEL
   Una fila de fotos que se desliza con el dedo, con botones grandes de
   "anterior / siguiente" para quien no sabe deslizar. Sin avance automático:
   el ritmo lo pone quien mira.
----------------------------------------------------------------------------- */
function crearCarrusel(fotos, nombre) {
  const caja = el("div", "carrusel");
  caja.setAttribute("role", "region");
  caja.setAttribute("aria-roledescription", "carrusel");
  caja.setAttribute("aria-label", "Fotos de " + nombre);

  const pista = el("ul", "carrusel__pista");
  pista.tabIndex = 0;                       // con teclado: flechas para desplazarse
  fotos.forEach(function (foto, i) {
    const li = el("li", "carrusel__item");
    li.setAttribute("aria-roledescription", "diapositiva");
    li.setAttribute("aria-label", (i + 1) + " de " + fotos.length);

    const boton = el("button", "carrusel__foto");
    boton.type = "button";
    const img = el("img");
    img.src = foto.src;
    img.alt = foto.alt;
    img.width = 1200; img.height = 900;
    img.loading = "lazy";
    img.decoding = "async";
    boton.appendChild(img);

    const ampliar = el("span", "carrusel__ampliar");
    ampliar.appendChild(icono(ICONO_AMPLIAR, 16));
    ampliar.appendChild(el("span", "sr-only", "Ver en grande"));
    boton.appendChild(ampliar);

    if (foto.pie) boton.appendChild(el("span", "carrusel__pie", foto.pie));
    boton.addEventListener("click", function () { abrirVisor(fotos, i, boton); });

    li.appendChild(boton);
    pista.appendChild(li);
  });
  caja.appendChild(pista);

  const controles = el("div", "carrusel__controles");
  function flecha(trazos, etiqueta, direccion) {
    const b = el("button", "carrusel__flecha");
    b.type = "button";
    b.appendChild(icono(trazos, 22));
    b.appendChild(el("span", "sr-only", etiqueta));
    b.addEventListener("click", function () { desplazar(direccion); });
    return b;
  }
  const anterior = flecha(ICONO_ANTERIOR, "Fotos anteriores", -1);
  const contador = el("p", "carrusel__contador");
  const siguiente = flecha(ICONO_SIGUIENTE, "Fotos siguientes", 1);
  controles.appendChild(anterior);
  controles.appendChild(contador);
  controles.appendChild(siguiente);
  caja.appendChild(controles);

  const items = Array.prototype.slice.call(pista.querySelectorAll(".carrusel__item"));

  /* Posición exacta de cada foto dentro de la pista. */
  function posicionDe(indice) {
    return items[indice].offsetLeft - items[0].offsetLeft;
  }

  /* Qué foto está al principio de la pista ahora mismo. */
  function indiceVisible() {
    let mejor = 0;
    items.forEach(function (item, i) {
      if (Math.abs(posicionDe(i) - pista.scrollLeft) < Math.abs(posicionDe(mejor) - pista.scrollLeft)) mejor = i;
    });
    return mejor;
  }

  /* Mientras dura el desplazamiento animado, el destino manda: así dos clics
     rápidos avanzan dos fotos en vez de pisarse a mitad de camino. */
  let destino = null;
  let destinoHasta = 0;

  function ultimoIndiceAlcanzable() {
    const maximo = pista.scrollWidth - pista.clientWidth;
    for (let i = 0; i < items.length; i++) if (posicionDe(i) >= maximo - 4) return i;
    return items.length - 1;
  }

  // Momento en que la persona tocó la pista por última vez (para no pelearle el dedo).
  let ultimoToque = 0;
  ["pointerdown", "wheel", "touchstart"].forEach(function (tipo) {
    pista.addEventListener(tipo, function () { ultimoToque = Date.now(); }, { passive: true });
  });

  function desplazar(direccion) {
    const desde = destino !== null && Date.now() < destinoHasta ? destino : indiceVisible();
    const objetivo = Math.max(0, Math.min(ultimoIndiceAlcanzable(), desde + direccion));
    const momentoDelClic = Date.now();
    destino = objetivo;
    destinoHasta = momentoDelClic + 700;
    // "instant" y no "auto": "auto" obedece al CSS, que podría volver a animarlo.
    pista.scrollTo({ left: posicionDe(objetivo), behavior: movimientoReducido() ? "instant" : "smooth" });
    actualizar();

    /* Red de seguridad: en un celular muy cargado la animación puede quedarse
       a medias. Si no llegó, salta directo, salvo que la persona haya
       empezado a deslizar con el dedo después del clic. */
    setTimeout(function () {
      const llego = Math.abs(pista.scrollLeft - posicionDe(objetivo)) <= 4;
      if (!llego && destino === objetivo && ultimoToque < momentoDelClic) {
        pista.scrollTo({ left: posicionDe(objetivo), behavior: "instant" });
      }
      actualizar();
    }, 750);
  }

  function actualizar() {
    const enCamino = destino !== null && Date.now() < destinoHasta;
    const actual = enCamino ? destino : indiceVisible();
    contador.textContent = (actual + 1) + " de " + fotos.length;
    anterior.disabled = actual <= 0;
    siguiente.disabled = actual >= ultimoIndiceAlcanzable();
  }

  pista.addEventListener("scroll", actualizar, { passive: true });
  window.addEventListener("resize", actualizar);
  pista.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") { e.preventDefault(); desplazar(-1); }
    if (e.key === "ArrowRight") { e.preventDefault(); desplazar(1); }
  });
  // El contador se calcula cuando el carrusel ya está en la página.
  setTimeout(actualizar, 0);

  return caja;
}

/* -----------------------------------------------------------------------------
   TARJETAS DE EVENTOS
----------------------------------------------------------------------------- */
function crearEventoProximo(e) {
  const li = el("li", "evento");
  li.id = slug(e.titulo);
  li.appendChild(el("p", "evento__fecha", [e.fecha, e.lugar].filter(Boolean).join(" · ")));
  const titulo = el("h3", "evento__titulo", e.titulo);
  marcarSiEsPlaceholder(titulo, e);
  li.appendChild(titulo);
  if (e.descripcion) li.appendChild(el("p", "evento__descripcion", e.descripcion));
  return li;
}

function crearEventoPasado(e) {
  const articulo = el("article", "pasado");
  articulo.id = slug(e.titulo);

  /* --- Cabecera: símbolo + datos --- */
  const cabecera = el("div", "pasado__cabecera");
  const simbolo = urlSegura(e.simbolo);
  if (simbolo) {
    const img = el("img", "pasado__simbolo");
    img.src = simbolo;
    img.alt = "";                             // decorativa
    img.width = 160; img.height = 213;
    img.loading = "lazy";
    cabecera.appendChild(img);
  }
  const datos = el("div");
  datos.appendChild(el("p", "evento__fecha", [e.fecha, e.lugar].filter(Boolean).join(" · ")));
  const titulo = el("h3", "pasado__titulo", e.titulo);
  marcarSiEsPlaceholder(titulo, e);
  datos.appendChild(titulo);
  if (e.descripcion) datos.appendChild(el("p", "pasado__texto", e.descripcion));
  cabecera.appendChild(datos);
  articulo.appendChild(cabecera);

  /* --- Cada encuentro (viernes): toca y se abre su foto --- */
  const encuentros = (e.viernes || []).map(function (v, i) {
    const src = urlSegura(v.foto);
    const nombre = "Viernes " + (i + 1);
    return src ? { src: src, alt: v.alt || "", pie: v.tema ? nombre + " · " + v.tema : nombre, tema: v.tema || "", nombre: nombre } : null;
  }).filter(Boolean);

  if (encuentros.length) {
    const subtitulo = el("h4", "pasado__subtitulo", "Revive cada viernes");
    if (e.viernesEsPlaceholder) marcarSiEsPlaceholder(subtitulo, { esPlaceholder: true });
    articulo.appendChild(subtitulo);
    articulo.appendChild(el("p", "pasado__ayuda", "Toca un viernes para ver su foto."));

    const lista = el("ol", "viernes");
    encuentros.forEach(function (enc, i) {
      const li = el("li");
      const b = el("button", "viernes__boton");
      b.type = "button";
      const numero = el("span", "viernes__numero", String(i + 1));
      numero.setAttribute("aria-hidden", "true");
      b.appendChild(numero);
      const texto = el("span", "viernes__texto");
      texto.appendChild(el("span", "viernes__nombre", enc.nombre));
      if (enc.tema) texto.appendChild(el("span", "viernes__tema", enc.tema));
      b.appendChild(texto);
      b.setAttribute("aria-label", enc.pie + ": ver foto");
      b.addEventListener("click", function () { abrirVisor(encuentros, i, b); });
      li.appendChild(b);
      lista.appendChild(li);
    });
    articulo.appendChild(lista);
  }

  /* --- Carrusel con todas las fotos --- */
  const fotos = fotosValidas(e.fotos);
  if (fotos.length) {
    articulo.appendChild(el("h4", "pasado__subtitulo", "Galería"));
    articulo.appendChild(crearCarrusel(fotos, e.titulo));
  }

  return articulo;
}

/** Lo llama main.js al cargar la página. */
function renderEventos() {
  const proximos = id("eventosProximos");
  const pasados = id("eventosPasados");
  const eventos = CONTENT.eventos || [];

  if (proximos) {
    const futuros = eventos.filter(function (e) { return !e.pasado; });
    if (futuros.length === 0) proximos.appendChild(el("li", "evento", "No hay eventos programados por ahora."));
    futuros.forEach(function (e) { proximos.appendChild(crearEventoProximo(e)); });
  }

  if (pasados) {
    eventos.filter(function (e) { return e.pasado; }).forEach(function (e) {
      pasados.appendChild(crearEventoPasado(e));
    });
  }

  /* Si se llegó desde la portada con un #enlace, bajar hasta ese evento:
     el navegador no lo hace solo porque la tarjeta se creó después de cargar. */
  if (location.hash) {
    const destino = document.getElementById(decodeURIComponent(location.hash.slice(1)));
    if (destino) destino.scrollIntoView();
  }
}
