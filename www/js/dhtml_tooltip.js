var dhtml_tooltip = {
 	//---Public
	vertical_offset : '0px',
	horizontal_offset : '-3px',
	delay_disappear : 250,
	delay_appear : 1000,

	show : function(content, obj, e) {
		if (window.event) { 
			event.cancelBubble=true
		} else if (e.stopPropagation) {
			e.stopPropagation()
		}
		this._do_hide();
		var tooltip = document.getElementById("dhtml_tooltip_div")
		tooltip.innerHTML = content
		if (e.type == "click" && tooltip.style.visibility == 'hidden' || e.type == "mouseover") {
			this._show_handler = setTimeout("dhtml_tooltip._do_show()", this.delay_appear)
		} else if (e.type=="click") {
			tooltip.style.visibility = 'hidden'
		}
		tooltip.x = this._get_pos_offset(obj, true)
		tooltip.y = this._get_pos_offset(obj, false)
		tooltip.style.left = tooltip.x - this._clear_browser_edge(obj, true) + "px"
		tooltip.style.top = tooltip.y - this._clear_browser_edge(obj, false) + obj.offsetHeight + "px"
		return true;
	},

	hide : function() {
		this._must_hide = true;
 		this._hide_handler = setTimeout("dhtml_tooltip._do_hide()", this.delay_disappear)
 		return true;
	},
	
	//---Private
	_show_handler : null,
	_hide_handler : null,
	_must_hide : true,

	_clear_tip : function() {
		if (typeof this._hide_handler != "undefined" || this._hide_handler != null) {
			clearTimeout(this._hide_handler)
			delete(this._hide_handler);
		}
		if (typeof this._show_handler != "undefined" || this._show_handler != null) {
			clearTimeout(this._show_handler)
			delete(this._show_handler);
		}
	},
	
	_get_pos_offset : function(what, is_left) {
		var total_offset = (is_left) ? what.offsetLeft : what.offsetTop;
		var parentEl = what.offsetParent;
		while (parentEl != null) {
			total_offset = (is_left) ? total_offset + parentEl.offsetLeft : total_offset + parentEl.offsetTop;
			parentEl = parentEl.offsetParent;
		}
		return total_offset;
	},
	
	_clear_browser_edge : function(obj, is_horizontal) {
		var tooltip = document.getElementById("dhtml_tooltip_div")		
		var edge_offset = (is_horizontal) ? parseInt(this.horizontal_offset)*-1 : parseInt(this.vertical_offset)*-1
		var is_ie = document.all && !window.opera;
		if (is_ie) {
			var ie_body = this._ie_body();
		}
		if (is_horizontal) {
			var window_edge = is_ie ? ie_body.scrollLeft + ie_body.clientWidth-15 : window.pageXOffset+window.innerWidth-15
			var content_measure = tooltip.offsetWidth
			if (window_edge - tooltip.x < content_measure) {
				edge_offset= content_measure - obj.offsetWidth
			}
		} else {
			var window_edge = is_ie ? ie_body.scrollTop + ie_body.clientHeight-15 : window.pageYOffset+window.innerHeight-18
			var content_measure= tooltip.offsetHeight
			if (window_edge - tooltip.y < content_measure) {
				edge_offset = content_measure + obj.offsetHeight
			}
		}
		return edge_offset
	},
	
 	_ie_body : function() {
 		return (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body
	 },
	 
 	_do_show : function() {
	 	document.getElementById("dhtml_tooltip_div").style.visibility="visible"
 	},
 	
 	_do_hide : function() {
 		if (this._must_hide) {
			document.getElementById("dhtml_tooltip_div").style.visibility="hidden"
 		}
		this._clear_tip();
 	},
 	
 	_continue : function() {
 		this._must_hide = false;
 	},
 	
 	_stop : function() {
 		this._must_hide = true;
 		this.hide();
 	}
}

var html = '<div id="dhtml_tooltip_div" onmouseover="dhtml_tooltip._continue()" onmouseout="dhtml_tooltip._stop()"></div>'
if (typeof pagina_cargada != 'undefined' && pagina_cargada) {
	document.body.innerHTML += html;	
} else {
	document.write(html) 
}
if (typeof toba != 'undefined') 
	toba.confirmar_inclusion('dhtml_tooltip');