// Campaña de Navidad por CANTIDADES (reemplaza a la lista de casillas de campana-avance.gs).
// Se ejecuta una vez desde Apps Script con la cuenta diocesisdechosica.zona3@gmail.com.
// Se puede volver a ejecutar: borra y rehace las preguntas de productos y la pestaña Avance.
//
// Qué hace campanaPorCantidades():
//   1. En el formulario de donación cambia "¿Qué va a traer?" (casillas) y "¿En qué
//      cantidades?" por una pregunta de número por producto: "Arroz (kilos)",
//      "Juguetes nuevos (juguetes)"… Solo acepta números enteros; en blanco = no lo trae.
//      Google Forms no puede mostrar una pregunta solo si se marca una casilla, por eso
//      van todas a la vista y se deja en blanco lo que no se trae.
//   2. La pestaña "Avance" suma, por producto, las cantidades de las filas con
//      "¿Llegó?" marcado. Busca las columnas por su título (MATCH) y usa columnas
//      completas: ya no se descuadra cuando Forms inserta filas o columnas
//      (ese era el error: la fórmula había pasado a empezar en la fila 3).
//   3. La hoja pública (solo la tabla, sin datos personales) copia Avance con IMPORTRANGE.

var FORM_ID = '10TIQ4HZs80oyacAgaHpoq2-7I5umzvWoyEBA4WuW-ZQ';
var PUBLICO_ID = '1Etjbyv-KlXfLrg4m_fEYmxH6yMNUCX9VzA9mOeKn0vU';

// [nombre en la web, meta (de ejemplo), unidad]
var PRODUCTOS = [
  ['Arroz', 100, 'kilos'], ['Azúcar', 50, 'kilos'], ['Aceite', 50, 'litros'],
  ['Menestras', 50, 'kilos'], ['Fideos', 50, 'kilos'], ['Leche', 60, 'tarros'],
  ['Conservas', 60, 'latas'], ['Panetón', 40, 'unidades'], ['Chocolate para taza', 40, 'tabletas'],
  ['Juguetes nuevos', 40, 'juguetes'], ['Ropa de abrigo', 40, 'prendas'], ['Útiles de aseo', 40, 'unidades']
];
var TITULO_SECCION = '¿Qué va a traer y cuánto?';
var TITULO_OTRO = '¿Trae otra cosa?';

function campanaPorCantidades() {
  var form = FormApp.openById(FORM_ID);
  var titulos = PRODUCTOS.map(pregunta);
  var quitar = ['¿Qué va a traer?', '¿En qué cantidades?', TITULO_SECCION, TITULO_OTRO].concat(titulos);

  // 1. Formulario
  form.getItems().forEach(function (it) {
    if (quitar.indexOf(it.getTitle()) >= 0) form.deleteItem(it);
  });
  // Las preguntas de productos van justo después del celular.
  var pos = form.getItems().filter(function (it) { return /celular/i.test(it.getTitle()); })[0].getIndex() + 1;
  var seccion = form.addSectionHeaderItem().setTitle(TITULO_SECCION).setHelpText(
    'Escriba solo el número en lo que va a traer. Deje en blanco lo que no va a traer.\n' +
    'Ejemplo: si trae 5 kilos de arroz, escriba 5 en "Arroz (kilos)".\n' +
    'Los juguetes, por favor nuevos y sin envolver, para poder revisarlos.'
  );
  form.moveItem(seccion.getIndex(), pos);
  var validacion = FormApp.createTextValidation()
    .setHelpText('Escriba solo el número, sin letras. Por ejemplo: 5')
    .requireWholeNumber()
    .build();
  PRODUCTOS.forEach(function (p, i) {
    var item = form.addTextItem().setTitle(pregunta(p)).setRequired(false).setValidation(validacion);
    form.moveItem(item.getIndex(), pos + 1 + i);
  });
  var otro = form.addTextItem().setTitle(TITULO_OTRO).setRequired(false)
    .setHelpText('Escriba qué y cuánto. Por ejemplo: 3 paquetes de galletas.');
  form.moveItem(otro.getIndex(), pos + 1 + PRODUCTOS.length);

  // 2. Avance (en la hoja de respuestas, privada)
  var libro = SpreadsheetApp.openById(form.getDestinationId());
  var resp = libro.getSheets().filter(function (h) { return h.getFormUrl(); })[0];
  if (resp.getMaxColumns() < 78) resp.insertColumnsAfter(resp.getMaxColumns(), 78 - resp.getMaxColumns());
  var h = "'" + resp.getName() + "'!";
  var datos = h + '$A:$BZ';
  var cabecera = h + '$1:$1';
  var llego = 'INDEX(' + datos + ',0,MATCH("¿Llegó?",' + cabecera + ',0))';
  var avance = libro.getSheetByName('Avance') || libro.insertSheet('Avance');
  avance.clear();
  var filas = [['item', 'meta', 'unidad', 'actual', 'pregunta del formulario']];
  PRODUCTOS.forEach(function (p, i) {
    var f = i + 2;
    filas.push([p[0], p[1], p[2],
      '=IFERROR(SUMIFS(INDEX(' + datos + ',0,MATCH($E' + f + ',' + cabecera + ',0)),' + llego + ',TRUE),0)',
      pregunta(p)]);
  });
  avance.getRange(1, 1, filas.length, 5).setValues(filas);
  avance.getRange(1, 1, 1, 5).setFontWeight('bold').setBackground('#FDF3D2');
  avance.setFrozenRows(1);
  avance.getRange(filas.length + 2, 1).setValue(
    'Las metas (columna B) son de ejemplo: cámbielas cuando pastoral las confirme. ' +
    'La columna "actual" se llena sola con lo que tiene marcado "¿Llegó?". ' +
    'Si cambia un producto, cambie también su pregunta en el formulario (columna E).');

  // 3. Hoja pública: solo item, meta, unidad, actual
  var publico = SpreadsheetApp.openById(PUBLICO_ID).getSheets()[0];
  publico.clear();
  publico.getRange('A1').setFormula('=IMPORTRANGE("' + libro.getUrl() + '","Avance!A1:D' + filas.length + '")');

  SpreadsheetApp.flush();
  revisar();
}

function pregunta(p) {
  return p[0] + ' (' + p[2] + ')';
}

function revisar() {
  var form = FormApp.openById(FORM_ID);
  form.getItems().forEach(function (it, i) { Logger.log('PREGUNTA ' + i + ' [' + it.getType() + '] ' + it.getTitle()); });
  var libro = SpreadsheetApp.openById(form.getDestinationId());
  var resp = libro.getSheets().filter(function (h) { return h.getFormUrl(); })[0];
  Logger.log('CABECERA: ' + resp.getRange(1, 1, 1, resp.getLastColumn()).getValues()[0].join(' | '));
  Logger.log('AVANCE: ' + JSON.stringify(libro.getSheetByName('Avance').getRange(1, 1, PRODUCTOS.length + 1, 4).getDisplayValues()));
  Logger.log('PUBLICA: ' + JSON.stringify(SpreadsheetApp.openById(PUBLICO_ID).getSheets()[0].getRange(1, 1, 4, 4).getDisplayValues()));
}
