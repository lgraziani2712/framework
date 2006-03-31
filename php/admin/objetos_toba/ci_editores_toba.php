<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/admin_util.php');

abstract class ci_editores_toba extends objeto_ci
{
	protected $id_objeto;
	protected $cambio_objeto;
	protected $cargado = false;
	protected $etapa_particular;
	private $falla_carga = false;
	private $elemento_eliminado = false;

	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona		
		$zona = toba::get_solicitud()->zona();
		if ($editable = $zona->obtener_editable_propagado()){
			$zona->cargar_editable(); 
			list($proyecto, $objeto) = $editable;
		}	
		//Se notifica un objeto y un proyecto	
		if (isset($objeto) && isset($proyecto)) {
			//Se determina si es un nuevo objeto
			$es_nuevo = (!isset($this->id_objeto) || 
						($this->id_objeto['proyecto'] != $proyecto || $this->id_objeto['objeto'] != $objeto));
			if ($es_nuevo) {
				$this->set_objeto( 	array('proyecto'=>$proyecto, 'objeto'=>$objeto) );
				$this->cambio_objeto = true;
			} else {
				$this->cambio_objeto = false;	
			}
		}
		$this->etapa_particular = toba::get_hilo()->obtener_parametro('etapa');
		//Necesito cargar la entidad antes de mostrar la pantalla
	}
	
	function get_entidad()
	//Acceso al DATOS_RELACION
	{
		if($this->cambio_objeto && !$this->falla_carga){
			toba::get_logger()->debug($this->get_txt() . '*** se cargo la relacion: ' . $this->id_objeto['objeto']); 	
			if( $this->dependencia('datos')->cargar( $this->id_objeto ) ){
				$this->cargado = true;
				$this->cambio_objeto = false;//Sino sigue entrando aca por cada vez que se solicita la entidad
			}else{
				$this->falla_carga = true;	
			}
		}		
		return $this->dependencia('datos');
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		$propiedades[] = "cargado";
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = 	$id;
	}
	
	function obtener_html()
	{
		if($this->falla_carga === true){
			echo ei_mensaje("El elemento seleccionado no existe.","error");
			return;
		}
		if($this->elemento_eliminado){
			echo ei_mensaje("El elemento ha sido eliminado.");
			return;
		}
		
		parent::obtener_html();
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if(!$this->cargado){
			unset($eventos['eliminar']);
		}
		return $eventos;
	}

	function get_etapa_actual()
	{
		if (isset($this->etapa_particular)) {
			return $this->etapa_particular;	
		} else {
			return parent::get_etapa_actual();	
		}
	}

	function evt__eliminar()
	{
		$this->get_entidad()->eliminar();
		$this->elemento_eliminado = true;
		$zona = toba::get_solicitud()->zona();
		$zona->refrescar_listado_editable_apex();
		$zona->resetear();
		admin_util::refrescar_editor_item();
	}
	

	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		if (!$this->cargado) {
			//Seteo los datos asociados al uso de este editor
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", $this->get_clase_actual() );
		}
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();
	}
	// *******************************************************************
	
	//---------------------------------------------------------------
	//-------------------------- Consultas --------------------------
	//---------------------------------------------------------------

	function get_clase_actual()
	{
		if (isset($this->clase_actual)) {
			return $this->clase_actual;
		} else {
			throw new excepcion_toba("El editor actual no tiene definida sobre que clase de objeto trabaja");
		}
	}
		
	
}
?>