<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_authors_search.class.php,v 1.10.4.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_authorities_search.class.php");

class level1_authors_search extends level1_authorities_search {

	protected $author_type;

	public function set_author_type($author_type) {
		$this->author_type = intval($author_type);
	}

	protected function get_hidden_search_content_form() {
		$content_form = parent::get_hidden_search_content_form();
		$content_form .= "<input type=\"hidden\" name=\"author_type\" value=\"\">";
		return $content_form;
	}

	protected function get_results_all_types() {
		global $msg, $charset;
		global $titre_resume;
		global $nb_result_resume;
		global $link_type_resume;

		$level1_authors_search = new level1_authors_search('authors', 'level1_authors_search');
		$level1_authors_search->set_user_query($this->user_query);
		$level1_authors_search->set_author_type(70);
		$nb_result_auteurs_physiques = $level1_authors_search->get_nb_results();

		$level1_authors_search = new level1_authors_search('authors', 'level1_authors_search');
		$level1_authors_search->set_user_query($this->user_query);
		$level1_authors_search->set_author_type(71);
		$nb_result_auteurs_collectivites = $level1_authors_search->get_nb_results();

		$level1_authors_search = new level1_authors_search('authors', 'level1_authors_search');
		$level1_authors_search->set_user_query($this->user_query);
		$level1_authors_search->set_author_type(72);
		$nb_result_auteurs_congres = $level1_authors_search->get_nb_results();

		$this->nb_results = $nb_result_auteurs_physiques+$nb_result_auteurs_collectivites+$nb_result_auteurs_congres;
		$this->add_in_session();

		if($nb_result_auteurs_physiques == $this->nb_results) {
			// Il n'y a que des auteurs physiques, affichage type: Auteurs xx résultat(s) afficher
			$titre_resume[0]=$msg["authors"];
			$nb_result_resume[0]=$this->nb_results;
			$link_type_resume[0]="70";
		} else if($nb_result_auteurs_collectivites == $this->nb_results) {
			// Il n'y a que des collectivites, affichage type: Collectivités xx résultat(s) afficher
			$titre_resume[0]=$msg["collectivites_search"];
			$nb_result_resume[0]=$this->nb_results;
			$link_type_resume[0]="71";
		} else if($nb_result_auteurs_congres == $this->nb_results) {
			// Il n'y a que des congres, affichage type: Collectivités xx résultat(s) afficher
			$titre_resume[0]=$msg["congres_search"];
			$nb_result_resume[0]=$this->nb_results;
			$link_type_resume[0]="72";
		} else {
			// il y a un peu de tout, affichage en titre type: Auteurs xx résultat(s) afficher
			$titre_resume[0]=$msg["authors"];
			$nb_result_resume[0]=$this->nb_results;
			$link_type_resume[0]="";

			if($nb_result_auteurs_physiques) {
				// Il n'y a des auteurs physiques, affichage en sous-titre titre: Auteurs physiques xx résultat(s) afficher
				$titre_resume[]=$msg["personnes_physiques_search"];
				$nb_result_resume[]=$nb_result_auteurs_physiques;
				$link_type_resume[]="70";
			}
			if($nb_result_auteurs_collectivites) {
				// Il n'y a des collectivites, affichage en sous-titre titre: Collectivités xx résultat(s) afficher
				$titre_resume[]=$msg["collectivites_search"];
				$nb_result_resume[]=$nb_result_auteurs_collectivites;
				$link_type_resume[]="71";
			}
			if($nb_result_auteurs_congres) {
				// Il n'y a des congres, affichage en sous-titre titre: Congrès xx résultat(s) afficher
				$titre_resume[]=$msg["congres_search"];
				$nb_result_resume[]=$nb_result_auteurs_congres;
				$link_type_resume[]="72";
			}
		}
	}

	public function proceed() {
		global $msg, $charset;
		global $opac_allow_affiliate_search;
		global $titre_resume;
		global $nb_result_resume;
		global $link_type_resume, $opac_rgaa_active;

		$this->get_results_all_types();
		if($opac_allow_affiliate_search){
			print "<div id='author_result'>
				<strong>".$titre_resume[0]."</strong>";
			print"
				<blockquote role='presentation' id='author_result_blockquote'>
					<div id='author_results_in_catalog'>";
			for($i=0;$i<count($titre_resume);$i++)  {
				if($i==0){
					print "
						<strong>".$msg['in_catalog']."</strong> ".$nb_result_resume[$i]." ".$msg['results']." ";
				}else{
					if($i==1) {
						print "<blockquote role='presentation'>";
					}
					print "
						<strong>".$titre_resume[$i]."</strong> ".$nb_result_resume[$i]." ".$msg['results']." ";
				}

				if ($nb_result_resume[$i]) {
				    if ($opac_rgaa_active) {
				        print "
        	            <button type='button' class='search_result' onclick=\"
                            document.forms.".$this->get_hidden_search_form_name().".count.value='".$nb_result_resume[$i]."';
        					document.forms.".$this->get_hidden_search_form_name().".author_type.value='$link_type_resume[$i]';
        					document.forms.".$this->get_hidden_search_form_name().".action ='".$this->get_form_action()."&tab=catalog';
        					document.forms['".$this->get_hidden_search_form_name()."'].submit(); return false;\">
                           " . $msg['suite'] . "&nbsp;<img src='" . get_url_icon('search.gif') . "' aria-hidden='true' style='border:0px' />
        	            </button>";
				    } else {
    					print "<a href=\"#\" onClick=\"
    					document.forms.".$this->get_hidden_search_form_name().".count.value='".$nb_result_resume[$i]."';
    					document.forms.".$this->get_hidden_search_form_name().".author_type.value='$link_type_resume[$i]';
    					document.forms.".$this->get_hidden_search_form_name().".action ='".$this->get_form_action()."&tab=catalog';
    					document.forms['".$this->get_hidden_search_form_name()."'].submit(); return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a>";
				    }
				}
				print "<br />";
			}
			if($i>1) {
				print "</blockquote>";
			}
			print "
				</div>
				<div id='author_results_affiliate'>
					<strong>".$msg['in_affiliate_source']."</strong><img src='".get_url_icon('patience.gif')."' />
				</div>
				<script>
					var author_search = new http_request();
					author_search.request('./ajax.php?module=ajax&categ=search',true,'&search_type=authorities&type=author&user_query=".rawurlencode(stripslashes((($charset == "utf-8")?$this->user_query:encoding_normalize::utf8_normalize($this->user_query))))."',true,authorResults);
					function authorResults(response){
						var rep = eval('('+response+')');
						var div = document.getElementById('author_results_affiliate');
						div.innerHTML='';
						var strong = document.createElement('strong');
						strong.innerHTML = \"".$msg['in_affiliate_source']."\";
						div.appendChild(strong);
						var text_node = document.createTextNode(' '+ rep.nb_results.total + ' '+pmbDojo.messages.getMessage('search', 'results')+' ');
						div.appendChild(text_node);
						if(rep.nb_results.total>0){
						    ". $this->get_display_link_affiliate_js("search_authors") ."
							var test = (rep.nb_results.authors>0 && (rep.nb_results.coll>0 || rep.nb_results.congres>0))|| (rep.nb_results.coll>0 && (rep.nb_results.authors>0 || rep.nb_results.congres>0));
							if(test){
								var bool = false;
								var block = document.createElement('blockquote');
								if(rep.nb_results.authors>0){
									createItem(rep.nb_results.authors,'".$msg['personnes_physiques_search']."','70',block);
									bool = true;
								}
								if(rep.nb_results.coll>0){
									if(bool) block.appendChild(document.createElement('br'));
									createItem(rep.nb_results.coll,'".$msg['collectivites_search']."','71',block);
									bool = true;
								}
								if(rep.nb_results.congres>0){
									if(bool) block.appendChild(document.createElement('br'));
									createItem(rep.nb_results.congres,'".$msg['congres_search']."','72',block);
								}
								div.appendChild(block);
							}
							document.getElementById('author_result').style.display = 'block';
						}
					}

					function createItem(nb_results,label,type,container){
						var span = document.createElement('span');
						span.innerHTML = '<strong>'+label+'</strong> '+ nb_results + ' ". $msg['results']." ';
						var a = document.createElement('a');
						a.setAttribute('href','#');
						a.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";
						if(a.addEventListener){
							a.addEventListener('click',function(){
								document.".$this->get_hidden_search_form_name().".action='".$this->get_form_action()."&tab=affiliate';
								document.".$this->get_hidden_search_form_name().".author_type.value = type;
								document.".$this->get_hidden_search_form_name().".submit();
								return false;
							},true);
						}else{
							a.addEvent('onclick',function(){
								document.".$this->get_hidden_search_form_name().".action='".$this->get_form_action()."&tab=affiliate';
								document.".$this->get_hidden_search_form_name().".author_type.value = type;
								document.".$this->get_hidden_search_form_name().".submit();
								return false;
							});
						}
						span.appendChild(a);
						container.appendChild(span);
					}
				</script>
			</blockquote>";
			$form = "<div class='search_result'>";
			$form .= $this->get_hidden_search_form();
			$form .= "</div>";
			print $form;
			print "</div>";
		}else{
			if ($this->get_nb_results()) {
				print "<div id=\"auteur\" name=\"auteur\">";
				for($i=0;$i<count($titre_resume);$i++)  {
					if($i==1) {
						print "<blockquote role='presentation'>";
					}
					print "<strong>$titre_resume[$i]</strong> ".$nb_result_resume[$i]." ".$msg['results']." ";
					// Le lien validant le formulaire est inséré avant le formulaire, cela évite les blancs à l'écran

					if ($nb_result_resume[$i]) {
						print "<a href=\"#\" onClick=\"
						document.forms.".$this->get_hidden_search_form_name().".count.value='".$nb_result_resume[$i]."';
						document.forms.".$this->get_hidden_search_form_name().".author_type.value='$link_type_resume[$i]';
						document.forms['".$this->get_hidden_search_form_name()."'].submit(); return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a>";
					}
					print "<br />";
				}
				if($i>1) {
					print "</blockquote>";
				}
				// tout bon, y'a du résultat, on lance le pataquès d'affichage

				$form = "<div class='search_result'>";
				$form .= $this->get_hidden_search_form();
				$form .= "</div>";
				print $form;
				print "</div>";
			}
		}
		$this->search_log($this->get_nb_results());
	}

    /**
     * Enregistrement des stats
     */
    protected function search_log($count) {
    	global $nb_results_tab;
		global $author_type;

		switch($author_type) {
			case '71':
				$nb_results_tab['collectivites'] = $count;
				break;
			case '72':
				$nb_results_tab['congres'] = $count;
				break;
			case '70':
			default:
				$nb_results_tab['physiques'] = $count;
				break;
		}
    }


    public function get_nb_results() {
        if(!isset($this->nb_results)) {
            $searcher = $this->get_searcher_instance();
            if(is_object($searcher)){
                $elements_ids = $searcher->get_result();
                if($elements_ids){
                    $query = "select count(id_authority) from authorities ";
                    if($this->author_type){
                        $query.= "join authors on author_id = num_object and author_type = '".$this->author_type."' ";
                    }
                    $query.= "where authorities.num_object AND authorities.type_object = ".$this->get_authority_type_const()." AND id_authority IN (".$elements_ids.")";
                    $result = pmb_mysql_query($query);
                    $this->nb_results = pmb_mysql_result($result, 0 , 0);
                }
            }
            if($this->nb_results) {
                $this->add_in_session();
            }
        }
        return $this->nb_results;
    }
}
?>