<?php

class toba_mc_comp__1712
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1712,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_cuadro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Cuadro Cortes Estandar 2',
    'titulo' => 'Localidades de Santa Fe',
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
    'solicitud_registrar' => NULL,
    'solicitud_obj_obs_tipo' => NULL,
    'solicitud_obj_observacion' => NULL,
    'parametro_a' => NULL,
    'parametro_b' => NULL,
    'parametro_c' => NULL,
    'parametro_d' => NULL,
    'parametro_e' => NULL,
    'parametro_f' => NULL,
    'usuario' => NULL,
    'creacion' => '2005-11-08 23:29:00',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_icono' => 'objetos/cuadro_array.gif',
    'clase_descripcion_corta' => 'ei_cuadro',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1843',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '0',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_cuadro' => 
  array (
    'titulo' => NULL,
    'subtitulo' => NULL,
    'sql' => NULL,
    'columnas_clave' => 'zona_id, dep_id, loc_id',
    'clave_datos_tabla' => NULL,
    'archivos_callbacks' => NULL,
    'ancho' => '100%',
    'ordenar' => NULL,
    'exportar_xls' => NULL,
    'exportar_pdf' => NULL,
    'paginar' => NULL,
    'tamano_pagina' => NULL,
    'tipo_paginado' => NULL,
    'scroll' => NULL,
    'alto' => NULL,
    'eof_invisible' => NULL,
    'eof_customizado' => NULL,
    'pdf_respetar_paginacion' => NULL,
    'pdf_propiedades' => NULL,
    'asociacion_columnas' => NULL,
    'dao_nucleo_proyecto' => NULL,
    'dao_clase' => NULL,
    'dao_metodo' => NULL,
    'dao_parametros' => NULL,
    'dao_archivo' => '',
    'cc_modo' => 't',
    'cc_modo_anidado_colap' => 0,
    'cc_modo_anidado_totcol' => NULL,
    'cc_modo_anidado_totcua' => NULL,
  ),
  '_info_cuadro_columna' => 
  array (
    0 => 
    array (
      'orden' => '1',
      'titulo' => 'Localidad',
      'estilo_titulo' => 'ei-cuadro-col-tit',
      'estilo' => 'col-tex-p2',
      'ancho' => NULL,
      'clave' => 'localidad',
      'formateo' => 'indivisible',
      'vinculo_indice' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'total_cc' => '',
    ),
    1 => 
    array (
      'orden' => '2',
      'titulo' => 'Hab. Varones',
      'estilo_titulo' => 'ei-cuadro-col-tit',
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'hab_varones',
      'formateo' => 'millares',
      'vinculo_indice' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'total_cc' => '',
    ),
    2 => 
    array (
      'orden' => '3',
      'titulo' => 'Hab. Mujeres',
      'estilo_titulo' => 'ei-cuadro-col-tit',
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'hab_mujeres',
      'formateo' => 'millares',
      'vinculo_indice' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'total_cc' => '',
    ),
    3 => 
    array (
      'orden' => '4',
      'titulo' => 'Hab. Total',
      'estilo_titulo' => 'ei-cuadro-col-tit',
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'hab_total',
      'formateo' => 'millares',
      'vinculo_indice' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'total_cc' => '',
    ),
    4 => 
    array (
      'orden' => '5',
      'titulo' => 'Superficie',
      'estilo_titulo' => 'ei-cuadro-col-tit',
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'superficie',
      'formateo' => 'superficie',
      'vinculo_indice' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'total_cc' => '',
    ),
  ),
  '_info_cuadro_cortes' => 
  array (
    0 => 
    array (
      'orden' => '1',
      'columnas_id' => 'zona_id',
      'columnas_descripcion' => 'zona',
      'identificador' => 'zona',
      'pie_contar_filas' => '0',
      'pie_mostrar_titular' => NULL,
      'pie_mostrar_titulos' => 0,
      'imp_paginar' => NULL,
      'descripcion' => 'Zona',
    ),
  ),
);
	}

}

?>