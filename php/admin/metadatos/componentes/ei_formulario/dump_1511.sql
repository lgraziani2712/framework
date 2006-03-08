------------------------------------------------------------
--[1511]--  OBJETO - DBR - Prop. basicas 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '1511', NULL, NULL, 'toba', 'objeto_ei_formulario', 'eiform_ap', 'admin/objetos_toba/db_tablas/eiform_ap.php', NULL, NULL, 'OBJETO - DBR - Prop. basicas', 'Administrador de Persistencia PREDETERMINADO', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-07-26 23:56:28');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug) VALUES ('toba', '88', '1511', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba', '1511', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1511', '1258', 'ap', 'ef_combo_db', 'ap', NULL, 'sql: SELECT ap, descripcion FROM apex_admin_persistencia
WHERE categoria = \'R\';', '2', 'AP por defecto', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1511', '1259', 'ap_archivo', 'ef_popup', 'ap_archivo', NULL, 'tamano: 60;
maximo: 80;
item_destino: /admin/objetos_toba/selector_archivo,toba;
ventana: 400,400,yes;', '4', 'AP - Archivo', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1511', '1260', 'ap_clase', 'ef_editable', 'ap_clase', NULL, 'tamano: 40;
maximo: 80;', '3', 'AP - Clase', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1511', '1442', 'clave', 'ef_editable', 'clave', NULL, 'tamano: 40;
maximo: 60;', '1', 'Clave', NULL, 'Componentes asociativos de la clave del elemento.', NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1511', '4413', 'debug', 'ef_checkbox', 'debug', NULL, 'valor: 1;
valor_no_seteado: 0;
estado: 0;', '5', 'Modo Debug', NULL, 'En el modo debug el objeto muestra un esquema de las tablas al inicio del pedido de p�gina y otro al final. En caso de querer hacer un dump puntual usar el m�todo [api:Objetos/Persistencia/objeto_datos_relacion dump_esquema].<br><br>
La salida es en formato SVG, ver requisitos del [wiki:Referencia/Objetos/ei_esquema objeto_ei_esquema] encargado de construirlo.', NULL, NULL, NULL, '0');
