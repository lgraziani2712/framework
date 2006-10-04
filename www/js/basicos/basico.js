var agt=navigator.userAgent.toLowerCase();
var ie= ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var ie7 = (ie && agt.indexOf("msie 7") != -1);
var ie6omenor = (ie && !ie7);
var ns6=document.getElementById && !document.all;
var ereg_numero = /^[1234567890,.-]*$/;
var ereg_nulo = /^\s*$/;
var pagina_cargada = false;


function isset(o) {
	return typeof o != 'undefined' && o !== null;	
}

//---STRING
//----------------------------------------------------------
function trim(s){
    return s.replace(/^\s*(\S*(\s+\S+)*)\s*$/,'$1');
}

/**
 * Retorna la primer ocurrencia de alguna de estas cadenas
 */
String.prototype.primer_ocurrencia = function (cadenas) {
	var primera = this.length;
	for (var i =0 ; i < cadenas.length; i++) {
		var ocurrencia = this.indexOf(cadenas[i]);
		if (ocurrencia != -1 && ocurrencia < primera) {
			primera = ocurrencia;
		}
	}
	return primera;
};

/**
 * Retorna la ultima ocurrencia de alguna de estas cadenas
 */
String.prototype.ultima_ocurrencia = function (cadenas) {
	var ultima = 0;
	for (var i =0 ; i < cadenas.length; i++) {
		ocurrencia = this.lastIndexOf(cadenas[i]);
		if (ocurrencia > ultima) {
			ultima = ocurrencia;
		}
	}
	return ultima;
};

/**
 * Retorna el quote necesario para usar en una exp. regular
 */
String.prototype.quote_exp_reg = function () {
	return this.reemplazar('$', '\\$');
};

/**
 * Reemplaza todas las ocurrencias de un string en otro
 */
String.prototype.reemplazar = function (buscado, nuevo) {
	var srchNdx = 0; 
	var newStr = ""; 
	while (this.indexOf(buscado,srchNdx) != -1) {
	  newStr += this.substring(srchNdx,this.indexOf(buscado,srchNdx));
	  newStr += nuevo;
	  srchNdx = (this.indexOf(buscado,srchNdx) + buscado.length);
	}
	newStr += this.substring(srchNdx,this.length);
	return newStr;
};

/**
 * Intercambia las ocurrencias de un caracter con las de otro
 */
String.prototype.intercambiar_caracteres = function(c1, c2) {
	var car_template = '_^_';
	var v1 = this.reemplazar(c2, car_template);
	var v2 = v1.reemplazar(c1, c2);
	var v3 = v2.reemplazar(car_template, c1);
	return v3;
};

String.prototype.trim = function() {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/,'$1');
};

String.prototype.pad = function(l, s, t){
    return s || (s = " "), (l -= this.length) > 0 ? (s = new Array(Math.ceil(l / s.length)
        + 1).join(s)).substr(0, t = !t ? l : t == 1 ? 0 : Math.ceil(l / 2))
        + this + s.substr(0, l - t) : this;
};

//---ARRAY
//----------------------------------------------------------

/**
 * Busca si un valor pertenece al arreglo
 */
function in_array (elemento, arreglo) {
	for (var i=0 ; i < arreglo.length; i++) {
		if (arreglo[i] == elemento) {
			return true;
		}
	}
	return false;
}

/**
 * Retorna el valor maximo que existe en un arreglo
 */
function array_maximo(arreglo) {
	var maximo = 0;
	for (var i=0 ; i < arreglo.length; i++) {
		if (arreglo[i] > maximo) {
			maximo = arreglo[i];
		}
	}
	return maximo;
}

//---Eventos
//--------------------------------------------

/**
 * Agrega dinamicamente un evento a un elemento html sin pisar los eventos anteriores
 * @param {element} Elemento base
 * @param {string} _e Nombre del evento (ej. onclick)
 * @param {string} _c Cuerpo del callback a ejecutar
 * @param {boolean} _b Agregar al fondo (por defecto true)
 */
function addEvent(o, _e, c, _b){
	if (!o) {return;}		
	var e = _e.toLowerCase();
	var b = (typeof _b == "boolean") ? _b : true;

	// strip out the body of the functions	
	if (typeof o[e] == 'function') {
		var x = o[e].toString();
		x = x.substring(x.indexOf("{")+1, x.lastIndexOf("}"));		
	} else {
		x = (o[e]) ? o[e] : '';
	}
	if (typeof c == 'function') {
		c = c.toString();
		c = c.substring(c.indexOf("{")+1, c.lastIndexOf("}"));		
	}

	x = ((b) ? (x + ';' + c) : (c + ';' +  x)) + "\n";
	o[e] = (!!window.Event) ? new Function("event", x) : new Function(x);
	return o[e];
}

//---- DOM
/**
 * Muestra u oculta un element HTML
 */
function toggle_nodo(o) {
	o.style.display = (o.style.display == 'none') ? '' : 'none';
}

function getElementPosition(offsetTrail) {
    var offsetLeft = 0;
    var offsetTop = 0;
    while (offsetTrail) {
        offsetLeft += offsetTrail.offsetLeft;
        offsetTop += offsetTrail.offsetTop;
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && 
        typeof document.body.leftMargin != "undefined") {
        offsetLeft += document.body.leftMargin;
        offsetTop += document.body.topMargin;
    }
    return {left:offsetLeft, top:offsetTop};
}

/**
 * Recorre el arbol DOM en busqueda de un input HTML que acepte focus
 */
function firstFocus()
{
	for (var i=0; i< document.forms.length; i++) {
		var formulario = document.forms[i];
		for (var j=0;j<formulario.length;j++) {
			var elemento = formulario.elements[j];
			var display = elemento.style.display;
			if ((elemento.type=="text" || elemento.type=="textarea") && (!elemento.disabled)  && ( display != 'none') && ( display != 'hidden') ) {
				var error =false;
				try {
				   elemento.focus();
				} catch(e) {
					error = true;
				}
			   if (!error) {return;}
			}
		}
	}
}

function include_source(file) {
    var html_doc = document.getElementsByTagName('head').item(0);
    var js = document.createElement('script');
    js.setAttribute('language', 'javascript');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', file);
    html_doc.appendChild(js);
}

//********************  POPUPS  ************************

/**
*	@deprecated Usar abrir_popup
*/
function solicitar_item_popup( url, tx, ty, scroll, resizable, extra ){
	var opciones = {'width': tx, 'scrollbars' : scroll, 'height': ty, 'resizable': resizable};
	abrir_popup('general', url, opciones, extra);
}

var ventana_hija = {};
function abrir_popup(id, url, opciones, extra, dep) {
	vars = '';
	if (typeof opciones != 'undefined') {
		for (var o in opciones) {
			vars += o + '=' + opciones[o] + ',';
		}
	}
	if (typeof dep == 'undefined') {dep = true;}
	if (dep) {
		vars += 'dependent=1';
	}
	if (typeof extra != 'undefined') {
		vars += extra;
	}
	var no_esta_definida  = !ventana_hija[id] || ventana_hija[id].closed || !ventana_hija[id].focus;
	if (no_esta_definida) {
		// No fue definida, esta cerrada o no puede tener foco
		ventana_hija[id] = window.open( url , id, vars);
		ventana_hija[id].focus();
	} else {
		// Ya fue definida, no esta cerrada  y puede tener foco
		ventana_hija[id].focus();		
		ventana_hija[id].location.href = url;
		ventana_hija[id].opener = window;
	}
	return false;	
}


/**
 * @class Clase estatica que permite recolectar el tiempo de ejecuci�n entre distintos sucesos
 * @constructor
 */
var cronometro;
cronometro = new function() {
	this.res = [];
}
	cronometro.limpiar = function() {
		this.res = [];
	};
	cronometro.marcar = function(descripcion) {
		cronometro.res.push([new Date(), descripcion]);
	};
	cronometro.resultados = function() {
		var html = '';
		for (var i=0 ; i < cronometro.res.length; i++) {
			if (i > 0) {
				var ms = cronometro.res[i][0] - cronometro.res[i-1][0];
				html += '[' + cronometro.res[i-1][1] + ' - ' + cronometro.res[i][1] + '] = ' + ms + 'ms.<br>';
			}
		}
		return html;
	}

function cambiar_colapsado(boton, cuerpo) {
	if (cuerpo.style.display == 'none') {
		descolapsar(boton, cuerpo);
	} else {
		colapsar(boton, cuerpo);
	}
};

function colapsar (boton, cuerpo) {
	if (boton) {
		boton.src = toba.imagen('maximizar');
	}
	cuerpo.style.display='none';
}

function descolapsar (boton, cuerpo) {
	if (boton) {
		boton.src = toba.imagen('minimizar');
	}
	cuerpo.style.display='block';	
}


//----Reflexion
//--------------------------------------------

function getObjectClass(obj)
{
    if (obj && obj.constructor && obj.constructor.toString) {
        var arr = obj.constructor.toString().match(/function\s*(\w+)/);
        return arr && arr.length == 2 ? arr[1] : undefined;
    } else {
        return undefined;
    }
}

function existe_funcion(obj, f) {
	for (funcion in obj) {
		if (funcion == f && typeof(obj[funcion])=="function") {
			return true;
		}
	}		
	return false;
}


function getElementsByClass(searchClass,node,tag) {
	var classElements = [];
	if ( typeof node == 'undefined' ) {node = document;}
	if ( typeof tag == 'undefined') {tag = '*';}
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp('(^|\\s)'+searchClass+'(\\s|$)');
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

function $() {
	var elements = [];
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string') {
			element = document.getElementById(element);
		}
		if (arguments.length == 1) {
			return element;
		}
		elements.push(element);
	}
	return elements;
}
//----Varios
//--------------------------------------------

function actualizar_logger(cant) {
	var vinc = document.getElementById('vinculo_logger');
	if (vinc) {
		vinc.innerHTML += '[' + cant + ']';
	}
}

function var_dump(variable, ret) {
	DumperIndentText = "&nbsp";
	DumperNewline = "<br>";
	DumperSepPre = "<a href='javascript: ;' onclick=\"o = this.nextSibling; o.style.display = (o.style.display == 'none') ? '' : 'none';\"> m�s </a><span style='display: none'>";
	DumperSepPos = "</span>";		
	DumperMaxDepth = 8;
	if (ret) {
		return Dumper(variable);
	} else {
		DumperPopup(variable);
	}
}

function salir(){
	if(confirm('Desea terminar la sesi�n?')) {
		var vinculo = toba_prefijo_vinculo + '&fs=1';
		if (top) {
			top.location.href= vinculo;
		} else {
			location.href = vinculo; 
		}
	}
}

//Se agrega una forma de distinguir si esta cargada la pagina y se lanza el firstFocus
addEvent(window, "onload", "pagina_cargada=true;firstFocus();");


