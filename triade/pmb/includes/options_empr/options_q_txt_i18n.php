<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_q_txt_i18n.php,v 1.8 2020/07/02 13:30:29 qvarin Exp $

//Gestion des options de type q_txt_i18n
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
$base_use_dojo=1;
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php");

if(!isset($first)) $first = '';

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param['FOR'] = 'q_txt_i18n';
	$param['SIZE'][0]['value'] = stripslashes(intval($SIZE));
	$param['MAXSIZE'][0]['value'] = stripslashes(intval($MAXSIZE));
	$param['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
	$param['ISHTML'][0]['value'] = $ISHTML ? 1 : 0;
	$param['NUM_AUTO'][0]['value'] = ($NUM_AUTO=='yes' ? 'yes' : 'no');
	$param['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
	$param['UNSELECT_ITEM'][0]['value']="<![CDATA[".stripslashes($UNSELECT_ITEM_LIB)."]]>";
	$param['DEFAULT_VALUE'][0]['value']=stripslashes($DEFAULT_VALUE);
	$param['TYPE'][0]['value'] = empty($TYPE) ? "text" : stripslashes($TYPE);
	
	if ($param['TYPE'][0]['value'] == "textarea") {
	    $param['MAXSIZE'][0]['value'] = 65535;
	    $param['SIZE'][0]['value'] = 255;
	}
	
	
	if($idchamp) {
		$merge_items = array();
		$values = array();
		$nb= 0;
		for($i=0; $i<count($ITEMS);$i++){
			if(in_array($ITEMS[$i]['value'], $values)) {
				echo "<script>
						alert('".$ITEMS[$i]['value']." - ".$ITEMS[$i]['label'].". ".$msg["parperso_valeur_existe_liste"]."');
						history.go(-1);
					</script>";
				exit();
			} else {
				if($ITEMS[$i]['value'] && $ITEMS[$i]['label']){
					$merge_items[$nb]['value'] = $ITEMS[$i]['value'];
					$merge_items[$nb]['label'] = stripslashes($ITEMS[$i]['label']);
					$merge_items[$nb]['order'] = ($ITEMS[$i]['order'] ? $ITEMS[$i]['order'] : 0);
					$values[] = $ITEMS[$i]['value'];
					$nb++;
				}	
			}
		}
		$requete="delete from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$idchamp;
		pmb_mysql_query($requete);
		$requete="SELECT datatype FROM ".$_custom_prefixe_."_custom WHERE idchamp = $idchamp";
		$resultat = pmb_mysql_query($requete);
		$dtype = pmb_mysql_result($resultat,0,0);
		for ($i=0; $i<count($merge_items); $i++) {
			$requete="insert into ".$_custom_prefixe_."_custom_lists (".$_custom_prefixe_."_custom_champ, ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre) values($idchamp, '".$merge_items[$i]['value']."','".$merge_items[$i]['label']."','".$merge_items[$i]['order']."')";
			pmb_mysql_query($requete);
		}
	}
	$param['DEFAULT_LANG'][0]['value']=stripslashes($DEFAULT_LANG);

	$options = array_to_xml($param, "OPTIONS");
	?> 
	<script>
	opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
	opener.document.formulaire.<?php  echo $name; ?>_for.value="q_txt_i18n";
	self.close();
	</script>
<?php
 } else {
 	print "<h3>".$msg['procs_options_param'].$name."</h3><hr />";
 	$items = array();
 	if(!$first) {
 		if (!$options) $options = "<OPTIONS></OPTIONS>";
 		$param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
 		if (!isset($param["FOR"]) || $param["FOR"] != "q_txt_i18n") {
 			$param = array();
 			$param["FOR"] = "q_txt_i18n";
 			$param['SIZE'][0]['value'] = '50';
 			$param['MAXSIZE'][0]['value'] = '255';
 			$param['REPEATABLE'][0]['value'] = '';
 			$param['ISHTML'][0]['value'] = '';
 			$param['NUM_AUTO'][0]['value'] = '';
 			$param['UNSELECT_ITEM'][0]['VALUE']='';
 			$param['UNSELECT_ITEM'][0]['value']='';
 			$param['DEFAULT_VALUE'][0]['value']='';
 			$param['DEFAULT_LANG'][0]['value']='';
 			$param['TYPE'][0]['value'] = "text";
 		}
 		//Récupération des valeurs de la liste
 		if ($idchamp) {
 			$requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=$idchamp order by ordre";
 			$resultat=pmb_mysql_query($requete);
 			if (pmb_mysql_num_rows($resultat)) {
 				$i=0;
 				while (($r=pmb_mysql_fetch_array($resultat))) {
					$items[$i]['value'] = $r[$_custom_prefixe_."_custom_list_value"];
					$items[$i]['label'] = $r[$_custom_prefixe_."_custom_list_lib"];
					$items[$i]['order'] = $r["ordre"];
 					$i++;
 				}
 			}
 		}
 	} else {
 		$param['FOR'] = "q_txt_i18n";
 		$param['SIZE'][0]['value'] = stripslashes($SIZE*1);
 		$param['MAXSIZE'][0]['value'] = stripslashes($MAXSIZE*1);
 		$param['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
 		$param['ISHTML'][0]['value'] = $ISHTML ? 1 : 0;
 		$param['NUM_AUTO'][0]['value'] = ($NUM_AUTO=='yes' ? 'yes' : 'no');
 		$param['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
 		$param['UNSELECT_ITEM'][0]['value']=stripslashes($UNSELECT_ITEM_LIB);
 		$param['DEFAULT_VALUE'][0]['value']=stripslashes($DEFAULT_VALUE);
 		$param['DEFAULT_LANG'][0]['value']=stripslashes($DEFAULT_LANG);
 		$param['TYPE'][0]['value'] = empty($TYPE) ? "text" : stripslashes($TYPE);
 		$options = array_to_xml($param, "OPTIONS");
 		
 		if($first == 2) {
 			for($i=0; $i<count($ITEMS);$i++){
 				if(count($checked)==0 || (count($checked)>0 && !in_array($ITEMS[$i]['value'],$checked))){
 					if($ITEMS[$i]['value'] && $ITEMS[$i]['label']){
 						$array= array(
 								'value' => $ITEMS[$i]['value'],
 								'label' => $ITEMS[$i]['label'],
 								'order' => ($ITEMS[$i]['order'] ? $ITEMS[$i]['order'] : 0)
 						);
 						$items[]=$array;
 					}
 				}
 			}
 		}
 		if($first == 3) {
 			//Tri des options
			$opts = array();
			for($i=0; $i<count($ITEMS);$i++){
			    $opts[$i] = convert_diacrit($ITEMS[$i]['label']);
 			}
 			asort($opts);
 			foreach ($opts as $i=>$opt) {
 				$array= array(
 						'value' => $ITEMS[$i]['value'],
 						'label' => $ITEMS[$i]['label'],
 						'order' => $i
 				);
 				$items[]=$array;
 			}
 		}
 	}
 	if (!isset($langue_doc) || !count($langue_doc)) {
 		$langue_doc = new marc_list('lang');
 		$langue_doc = $langue_doc->table;
 	}
 	if (empty($param['TYPE'][0]['value'])) {
 	    $param['TYPE'][0]['value'] = "text";
 	}
	//Formulaire
	?> 
	
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_q_txt_i18n.php" method="post">
	<h3><?php  echo $type_list_empr[$type]; ?></h3>
	<div class='form-contenu'>
	<input type="hidden" name="first" value="1">
	<input type="hidden" name="_custom_prefixe_" value="<?php echo $_custom_prefixe_;?>">
	<input type="hidden" name="type" value="<?php echo $type; ?>">
	<input type="hidden" name="idchamp" value="<?php echo $idchamp; ?>">
	<input type="hidden" name="name" value="<?php  echo htmlentities($name, ENT_QUOTES, $charset);
	?>">
	<table class='table-no-border' width=100%>
    	<tr>
    		<td><?= $msg["procs_options_text_type"]; ?></td>
    		<td>
    			<input type="radio" id="text" name="TYPE" value="text" onChange="changeType(this)" <?= ($param['TYPE'][0]['value'] == "text" ? "checked" : "") ?>>
    			<label for="text"><?= $msg["procs_options_text_type_text"]; ?></label>
        		<input type="radio" id="textarea" name="TYPE" value="textarea" onChange="changeType(this)" <?= ($param['TYPE'][0]['value'] == "textarea" ? "checked" : "") ?>>
        		<label for="textarea"><?= $msg["procs_options_text_type_textarea"]; ?></label>
    		</td>
    	</tr>
		<tr class="paramType" <?= ($param['TYPE'][0]['value'] == 'text' ? '' : 'style="visibility:collapse;"') ?>>
			<td><?php  echo $msg["procs_options_text_taille"];?> </td>
			<td>
				<input class='saisie-10em' id="size" type="text" name="SIZE" value="<?php  echo htmlentities($param['SIZE'][0]['value'],ENT_QUOTES,$charset);?>" <?= ($param['TYPE'][0]['value'] == 'textarea' ? 'readonly' : '') ?>>
			</td>
		</tr>
		<tr class="paramType" <?= ($param['TYPE'][0]['value'] == 'text' ? '' : 'style="visibility:collapse;"') ?>>
			<td><?php  echo $msg["procs_options_text_max"]."<br /><span style='font-size: 0.8em'>".$msg['procs_options_text_max_help']."</span>";?> </td>
			<td>
				<input type="text" id="maxsize" class='saisie-10em' name="MAXSIZE" value="<?php  echo htmlentities($param['MAXSIZE'][0]['value'],ENT_QUOTES,$charset);?>" <?= ($param['TYPE'][0]['value'] == 'textarea' ? 'readonly' : '') ?>>
			</td>
		</tr>
		<tr>
			<td><?php  echo $msg["persofield_textrepeat"];?> </td>
			<td>
				<input type="checkbox" name="REPEATABLE" <?php  echo $param['REPEATABLE'][0]['value'] ? ' checked ' : "";?>>
			</td>
		</tr>
		<tr>
			<td><?php  echo $msg["persofield_textishtml"];?> </td>
			<td>
				<input type="checkbox" name="ISHTML" <?php  echo $param['ISHTML'][0]['value'] ? ' checked ' : "";?>>
			</td>
		</tr>
	</table>
	<h3><?php echo $msg["procs_options_qualification_options"];?></h3>
	<table class='table-no-border' width=100%>
		<tr><td><?php echo $msg["num_auto_list"]; 
		?></td><td><input type="checkbox" value="yes" id="NUM_AUTO" name="NUM_AUTO" <?php if ($param['NUM_AUTO'][0]['value']=="yes") echo "checked"; ?>/>
		</td></tr>
		<tr><td><?php echo $msg["procs_options_choix_vide"]; 
		?></td><td><?php echo $msg["procs_options_value"]; ?> : <input type="text" size="5" name="UNSELECT_ITEM_VALUE" value="<?php echo htmlentities($param['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset); ?>">&nbsp;<?php echo $msg["procs_options_label"]; ?> : <input type="text" name="UNSELECT_ITEM_LIB" value="<?php echo htmlentities($param['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset); ?>" />
		</td></tr>
		<tr><td><?php echo $msg["proc_options_default_value"]; 
		?></td><td><?php echo $msg["procs_options_value"]; ?> : <input type="text" class="saisie-10em" name="DEFAULT_VALUE" value="<?php echo htmlentities($param['DEFAULT_VALUE'][0]['value'],ENT_QUOTES,$charset);?>" />
		</td></tr>
		<?php 
			if ($idchamp) {
		?>
		<tr><td colspan="2">
			<table border="1" id="qualification_table" style="text-align:center">
				<tr>
					<th></th>
					<th><?php echo $msg["procs_options_qualification_options_value"];?></th>
					<th><?php echo $msg["procs_options_qualification_options_label"];?></th>
					<th><?php echo $msg["procs_options_qualification_options_order"];?></th>
				</tr>
				<?php
				$requete="SELECT datatype FROM ".$_custom_prefixe_."_custom WHERE idchamp = $idchamp";
				$resultat = pmb_mysql_query($requete);
				$dtype = pmb_mysql_result($resultat,0,0);
				
				$max = 0;
				for($i=0; $i<count($items);$i++){
					$is_deletable=true;
					$requete="select count(".$_custom_prefixe_."_custom_$dtype) from ".$_custom_prefixe_."_custom_values where ".$_custom_prefixe_."_custom_champ=".$idchamp." and SUBSTRING_INDEX(".$_custom_prefixe_."_custom_$dtype,'|',-1) like '".$items[$i]['value']."'";
					$res = pmb_mysql_query($requete);
					if($res && pmb_mysql_result($res, 0, 0)) {
						$is_deletable = false;
					}	
					print "
					<tr>
						<td ".(!$is_deletable ?"title='".htmlentities($msg['perso_field_used'],ENT_QUOTES,$charset)."' ":"")."><input type='checkbox' name='checked[".$i."]' value='".htmlentities($items[$i]['value'],ENT_QUOTES,$charset)."' ".(!$is_deletable ? "disabled='disabled' ": "")."/></td>
						<td ".(!$is_deletable ?"title='".htmlentities($msg['perso_field_used'],ENT_QUOTES,$charset)."' ":"")."><input type='text' name='ITEMS[".$i."][value]' size='2' value='".htmlentities($items[$i]['value'],ENT_QUOTES,$charset)."' ".(!$is_deletable ? "readonly='readonly' ": "")."/></td>
						<td><input type='text' name='ITEMS[".$i."][label]' size='30' value='".htmlentities($items[$i]['label'],ENT_QUOTES,$charset)."'/></td>
						<td><input type='text' name='ITEMS[".$i."][order]' size='10' value='".htmlentities($items[$i]['order'],ENT_QUOTES,$charset)."'/></td>
					</tr>";
				}
				?>			
			</table>
		</td></tr>
		<?php 
			} else {
				echo "<tr><td colspan='2'><b>".$msg["parperso_options_list_before_rec"]."</b></td></tr>";
			}	
		?>
	</table>
	<h3><?php echo $msg["procs_options_lang_options"];
		?></h3>
	<table class='table-no-border' width=100%>
	<tr><td><?php  echo $msg["proc_options_default_value"];
	?> </td><td>
		<input type="hidden" id="DEFAULT_LANG" name="DEFAULT_LANG" value="<?php  echo htmlentities($param['DEFAULT_LANG'][0]['value'],ENT_QUOTES,$charset);?>" />
		<input type="text" id="DEFAULT_LANG_LABEL" name="DEFAULT_LANG_LABEL" class="saisie-20emr" value="<?php  echo htmlentities($langue_doc[$param['DEFAULT_LANG'][0]['value']],ENT_QUOTES,$charset);
	?>">
		<input type="button" class="bouton" value="..." onclick="openPopUp('<?php echo $base_path; ?>/select.php?what=lang&caller=formulaire&p1=DEFAULT_LANG&p2=DEFAULT_LANG_LABEL', 'selector')" />
		<input type="button" class="bouton" value="X" onclick="this.form.DEFAULT_LANG.value='';this.form.DEFAULT_LANG_LABEL.value='';" />
		</td></tr>
	</table>
	</div>
	<?php 
	if ($idchamp) {
		?>
		<input class="bouton" type="submit" value="<?php echo $msg["ajouter"]; ?>" onclick="add_entry();return false;">&nbsp;
		<input class="bouton" type="submit" value="<?php echo $msg["procs_options_suppr_options_coche"]; ?>" onClick="this.form.first.value=2">&nbsp;
		<input class="bouton" type="submit" value="<?php echo $msg["proc_options_sort_list"];?>" onClick="this.form.first.value=3">&nbsp;
		<?php 
	}
	?>
	<input class="bouton" type="submit" value="<?php  echo $msg[77]; ?>">
	</form>
	<script type="text/javascript">
		var tab = new Array();
		<?php
			for($i=0; $i<count($items);$i++){
				print "
		tab[$i] = ".$items[$i]['value'].";";
			}
		?>
		function getMaxId() {
			var max = 0;
			for(var i=0 ; i<tab.length; i++){
				if(!isNaN(tab[i])) {
					if(parseInt(tab[i])>max) max = parseInt(tab[i]);
				}	
			}
			return max;
		}
		function add_entry(){
			var key = tab.length;
			if(document.getElementById('NUM_AUTO').checked) {
				var new_id = getMaxId()+1;
			} else {
				var new_id = '';
			}
			tab.push(new_id);
			var table = document.getElementById("qualification_table");
			var row = table.insertRow(table.rows.length);
			var cell = row.insertCell(row.cells.length);
			var check = document.createElement("input");
			check.setAttribute("type","checkbox");
			check.setAttribute("name","checked["+key+"]");
			check.setAttribute("value",new_id);		
			cell.appendChild(check);	
			var cell1 = row.insertCell(row.cells.length);
			var id = document.createElement("input");
			id.setAttribute("type","text");
			id.setAttribute("name","ITEMS["+key+"][value]");
			id.setAttribute("size","2");
			id.setAttribute("value",new_id);
			cell1.appendChild(id);
			var cell2 = row.insertCell(row.cells.length);
			var label = document.createElement("input");
			label.setAttribute("type","text");
			label.setAttribute("name","ITEMS["+key+"][label]");
			label.setAttribute("size","30");
			cell2.appendChild(label);
			var cell3 = row.insertCell(row.cells.length);	
			var order = document.createElement("input");
			order.setAttribute("type","text");
			order.setAttribute("name","ITEMS["+key+"][order]");
			order.setAttribute("size","10");
			cell3.appendChild(order);
		}
		function changeType(typeNode) {
			if (!typeNode.checked) {
				return;
			}

			var paramTypeList = document.querySelectorAll("tr.paramType");
			var maxsize = document.getElementById("maxsize");
			var size = document.getElementById("size");
			if (paramTypeList.length <= 0) {
				return;
			}
			
			switch (typeNode.value) {
    			case "textarea":
        			for(i=0; i < paramTypeList.length; i++) {
        				paramTypeList[i].style.visibility = "collapse";
        			}
        			if (size && !size.readOnly) {
        				size.readOnly = true;
        			}
        			if (maxsize && !maxsize.readOnly) {
        				maxsize.readOnly = true;
        			}
    				break;

    			default:
    			case "text":
        			if (size && size.readOnly) {
        				size.readOnly = false;
        			}
        			if (maxsize && maxsize.readOnly) {
        				maxsize.readOnly = false;
        			}
    				for(i=0; i < paramTypeList.length; i++) {
        				paramTypeList[i].style.visibility = "";
        			}
    				break;
			}
		}
	</script>
	<?php
}
?>
</body>
</html>