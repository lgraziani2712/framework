------------------------------------------------------------
--[1744]--  OBJETO - General - Eventos (Form) 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '1744', NULL, NULL, 'toba', 'objeto_ei_formulario_ml', 'eiform_abm_detalle', 'admin/objetos_toba/eiform_abm_detalle.php', NULL, NULL, 'OBJETO - General - Eventos (Form)', NULL, NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-11-22 10:25:29');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('toba', '282', '1744', 'seleccion', NULL, '1', '1', '', NULL, 'apex', 'doc.gif', '0', 'Seleccionar la fila', '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('toba', '281', '1744', 'modificacion', 'Modificacion', '1', '0', NULL, NULL, NULL, NULL, '0', NULL, '2', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba', '1744', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '500', NULL, NULL, NULL, NULL, '1', '1', NULL, '1', 'orden', NULL, '1', NULL, 'LINEA');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4408', 'etiqueta', 'ef_editable', 'etiqueta', NULL, 'tamano: 20;
maximo: 40;', '2', 'Etiqueta', NULL, NULL, NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4409', 'identificador', 'ef_editable', 'identificador', '1', 'tamano: 15;
maximo: 40;', '1', 'Identificador', NULL, NULL, NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4410', 'imagen', 'ef_editable', 'imagen', NULL, 'tamano: 20;
maximo: 60;', '4', 'Imagen', NULL, NULL, NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4411', 'imagen_recurso', 'ef_combo_db', 'imagen_recurso_origen', NULL, 'no_seteado: Ninguno;
sql: SELECT recurso_origen, descripcion FROM apex_recurso_origen ORDER BY descripcion;', '3', 'Imagen - origen', NULL, NULL, NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4412', 'implicito', 'ef_checkbox', 'implicito', NULL, 'valor: 1;
valor_no_seteado: 0;
estado: 0;', '6', 'Impl.', NULL, 'Si un evento es implicito se dispara automaticamente.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4413', 'en_botonera', 'ef_checkbox', 'en_botonera', NULL, 'valor: 1;
valor_no_seteado: 0;', '5', 'en Bot.', NULL, 'Se genera un boton que dispara este evento.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1744', '4415', 'maneja_datos', 'ef_checkbox', 'maneja_datos', NULL, 'valor: 1;
valor_no_seteado: 0;', '7', 'Man. Datos', NULL, 'Si un evento maneja datos realiza validaciones de lo editado y acarrea estos datos como parametros del evento.', NULL, NULL, NULL, '0');
