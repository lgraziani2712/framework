<?php
require_once("objeto.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");

class objeto_ci extends objeto
/*
 	@@acceso: nucleo
	@@desc: Descripcion

	ATENCION: Falta el control del estado en el servidor
*/
{
	var $nombre_formulario;			// privado | string | Nombre del <form> del MT
	var $cn;								// Controlador de negocio asociado
	var $datos_cargados;		
	var $submit;						// Boton de SUBMIT
	var $submit_etiq;					// Etiqueta del boton SUBMIT
	var $cancelar_etiq;
	var $flag_cancelar_operacion;		//Flag de GET que cancela la operacion
	var $dependencias_actual = array();
	var $estado_procesar = false;
	var $estado_cancelar = false;	
	
	function objeto_ci($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto($id);
		//Inicializo VARIOS
		$this->nombre_formulario = "CI_" . $this->id[1] ;//Cargo el nombre del <form>
		$this->submit = $this->nombre_formulario . "_submit";
  		$this->cargar_memoria(); 			//Cargo la MEMORIA sincronizada
		$this->flag_no_propagacion = "no_prop" . $this->id[1];
		//Cargo las DEPENDENCIAS
		$this->cargar_info_dependencias();
		//Boton SUBMIT
		if($this->info_ci['ev_procesar_etiq']){
			$this->submit_etiq = $this->info_ci['ev_procesar_etiq'];
		}else{
			$this->submit_etiq = "Procesar";
		}
		//Boton CANCELAR
		if($this->info_ci['ev_cancelar_etiq']){
			$this->cancelar_etiq = $this->info_ci['ev_cancelar_etiq'];
		}else{
			$this->cancelar_etiq = "cancelar";
		}
		$this->flag_cancelar_operacion = "ci_canop". $this->id[1];
		//Si tengo al CN como dependencia, lo cargo!
		//EL ID que puede tomar el CN esta harcodeado
		if(isset($this->indice_dependencias['__cn'])){
			$this->cargar_dependencia('__cn');
			//Asigno la referencia a la variable del CN de la clase
			$this->asignar_controlador_negocio( $this->dependencias['__cn'] );
		}
	}

	function set_nombre_formulario($nombre)
	{
		$this->nombre_formulario = $nombre;
	}

	function destruir()
	{
		parent::destruir();
		$this->memorizar();					//GUARDO Memoria sincronizada
	}
	
	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//-- CI ----------------------
		$sql["info_ci"]["sql"] = "SELECT		incremental	as	incremental,
												debug_eventos 			as	debug_eventos,
												ev_procesar				as	ev_procesar,
												ev_procesar_etiq		as	ev_procesar_etiq,
												activacion_procesar		as	activacion_procesar,
												metodo_despachador		as	metodo_despachador,
												ev_cancelar				as	ev_cancelar,
												ev_cancelar_etiq		as	ev_cancelar_etiq,
												objetos					as	objetos,			
												ancho					as	ancho,			
												alto					as	alto
										FROM	apex_objeto_mt_me
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me='".$this->id[1]."';";
		$sql["info_ci"]["tipo"]="1";
		$sql["info_ci"]["estricto"]="1";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//----------------------------  PROCESO   ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar()
	/*
		Hay casos en los que se requiere procesamiento pero no se necesita
		cargar los datos de las dependencias.
		Arreglar esto deberia incluir la unificacion con los metodos 
		procesar en los hijos.
	*/
	{
		// 0 - Cancelar la operacion?
		if( $this->operacion_cancelada() ){
			$this->cancelar_operacion();
		}
		// 1 - Cargo las dependencias
		if(isset($this->info_ci["objetos"])){
			$dependencias = explode(",",$this->info_ci["objetos"]);
			$this->dependencias_actual = array_map("trim",$dependencias);
		}
		$this->cargar_dependencias($this->dependencias_actual);
		// 2 - Busco eventos en los EI
		$this->controlar_eventos($this->dependencias_actual);

		// 3 - Proceso la operacion
		if($this->controlar_activacion()){ //Procesar el Marco transaccional
			//$this->cargar_dependencias_inactivas();
			$this->procesar_operacion();
		}
		// 4- Cargo los DAOS //Seba, lo saque del if anterior
		$this->cargar_daos();

		// 5 - Cargo las interfaces de los EI
		//Esto deberia estar en un metodo aparte
		$this->cargar_datos_dependencias();
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias($dependencias)
	//Hay logica para subir al padre
	{
		//Parametros a los formularios
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		//Cargo dependencias
		foreach($dependencias as $dep){
			//Creo la dependencia
			$this->cargar_dependencia($dep);		
			if($this->dependencias[$dep] instanceof objeto_ci ){
				//-- CI! --
				$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
				$this->dependencias[$dep]->set_nombre_formulario($this->nombre_formulario);
				$this->dependencias[$dep]->procesar();
			}else{
				// Inicializar
				$this->dependencias[$dep]->inicializar($parametro);
			}
		}
	}
	//-------------------------------------------------------------------------------
	
	function cargar_daos()
	{
		//Manejo de DAOS
		//Los daos solo tienen que ejecutarse sobre las depedencias actuales
		foreach($this->dependencias_actual as $dep)
		{			
			if(	$this->dependencias[$dep] instanceof objeto_ei_formulario ||	
				$this->dependencias[$dep] instanceof objeto_ei_cuadro )
			{
				if( $dao_form = $this->dependencias[$dep]->obtener_consumo_dao() ){
					//ei_arbol($dao_form,"DAO");
					//Por cada elemento de formulario que necesita DAOS
					foreach($dao_form as $ef => $dao){
						$sentencia = "\$datos = \$this->cn->{$dao}();";
						//echo $sentencia;
						eval($sentencia);
						//ei_arbol($datos,"DATOS $ef");
						//El cuadro carga sus daos de otra forma
						$this->dependencias[$dep]->ejecutar_metodo_ef($ef,"cargar_datos",$datos);
					}
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_dependencias()
	//Carga los datos de las dependencias actuales
	{
		foreach($this->dependencias_actual as $dep)
		{			
			//En el PARAMETRO B de la dependencia se especifica el metodo
			//Del controlador de negocio que carga a la dependencia
			if($info = $this->consultar_info_dependencia($dep,"parametros_b") )
			{
				$temp = explode(",", $info);
				//-- Metodo a llamar en el CN
				$metodo = trim($temp[0]);
				//-- Parametros al metodo del CN
				if(isset($temp[1])){
					$parametros = explode("|",$temp[1]);
					$parametros = array_map("trim",$parametros);
				}else{
					$parametros = null;
				}
				//-- Hay que llamar un metodo distinto del "CARGAR_DATOS"	en el EI
				if( isset($temp[2])){
					if (trim($temp[2]) != ""){
						$metodo_ei = $temp[2];					
					}else{
						$metodo_ei = "cargar_datos";
					}
				}else{
					$metodo_ei = "cargar_datos";
				}
				//echo "Cargando dependencia : $dep";
				//SI el CN me devuelve un DATO para la dependencia
				$dato = $this->cn->$metodo($parametros);
				//ei_arbol($dato, $dep);
				$this->dependencias[$dep]->$metodo_ei( $dato );
				$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (cargar_dependencia: $dep)\n -- LLAMADA CN -- \nMETODO: $metodo \nPARAMETROS:\n" . var_export($parametros,true) . "\n\n");
				//$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (cargar_dependencia: $dep)\n -- DATOS RECIBIDOS -- \n" . var_export($dato,true) . "\n\n");
			}
			//ei_arbol($this->dependencias[$dependencia]->info_estado());
		}			
	}

	//-------------------------------------------------------------------------------

	function controlar_activacion()
/*
 	@@acceso: interno
	@@desc: Determina si se activo este marco transaccional (si el submit se disparo desde el formulario HTML del mismo)
*/
	{
		if(isset($_POST[$this->submit])){
			//Apretaron el SUBMIT de este FORM
			return true;		
		}else{
			//El submit no es de este formulario, la atencion esta en otro lugar...
			return false;	
		}
	}
	//-------------------------------------------------------------------------------

	function operacion_cancelada()
	{
		if($this->solicitud->hilo->obtener_parametro( $this->flag_cancelar_operacion )){
			return true;
		}
		return false;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------  Relacion con el Controlador de Negocio   ------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function asignar_controlador_negocio( $controlador )
	{
		$this->cn = $controlador;
	}
	//-------------------------------------------------------------------------------

	function cancelar_operacion()
	{	
		//Se dispara la cancelacion en el controlador de negocio
		$this->estado_cancelar = true;
		$this->cn->cancelar();
		$this->borrar_memoria();
		//ATENCION: falta limpiar el estado interno
	}
	//-------------------------------------------------------------------------------

	function procesar_operacion()
	{
		//Se dispara el procesamiento del controlador de negocio
		$this->estado_procesar = true;
		$this->cn->procesar();
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------  PROCESAMIENTO de EVENTOS  ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
/*
	- ATENCION: el orden de los eventos no es indiferente:
		- ej: 	cuadro -> seleccion registro
				form -> modificar registro
			Si se selecciona un registro distinto, antes de modificar se modifica una incorrecto		
*/
	function controlar_eventos($dependencias)
	//Escanea las dependencias buscando eventos
	{
		foreach($dependencias as $dep)
		{
			//-- ! CI! --
			if( ! ($this->dependencias[$dep] instanceof objeto_ci ) )
			{
				//-[1]- Cargo la actividad del usuario
				$this->dependencias[$dep]->recuperar_interaccion();
				//-[2]- Valido el ESTADO
				//$this->dependencias[$dep]->validar_estado();
				//-[3]- Controlo los eventos
				if($evento = $this->dependencias[$dep]->obtener_evento() ){
					$this->procesar_evento($dep, $evento);
				}
				//Se proceso el evento... si es un formulario limpio la interface
				if($this->dependencias[$dep] instanceof objeto_ei_formulario ){
					$this->dependencias[$dep]->limpiar_interface();
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_evento($dep, $evento)
	{
		//Busco un plan de ruteo para el evento reportado
		if($temp = $this->consultar_info_dependencia($dep,"parametros_a") ){
			if($plan_ruteo = parsear_propiedades_array($temp))
			{
				if(isset($plan_ruteo[$evento])){
					//FALTAN CONTROLES de SINTAXIS!!
					$metodo_dep = trim($plan_ruteo[$evento][0]);//echo "Mdep: $metodo_dep";
					$metodo_cn = trim($plan_ruteo[$evento][1]);//echo "Mcn: $metodo_cn";	
					//Tomo el resto de los valores como parametros}
					if(count($plan_ruteo[$evento])>2){
						$temp = trim($plan_ruteo[$evento][2]);
						$parametros = explode("|",$temp);
						$parametros = array_map("trim",$parametros);
					}else{
						$parametros = null;
					}
					try
					{
						if($metodo_dep=="null"){
							$ruteo_eventos[$dep]["CN"] = $metodo_cn;
							$ruteo_eventos[$evento]["CN"]["parametros"] = $parametros;
							$evento = var_export($ruteo_eventos, true);
							$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (procesar_evento: $dep)\n -- EVENTO! -- \n$evento");
							$this->cn->$metodo_cn($parametros);
						}else{
							$datos_evento = $this->dependencias[$dep]->$metodo_dep();
							//LOG: mejorar la descripcion
							$ruteo_eventos[$evento]["EI"]["metodo"] = $metodo_dep;
							$ruteo_eventos[$evento]["EI"]["retorno"] = $datos_evento;
							$ruteo_eventos[$evento]["CN"]["metodo"] = $metodo_cn;
							$ruteo_eventos[$evento]["CN"]["parametros"] = $parametros;
							$evento = var_export($ruteo_eventos, true);
							$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (procesar_evento: $dep)\n -- EVENTO! -- \n$evento");
							$this->cn->$metodo_cn( $datos_evento, $parametros );
						}
					}catch (excepcion_toba $e)
					{
						$this->solicitud->log->debug($e);
					}
				}else{
					//Este evento no esta mapeado
					$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (procesar_evento: $dep) No hay plan para el evento '$evento'");
				}
			}else{
				$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (procesar_evento: $dep) Mapeo INVALIDO");
			}
		}else{
			$this->log->debug("CI ". get_class($this) . " [". $this->id[1] . "] (procesar_evento: $dep) No hay plan de ruteo para la dependencia (evento: $evento)");
		}

	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  SALIDA --------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function acc_editor_depencencias($dep)
	{
		//if($this->solicitud->hilo->entorno_instanciador() )
		if(apex_pa_acceso_directo_editor)
		{
			//-[ 1 ]- RUTEO DE EVENTOS
			$msg = $this->consultar_info_dependencia($dep,"parametros_a");
			$msg = ereg_replace("\n"," -- ",$msg);
			//echo recurso::imagen_apl("eventos_ruteo.gif",true,null,null,$msg);

			//-[ 2 ]-Acceso al PLAN de ruteo de EVENTOS de la DEPENDENCIA
			//Vinculo al EDITOR del OBJETO. Hay que controlar que el objeto no se CI
			$zona = implode(apex_qs_separador,$this->id);
			//Armo la clave a enviar por el CANAL del FORM de edicion de deps
			$temp = $this->id; 
			$temp[] = $dep; 
			$ei = implode(apex_qs_separador, $temp);
			//Esta es una llamada harcodeada al FORM de propiedades de la dependencia
			$id_objeto_formulario =  151;
			$vinc_ruteo= $this->solicitud->vinculador->obtener_vinculo_a_item(
						"toba","/admin/objetos/dependencias",
						array(	apex_hilo_qs_zona => $zona,
								apex_hilo_qs_canal_obj .$id_objeto_formulario => $ei ),
						true);   
	
			if($vinc_ruteo){
	            echo $vinc_ruteo;
			}

			//-[ 3 ]- Metodo de CARGA
			$msg = $this->consultar_info_dependencia($dep,"parametros_b");
			$msg = ereg_replace("\n"," -- ",$msg);
			//echo recurso::imagen_apl("objetos/negocio.gif",true,null,null,$msg);

		}
	}

	//-------------------------------------------------------------------------------
	//---- HTML ---------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del Marco Transaccional
*/
	{
		//-[1]- Muestro la cola de mensajes
		$this->solicitud->cola_mensajes->mostrar();
		
		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n\n\n";
		$this->obtener_javascript_global_consumido();

		echo "<br>\n";
		$javascript_submit = " onSubmit='return validar_ci_".$this->nombre_formulario."(this)' ";
		echo form::abrir($this->nombre_formulario, $vinculo, $javascript_submit);
		echo "<div align='center'>\n";
		$ancho = isset($this->info_ci["ancho"]) ? $this->info_ci["ancho"] : "500";
		echo "<table width='$ancho' class='objeto-base'>\n";

		//--> Barra SUPERIOR
		echo "<tr><td>";
		$this->barra_superior();
		echo "</td></tr>\n";

		//--> Interface especifica del CI
		echo "<tr><td class='ci-cuerpo'>";
		$this->obtener_interface();
		echo "</td></tr>\n";

		//--> Pie del CI
		echo "<tr><td class='abm-zona-botones'>";
		$this->obtener_pie();
		echo "</td></tr>\n";

		echo "</table>\n";
		echo "</div>\n";
		echo form::cerrar();
		$this->obtener_javascript_validador_form();
		echo "<br>\n";
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_interface()
	{
		$existe_previo = 0;
		echo "<table class='tabla-0'  width='100%'>\n";
		foreach($this->dependencias_actual as $dep)
		{
			if($existe_previo){
				//Separador
				echo "<tr><td class='celda-vacia'><hr></td></tr>\n";
			}
			echo "<tr><td class='celda-vacia'>";
			if( $this->dependencias[$dep] instanceof objeto_ci )
			// -- CI! --
			{
				//HTML del CI
				$this->dependencias[$dep]->obtener_interface();
			}else{
				//Acceso al editor
				$this->acc_editor_depencencias($dep);
				//HTML de la DEPENDENCIA
				$this->dependencias[$dep]->obtener_html();	
			}
			echo "</td></tr>\n";
			$existe_previo = 1;
		}
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_pie()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del Marco Transaccional
*/
	{
		if($this->info_ci['ev_procesar']){
			echo form::submit($this->submit,$this->submit_etiq,"abm-input");
		}else{
			if(trim($this->info_ci['activacion_procesar'])!=""){
				$metodo = $this->info_ci['activacion_procesar'];
				if($this->$cn->$metodo()){
					echo form::submit($this->submit,$this->submit_etiq,"abm-input");
				}
			}
		}
		if($this->info_ci['ev_cancelar']){
			echo "&nbsp;" . form::button("boton", $this->cancelar_etiq ,"onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->flag_cancelar_operacion=>1),true)."';\"","abm-input");
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_javascript_global_consumido()
/*
 	@@acceso: interno
	@@desc: Genera el javascript GLOBAL que se consumen los EF. El javascript GLOBAL esta compuesto
	@@desc: por porciones de codigo reutilizadas entre distintos subelementos.
*/
	{
		$consumo_js = $this->consumo_javascript_global();
		$consumo_js = array_unique($consumo_js);
		//--> Expresion regular que machea NULOS
		if(in_array("ereg_nulo",$consumo_js)){
			echo "\n<script language='javascript'> ereg_nulo = /^\s*$/;</script>\n"; 
		}
		//--> Expresion regular que machea NUMEROS
		if(in_array("ereg_numero",$consumo_js)){
			echo "\n<script language='javascript'> ereg_numero = /^[1234567890,.-]*$/;</script>\n"; 
		}
		//--> Codigo necesario para los ef_fecha
		if(in_array("fecha",$consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("calendario_es.js")."'></SCRIPT>\n";
			echo "<SCRIPT language='javascript'>document.write(getCalendarStyles());</SCRIPT>\n";
			echo "<SCRIPT language='javascript'>var calendario = new CalendarPopup('div_calendario');calendario.showYearNavigation();calendario.showYearNavigationInput();</SCRIPT>\n";
            echo  file_get_contents("nucleo/browser/interface/validacion_fecha.inc",true);
			echo "<DIV id='div_calendario'  style='VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white'></DIV>\n";
		}
		//--> Codigo necesario para el EDITOR HTML embebido
        if (in_array("fck_editor", $consumo_js)){
			echo "\n\n<SCRIPT type='text/javascript' src='".recurso::js("fckeditor/fckeditor.js")."'></SCRIPT>\n";
        }
        //--> Codigo necesario para los EF que necesitan ventanas flotantes
        if (in_array("popup", $consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("popup.js")."'></SCRIPT>\n";        
        }
        if (in_array("financiero", $consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("financiero.js")."'></SCRIPT>\n";        
        }		
	}
	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
/*
 	@@acceso: interno
	@@desc: Javascript global requerido por los HIJOS de este CI
*/
	{
		$consumo_js = array();
		foreach($this->dependencias_actual as $dep){
			//Es un formulario?
			if(	$this->dependencias[$dep] instanceof objeto_ei_formulario ||
				$this->dependencias[$dep] instanceof objeto_ci )
			{
				$temp = $this->dependencias[$dep]->consumo_javascript_global();
				if(isset($temp)) $consumo_js = array_merge($consumo_js, $temp);
			}else{
					//echo "no es un formulario";
			}
		}
		return $consumo_js;
	}
	//-------------------------------------------------------------------------------
	
	function obtener_javascript_validador_form()
/*
 	@@acceso: interno
	@@desc: Javascript asociado al SUBMIT del FROM
*/
	{
		//-[2]- Incluyo el JAVASCRIPT de CONTROLA el FORM
		echo "\n<script language='javascript'>\n";
		echo "//----------- Funcion VALIDADORA del FORM ----------\n";
		echo "function validar_ci_{$this->nombre_formulario}(formulario){\n";
//		echo "alert(\"estoy aca!!\");return false;\n";
		$this->obtener_javascript();
		echo "\n\nreturn true;\n";//Todo OK, salgo de la validacion del formulario
		echo "}\n</script>\n\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript()
	//Javascript que los HIJOS incorporan en la validacion del formulario
	{
		foreach($this->dependencias_actual as $dep)
		{
			if(	$this->dependencias[$dep] instanceof objeto_ei_formulario ||
				$this->dependencias[$dep] instanceof objeto_ci )
			{
				echo $this->dependencias[$dep]->obtener_javascript();
			}
		}
	}
	//-------------------------------------------------------------------------------
}
?>
