<?php
/**
 * Consultas transversales sobre componentes
 * 
 * @package Componentes
 */
class toba_catalogo
{
	/**
	*	Lista de componentes
	*/
	static function get_lista_tipo_componentes()
	{
		$datos[] = 'item';
		$datos[] = 'ci';
		$datos[] = 'ei_arbol';
		$datos[] = 'ei_archivos';
		$datos[] = 'ei_calendario';
		$datos[] = 'ei_filtro';
		$datos[] = 'ei_formulario';
		$datos[] = 'ei_formulario_ml';
		$datos[] = 'ei_cuadro';
		$datos[] = 'ei_esquema';
		$datos[] = 'cn';
		$datos[] = 'datos_tabla';
		$datos[] = 'datos_relacion';
		return $datos;
	}

	/**
	*	Se agregan los transversales para el DUMP
	*/
	static function get_lista_tipo_componentes_dump()
	{
		return self::get_lista_tipo_componentes();
	}

	static function get_tipo( $componente )
	{
		$sql = " 	SELECT clase
					FROM apex_objeto
					WHERE (objeto = '{$componente['componente']}')
					AND (proyecto = '{$componente['proyecto']}')";
		$datos = toba::instancia()->get_db()->consultar($sql);
		return $datos[0]['clase'];
	}

	/**
	*	La DB como parametro se pasa en el caso de que esto se utilice desde la consola
	*/
	static function get_lista_componentes( $tipo_componente, $proyecto, $db = null )
	{
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = toba::instancia()->get_db();	
		}
		if ($tipo_componente == 'item' ) {
			$sql = "SELECT 	proyecto as 		proyecto,
							item as 			componente
					FROM apex_item 
					WHERE proyecto = '$proyecto'
					ORDER BY 1;";
			$datos = $db->consultar( $sql );
		} else {
			$tipo_componente = 'objeto_'.$tipo_componente;
			$sql = "SELECT 	proyecto as 		proyecto,
							objeto as 			componente
					FROM apex_objeto 
					WHERE proyecto = '$proyecto'
					AND clase = '$tipo_componente'
					ORDER BY 1;";
			$datos = $db->consultar( $sql );
		}
		return $datos;
	}

	static function control_clave_valida( $clave_componente )
	{
		if(! is_array($clave_componente) 
			|| !isset($clave_componente['componente']) 
			|| !isset($clave_componente['proyecto']) ) {
			throw new toba_error("La clave utilizada para invocar el componente no es valida: ".var_export($clave_componente, true));	
		}
	}
}
?>