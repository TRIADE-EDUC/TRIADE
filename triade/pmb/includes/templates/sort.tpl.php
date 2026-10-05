<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.tpl.php,v 1.29 2024/02/21 09:25:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $show_tris_form, $msg, $ligne_tableau_tris, $ligne_tableau_tris_etagere, $ligne_tableau_tris_rss_flux, $show_sel_form;
global $ligne_tableau_external_tris, $popup_ligne_tableau_tris;
global $popup, $current_module, $sort_input_hidden_common, $id_authperso;
global $opac_show_languages;

$sort_input_hidden_common = "
			<input type='hidden' name='caller' value='!!caller!!'>
			<input type='hidden' name='id_tri' value='!!id_tri!!'>
			<input type='hidden' name='type_tri' value='!!sortname!!'>
			<input type='hidden' name='module' value='$current_module'>
			<input type='hidden' name='id_authperso' value='$id_authperso'>"; 

// les templates pour l'écran d'affichage de la liste des tris
$show_tris_form="<body class='$current_module'>
    <div id='contenu-frame'>
		<script>
			function agitTri(actionTri,idTri) {				
				document.sort_form.action_tri.value = actionTri;
				document.sort_form.id_tri.value = idTri;
				document.sort_form.submit();
			}
			function suppr(idTri) {
				if (confirm('" . $msg['tri_confirm_supp'] . "')) {
					agitTri('supp',idTri);
				}
			}
		</script>
		<table style='width:100%'>
            <tr>
                <td class='align_left'>&nbsp;</td>
                <td>
                    <div class='right'>
                        <a href='#' onClick=\"parent.document.getElementById('history').style.display='none'; return false;\">
                            <img src='".get_url_icon('close.gif')."' border='0' class='center'>
                        </a>
                    </div>
                </td>
            </tr>
        </table>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=2 style='vertical-align:top'>
					<table>
						<th colspan=3>" . $msg['tris_dispos'] . "</th>
						!!liste_tris!!
					</table>
					</td>
				</tr>
				<tr>
					<td style='width:10%'>
						&nbsp;
					</td>
					<td style='vertical-align:top'><SPAN class='right'>
						<input type='button' class='bouton' name='".$msg['tri_inactif']."' value='".$msg['tri_inactif']."' onClick=\"!!callback!!\">
						<input type='button' class='bouton' name='".$msg['definir_tri']."' value='".$msg['definir_tri']."' onClick=\"agitTri('modif','');\">
					</SPAN></td>
				</tr>
			</table>
			<input type='hidden' name='action_tri' value=''>
			$sort_input_hidden_common
		</form>
	</div>
</body>
";
$show_popup_tris_form="<body class='$current_module'>
    <div id='contenu-frame'>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=2 style='vertical-align:top'>
					<table>
						<th colspan=3>" . $msg['tris_dispos'] . "</th>
						!!liste_tris!!
					</table>
					</td>
				</tr>
				<tr>
					<td style='width:10%'>
						&nbsp;
					</td>
					<td style='vertical-align:top'><SPAN class='right'>
						<input type='button' class='bouton sort' name='".$msg['tri_inactif']."' value='".$msg['tri_inactif']."' data-sort_link='!!callback!!'>
						<input type='button' class='bouton' name='".$msg['definir_tri']."' value='".$msg['definir_tri']."' onClick=\"agitTri('modif','');\">
					</SPAN></td>
				</tr>
			</table>
			<input type='hidden' name='popup' value='1'>
			<input type='hidden' name='action_tri' value=''>
			$sort_input_hidden_common
		</form>
	</div>
</body>
";

$show_popup_tris_form_segment="<body class='$current_module'>
    <div id='contenu-frame'>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=2 style='vertical-align:top'>
					<table>
						<th colspan=3>" . $msg['tris_dispos'] . "</th>
						!!liste_tris!!
					</table>
					</td>
				</tr>
				<tr>
					<td style='width:10%'>
						&nbsp;
					</td>
					<td style='vertical-align:top'><SPAN class='right'>
						<input type='button' class='bouton' name='".$msg['definir_tri']."' value='".$msg['definir_tri']."' onClick=\"defineSort('modif','!!segment_id!!');\">
					</SPAN></td>
				</tr>

			</table>
			<input type='hidden' name='action_tri' value='' id='popup_action_tri'>
			<input type='hidden' name='sort_string' value='d_num_8' id='popup_sort_string'>
			<input type='hidden' name='origin' value='search_segment'>
			<input type='hidden' name='num_segment' value='!!id_segment!!'>
			<input type='hidden' name='index_tri' value='' id='popup_index_tri'>
			<input type='hidden' name='popup' value='1'>
			$sort_input_hidden_common
		</form>
	</div>
</body>
";

//le template pour la modification d'un tri
$close_btn = "<td class='align_right'><a href='#' onClick=\"parent.document.getElementById('history').src='./sort.php?action=0';parent.document.getElementById('history').style.display='none'; return false;\"><img src='".get_url_icon('close.gif')."' border='0' class='center'></a></td>"; 
$show_sel_form=" <body class='$current_module' onLoad='document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();'>
    <div id='contenu-frame'>
        !!sel_form_action!!
		<script>
			function sauvegarder() {
				if (document.forms[\"sort_form\"].elements[\"nom_tri\"].value!=\"\") {    
					right=document.sort_form.elements['liste_sel[]'];
					for (i=right.length-1; i>=0; i--) {
						right.options[i].selected=true;
					}
					document.sort_form.action_tri.value = \"enreg\";
                    ".(!empty($popup) && $popup ? "" : "document.forms[\"sort_form\"].submit();")."
				} else {
					alert (\"".$msg['erreur_nom_tri']."\");
					document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();
				}
			}
    	</script>
    	<table style='width:100%' role='presentation'>
            <tr>
                <td class='align_left'>
                    <h3>".$msg['definir_tri']."</h3>
                </td>".(!empty($popup) && $popup ? "" : $close_btn)."
            </tr>
        </table>
		<form id='sort_form' name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%' role='presentation'>
				<tr>
					<td colspan=3>
                        ".$msg['nom_tri']."&nbsp;&nbsp;<input type='text' id='nom_tri' name='nom_tri' value='!!nom_tri!!' data-translation-fieldname='nom_tri' />
                        <input type='hidden' name='id_tri' value='!!id_tri!!' />
					</td>
				</tr>
				<tr>
					<td style='width:40%'>
					   ".$msg['criteres_tri_dispos']."
					</td>
					<td style='width:20%'>
					   &nbsp;
					</td>
					<td style='width:40%'>
                        ".$msg['criteres_tri_retenus']."
					</td>
				</tr>
				<tr>
					<td style='vertical-align:top; width:40%; height:100%'>
						<select name='liste_critere' multiple='yes' style='width:100%;height:400px' onDblClick='left_to_right()'>
							!!liste_criteres!!
						</seclect>
					</td>
					<td style='text-align:center; width:20%'>
                        <table style='height:100%' role='presentation'>
                            <tr>
                                <td style='text-align:center'>
                                    <input type='button' value='&gt;&gt;' onClick='left_to_right()' />
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align:center'>
                                    <input type='button' value='&lt;&lt;' onClick='right_to_left()' />
                                </td>
                            </tr>
                        </table>.
					</td>
					<td style='vertical-align:top' style='text-align:center; width:40%; height:100%'>
						<select name='liste_sel[]' multiple='yes' style='width:100%;height:400px' onDblClick='right_to_left()'>
							!!liste_selectionnes!!
						</select>
					</td>		
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan=2>
                        <span class='right'>
					       <input type='image' src='".get_url_icon('arrow_up.png')."' alt='".$msg['monter']."' title='".$msg['monter']."' value='".$msg['monter']."' onClick='move_up(); return false;'>
                            &nbsp;<input type='image' src='".get_url_icon('arrow_down.png')."' alt='".$msg['descendre']."' title='".$msg['descendre']."' value='".$msg['descendre']."' onClick='move_down(); return false;'>
                            &nbsp;&nbsp;
                            <input type='image' alt='".$msg['tri_croissant']."' title='".$msg['tri_croissant']."' value='".$msg['tri_croissant']."' src='".get_url_icon('fleche_diago_haut.png')."' onClick=\"change_order('c'); return false;\">
                            &nbsp;<input type='image' alt='".$msg['tri_decroissant']."' title='".$msg['tri_decroissant']."' value='".$msg['tri_decroissant']."' src='images/fleche_diago_bas.png' onClick=\"change_order('d'); return false;\">
                            &nbsp;<input name='ordre' type='button' class='bouton' num_text='0 - 9' onClick=\"swap_type_tri(this.getAttribute('num_text'));\" value='A - Z/0 - 9'>
                        </span>
                    </td>
				</tr>
				<tr><td colspan=3>&nbsp;</td></tr>
				<tr>
					<td colspan=3>
                        <SPAN class='right'>
                            <input type='button' class='bouton cancel' name='".$msg['76']."' value='".$msg['76']."' ".(!empty($popup) && $popup ? "" : "onClick='document.forms[\"sort_form\"].submit();'").">
                            &nbsp;&nbsp;
                            <input type='button' class='bouton save' name='".$msg['sauvegarder_tri']."' value='".$msg['sauvegarder_tri']."' onClick='sauvegarder()'>
                        </span>
					</td>
				</tr>
			</table>
            ".(!empty($popup) && $popup ? "<input type='hidden' name='popup' value='1'>" : "")."
			<input type='hidden' name='action_tri' value='affliste'>
			$sort_input_hidden_common
		</form>
	</div>
</body>
";

$show_sel_form_segments = " <body class='$current_module' onLoad='document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();'>
    <div id='contenu-frame'>
        !!sel_form_action!!
		<script>
			function sauvegarder() {
				if (document.forms[\"sort_form\"].elements[\"nom_tri\"].value!=\"\") {
					right=document.sort_form.elements['liste_sel[]'];
                    var sort_string = '';
                    var sort_name = '';
                    var index_tri = document.forms[\"sort_form\"].elements[\"index_tri\"].value;
                    sort_name += document.forms[\"sort_form\"].elements[\"nom_tri\"].value;

					for (i=0; i<right.length; i++) {
                        sort_string+= right.options[i].value;
                        if (i<right.length-1) {
                            sort_string += ',';
                        }
					}
                    //affectation des valeur au champs caché de la page principale
                    var id_sort_input = 'segment_sort_list_'+index_tri;
                    var id_name_input = 'segment_sort_name_'+index_tri;
                    var id_name_label = 'segment_sort_label_'+index_tri;
                    parent.document.getElementById(id_name_input).value = sort_name;
                    parent.document.getElementById(id_name_label).innerHTML = sort_name;

                    var opac_show_languages = '".(!empty($opac_show_languages) ? explode(' ', trim($opac_show_languages))[1] : '')."';
                    if(opac_show_languages.length) {
                        var languages = opac_show_languages.split(',');console.log('languages', languages);
                        languages.forEach(function (language) {
                            var id_lang_name_input = language+'_segment_sort_name_'+index_tri;
                            if(parent.document.getElementById(id_lang_name_input)) {
                                var lang_sort_name = document.forms['sort_form'].elements[language+'_nom_tri'].value;
                                parent.document.getElementById(id_lang_name_input).value = lang_sort_name;
                            }
                        });
                    }

                    parent.document.getElementById(id_sort_input).value = sort_string;
                    parent.document.getElementById('history').style.display = 'none';
                    parent.document.getElementById('history').src = './sort.php?action=0';
                    return false;
				} else {
					alert (\"".$msg['erreur_nom_tri']."\");
					document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();
				}
			}
    	</script>
    	<table style='width:100%' role='presentation'>
            <tr>
                <td class='align_left'>
                    <h3>".$msg['definir_tri']."</h3>
                </td>".(!empty($popup) && $popup ? $close_btn : "")."
            </tr>
        </table>
    	<form id='sort_form' name='sort_form' method='post' action='sort.php?categ=search_universes&num_segment=!!id_segment!!'>
    	   <table style='width:100%; height:100%' role='presentation'>
    			<tr>
    				<td colspan=3>
                        ".$msg['nom_tri']."&nbsp;&nbsp;<input type='text' name='nom_tri' value='!!nom_tri!!' data-translation-fieldname='segment_sort' />
                        <input type=hidden name='id_tri' value='!!id_tri!!' />
    				</td>
    			</tr>
    			<tr>
    				<td style='width:40%'>
    				   ".$msg['criteres_tri_dispos']."
    				</td>
    				<td style='width:20%'>
    				   &nbsp;
    				</td>
    				<td style='width:40%'>
                        ".$msg['criteres_tri_retenus']."
    				</td>
    			</tr>
    			<tr>
    				<td style='vertical-align:top; width:40%; height:100%'>
    					<select name='liste_critere' multiple='yes' style='width:100%;height:400px' onDblClick='left_to_right()'>
    						!!liste_criteres!!
    					</seclect>
    				</td>
    				<td style='text-align:center; width:20%'>
        				<table style='height:100%' role='presentation'>
                            <tr>
                                <td style='text-align:center'>
                                    <input type='button' value='&gt;&gt;' onClick='left_to_right()' />
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align:center'>
                                    <input type='button' value='&lt;&lt;' onClick='right_to_left()' />
                                </td>
                            </tr>
                        </table>.
    				</td>
    				<td style='vertical-align:top' style='text-align:center; width:40%; height:100%'>
    					<select name='liste_sel[]' multiple='yes' style='width:100%;height:400px' onDblClick='right_to_left()'>
    						!!liste_selectionnes!!
    					</select>
    				</td>
    			</tr>
    			<tr>
    				<td>&nbsp;</td>
    				<td colspan=2>
                        <span class='right'>
    				       <input type='image' src='".get_url_icon('arrow_up.png')."' alt='".$msg['monter']."' title='".$msg['monter']."' value='".$msg['monter']."' onClick='move_up(); return false;'>
                            &nbsp;<input type='image' src='".get_url_icon('arrow_down.png')."' alt='".$msg['descendre']."' title='".$msg['descendre']."' value='".$msg['descendre']."' onClick='move_down(); return false;'>
                            &nbsp;&nbsp;
                            <input type='image' alt='".$msg['tri_croissant']."' title='".$msg['tri_croissant']."' value='".$msg['tri_croissant']."' src='".get_url_icon('fleche_diago_haut.png')."' onClick=\"change_order('c'); return false;\">
                            &nbsp;<input type='image' alt='".$msg['tri_decroissant']."' title='".$msg['tri_decroissant']."' value='".$msg['tri_decroissant']."' src='images/fleche_diago_bas.png' onClick=\"change_order('d'); return false;\">
                            &nbsp;<input name='ordre' type='button' class='bouton' num_text='0 - 9' onClick=\"swap_type_tri(this.getAttribute('num_text'));\" value='A - Z/0 - 9'>
                        </span>
                    </td>
    			</tr>
    			<tr><td colspan=3>&nbsp;</td></tr>
    			<tr>
    				<td colspan=3>
                        <span class='right'>
                            <input type='button' class='bouton cancel' name='".$msg['76']."' value='".$msg['76']."' onClick=\"parent.document.getElementById('history').src='./sort.php?action=0';parent.document.getElementById('history').style.display='none'; return false;\">
                            &nbsp;&nbsp;
                            <input type='button' class='bouton save' name='".$msg['sauvegarder_tri']."' value='".$msg['modif_tri']."' onClick='sauvegarder()'>
                        </span>
    				</td>
    			</tr>
    		</table>
            ".(!empty($popup) && $popup ? "<input type='hidden' name='popup' value='1'>" : "")."
    		<input type='hidden' name='action_tri' value='affliste'>
    		<input type='hidden' name='index_tri' value='".(isset($_POST['index_tri'])?$_POST['index_tri']:'')."'>
    		$sort_input_hidden_common
    	</form>
	</div>
</body>
";

$sel_form_actions="
<script>
			function left_to_right() {
		 		var order;
				var temp;
				left=document.sort_form.liste_critere;
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<left.length; i++) {
					if (left.options[i].selected) {
						temp=left.options[i].value;
						temp=temp.substr(2,temp.lastIndexOf('_')-2);
								
						switch (temp) {
							case 'text': 
								order='A-Z ';
							break;
							case 'num':
								order='0-9 ';
							break;
						}
						new_option=new Option(order+left.options[i].text,left.options[i].value);
						right.options[right.length]=new_option;
						left.options[i]=null;
						i=i-1;
					}
				}
			}
			function right_to_left() {
				left=document.sort_form.liste_critere;
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						new_option=new Option(right.options[i].text.substr(4),right.options[i].value);
						left.options[left.length]=new_option;
						right.options[i]=null;
						i=i-1;
					}
				}
			}
			function swap_type_tri(type) {
				var valeur;
				valeur=document.sort_form.ordre.getAttribute('num_text');
				if (valeur=='A - Z') {
					valeur='0 - 9';
				} else {
					valeur='A - Z';
				}
				document.sort_form.ordre.setAttribute('num_text',valeur);
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						temp=right.options[i].value;
						switch (document.sort_form.ordre.getAttribute('num_text'))
						{
							case 'A - Z':
							if (temp.substr(0,1)=='c') {
								order='A-Z';
							} else {
								order='Z-A';
							}
							type='text';
							break;
							case '0 - 9':
							if (temp.substr(0,1)=='c') {
								order='0-9';
							} else {
								order='9-0';
							}
							type='num';
							break;
						}
						right.options[i].value=temp.substr(0,1)+'_'+type+'_'+temp.substring(temp.lastIndexOf('_')+1);
						right.options[i].text=order.toUpperCase()+' '+right.options[i].text.substr(4);		
					}
				}
				
			}
			function change_order(order_value) {
		 		var order;
				var temp;
				var type;		
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						temp=right.options[i].value;
						if (temp.substr(0,1)!=order_value) {
							switch (temp.substr(2,temp.lastIndexOf('_')-2)) {
							case 'num':
								document.sort_form.ordre.setAttribute('num_text','0 - 9');
							break;
							case 'text':
								document.sort_form.ordre.setAttribute('num_text','A - Z');
							break;
							}
							switch (document.sort_form.ordre.getAttribute('num_text'))
							{
							case 'A - Z':
								if (order_value=='c') {
									order='A-Z';
								} else {
									order='Z-A';
								}
								type='text';
							break;
							case '0 - 9':
								if (order_value=='c') {
									order='0-9';
								} else {
									order='9-0';
								}
								type='num';
							break;
						}
						right.options[i].value=order_value+'_'+type+'_'+temp.substring(temp.lastIndexOf('_')+1);
						right.options[i].text=order.toUpperCase()+' '+right.options[i].text.substr(4);
						}		
					}
				}
			}
			function move_up() {
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						if (i>0) {
							swap_i=new Option(right.options[i].text,right.options[i].value);
							swap_i_1=new Option(right.options[i-1].text,right.options[i-1].value);
							right.options[i]=swap_i_1;
							right.options[i-1]=swap_i;
							right.options[i-1].selected=true;
						}
					}
				}
			}
			function move_down() {
				right=document.sort_form.elements['liste_sel[]'];
				for (i=right.length-1; i>=0; i--) {
					if (right.options[i].selected) {
						if (i<right.length-1) {
							swap_i=new Option(right.options[i].text,right.options[i].value);
							swap_i_1=new Option(right.options[i+1].text,right.options[i+1].value);
							right.options[i]=swap_i_1;
							right.options[i+1]=swap_i;
							right.options[i+1].selected=true;
						}
					}
				}
			}
	</script>
";