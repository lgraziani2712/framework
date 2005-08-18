<?php
require_once("objeto_ei.php");

class objeto_ei_arbol extends objeto_ei
{
	protected $nodo_inicial;
	protected $item_propiedades = array();
	protected $mostrar_raiz = true;
	protected $nivel_apertura = 1;
	protected $puede_sacar_foto = true;
	protected $foto_seleccionada = array();
	protected $frame_destino = null;

    function __construct($id)
    {
        parent::__construct($id);
		$this->submit = "ei_arbol" . $this->id[1];
		$this->objeto_js = "objeto_ei_arbol_{$id[1]}";
	}
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria["eventos"][$id] = true;
			}
		}	
		parent::destruir();
	}

	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
	
	function set_item_propiedades($id_item)
	{
		$this->item_propiedades = $id_item;
	}
	
	function set_foto($datos_foto)	//$datos_foto = array('id_nodo' => boolean, ...)
	{
		$this->foto_seleccionada = $datos_foto;
	}
	
	function set_nivel_apertura($nivel)
	{
		$this->nivel_apertura = $nivel;
	}
	
	function set_puede_sacar_foto($puede)
	{
		$this->puede_sacar_foto = $puede;
	}
	
	function set_mostrar_raiz($mostrar)
	{
		$this->mostrar_raiz = $mostrar;
	}
	
	function set_frame_destino($frame)
	{
		$this->frame_destino = $frame;
	}
	
    function cargar_datos($nodo=null, $memorizar=true)
    {
		$this->nodo_inicial = $nodo;
	}
	
	function get_lista_eventos()
	{
		$eventos = array();
		$eventos += eventos::ver_propiedades();
		if ($this->puede_sacar_foto) {
			$eventos += eventos::evento_estandar('sacar_foto', '', true);
		}
		return $eventos;
	}
	
	function disparar_eventos()
	{
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				//Se selecciono algo??
				$parametros = null;
				if ($evento == 'ver_propiedades' && isset($_POST[$this->submit."__seleccion"])) {
					$this->reportar_evento( $evento, $_POST[$this->submit."__seleccion"] );
				}
				if ($evento=='sacar_foto'
								&& isset($_POST[$this->submit."__foto_nombre"]) 
								&& isset($_POST[$this->submit."__foto_datos"])) {
					$nombre = $_POST[$this->submit."__foto_nombre"];
					$datos = $_POST[$this->submit."__foto_datos"];
					$pares = explode("||", $datos);
					$nodos = array();
					foreach ($pares as $par) {
						list($id, $visible) = explode("=", $par);
						$nodos[$id] = $visible;
					}
					$this->reportar_evento( $evento, $nombre, $nodos );
				}
			}
		}
	}
	
	function obtener_html()
	{
		$salida = "";
		$salida .= form::hidden($this->submit, '');
		$salida .= form::hidden($this->submit."__seleccion", '');
		if ($this->puede_sacar_foto) {
			$salida .= form::hidden($this->submit."__foto_nombre", '');
			$salida .= form::hidden($this->submit."__foto_datos", '');
			$salida .= "<span style='float:right'>";
			$salida .= "<a href='#' onclick='{$this->objeto_js}.sacar_foto()' 
									title='Saca una foto para poder recrear el estado del �rbol'>".
									recurso::imagen_apl('arbol/foto.gif', true)."</a>";
			$salida .= "</span>";
			$salida .= "<br><br>";
		}
		if ($this->nodo_inicial != null) {
			$salida .= "\n<ul id='{$this->objeto_js}_nodo_raiz' class='ei-arbol-raiz'>";
			$salida .= $this->recorrer_recursivo($this->nodo_inicial, true);		
			$salida .= "</ul>";
		}
		echo $salida;
	}
	
	protected function recorrer_recursivo(recorrible_como_arbol $nodo, $es_raiz = false, $nivel = 0)
	{
		//Determina si el nodo es visible en la foto
		$es_visible = $this->nodo_es_visible($nodo, $nivel);
		$salida = "\n\t<li class='ei-arbol-nodo'>";
		if (!$es_raiz || $this->mostrar_raiz) {
			$salida .= $this->mostrar_utilerias($nodo);
			if (! $nodo->es_hoja()) {
				if ($es_visible)
					$img_exp_contr = recurso::imagen_apl('arbol/contraer.gif', false); 
				else
					$img_exp_contr = recurso::imagen_apl('arbol/expandir.gif', false);
				$salida .= "<img src='$img_exp_contr' onclick='{$this->objeto_js}.cambiar_expansion(this);' 
							 class='ei-arbol-exp-contr'> ";
			} else {
				$salida .= gif_nulo(14,1);
			}
			$salida .= $this->mostrar_iconos($nodo);
			
			//Nombre
			$corto = $this->acortar_nombre($nodo->nombre_corto());
			$title= "title='Nombre: ".$nodo->nombre_largo()."\nId:  ".$nodo->id()."'";
			$nombre= "<span class='ei-arbol-nombre' $title>$corto</span>";
			if ($nodo->tiene_propiedades()) {
				$salida .= "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"".$nodo->id()."\");' ".
							"class='ei-arbol-ver-prop' $title>$nombre</a>";			
			} else {
				$salida .= $nombre;
			}
		}
		//Recursividad
		if (! $nodo->es_hoja()) {
			$estilo =  ($es_visible) ? "" : "style='display:none'";
			$salida .= "\n<ul id_nodo='{$nodo->id()}' class='ei-arbol-rama' $estilo>";
			$nivel = $nivel + 1;
			foreach ($nodo->hijos() as $nodo_hijo) {
				$salida .= $this->recorrer_recursivo($nodo_hijo, false, $nivel);
			}
			$salida .= "</ul>";
		}
		$salida .= "</li>\n";
		return $salida;
	}
	
	protected function nodo_es_visible($nodo, $nivel)
	//Determina si un nodo es visible viendo en la foto
	{
		if (isset($this->foto_seleccionada[$nodo->id()])) {
			return $this->foto_seleccionada[$nodo->id()];
		}
		//Si no esta en la foto se determina por el nivel de apertura estandar
		return ($nivel < $this->nivel_apertura);
	}
	
	protected function mostrar_iconos($nodo)
	{
		$salida = '';
		foreach ($nodo->iconos() as $icono) {
			$img = recurso::imagen($icono['imagen'], null, null, $icono['ayuda']);
			if (isset($icono['vinculo'])) {
				$salida .= "<a target='{$this->frame_destino}' href='".$icono['vinculo']."'>$img</a>\n";
			} else {
				$salida .= $img."\n";
			}
		}	
		return $salida;
	}
	
	protected function mostrar_utilerias($nodo)
	{
		$utilerias = $nodo->utilerias();
		if (count($utilerias) > 0) {
			$salida = "<span style='float: right'>";
			foreach ($utilerias as $utileria) {
				$img = recurso::imagen($utileria['imagen'], null, null, $utileria['ayuda']);
				if (isset($utileria['vinculo'])) {
					$salida .= "<a target='{$this->frame_destino}' href='".$utileria['vinculo']."'>$img</a>\n";
				} else {
					$salida .= $img;
				}
			}
			$salida .= "</span>";
			return $salida;
		}
	}

	protected function acortar_nombre($nombre) 
	{
		$limite = 30;
		if (strlen($nombre) <= $limite)
			return $nombre;
		else
			return substr($nombre, 0, $limite)."...";
		return $nombre;
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		$item = js::arreglo($this->item_propiedades, false);
		echo $identado."var {$this->objeto_js} = new objeto_ei_arbol('{$this->objeto_js}',
												 '{$this->submit}', $item);\n";

	}

	//-------------------------------------------------------------------------------

	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_arbol';
		return $consumo;
	}	

}

?>