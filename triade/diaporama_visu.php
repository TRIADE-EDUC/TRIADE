<?php
session_start()
?>
<html>
<head>
<!-- FGJSDIAPO -->
<!-- Application web de diaporama version 1.4 -->

<!-- Chargement du script pour l'ajout des commentaires -->
<!-- Implémentation en natif à venir ... -->
<script>
	function jsdiapo_comm_update()
	{
        if(fgjsdiapo_img_comm_error == 0)
        {
        window.onerror = function a(){return true;};
        }

        if(fgjsdiapo_img_comm==1)
        {
            document.getElementById('fgjsdiapo_img_comm').innerHTML = (jsdiapo_images_comm[jsdiapo_images_now]);
        }

        if(fgjsdiapo_img_comm_error == 0)
        {
            window.onerror = "";
            if(jsdiapo_error_report_active > 0)
            {
            window.onerror = jsdiapo_error;
            }
        }
	}
</script>

<!-- Chargement de la librairie JSHP : Permet de récupérer les variables par l'url. -->
<script src="./FGJsDiapo/script/JSHP.lib" type="text/javascript"></script>

<!-- Insertion du fichier de configuration -->
<script type="text/javascript">
//Si "oc" est préciser un ajout le fichier de configuration précisé sinon on ajoute celui par defaut
if(jshp_get_var("oc")){document.write('<scr'+'ipt src="./FGJsDiapo/'+jshp_get_var("oc")+'" type="text/javascript"></scr'+'ipt>');	}else{document.write('<scr'+'ipt src="./FGJsDiapo/config.txt" type="text/javascript" language="javascript" id="srcconfig"></scr'+'ipt>');}
//Si "?a=" précisé alors on ajoute la librairie des fonctions rapides.
if(jshp_get_var("a") > "" || jshp_get_var("o") > ""){document.write('<scr'+'ipt src="./FGJsDiapo/script/FGJSDIAPO_rapidfunction.lib" type="text/javascript"></scr'+'ipt>');}

//Support des effets pour IE en natif
firstopen = 0;
compatible = 1;
if(document.all && document.getElementById)
{
compatible = 0;
}
</script>

<!-- Chargement du fichier script principal de FGJSDIAPO -->
<script src="./FGJsDiapo/script/FGJSDIAPO.js" type="text/javascript"></script>
</head>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<body id="bodyfond2">

<div id="about" class="fgjsdiapo_div_optionsabout" style="position: absolute; width: 250px; height: 230px; left:593px; top:0px;visibility:hidden;z-index:1">
	<!-- Merci de laisser ces quelques lignes pour faire connaitre le script -->
		<button onclick="fgjsdiapo_fen_about()" style="width:20px;height:20px;position:relative;left:228px;top:2px;">X</button>
		<h3><script type="text/javascript">fgjsdiapo_write_var(fgjsdiapo_fen_about_title);</script></h3>
		Developpeur : FG<br>
		Site web : <a href='http://fg.logiciel.free.fr' target='_blank'>http://fg.logiciel.free.fr</a><br />
		Traducteur : <script type="text/javascript">fgjsdiapo_write_var(fgjsdiapo_trad_pseudo);</script><br />
		Version de FGJSDIAPO (js) : v<script type="text/javascript">fgjsdiapo_write_var(fgjsdiapo_js_version);</script>
</div>
<div id="options" class="fgjsdiapo_div_optionsabout" style="position: absolute; width: 520px; height: 230px; left:23px; top:0px;visibility:hidden;z-index:1">
	<button onclick="fgjsdiapo_fen_options()" style="width:20px;height:20px;position:relative;left:498px;top:2px;">X</button>
	<h3>
		<script type="text/javascript">fgjsdiapo_write_var(fen_options_title);</script>

	</h3>

	<br />
	<div align="left" style="vertical-align: top">
		<table border="0" cellspacing="4" id="table2" width="100%">
		<tr id="fen_options_tr_select1">
			<td height="5">
				<script type="text/javascript">fgjsdiapo_write_var(fen_options_title_slide_auto);</script>
			</td>
			<td>
				<input id="fen_options_form01_input" type="text">
			</td>
			<td>
				<button id="fen_options_btn_slide_auto" onclick="fgjsdiapo_options_slide_auto()"></button>
				<script type="text/javascript">fgjsdiapo_write_bvar("fen_options_btn_slide_auto",fen_options_btn_slide_auto0);</script>
			</td>
			<td>
				<img src="./image/commun/nav_about.png" onclick="alert(fen_options_nfo_slide_auto)" alt="Information">
			</td>
		</tr>
		<tr id="fen_options_tr_select2">
			<td height="5">
				<script type="text/javascript">fgjsdiapo_write_var(fen_options_title_select_lang)</script>
			</td>
			<td id="fen_options_select2">
				<select id="fen_options_form01_select2" name="fen_options_form01_select2" size="1">
					<script type="text/javascript">fgjsdiapo_fen_options_lang_write()</script>
				</select>
			</td>
			<td>
				<button id="fen_options_btn_slide_auto0" onclick="fgjsdiapo_fen_options_lang_apply()"></button>
				<script type="text/javascript">fgjsdiapo_write_bvar("fen_options_btn_slide_auto0",fen_options_btn_select_lang);</script>
			</td>
			<td>
				<img src="./image/commun/nav_about.png" onclick="alert(fen_options_nfo_select_lang)" alt="Information">
			</td>
		</tr>

		<tr id="fen_options_tr_select3">
			<td height="5">
				<script type="text/javascript">fgjsdiapo_write_var(fen_options_title_select_style);</script>
			</td>
			<td id="fen_options_select3">
				<select id="fen_options_form01_select3" name="fen_options_form01_select3" size="1">
					<script type="text/javascript">fgjsdiapo_fen_options_style_write();</script>
				</select>
			</td>
			<td>
				<button id="fen_options_btn_slide_auto1" onclick="fgjsdiapo_fen_options_style_apply(document.getElementById('fen_options_form01_select3').value)"></button>
				<script type="text/javascript">fgjsdiapo_write_bvar("fen_options_btn_slide_auto1",fen_options_btn_select_style);</script>
			</td>
			<td>
				<img src="./image/commun/nav_about.png" onclick="alert(fen_options_nfo_select_style)" alt="Information">
			</td>
		</tr>

		<tr id="fen_options_tr_select4">
			<td height="5">
				<script type="text/javascript">fgjsdiapo_write_var(fen_options_title_select_img)</script>
			</td>
			<td id="fen_options_select4">
				<select id="fen_options_form01_select4"  type="text" name="fen_options_form01_select4" size="1">
					<script type="text/javascript">fgjsdiapo_fen_options_img_write()</script>
				</select>
			</td>
			<td>
				<button id="fen_options_btn_slide_auto2" onclick="fgjsdiapo_fen_option_img_apply(document.getElementById('fen_options_form01_select4').value)"></button>
				<script type="text/javascript">
					fgjsdiapo_write_bvar("fen_options_btn_slide_auto2",fen_options_btn_select_img);
					//v1.4
					if(fgjsdiapo_img_browse.length == 2)
					{
						tr = document.getElementById('fen_options_tr_select4').style
						tr.visibility="hidden";
						tr.display="none";
					}
				</script>
			</td>
			<td>
				<img src="./image/commun/nav_about.png" onclick="alert(fen_options_nfo_select_img)" alt="Information">
			</td>
		</tr>
	</table>
</div>
</div>

<!-- Chargement de l'interface de FGJSDIAPO -->
<table border="0" width="100%" cellspacing="0" cellpadding="0" id="fgjsdiapo_table_index">
	<tr>
		<td align="center" valign="top" height="500">
		<!-- Tableau FGJSDIAPO -->
		<table border="0" id="fgjsdiapo_table_script" class="fgjsdiapo_table_script">
			<tr>
				<td id="fgjsdiapo_table_script_td_nav" class="fgjsdiapo_table_script_td_nav" colspan="2">
				<script type="text/javascript">
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="jsdiapo_move(\'first\')"><img src="./image/commun/nav_back.png">'+btn_first+'</button>');
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="jsdiapo_move(\'back\')"><img src="" height="0" width="0">'+btn_back+'</button>');
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="jsdiapo_move(\'next\')">'+btn_next+'<img src="" height="0" width="0"></button>');
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="jsdiapo_move(\'last\')">'+btn_last+'<img src="./image/commun/nav_next.png"></button>');
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="fgjsdiapo_fen_options()"><img src="./image/commun/nav_options.png">'+btn_options+'</button>');
					document.write('<button class="fgjsdiapo_table_script_td_nav_btn" onclick="fgjsdiapo_fen_about()"><img src="./image/commun/nav_about.png">'+btn_about+'</button>');
				</script>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
                        <script type="text/javascript">
                     window.onerror = function a(){return true;};
                     if(fgjsdiapo_img_comm==1 && jsdiapo_images_comm)
                     {
                     document.write('<div id="fgjsdiapo_img_comm" class="jsdiapo_move_nfo"></div>');
                     }
                     window.onerror = function b(){};
                     if(jsdiapo_error_report_active > 0)
                     {
                        window.onerror = jsdiapo_error;
                     }
                     </script>
						<div id="fgjsdiapo_fen_preview_h" style="overflow: auto;border: 0px solid #000;">
							<table width="100%" cellspacing="0" cellpadding="0" class="fgjsdiapo_table_script_td_nav">
								<script type="text/javascript">fgjsdiapo_write_preview('h');</script>
							</table>
						</div>
					</center>
				</td>
			</tr>
			<tr>
				<td height="404" width="100%">
				<center>
					<img id="jsdiapo_images_img" onclick="jsdiapo_move('next')" class="jsdiapo_images_img" onload="window.defaultStatus='Chargement terminé';window.defaultStatus=fgjsdiapo_fen_status" alt="">
				</center>
				</td>
				<td>
				<div id="fgjsdiapo_fen_preview_v" style="overflow: auto;border: 0px solid #000;">
					<table width="100%" cellspacing="0" cellpadding="0" class="fgjsdiapo_table_script_td_nav">
						<script type="text/javascript">fgjsdiapo_write_preview('v')</script>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td id="fgjsdiapo_table_script_td_bottom" class="fgjsdiapo_table_script_td_bottom" colspan="2">
					<div id="jsdiapo_move_nfo" class="jsdiapo_move_nfo"></div>
				</td>
			</tr>
		</table>

		<table border="0" width="368" cellspacing="0" cellpadding="0" id="table1">
			<tr>
				<td style="text-align:center">
					<span class="copyright">
						<script type='text/javascript' >document.write(langmenupied);</script>
					</span>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!-- Chargement de la gestion des fenêtres -->
<script src="./FGJsDiapo/script/FGJSDIAPO_fen.js" type="text/javascript" language="javascript"></script>
<!-- Chargement de la librairie JSDIAPO v0.4 -->
<script src="./FGJsDiapo/script/JSDIAPO.lib" type="text/javascript" language="javascript" id="fgjsdiapo_jsdiapo_lib"></script>
</body>
</html>