------------------------------------------------------------
--[45000004]--  Restricciones Funcionales - restricciones 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 45
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_usuarios', --proyecto
	'45000004', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Restricciones Funcionales - restricciones', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_usuarios', --fuente_datos_proyecto
	'toba_usuarios', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2010-03-08 18:58:41', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 45

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, ap, ap_clase, ap_archivo, tabla, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica) VALUES (
	'toba_usuarios', --objeto_proyecto
	'45000004', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_restriccion_funcional', --tabla
	NULL, --alias
	'0', --modificar_claves
	'toba_usuarios', --fuente_datos_proyecto
	'toba_usuarios', --fuente_datos
	'1'  --permite_actualizacion_automatica
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 45
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_usuarios', --objeto_proyecto
	'45000004', --objeto
	'45000001', --col_id
	'descripcion', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'255', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_usuarios', --objeto_proyecto
	'45000004', --objeto
	'45000002', --col_id
	'proyecto', --columna
	'C', --tipo
	'1', --pk
	'', --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_usuarios', --objeto_proyecto
	'45000004', --objeto
	'45000003', --col_id
	'restriccion_funcional', --columna
	'E', --tipo
	'1', --pk
	'\"apex_restriccion_funcional_seq\"', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
--- FIN Grupo de desarrollo 45