------------------------------------------------------------
--[/admin/items/vinculos]--  ITEM - Vinculos 
------------------------------------------------------------
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, creacion) VALUES ('305', 'admin', '/admin/items/vinculos', '9', 'admin', '/items', '0', '0', 'browser', 'toba', 'titulo', 'ITEM - Vinculos', 'ITEMs a los que se puede navegar', 'toba', '0', 'toba', 'especifico', 'acciones/items/vinculos.php', '1', '12', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'zona_item', '12', '1', 'apex', 'vinculos.gif', NULL, NULL, NULL, NULL, NULL, NULL, '2003-09-29 22:24:08');
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES ('305', 'admin', '/admin/items/vinculos', '154', '1', NULL);
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES ('305', 'admin', '/admin/items/vinculos', '155', '1', NULL);
