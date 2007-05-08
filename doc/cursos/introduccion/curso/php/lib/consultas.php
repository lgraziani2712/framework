<?php

class constultas
{
	function get_instituciones($filtro=null)
	{
		$sql = "SELECT 	institucion as 			institucion, 
						nombre_abreviado as		nombre,
						sigla as				sigla
				FROM soe_instituciones
				ORDER by 2";
		return consultar_fuente($sql);
	}

	function get_sedes($filtro=null)
	{
		$sql = "SELECT 	institucion as 			institucion, 
						sede as					sede,
						nombre as				nombre
				FROM soe_sedes
				ORDER by 2";
		return consultar_fuente($sql);
	}

	function get_unidadacad($institucion)
	{
		$sql = "SELECT 	unidadacad as			unidadacad, 
						nombre as				nombre
				FROM soe_unidadesacad
				ORDER by 2";
		return consultar_fuente($sql);
	}

	function get_jurisdicciones()
	{
		$sql = "SELECT 	jurisdiccion as			id, 
						descripcion as			nombre
				FROM soe_jurisdicciones
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	function get_paises()
	{
		$sql = "SELECT 	idpais as 				id, 
						nombre as				nombre
				FROM ona_pais 
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	function get_provincias($pais)
	{
		$sql = "SELECT 	idprovincia as 			id,
						nombre as				nombre 
				FROM ona_provincia
				WHERE idpais = '$pais'
				ORDER by 2";
		return consultar_fuente($sql);
	}

	function get_localidades($provincia)
	{
		$sql = "SELECT 	codigopostal as 		id,
						nombre as				nombre
				FROM ona_localidad
				WHERE idprovincia = '$provincia'
				ORDER by 2";
		return consultar_fuente($sql);
	}
}
?>