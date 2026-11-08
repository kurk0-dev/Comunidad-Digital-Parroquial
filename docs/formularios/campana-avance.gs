// Campaña de Navidad: lista de donaciones, columna "¿Llegó?" y avance para la web.
// Se ejecutó una vez desde Apps Script (cuenta diocesisdechosica.zona3@gmail.com),
// en el mismo proyecto que crear-formularios.gs.
//
// Qué hace armarAvance():
//   1. Pone en el formulario de donación la lista de cosas que se reciben.
//   2. En la hoja de respuestas agrega la columna "¿Llegó?" con casillas (sin valor,
//      para que Forms siga escribiendo cada respuesta en la fila siguiente).
//   3. Crea la pestaña "Avance" (privada) que cuenta, por producto, las donaciones
//      con la casilla marcada.
//   4. Crea una hoja APARTE, pública, que copia solo esa tabla con IMPORTRANGE.
//      Esa es la que se publica como CSV: la hoja de respuestas (nombres y
//      teléfonos) nunca se publica, ni siquiera una pestaña.

var IDS = ['10TIQ4HZs80oyacAgaHpoq2-7I5umzvWoyEBA4WuW-ZQ', '1_fD3SxKITFO0IOOqgQpp5MI1edpykLKa9suioJ161k4', '157A0iyxeaIpPtY6wJqSjnZMdtoDa5UNhYrHe3AlvBe4'];

var PRODUCTOS = [
  ['Arroz', 40], ['Azúcar', 30], ['Aceite', 30], ['Menestras', 30], ['Fideos', 30],
  ['Leche', 30], ['Conservas', 30], ['Panetón', 40], ['Chocolate para taza', 30],
  ['Juguetes nuevos', 40], ['Ropa de abrigo', 30], ['Útiles de aseo', 30]
];

function armarAvance() {
  var form = FormApp.openById(IDS[0]);
  form.setDescription(
    'Gracias por querer compartir con las familias que más lo necesitan en esta Navidad.\n' +
    'Déjenos sus datos y lo que piensa traer. Nosotros le escribimos para ponernos de acuerdo en la entrega.\n\n' +
    'Recibimos víveres, panetones, juguetes nuevos, ropa y útiles de aseo. NO recibimos dinero, y nadie del grupo le pedirá dinero.\n\n' +
    '"Dios ama al que da con alegría." (2 Corintios 9, 7)'
  );
  var que = form.getItems(FormApp.ItemType.CHECKBOX).filter(function (it) { return it.getTitle() === '¿Qué va a traer?'; })[0].asCheckboxItem();
  que.setHelpText('Puede marcar varias. Los juguetes, por favor nuevos y sin envolver, para poder revisarlos.');
  que.setChoices(PRODUCTOS.map(function (p) { return que.createChoice(p[0]); }));
  que.showOtherOption(true);

  var libro = SpreadsheetApp.openById(form.getDestinationId());
  var resp = libro.getSheets().filter(function (h) { return h.getFormUrl(); })[0];
  var cab = resp.getRange(1, 1, 1, resp.getLastColumn()).getValues()[0];
  var colQue = cab.indexOf('¿Qué va a traer?') + 1;
  var colLlego = cab.indexOf('¿Llegó?') + 1;
  if (!colLlego) {
    colLlego = resp.getLastColumn() + 1;
    resp.getRange(1, colLlego).setValue('¿Llegó?').setFontWeight('bold').setBackground('#FDF3D2');
    resp.getRange(1, colLlego).setNote('Marque la casilla cuando la donación llegue a la capilla. Solo lo marcado cuenta en la barra de la web.');
  }
  resp.getRange(2, colLlego, 1999, 1).setDataValidation(SpreadsheetApp.newDataValidation().requireCheckbox().build());
  resp.setFrozenRows(1);

  var nombre = "'" + resp.getName() + "'!";
  var rQue = nombre + resp.getRange(2, colQue, 1999, 1).getA1Notation();
  var rLlego = nombre + resp.getRange(2, colLlego, 1999, 1).getA1Notation();
  var avance = libro.getSheetByName('Avance') || libro.insertSheet('Avance');
  avance.clear();
  var filas = [['item', 'meta', 'unidad', 'actual']];
  PRODUCTOS.forEach(function (p, i) {
    var f = i + 2;
    filas.push([p[0], p[1], 'donaciones', '=COUNTIFS(' + rQue + ',"*"&A' + f + '&"*",' + rLlego + ',TRUE)']);
  });
  avance.getRange(1, 1, filas.length, 4).setValues(filas);
  avance.getRange(1, 1, 1, 4).setFontWeight('bold').setBackground('#FDF3D2');
  avance.setFrozenRows(1);
  avance.getRange(filas.length + 2, 1).setValue('Las metas (columna B) son de ejemplo: cámbielas cuando pastoral las confirme. La columna "actual" se llena sola.');

  var publico = SpreadsheetApp.create('Avance Campaña de Navidad (público, sin datos personales)');
  var hoja = publico.getSheets()[0].setName('Avance');
  hoja.getRange('A1').setFormula('=IMPORTRANGE("' + libro.getUrl() + '","Avance!A1:D' + filas.length + '")');
  Logger.log('Respuestas: ' + libro.getUrl());
  Logger.log('PUBLICO: ' + publico.getUrl());
}

function revisarAvance() {
  var libro = SpreadsheetApp.openById(FormApp.openById(IDS[0]).getDestinationId());
  var resp = libro.getSheets().filter(function (h) { return h.getFormUrl(); })[0];
  var cab = resp.getRange(1, 1, 1, resp.getLastColumn()).getValues()[0];
  var col = cab.indexOf('¿Llegó?') + 1;
  var val = col ? resp.getRange(2, col).getDataValidation() : null;
  Logger.log('Columnas: ' + cab.join(' | '));
  Logger.log('Casilla en Llegó: ' + (val ? val.getCriteriaType() : 'NO'));
  var av = libro.getSheetByName('Avance');
  Logger.log('Avance: ' + JSON.stringify(av.getRange(1, 1, 4, 4).getValues()));
  Logger.log('Fórmula D2: ' + av.getRange('D2').getFormula());
}
