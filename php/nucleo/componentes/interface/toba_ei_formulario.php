<?
require_once("toba_ei.php");	//Ancestro de todos los	OE
require_once("nucleo/componentes/interface/efs/toba_ef.php");

/**
 * Un formulario simple presenta una grilla de campos editables. 
 * A cada uno de estos campos se los denomina Elementos de Formulario (efs).
 * @todo Los EF deberian cargar su estado en el momento de obtener la interface, no en su creacion.
 * @package Componentes
 * @subpackage Eis
 */
class toba_ei_formulario extends toba_ei
{
	protected $prefijo = 'form';	
	protected $elemento_formulario;			// interno | array |	Rererencias	a los	ELEMENTOS de FORMULARIO
	protected $nombre_formulario;			// interno | string | Nombre del	FORMULARIO en el cliente
	protected $lista_ef = array();			// interno | array |	Lista	completa	de	a los	EF
	protected $lista_ef_post = array();		// interno | array |	Lista	de	elementos que se reciben por POST
	protected $lista_toba_ef_ocultos = array();
	protected $nombre_ef_cli = array(); 	// interno | array | ID html de los elementos
	protected $parametros_carga_efs;		// Par�metros que se utilizan para cargar opciones a los efs
	protected $modelo_eventos;
	protected $flag_out = false;			// indica si el formulario genero output
	protected $evento_mod_estricto;			// Solo dispara la modificacion si se apreto el boton procesar
	protected $rango_tabs;					// Rango de n�meros disponibles para asignar al taborder
	protected $objeto_js;	
	protected $ancho_etiqueta = '150px';
	protected $efs_invalidos = array();

	protected $eventos_ext = null;			// Eventos seteados desde afuera
	protected $observadores;
	protected $item_editor = '/admin/objetos_toba/editores/ei_formulario';
		
	//---Cascadas
	protected $cascadas_maestros = array();		//Arreglo de maestros indexados por esclavo
	protected $cascadas_esclavos = array();		//Arreglo de esclavos indexados por maestro

	function __construct($id)
	{
		parent::__construct($id);
		//Nombre de los botones de javascript
		$this->js_eliminar = "eliminar_ei_{$this->submit}";
		$this->js_agregar = "agregar_ei_{$this->submit}";
		$this->evento_mod_estricto = true;
	}

	function destruir()
	{
		//Memorizo la lista de efs enviados
		$this->memoria['lista_efs'] = $this->lista_ef_post;
		parent::destruir();
	}
	
	function inicializar($parametros)
	{
		parent::inicializar($parametros);
		$this->nombre_formulario =	$parametros["nombre_formulario"];
		if (isset($this->info_formulario['ancho_etiqueta']) && $this->info_formulario['ancho_etiqueta'] != '') {
			$this->ancho_etiqueta = $this->info_formulario['ancho_etiqueta'];
		}	
		//Creo el array de objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo IDs en el CLIENTE
		foreach ($this->lista_ef_post as $ef) {
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->get_id_form();
		}
		//Inicializacion de especifica de cada tipo de formulario
		$this->inicializar_especifico();
	}
	
	protected function crear_elementos_formulario()
	{
		$this->lista_ef = array();
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
			//-[1]- Armo las listas	que determinan	el	plan de accion	del ABM
			$id_ef = $this->info_formulario_ef[$a]["identificador"];
			$this->lista_ef[]	= $id_ef;
			switch ($this->info_formulario_ef[$a]["elemento_formulario"]) {
				case	"ef_oculto":
				case	"ef_oculto_secuencia":
				case	"ef_oculto_proyecto":
				case	"ef_oculto_usuario":
					$this->lista_toba_ef_ocultos[] = $id_ef;
					break;
				default:
					$this->lista_ef_post[] = $id_ef;
			}
			//$parametros	= parsear_propiedades($this->info_formulario_ef[$a]["inicializacion"], '_');
			$parametros = $this->info_formulario_ef[$a];
			if (isset($parametros['carga_sql']) && !isset($parametros['carga_fuente'])) {
				$parametros['carga_fuente']=$this->info['fuente'];
			}

			//Preparo el identificador	del dato	que maneja el EF.
			//Esta parametro puede ser	un	ARRAY	o un string: exiten EF complejos	que manejan	mas de una
			//Columna de la tabla a	la	que esta	asociada	el	ABM
			if(ereg(",",$this->info_formulario_ef[$a]["columnas"])){
				 $dato =	explode(",",$this->info_formulario_ef[$a]["columnas"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las	claves
					$dato[$d]=trim($dato[$d]);
				}
			}else{
				 $dato =	$this->info_formulario_ef[$a]["columnas"];
			}
			//Nombre	del formulario.
			$id_ef = $this->info_formulario_ef[$a]["identificador"];
			$this->parametros_carga_efs[$id_ef] = $parametros;
			$clase_ef = 'toba_'.$this->info_formulario_ef[$a]["elemento_formulario"];
			$this->elemento_formulario[$id_ef] = new $clase_ef(
															$this, 
															$this->nombre_formulario,
															$this->info_formulario_ef[$a]["identificador"],
															$this->info_formulario_ef[$a]["etiqueta"],
															addslashes($this->info_formulario_ef[$a]["descripcion"]),
															$dato,
															$this->info_formulario_ef[$a]["obligatorio"],
															$parametros);
			$this->elemento_formulario[$id_ef]->set_expandido(! $this->info_formulario_ef[$a]['colapsado']);
			if (isset($this->info_formulario_ef[$a]['etiqueta_estilo'])) {
				$this->elemento_formulario[$id_ef]->set_estilo_etiqueta( $this->info_formulario_ef[$a]['etiqueta_estilo'] );
			}
		}
	}
	
	function inicializar_especifico()
	{
		$this->set_grupo_eventos_activo('no_cargado');
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------

	function pre_eventos()
	{
		//-- Resguarda la lista de efs para servicio
		$this->lista_efs_servicio = $this->lista_ef_post;
		$this->lista_ef_post = $this->memoria['lista_efs'];	
		if (isset($this->memoria['efs'])) {
			foreach (array_keys($this->memoria['efs']) as $id_ef) {
				if (isset($this->memoria['efs'][$id_ef]['obligatorio'])) {
					$this->elemento_formulario[$id_ef]->set_obligatorio($this->memoria['efs'][$id_ef]['obligatorio']);
				}
			}
		}
	}

	function post_eventos()
	{
		if (isset($this->memoria['efs'])) {
			//--- Restaura lo obligatorio
			foreach ($this->info_formulario_ef as $def_ef) {
				$id_ef = $def_ef['identificador'];
				if (isset($this->memoria['efs'][$id_ef])) {
					$this->elemento_formulario[$id_ef]->set_obligatorio($def_ef['obligatorio']);
				}
			}
			unset($this->memoria['efs']);
		}
		$this->limpiar_interface();	
		//-- Restaura la lista de efs para servicio		
		$this->lista_ef_post = $this->lista_efs_servicio;
	}	
	
	function disparar_eventos()
	{
		$this->log->debug( $this->get_txt() . " disparar_eventos", 'toba');		
		$this->pre_eventos();
		$this->recuperar_interaccion();
		$datos = $this->get_datos();
		$validado = false;
		//Veo si se devolvio algun evento!
		if (isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset($this->memoria['eventos'][$evento])) {
				//Me fijo si el evento requiere validacion
				$maneja_datos = ($this->memoria['eventos'][$evento] == apex_ei_evt_maneja_datos);
				if($maneja_datos) {
					if (! $validado) {
						$this->validar_estado();
						$validado = true;
					}
					$parametros = $datos;
				} else {
					$parametros = null;
				}
				//El evento es valido, lo reporto al contenedor
				$this->reportar_evento( $evento, $parametros );
			}
		}
		$this->post_eventos();
	}

	function recuperar_interaccion()
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->cargar_estado_post();
		}
	}

	function validar_estado()
	{
		//Valida el	estado de los ELEMENTOS	de	FORMULARIO
		foreach ($this->lista_ef_post as $ef) {
			$validacion = $this->elemento_formulario[$ef]->validar_estado();
			if ($validacion !== true) {
				$this->efs_invalidos[$ef] = $validacion;
				$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
				throw new toba_error_validacion($etiqueta.': '.$validacion, $this->ef($ef));
			}
		}
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	CASCADAS  -------------------------------
	//-------------------------------------------------------------------------------

	function registrar_cascadas()
	{
		$this->cascadas_maestros = array();
		$this->cascadas_esclavos = array();
		foreach ($this->lista_ef_post as $esclavo) {
			$this->cascadas_maestros[$esclavo] = $this->elemento_formulario[$esclavo]->get_maestros();
			foreach ($this->cascadas_maestros[$esclavo] as $maestro) {
				if (! isset($this->elemento_formulario[$maestro])) {
					throw new toba_error_def("Cascadas: El ef '$maestro' no esta definido");
				}
				$this->cascadas_esclavos[$maestro][] = $esclavo;

				$id_form_dep = $this->elemento_formulario[$esclavo]->get_id_form();
				$js = "{$this->objeto_js}.cascadas_cambio_maestro('$maestro')";
				$this->elemento_formulario[$maestro]->set_cuando_cambia_valor($js);
			}
		}
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------	Manejos de EFS ------------------------------
	//-------------------------------------------------------------------------------

	function limpiar_interface()
	{
		foreach ($this->lista_ef as $ef) {
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}

	function get_nombres_ef()
	{
		return $this->lista_ef_post;
	}
	
	/**
	 * Retorna la lista de identificadores que no estan desactivados
	 */
	protected function get_efs_activos()
	{
		$lista = array();
		foreach ($this->lista_ef as $id_ef) {
			if (in_array($id_ef, $this->lista_ef_post) || in_array($id_ef, $this->lista_toba_ef_ocultos)) {
				$lista[] = $id_ef;
			}	
		}
		return $lista;
	}
	
	/**
	 * Retorna la referencia a un ef contenido
	 * @return ef
	 */
	function ef($id) 
	{
		return $this->elemento_formulario[$id];
	}

	
	/**
	 * Permite o no la edici�n de un conjunto de efs de este formulario, pero sus valores se muestran al usuario
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $readonly �Hacer solo_lectura? (true por defecto)
	 */
	function set_solo_lectura($efs=null, $readonly=true)
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef) {
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->set_solo_lectura($readonly);
			}else{
				throw new toba_error("Deshabilitar EF: El ef '$ef' no existe");
			}
		}
	}

	/**
	 * Establece que un conjunto de efs ser�n o no obligatorios
	 * Este estado perdura durante una interaccion
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $obligatorios �Hacer obligatorio? (true por defecto)
	 */
	function set_efs_obligatorios($efs=null, $obligatorios=true) {
		if (!isset($efs)) {
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef) {
			if (isset($this->elemento_formulario[$ef])) {
				$this->elemento_formulario[$ef]->set_obligatorio($obligatorios);
				$this->memoria['efs'][$ef]['obligatorio'] = $obligatorios;
			} else {
				throw new toba_error("El ef '$ef' no existe");
			}
		}
	}
	
	/**
	 * Establece que un conjunto de efs NO seran enviados al cliente durante una interacci�n
	 * 
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 */
	function desactivar_efs($efs=null)
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);
		}
		foreach ($efs as $ef) {
			$pos = array_search($ef, $this->lista_ef_post);
			if ($pos !== false) {
				array_splice($this->lista_ef_post, $pos, 1);
			} else {
				throw new toba_error("No se puede desactivar el ef '$ef' ya que no se encuentra en la lista de efs activos");
			}
		}
	}
	
	function cargar_estado_ef($array_ef)
	{
		if (is_array($array_ef)){
			foreach($array_ef	as	$ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->set_estado($valor);
				}else{
					throw new toba_error("[cargar_estado_ef] No existe	un	elemento	de	formulario identificado	'$ef'");
				}
			}
		}else{
			throw new toba_error("[cargar_estado_ef] Los	EF	se	cargan a	travez de un array asociativo	(\"clave\"=>\"dato a	cargar\")!");
		}
	}

	function get_tab_index()
	{
		if (isset($this->rango_tabs)) {
			return $this->rango_tabs[0]++;
		}	
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	  -------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Recupera el estado actual del formulario. Genera un array asociativo de una dimension
	 */
	function get_datos()
	{
		$registro = array();
		foreach ($this->get_efs_activos() as $ef) {
			$dato	= $this->elemento_formulario[$ef]->get_dato();
			$estado = $this->elemento_formulario[$ef]->get_estado();
			if (is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if ($this->elemento_formulario[$ef]->es_estado_unico()) {
					if ((count($dato))!=(count($estado))) {//Error	de	consistencia interna	del EF
						throw new toba_error_def("Error de consistencia	interna en el EF etiquetado: ".
											$this->elemento_formulario[$ef]->get_etiqueta().
											"\nRecibido: ".var_export($estado, true));
					}
					for($x=0;$x<count($dato);$x++){
						$registro[$dato[$x]] = $estado[$dato[$x]];
					}
				} else {
					//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo					
					$salida = array();
					$registro[$ef] = array();
					foreach ($estado as $sub_estado) {
						if (count($dato) != count($sub_estado)) {
							//Error	de	consistencia interna	del EF
							throw new toba_error_def("Error de consistencia	interna en el EF etiquetado: ".
												$this->elemento_formulario[$ef]->get_etiqueta().
												"\nRecibido: ".var_export($sub_estado, true));
						}
						for ($x=0;$x<count($dato);$x++) {
							$salida[$dato[$x]] = $sub_estado[$dato[$x]];
						}
						$registro[$ef][] = $salida;						
					}
				}
			} else {
				$registro[$dato] = $estado;
			}
		}
		return $registro;
	}

	function post_configurar()
	{
		parent::post_configurar();
		//---Registar esclavos en los maestro
		$this->registrar_cascadas();		
	}
	
	function set_datos($datos)
	{
		if (isset($datos)){
			//ei_arbol($datos,"DATOS para llenar el EI_FORM");
			//Seteo los	EF	con el valor recuperado
			foreach ($this->lista_ef as $ef) {	//Tengo que	recorrer	todos	los EF...
				$temp = null;
				$dato = $this->elemento_formulario[$ef]->get_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					if ($this->elemento_formulario[$ef]->es_estado_unico()) {
						$temp = null;
						for($x=0;$x<count($dato);$x++) {
							if(isset($datos[$dato[$x]])) {
								$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
							}
						}
					} else {
						//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo
						$temp = $datos[$ef];
					}
				} else {					//El EF maneja	un	DATO SIMPLE
					if (isset($datos[$dato])){
						if (!is_array($datos[$dato]))
							$temp = stripslashes($datos[$dato]);
						elseif (is_array($datos[$dato])) { //ATENCION: Este es el caso para el multi-seleccion, hay que mejorarlo
							$temp = array();
							foreach ($datos[$dato] as $string) {
								$temp[] = stripslashes($string);
							}
						}
					}
				}
				if(isset($temp)){
					$this->elemento_formulario[$ef]->set_estado($temp);
				}
			}
			if ($this->grupo_eventos_activo == 'no_cargado') {
				$this->set_grupo_eventos_activo('cargado');
			}
		}
	}
	
	//---------------------------------------------------------------------------
	//-------------	  CARGA DE OPCIONES de  efs	  -------------------------------
	//---------------------------------------------------------------------------

	function ef_tiene_maestros_seteados($id_ef)
	{
		foreach ($this->cascadas_maestros[$id_ef] as $maestro) {
			if (! $this->elemento_formulario[$maestro]->tiene_estado()) {
				return false;
			}
		}
		return true;			
	}
	
	protected function cargar_opciones_efs()
	{
		foreach ($this->lista_ef_post as $id_ef) {
			if ($this->ef_requiere_carga($id_ef)) {
				$param = array();
				//-- Tiene maestros el ef? Todos tienen estado?
				$cargar = true;
				$tiene_maestros = false;
				if (isset($this->cascadas_maestros[$id_ef]) && !empty($this->cascadas_maestros[$id_ef])) {
					$tiene_maestros = true;
					foreach ($this->cascadas_maestros[$id_ef] as $maestro) {
						if ($this->elemento_formulario[$maestro]->tiene_estado()) {
							$estado = $this->elemento_formulario[$maestro]->get_estado();
							$param[$maestro] = $estado;
						} else {
							$cargar = false;
						}
					}
				}
				//--- Existe la posibilidad que no tenga maestros y ya ha sido cargado anteriormente
				//--- En este caso se evita una re-carga porque se asume que no hay condiciones que puedan variar las opciones
				$cargado = false;
				if (! $tiene_maestros && $cargar) {
					if ($this->elemento_formulario[$id_ef]->tiene_opciones_cargadas()) {
						$cargado = true;
					}
				}
				if (! $cargado) {
					if ($cargar) {
						if ($this->elemento_formulario[$id_ef]->carga_depende_de_estado()) {
							$param[$id_ef] = $this->elemento_formulario[$id_ef]->get_estado();
						}
						$datos = $this->ejecutar_metodo_carga_ef($id_ef, $param);
					} else {
						$datos = null;	
					}
					$this->elemento_formulario[$id_ef]->set_opciones($datos);
				}
			}
		}
	}
	
	protected function ef_requiere_carga($id_ef)
	{
		return 
			isset($this->parametros_carga_efs[$id_ef]['carga_metodo'])
			|| isset($this->parametros_carga_efs[$id_ef]['carga_lista'])
			|| isset($this->parametros_carga_efs[$id_ef]['carga_sql']);
	}
	
	protected function ejecutar_metodo_carga_ef($id_ef, $maestros = array())
	{
		$parametros = $this->parametros_carga_efs[$id_ef];
		$seleccionable = $this->elemento_formulario[$id_ef]->es_seleccionable();
		
		$es_posicional = true;
		if ($seleccionable) {
			//--- Se determinan cuales son los campos claves y el campo de valor
			$campos_clave = $this->elemento_formulario[$id_ef]->get_campos_clave();
			$campo_valor = $this->elemento_formulario[$id_ef]->get_campo_valor();
			$es_posicional = $this->elemento_formulario[$id_ef]->son_campos_posicionales();
	
			$valores = array();
			if (isset($parametros['carga_no_seteado']) && ! isset($valores[apex_ef_no_seteado])) {
				$valores[apex_ef_no_seteado] = $parametros['carga_no_seteado'];
			}
		}
		if (isset($parametros['carga_lista'])) {
			//--- Carga a partir de una lista de valores
			$nuevos = $this->ef_metodo_carga_lista($id_ef, $parametros, $maestros);
			return $valores + $nuevos;
		} elseif (isset($parametros['carga_sql'])) {
			//--- Carga a partir de un SQL
			$nuevos = $this->ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional);
			if ($seleccionable) {
				return $valores + rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
			} else {
				if (! empty($nuevos)) {
					return $nuevos[0][0];					
				}
			}
		} elseif (isset($parametros['carga_metodo'])) {
			//--- Carga a partir de un M�todo PHP
			$nuevos = $this->ef_metodo_carga_php($id_ef, $parametros, $maestros);
			if ($seleccionable) {
				$val = $valores + rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
				return $val;
			} else {
				return $nuevos;	
			}
		}
	}

	protected function ef_metodo_carga_lista($id_ef, $parametros, $maestros)
	{
		$elementos = explode(",", $parametros['carga_lista']);
		$valores = array();
		foreach ($elementos as $elemento) {
			$campos = explode("/", $elemento);
			if (count($campos) == 1) {
				$valores[trim($campos[0])] = trim($campos[0]);
			} elseif (count($campos) == 2) {
				$valores[trim($campos[0])] = trim($campos[1]);
			} else {
				throw new toba_error_def("La lista de opciones del ef '$id_ef' es incorrecta.");
			}
		}		
		return $valores;
	}
	
	protected function ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional)
	{
		//--- Si la SQL contenia comillas fueron quoteadas cuando se guardaron en la base
		$parametros['carga_sql'] = stripslashes($parametros['carga_sql']);
        //Armo la sentencia que limita al proyecto
        $sql_where = "";
        if (isset($parametros['columna_proyecto'])) {
    		$sql_where .= $parametros["columna_proyecto"] . " = '".toba::proyecto()->get_id()."' ";
			if (isset($parametros["incluir_toba"]) && $parametros["incluir_toba"]) {
		        $sql_where .= " OR ".$parametros["columna_proyecto"]." = 'toba'";
			}
        }
		if ($sql_where != '') {
	        $where[] = "(" . $sql_where .")";
        	$parametros['carga_sql'] =  stripslashes(sql_agregar_clausulas_where($parametros['carga_sql'],$where));
		}
		foreach ($maestros as $id_maestro => $valor_maestro) {
			$parametros['carga_sql'] = ereg_replace(apex_ef_cascada.$id_maestro.apex_ef_cascada, $valor_maestro,
												$parametros['carga_sql']);
		}
		$modo = ($es_posicional) ? toba_db_fetch_num : toba_db_fetch_asoc;
		return toba::db($parametros['carga_fuente'])->consultar($parametros['carga_sql'], $modo);
	}
	
	protected function ef_metodo_carga_php($id_ef, $parametros, $maestros)
	{
		if (isset($parametros['carga_include'])) {
			$instanciable = (isset($parametros['instanciable']) && $parametros['instanciable']=='1');
			require_once($parametros['carga_include']);
			if ($instanciable) {
				$obj = new $parametros['carga_clase']();
				$metodo = array($obj, $parametros['carga_metodo']);
			} else {
				$metodo = array($parametros['carga_clase'], $parametros['carga_metodo']);
			}
			return call_user_func_array($metodo, $maestros);
		} else {
			//--- Es un metodo del CI contenedor
			return call_user_func_array( array($this->controlador, $parametros['carga_metodo']), $maestros);
		}
	}
	
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------
	//-------------------------------------------------------------------------------

	function servicio__cascadas_efs()
	{
		require_once('3ros/JSON.php');				
		if (! isset($_GET['cascadas-ef']) || ! isset($_GET['cascadas-maestros'])) {
			throw new toba_error("Cascadas: Invocaci�n incorrecta");	
		}
		$id_ef = trim(toba::memoria()->get_parametro('cascadas-ef'));
		$maestros = array();
		$ids_maestros = $this->cascadas_maestros[$id_ef];
		foreach (explode('-|-', toba::memoria()->get_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new toba_error("Cascadas: Cantidad incorrecta de parametros ($par).");						
				}
				$id_ef_maestro = $param[0];
				
				//--- Verifique que este entre los maestros y lo elimina de la lista
				if (!in_array($id_ef_maestro, $ids_maestros)) {
					throw new toba_error("Cascadas: El ef '$id_ef_maestro' no esta entre los maestros de '$id_ef'");
				}
				array_borrar_valor($ids_maestros, $id_ef_maestro);
				
				$campos = $this->elemento_formulario[$id_ef_maestro]->get_dato();
				$valores = explode(apex_qs_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_ef_maestro] = $param[1];
				} else {
					//--- Manejo de claves m�ltiples					
					if (count($valores) != count($campos)) {
						throw new excepction_toba("Cascadas: El ef $id_ef_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_ef_maestro] = $valores_clave;
				}
			}
		}
		//--- Recorro la lista de maestros para ver si falta alguno. Permite tener ocultos como maestros
		foreach ($ids_maestros as $id_ef_maestro) {
			if (! $this->ef($id_ef_maestro)->tiene_estado()) {
				throw new toba_error("Cascadas: El ef maestro '$id_ef_maestro' no tiene estado cargado");
			}
			$maestros[$id_ef_maestro] = $this->ef($id_ef_maestro)->get_estado();
		}
		toba::logger()->debug("Cascadas '$id_ef', Estado de los maestros: ".var_export($maestros, true));		
		$valores = $this->ejecutar_metodo_carga_ef($id_ef, $maestros);
		toba::logger()->debug("Cascadas '$id_ef', Respuesta: ".var_export($valores, true));				
		
		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		echo $json->encode($valores);
	}
	
	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
		//Campo de sincroniacion con JS
		echo toba_form::hidden($this->submit, '');
		echo toba_form::hidden($this->submit.'_implicito', '');
		$ancho = '';
		if (isset($this->info_formulario["ancho"])) {
			$ancho = convertir_a_medida_tabla($this->info_formulario["ancho"]);
		}
		echo "<table class='ei-base ei-form-base' $ancho id='{$this->objeto_js}_cont'>";
		echo "<tr><td>";
		$this->generar_html_barra_sup(null, true,"ei-form-barra-sup");
		echo "</td></tr>\n";
		echo "<tr><td>";
		$this->generar_formulario();	
		echo "</td></tr>\n";
		echo "</table>\n";
		$this->flag_out = true;
	}

	protected function generar_formulario()
	{
		//--- La carga de efs se realiza aqui para que sea contextual al servicio
		//--- ya que hay algunos que no lo necesitan (ej. cascadas)
		$this->cargar_opciones_efs();
		$this->rango_tabs = manejador_tabs::instancia()->reservar(250);		
				
		$ancho = ($this->info_formulario['ancho'] != '') ? "style='width: {$this->info_formulario['ancho']}'" : '';
		$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";
	
		echo "<div class='ei-cuerpo ei-form-cuerpo' $ancho id='cuerpo_{$this->objeto_js}'>";
		$hay_colapsado = false;
		foreach ($this->lista_ef_post as $ef){
			if (! $this->elemento_formulario[$ef]->esta_expandido()) {
				$hay_colapsado = true;
			}
			$this->generar_envoltura_ef($ef);
		}
		if ($hay_colapsado) {
			$img = toba_recurso::imagen_apl('expandir_vert.gif', false);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_expansion();\" title='Mostrar / Ocultar'";
			echo "<div class='ei-form-fila ei-form-expansion'>";
			echo "<img id='{$this->objeto_js}_cambiar_expansion' src='$img' $colapsado>";
			echo "</div>";
		}
		$this->generar_botones();
		echo "</div>\n";
	}
	
	protected function generar_envoltura_ef($ef)
	{
		$clase = 'ei-form-fila';
		$estilo_nodo = "";
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();
		if (! $this->elemento_formulario[$ef]->esta_expandido()) {
			$clase .= ' ei-form-fila-oculta';
			$estilo_nodo = "display:none";
		}
		
		if ($this->elemento_formulario[$ef]->tiene_etiqueta()) {
			echo "<div class='$clase' style='$estilo_nodo' id='nodo_$id_ef'>\n";
			$this->generar_etiqueta_ef($ef);
			//--- El margin-left de 0 y el heigth de 1% es para evitar el 'bug de los 3px'  del IE
			echo "<div id='cont_$id_ef' style='margin-left:{$this->ancho_etiqueta};_margin-left:0;_height:1%;'>\n";
			echo $this->elemento_formulario[$ef]->get_input();
			echo "</div>";
			echo "</div>\n";
		} else {		
			echo $this->elemento_formulario[$ef]->get_input();
		}
	}

	protected function generar_etiqueta_ef($ef)
	{
		$estilo = $this->elemento_formulario[$ef]->get_estilo_etiqueta();
		if ($estilo == '') {
	        if ($this->elemento_formulario[$ef]->es_obligatorio()) {
	    	        $estilo = 'ei-form-etiq-oblig';
					$marca = '(*)';
        	} else {
	            $estilo = 'ei-form-etiq';
				$marca ='';
    	    }
		}
		$desc = $this->elemento_formulario[$ef]->get_descripcion();
		if ($desc !=""){
			$desc = toba_recurso::imagen_apl("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();					
		$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
		//--- El _width es para evitar el 'bug de los 3px'  del IE
		echo "<label style='_width:{$this->ancho_etiqueta};' for='$id_ef' class='$estilo'>$editor $desc $etiqueta $marca</label>\n";
	}
	
	protected function generar_vinculo_editor($id_ef)
	{
		if (toba_editor::modo_prueba()) {
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->id),
									'ef' => $id_ef );
			return toba_editor::get_vinculo_subcomponente($this->item_editor, $param_editor);			
		}
		return null;
	}
		
	//-------------------------------------------------------------------------------
	//------------------------------ JAVASCRIPT  ------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		$esclavos = toba_js::arreglo($this->cascadas_esclavos, true, false);
		$maestros = toba_js::arreglo($this->cascadas_maestros, true, false);
		$id = toba_js::arreglo($this->id, false);
		$invalidos = toba_js::arreglo($this->efs_invalidos, true);
		echo $identado."window.{$this->objeto_js} = new ei_formulario($id, '{$this->objeto_js}', $rango_tabs, '{$this->submit}', $maestros, $esclavos, $invalidos);\n";
		foreach ($this->lista_ef_post as $ef) {
			echo $identado."{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
	}

	function get_objeto_js_ef($id)
	{
		return "{$this->objeto_js}.ef('$id')";
	}
	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_formulario';
		//Busco las	dependencias
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->get_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------
		
	function vista_impresion_html( $salida )
	{
		$this->cargar_opciones_efs();		
		$salida->subtitulo( $this->get_titulo() );
		echo "<table class='tabla-0' width='{$this->info_formulario['ancho']}'>";
		foreach ( $this->lista_ef_post as $ef){
			echo "<tr><td class='ei-form-etiq'>\n";
			echo $this->elemento_formulario[$ef]->get_etiqueta();
			$temp = $this->get_valor_imprimible_ef( $ef );
			echo "</td><td class='". $temp['css'] ."'>\n";
			echo $temp['valor'];
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}
	
	function get_valor_imprimible_ef( $id_ef ) 
	{
		require_once('nucleo/lib/interface/formateo.php');
		$ef = $this->elemento_formulario[$id_ef];
		$valor = $ef->get_descripcion_estado();
		if ( $ef instanceof toba_ef_editable_moneda ) {
			$temp = array( 'css' => 'col-num-p1', 'valor'=> formato_moneda($valor) );
		} elseif ( $ef instanceof toba_ef_editable_numero ) {
			$temp = array( 'css' => 'col-num-p1', 'valor'=> $valor );
		} elseif ( $ef instanceof toba_ef_editable_fecha ) {
			if ($valor!='') {
				$temp = array( 'css' => 'col-tex-p1', 'valor'=> formato_fecha($valor) );
			} else {
				$temp = array( 'css' => 'col-tex-p1', 'valor'=> '' );
			}
		} else {
			$temp = array( 'css' => 'col-tex-p1', 'valor'=> $valor );
		}
		return $temp;
	}
}
?>