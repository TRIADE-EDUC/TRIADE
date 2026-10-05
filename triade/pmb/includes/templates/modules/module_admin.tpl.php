<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_admin.tpl.php,v 1.4 2019/05/27 09:43:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $module_admin_left_menu, $module_admin_misc_files_content, $msg, $charset; 

$module_admin_left_menu = "
";

$module_admin_misc_files_content = "
<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
<script src='./javascript/ace/theme-eclipse.js' type='text/javascript' charset='utf-8'></script>
<script src='./javascript/ace/mode-twig.js' type='text/javascript' charset='utf-8'></script>
<h3 class='section-title'>".$msg['subst_files_management']."</h3>
<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	
	<div data-dojo-type='apps/misc/files/FilesUI' data-dojo-props='direction: \"vertical\",startExpanded: true, region:\"leading\"' style='width:20%;'></div>
	
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true, region:\"center\"' style='height:100%;width:auto;'>
		
		<div data-dojo-type='dijit/layout/TabContainer' data-dojo-props='splitter:true, region:\"center\"' style='width:auto;height:95%'>
			<div data-dojo-type='apps/misc/files/FileUI' title='".htmlentities($msg['file_setting'], ENT_QUOTES, $charset)."' data-dojo-props='splitter:true' ></div>
			<div data-dojo-type='apps/misc/files/FileContentUI' title='".htmlentities($msg['file_source_code'], ENT_QUOTES, $charset)."' data-dojo-props='splitter:true' ></div>
		</div>
		<div data-dojo-type='apps/misc/files/SubstFileContentUI' style='width:auto;height:25%' data-dojo-props='direction: \"horizontal\", startExpanded: false, splitter:true, region:\"bottom\"' title='Contenu'></div>
	</div>

</div>";

?>