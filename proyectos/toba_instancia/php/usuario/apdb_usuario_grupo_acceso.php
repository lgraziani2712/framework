<?
require_once("nucleo/componentes/persistencia/toba_ap_tabla_db_s.php");

class apdb_dependencias extends toba_ap_tabla_db_s
{
	function inicializar()
	{
		$this->activar_proceso_carga_externa_dao(	"get_info_dependencia",
													"dao_editores",
													"modelo/consultas/dao_editores.php", 
													array('proyecto','objeto_proveedor'), 
													array('clase','nombre_objeto','descripcion') );
	}
}	
?>