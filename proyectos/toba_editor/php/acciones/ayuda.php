<?php

	function cuadro_ayuda($titulo, $iconos)
	{
		foreach ($iconos as $icono) {
			echo "<li>";
			if (count($icono['url']) == 1) {
				foreach ($icono['url'] as $ayuda => $url) {
					echo "<a href='$url' target='{$icono['frame']}'>";
					echo toba_recurso::imagen_toba($icono['img'], true, null, null, $ayuda);
					echo "</a>";
				}
			} else {
				$ayuda = "<ul>";
				foreach ($icono['url'] as $desc => $url) {
					$ayuda .= "<li><a href=$url target={$icono['frame']}>$desc";
					$ayuda .= "</a></li>";
				}
				$ayuda .= "</ul>";
				echo toba_recurso::imagen_toba($icono['img'], true, null, null, $ayuda);
			}
			echo "</li>\n";
		}
	}
	
require_once('modelo/instalacion.php');
require_once('admin_util.php');

$url_trac = get_url_desarrollos();
$url_login = $url_trac.'/trac/toba/login';
$url_referencia = toba_recurso::url_proyecto('toba_referencia');


echo "
	<style type='text/css'>
		a {
			text-decoration:none;	
		}
		a:hover {
			text-decoration:underline;
		}
		ul { text-align: left; margin-left:30%;}
		h2 {
		    background-color: #890C71;
		    color: white;
		    padding-left: 10px;
		    margin-top: 20px;
		    border: 1px solid gray;
			font-size: 16px;
		}		
		h2 div {
			font-size: 12px;
		}
		li {  
			list-style-type:none;	
			margin-top: 10px;
			font-size: 12px;
		}
		li img {
			vertical-align: middle;
			border: 0;
		}
	</style>
";




/**
 * OFFLINE
 */

echo "
<h2>Offline<div>Contenido correspondiente a esta versión</div></h2>
		<ul>
			<li>
				<a href='".toba_recurso::url_proyecto()."/doc/wiki/trac/toba/wiki.html' title='Lugar central de la documentación'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/wiki-small.png'>
						Wiki</a>
			</li>
			<li>
				<a href='".toba_recurso::url_proyecto()."/doc/api/index.html' title='Documentación de referencia de la API PHP disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/php-small.png'>
						API PHP</a>
				
			</li>
			<li>
				<a href='".toba_recurso::url_proyecto()."/doc/api_js/index.html' title='Documentación de referencia de la API Javascript disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/javascript-small.png'>
						API Javascript</a>
			</li>
			<li>
				<a href='$url_referencia/?ai=toba_referencia||1000073' title='Tutoriales con explicaciones guiadas y videos introductorios'>
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>
						Tutorial</a>
			</li>			
			<li>
				<a href='$url_referencia/'>
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>			
						Proyecto Referencia</a>
			</li>
		</ul>";

/**
 * ONLINE
 */
echo "
<h2>Online<div>Contenido más actualizado</div></h2>";


	
echo "
		<ul>
			<li>
				<a href='$url_trac/trac/toba/newticket*$url_login' title='Lugar central de la documentación'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/wiki-small.png'>
						Reportar un bug o mejora</a>
			</li>					
			<li>
				<a href='$url_trac/trac/toba/wiki*$url_login' title='Lugar central de la documentación'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/wiki-small.png'>
						Wiki</a>
			</li>
			<li>
				<a href='$url_trac/toba_editor_trunk/doc/api/index.html' title='Documentación de referencia de la API PHP disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/php-small.png'>
						API PHP</a>
				
			</li>
			<li>
				<a href='$url_trac/toba_editor_trunk/doc/api_js/index.html' title='Documentación de referencia de la API Javascript disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/javascript-small.png'>
						API Javascript</a>
			</li>
			<li>
				<a href='$url_trac/toba_referencia_trunk/?ai=toba_referencia||1000073' title='Tutoriales con explicaciones guiadas y videos introductorios'>			
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>
					Tutorial</a>
			</li>			
			<li>
				<a href='$url_trac/toba_referencia_trunk/'>
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>			
					Proyecto Referencia</a>
			</li>";

	//--- LINKS
	$online = array();
	$online[] = array('url' => array("Especificación 4.01 (esp)" => "http://html.conclase.net/w3c/html401-es/cover.html#minitoc"
								),
					'img' => 'admin/botones/html80.png', 'frame' => 'html');
	
	$online[] = array('url' => array("Manual Introductorio (esp)" => 'http://www.sidar.org/recur/desdi/mcss/manual/indice.php',
										"Especificación 2.0 (esp)" => 'http://www.sidar.org/recur/desdi/traduc/es/css/cover.html',
										"Compatibilidad entre navegadores (eng)" => 'http://www.westciv.com/style_master/academy/browser_support/index.html',
										"Tutoriales (eng)" => 'http://css.maxdesign.com.au/',
										"Posicionamiento (eng)" => 'http://www.brainjar.com/css/positioning/default.asp',
										"Soporte CSS en Email (eng)" => 'http://www.campaignmonitor.com/blog/archives/2006/03/a_guide_to_css_1.html',
								),
					'img' => 'admin/botones/css80.png', 'frame' => 'html');
					
	$online[] = array('url' => array('Manual (esp)' => 'http://www.php.net/manual/es/',
								),
					'img' => 'admin/botones/php80.png', 'frame' => 'php');
					
	$online[] = array('url' => array('Manual 8.1 (eng)' => 'http://www.postgresql.org/docs/8.1/interactive/index.html',
									),
					'img' => 'admin/botones/postgres80x15.gif', 'frame' => 'postgres');
					
	$online[] = array('url' => array("Manual 2.2 (esp)" => 'http://httpd.apache.org/docs/2.2/es/',
								),
					'img' => 'admin/botones/apache80.png', 'frame' => 'apache');
					
	$online[] = array('url' => array("Libro (eng)" => 'http://svnbook.red-bean.com/nightly/en/index.html',
								),
					'img' => 'admin/botones/svn80.png', 'frame' => 'svn');
					
					
	cuadro_ayuda("Otros", $online);
	
	echo "</ul>";
?>