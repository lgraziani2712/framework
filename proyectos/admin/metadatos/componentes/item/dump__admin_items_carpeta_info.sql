------------------------------------------------------------
--[/admin/items/carpeta_info]--  CARPETA - Info 
------------------------------------------------------------
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, creacion) VALUES ('127', 'admin', '/admin/items/carpeta_info', '9', 'admin', '/items', '0', '0', 'browser', 'toba', 'titulo', 'CARPETA - Info', 'Ayuda sobre la CARPETA', 'toba', '0', 'toba', 'especifico', 'acciones/items/info.php', '1', '22', NULL, 'toba', 'item_datos', NULL, NULL, NULL, 'admin', 'zona_carpeta', '1', '1', 'apex', 'info_editor.gif', NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-02 03:43:07');
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES ('127', 'admin', '/admin/items/carpeta_info', '147', '0', NULL);
