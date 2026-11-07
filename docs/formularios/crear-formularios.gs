// Crea los 3 formularios del Grupo de Oración Divino Niño Jesús, cada uno con su hoja de respuestas.
// Los títulos y el color se ponen a mano en el editor (Personalizar tema); ver guia-formularios.md.

function crearFormularios() {
  var hechos = [crearDonacion(), crearContacto(), crearAyuda()];
  hechos.forEach(function (h) {
    Logger.log(h.nombre + '\nENLACE PARA LA WEB: ' + h.corto + '\nEditar: ' + h.editar + '\nRespuestas: ' + h.hoja + '\n');
  });
}

function nuevo(titulo, descripcion, gracias, nombreHoja) {
  var f = FormApp.create(titulo);
  f.setDescription(descripcion); // El nombre del grupo ya va en la imagen de cabecera.
  f.setConfirmationMessage(gracias);
  f.setCollectEmail(false);
  f.setLimitOneResponsePerUser(false);
  f.setAllowResponseEdits(false);
  f.setShowLinkToRespondAgain(false);
  var hoja = SpreadsheetApp.create(nombreHoja);
  f.setDestination(FormApp.DestinationType.SPREADSHEET, hoja.getId());
  return { f: f, hoja: hoja };
}

function celular(f, titulo, ayuda) {
  var v = FormApp.createTextValidation()
    .setHelpText('Escriba su celular: son 9 números y empieza con 9. Por ejemplo: 987654321')
    .requireTextMatchesPattern('^ *9[0-9]{2} ?[0-9]{3} ?[0-9]{3} *$')
    .build();
  f.addTextItem().setTitle(titulo).setHelpText(ayuda).setRequired(true).setValidation(v);
}

function permiso(f, texto) {
  var p = f.addCheckboxItem().setTitle('Sus datos').setRequired(true);
  p.setChoices([p.createChoice(texto)]);
}

function resultado(nombre, x) {
  return {
    nombre: nombre,
    corto: x.f.shortenFormUrl(x.f.getPublishedUrl()),
    editar: x.f.getEditUrl(),
    hoja: x.hoja.getUrl()
  };
}

function crearDonacion() {
  var x = nuevo(
    'Campaña de Navidad: quiero donar',
    'Gracias por querer compartir con las familias que más lo necesitan en esta Navidad.\n' +
    'Déjenos sus datos y lo que piensa traer. Nosotros le escribimos para ponernos de acuerdo en la entrega.\n\n' +
    'Solo recibimos víveres y ropa. NO recibimos dinero, y nadie del grupo le pedirá dinero.\n\n' +
    '"Dios ama al que da con alegría." (2 Corintios 9, 7)',
    'Muchas gracias. Pronto le escribiremos por WhatsApp para coordinar la entrega.\n' +
    'Que el Divino Niño Jesús bendiga su generosidad y a toda su familia.',
    'Respuestas: Campaña de Navidad'
  );
  var f = x.f;
  f.addTextItem().setTitle('Nombre y apellido').setRequired(true);
  celular(f, 'Número de celular o WhatsApp', 'A este número le escribiremos para coordinar.');
  var que = f.addCheckboxItem().setTitle('¿Qué va a traer?').setHelpText('Puede marcar más de una opción.').setRequired(true);
  que.setChoices(['Arroz', 'Aceite', 'Menestras', 'Ropa de abrigo'].map(function (c) { return que.createChoice(c); }));
  que.showOtherOption(true);
  f.addTextItem().setTitle('¿Cuánto, más o menos?').setHelpText('Por ejemplo: 5 kilos de arroz y 2 botellas de aceite.').setRequired(true);
  var cuando = f.addMultipleChoiceItem().setTitle('¿Cuándo lo podría traer?').setRequired(true);
  cuando.setChoices([
    cuando.createChoice('El viernes, después de la reunión en la capilla'),
    cuando.createChoice('Prefiero que me escriban para ponernos de acuerdo')
  ]);
  f.addParagraphTextItem().setTitle('¿Algo más que quiera contarnos?');
  permiso(f, 'Acepto que el grupo use mi nombre y mi celular solo para coordinar esta donación. No se los damos a nadie (Ley N.° 29733).');
  return resultado('1. DONACIÓN', x);
}

function crearContacto() {
  var x = nuevo(
    'Escríbanos',
    'Si tiene una pregunta, quiere unirse al grupo o desea que oremos por usted, escríbanos aquí.\n' +
    'Le responderemos lo antes posible.',
    'Recibimos su mensaje y le responderemos pronto.\nDios le bendiga.',
    'Respuestas: Escríbanos'
  );
  var f = x.f;
  f.addTextItem().setTitle('Su nombre').setRequired(true);
  f.addTextItem().setTitle('¿Cómo le respondemos?').setHelpText('Escriba su número de celular o su correo.').setRequired(true);
  var tema = f.addMultipleChoiceItem().setTitle('¿Sobre qué nos escribe?').setRequired(true);
  tema.setChoices([
    tema.createChoice('Tengo una pregunta'),
    tema.createChoice('Quiero unirme al grupo de oración'),
    tema.createChoice('Quiero pedir una oración'),
    tema.createChoice('Una sugerencia para la página web')
  ]);
  tema.showOtherOption(true);
  f.addParagraphTextItem().setTitle('Su mensaje').setRequired(true);
  permiso(f, 'Acepto que el grupo use estos datos solo para responderme. No se los damos a nadie (Ley N.° 29733).');
  return resultado('2. CONTACTO', x);
}

function crearAyuda() {
  var x = nuevo(
    'Pedir ayuda',
    'Si usted o una familia que conoce está pasando un momento difícil, cuéntenos.\n' +
    'No tiene que dar su nombre si no quiere.\n' +
    'Lo que escriba aquí solo lo lee la persona encargada de la ayuda social del grupo.',
    'Recibimos su pedido. Si nos dejó su número, le escribiremos pronto.\n' +
    'También puede acercarse un viernes a la Capilla Divino Niño: le recibiremos con mucho cariño.\n' +
    'Que Dios le acompañe.',
    'Respuestas: Pedir ayuda'
  );
  var f = x.f;
  var tipo = f.addCheckboxItem().setTitle('¿Qué ayuda necesita?').setHelpText('Puede marcar más de una opción.').setRequired(true);
  tipo.setChoices(['Alimentos', 'Ropa', 'Compañía y oración', 'Que nos orienten a dónde acudir'].map(function (c) { return tipo.createChoice(c); }));
  tipo.showOtherOption(true);
  f.addParagraphTextItem().setTitle('Cuéntenos un poco más').setHelpText('No hace falta poner nombres.').setRequired(true);
  permiso(f, 'Entiendo que esto solo lo lee la persona encargada de la ayuda social y que se usa solo para ayudarme (Ley N.° 29733).');
  var contactar = f.addMultipleChoiceItem().setTitle('¿Quiere que le llamemos o le escribamos?').setRequired(true);
  var datos = f.addPageBreakItem().setTitle('Sus datos').setHelpText('Solo los usaremos para comunicarnos con usted.');
  contactar.setChoices([
    contactar.createChoice('Sí, les dejo mi número', datos),
    contactar.createChoice('No, prefiero no dar mis datos', FormApp.PageNavigationType.SUBMIT)
  ]);
  f.addTextItem().setTitle('Su nombre').setHelpText('Puede dejarlo en blanco o poner solo su primer nombre.');
  celular(f, 'Número de celular o WhatsApp', 'A este número le llamaremos o escribiremos.');
  f.addTextItem().setTitle('¿A qué hora prefiere que le llamemos?').setHelpText('Por ejemplo: en las tardes, después de las 4.');
  return resultado('3. PEDIR AYUDA', x);
}
