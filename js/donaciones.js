/* =============================================================================
   donaciones.js — Avance de la campaña de donaciones
   -----------------------------------------------------------------------------
   De dónde salen los números:
     1. Si en content.js hay un "csvAvance" (el CSV publicado desde Google
        Sheets), se leen de ahí: así los coordinadores actualizan la hoja y la
        web cambia sola, sin tocar código.
     2. Si no hay CSV, o falla, se usan los números de "articulos" en content.js.

   La página pinta primero los números de content.js y, si el CSV responde, los
   reemplaza. Así nunca se queda en blanco esperando a Google.

   La hoja debe tener una pestaña con estas columnas (en la primera fila):
       item | meta | unidad | actual
   Paso a paso en docs/guia-donaciones-sheets.md.

   Aclaración importante: esto es informativo. La web no cobra ni recibe dinero.
   ============================================================================= */

const LIMITE_FILAS_CSV = 30;
const LIMITE_BYTES_CSV = 100000;

/**
 * Calcula el porcentaje de avance, protegido contra datos mal escritos:
 * si la meta es 0, negativa o no es un número, devuelve 0 en vez de romperse.
 */
function calcularPorcentaje(actual, meta) {
  const a = Number(actual);
  const m = Number(meta);
  if (!isFinite(a) || !isFinite(m) || m <= 0) return 0;
  return Math.max(0, Math.min(100, Math.round((a / m) * 100)));
}

function pintarCampanas(lista, articulos) {
  lista.textContent = "";

  if (articulos.length === 0) {
    lista.appendChild(el("li", "campana", "No hay campañas abiertas en este momento."));
    return;
  }

  articulos.forEach(function (campana) {
    const porcentaje = calcularPorcentaje(campana.actual, campana.meta);
    const completa = porcentaje >= 100;
    const unidad = campana.unidad || "";

    const li = el("li", completa ? "campana campana--completa" : "campana");

    const cabecera = el("div", "campana__cabecera");
    const titulo = el("h3", "campana__item", campana.item);
    marcarSiEsPlaceholder(titulo, campana);
    cabecera.appendChild(titulo);
    cabecera.appendChild(el("span", "campana__cifra", campana.actual + " / " + campana.meta + " " + unidad));
    li.appendChild(cabecera);

    /* role="progressbar" para que los lectores de pantalla anuncien el avance. */
    const barra = el("div", "campana__barra");
    barra.setAttribute("role", "progressbar");
    barra.setAttribute("aria-valuenow", String(porcentaje));
    barra.setAttribute("aria-valuemin", "0");
    barra.setAttribute("aria-valuemax", "100");
    barra.setAttribute("aria-label",
      "Avance de " + (campana.item || "la donación") + ": " + campana.actual + " de " +
      campana.meta + " " + unidad + ", " + porcentaje + " por ciento");
    const relleno = el("div", "campana__relleno");
    barra.appendChild(relleno);
    li.appendChild(barra);

    const pie = el("div", "campana__pie");
    pie.appendChild(el("span", null, porcentaje + "% completado"));
    const falta = Math.max(0, Number(campana.meta) - Number(campana.actual));
    if (!completa && isFinite(falta)) pie.appendChild(el("span", null, "Faltan " + falta + " " + unidad));
    li.appendChild(pie);

    // Texto, no solo color: un daltónico no distingue el verde del rojo.
    if (completa) li.appendChild(el("span", "campana__completa", "Meta cumplida, ¡gracias!"));
    if (campana.nota) li.appendChild(el("p", "campana__nota", campana.nota));

    lista.appendChild(li);

    /* El ancho se asigna de inmediato, no dentro de requestAnimationFrame:
       en una pestaña de segundo plano el navegador no lo ejecuta y las barras
       se quedarían en cero. La animación la da la transición del CSS. */
    relleno.style.width = porcentaje + "%";
  });
}

/* --- Lectura del CSV de Google Sheets --------------------------------------- */

/** Solo se aceptan CSV publicados desde Google Sheets. */
function urlCsvValida(url) {
  const segura = urlSegura(url);
  if (!segura) return "";
  try {
    const u = new URL(segura);
    if (u.protocol === "https:" && u.hostname === "docs.google.com" && u.pathname.indexOf("/spreadsheets/") === 0) {
      return u.href;
    }
  } catch (e) { /* dirección mal escrita */ }
  console.warn("csvAvance debe ser un enlace de Google Sheets publicado como CSV.");
  return "";
}

/** Lector de CSV que respeta comillas ("Arroz, 5 kg") y saltos de línea \r\n. */
function leerCsv(texto) {
  const filas = [];
  let fila = [], campo = "", entreComillas = false;

  for (let i = 0; i < texto.length; i++) {
    const c = texto[i];
    if (entreComillas) {
      if (c === '"' && texto[i + 1] === '"') { campo += '"'; i++; }
      else if (c === '"') entreComillas = false;
      else campo += c;
    } else if (c === '"') entreComillas = true;
    else if (c === ",") { fila.push(campo); campo = ""; }
    else if (c === "\n" || c === "\r") {
      if (c === "\r" && texto[i + 1] === "\n") i++;
      fila.push(campo); filas.push(fila); fila = []; campo = "";
    } else campo += c;
  }
  if (campo !== "" || fila.length) { fila.push(campo); filas.push(fila); }
  return filas;
}

/** "Meta", "META ", "Méta" → "meta" */
function normalizarCabecera(texto) {
  return String(texto || "").toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "").trim();
}

/** Acepta "50", "12.5" o "12,5" (la hoja puede estar en formato peruano). */
function leerNumero(valor) {
  const limpio = String(valor || "").replace(/\s/g, "");
  if (limpio === "") return 0;
  if (!/^\d+([.,]\d+)?$/.test(limpio)) return NaN;
  return parseFloat(limpio.replace(",", "."));
}

function filasACampanas(filas) {
  if (filas.length < 2) return [];

  const cabecera = filas[0].map(normalizarCabecera);
  const col = {
    item: cabecera.indexOf("item"),
    meta: cabecera.indexOf("meta"),
    actual: cabecera.indexOf("actual"),
    unidad: cabecera.indexOf("unidad")
  };
  if (col.item < 0 || col.meta < 0 || col.actual < 0) {
    console.warn("El CSV de donaciones necesita las columnas item, meta y actual.");
    return [];
  }

  const campanas = [];
  filas.slice(1, LIMITE_FILAS_CSV + 1).forEach(function (f) {
    const item = String(f[col.item] || "").trim().slice(0, 60);
    const meta = leerNumero(f[col.meta]);
    const actual = leerNumero(f[col.actual]);
    if (!item || !isFinite(meta) || !isFinite(actual) || meta <= 0) return;
    campanas.push({
      item: item,
      meta: meta,
      actual: actual,
      unidad: col.unidad >= 0 ? String(f[col.unidad] || "").trim().slice(0, 20) : ""
    });
  });
  return campanas;
}

function cargarCsv(url) {
  return fetch(url, { cache: "no-cache", credentials: "omit", redirect: "follow" })
    .then(function (respuesta) {
      if (!respuesta.ok) throw new Error("La hoja respondió " + respuesta.status);
      return respuesta.text();
    })
    .then(function (texto) {
      if (texto.length > LIMITE_BYTES_CSV) throw new Error("El CSV es demasiado grande");
      return filasACampanas(leerCsv(texto));
    });
}

/* --- Arranque (lo llama main.js) --------------------------------------------- */
function renderCampanas() {
  const lista = id("campanas");
  if (!lista) return;

  const d = CONTENT.donaciones || {};

  const titulo = id("campanaTitulo");
  if (titulo) titulo.textContent = d.titulo || "";

  const fecha = id("campanaFecha");
  if (fecha) fecha.textContent = d.fechaLimite || "";

  const destino = id("campanaDestino");
  if (destino) destino.textContent = d.destino || "";

  const punto = id("puntoEntrega");
  if (punto) {
    punto.textContent = d.puntoEntrega || "";
    marcarSiEsPlaceholder(punto, d);
  }

  const noRecibimos = id("noRecibimos");
  if (noRecibimos) noRecibimos.textContent = d.noRecibimos || "";

  pintarCampanas(lista, d.articulos || []);

  const url = urlCsvValida(d.csvAvance);
  if (!url) return;

  const fuente = id("campanaFuente");
  cargarCsv(url)
    .then(function (campanas) {
      if (campanas.length === 0) return;
      pintarCampanas(lista, campanas);
      if (fuente) fuente.textContent = "Avance actualizado desde la hoja del grupo.";
    })
    .catch(function (error) {
      // Si Google no responde, se quedan los números de content.js.
      console.warn("No se pudo leer el avance de donaciones:", error.message);
    });
}
