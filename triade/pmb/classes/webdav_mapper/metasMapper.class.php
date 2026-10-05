<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: metasMapper.class.php,v 1.7 2022/01/14 13:51:45 dgoron Exp $


class metasMapper {
	protected $config;
	protected $mimetype;
	protected $name;
	protected $metas;
	protected $data;
	protected $map;
	
	public function __construct($config){
		$this->config = $config;
		$this->mimetype = $mimetype;	
		$this->name = $name;	
		$this->metas = $metas;	
		$this->init_map();		
		$this->data=array();		
	}

	protected function init_map(){	
		global $pmb_keyword_sep;
		$this->map = [
			"meta"=>[
				"Title"=>[
					'field_type'=>'notice',
					'field'=>'tit1',
					'function'=>'concat',
					'params'=>[', ']
						
				],
				"Author"=>[
					'field_type'=>'authors',
					'field'=>'authors',
					'function'=>'',
					'params'=>[', ']
						
				],
				"Subject"=>[
					'field_type'=>'notice',
					'field'=>'tit4',
					'function'=>'concat',
					'params'=>[', ']
						
				],
				"CreateDate"=>[
					'field_type'=>'notice',
					'field'=>'year',
					'function'=>'creation_date',
					'params'=>[]
						
				],
				"PageCount"=>[
					'field_type'=>'notice',
					'field'=>'npages',
					'function'=>'concat',
					'params'=>[]
						
				],
				"Keywords"=>[
					'field_type'=>'notice',
					'field'=>'index_l',
					'function'=>'keywords',
					'params'=>[$pmb_keyword_sep]
						
				],					
			],			
		];
	}
	
	protected function concat($field,$new_field,$params){
		$sep=$params[0];
		if($field && $new_field)$field.=$sep;
		return $field.$new_field;
	}	

	protected function affecte($field,$new_field,$params){
		return $new_field;
	}
	
	protected function add($field,$new_field,$params){
		if(!$field){
			$field = array();
		}
		if($params){
			$f = explode($params[0],$new_field);
			$f = array_map(function($a){
				return trim(strtoupper($a));
			},$f);
			$field = array_merge($field,$f);
		}else{
			$field[]= trim(strtoupper($new_field));
		}
		
		return $field;
	}

	protected function creation_date($field,$new_field,$params){
		return substr($new_field,0,4);
	}

	protected function keywords($field,$new_field,$params){
		$keywords="";
		if(count($new_field))
		foreach($new_field as $keyword){
			if($keywords != "")	$keywords.= $params[0];
			$keywords.=$keyword;
		}
		return $keywords;
	}
	
	public function get_notice_id($metas, $mimetype="", $name="",$doindex=true){
		global $pmb_keyword_sep;
		
		$this->metas = $metas;
		$this->mimetype = $mimetype;
		$this->name = $name;
		
		$notice_id = 0;
		$this->data=array();
		$this->data['tit1'] = $this->data['tit4'] = $this->data['authors'] = $this->data['co_authors'] = $this->data['code'] = $this->data['npages'] = 
		$this->data['year'] = $this->data['index_l'] = $this->data['url'] = $this->data['thumbnail_content'] = $this->data['publisher'] = $this->data['n_resume'] = "";
		
		if($this->mimetype == "application/epub+zip"){
			//pour les ebook, on gère ca directement ici !

			$this->data['tit1'] = $this->metas['title'][0];
			$this->data['authors'] = $this->metas['creator'];
			$this->data['co_authors'] = $this->metas['contributor'];
			if($this->metas['identifier']['isbn']){
				$this->data['code'] = \formatISBN($this->metas['identifier']['isbn'],13);
			}else if($this->metas['identifier']['ean']){
				$this->data['code'] = \EANtoISBN($this->metas['identifier']['ean']);
				$this->data['code'] = \formatISBN($code,13);
			}
			if($this->metas['identifier']['uri']){
				$this->data['url'] = \clean_string($this->metas['identifier']['uri']);
			}
			$this->data['publisher'] = $this->metas['publisher'][0];
			$this->data['year'] = $this->metas['date'][0]['value'];
			if(strlen($this->data['year']) && strlen($this->data['year']) != 4){
				$this->data['year'] = \formatdate(detectFormatDate($this->data['year']));
			}
			$this->data['lang']= $this->metas['language'];
			$this->data['n_resume'] = implode("\n",$this->metas['description']);
			$this->data['keywords'] = implode($pmb_keyword_sep,$this->metas['subject']);
			$this->data['thumbnail_content']=$this->metas['thumbnail_content'];
		
		}else{	
			$this->exec_map();			
		}
		if(!$this->data['tit1']) $this->data['tit1'] = $this->name;
		
		$notice_id=$this->create_notice();
		$notice_id=$this->dedoublonne($notice_id);
		// Indexation
		if($doindex){
			\notice::majNoticesTotal($notice_id);
		}
		return $notice_id;
	}

	protected function exec_map(){
// 		debug($this->metas);
		foreach($this->map['meta'] as $map_field => $map){
			foreach($this->metas as $meta_field=>$meta_value){
				if($map_field==$meta_field){
					if(method_exists($this, $map['function'])){
						if($map['field_type'] == "notice"){
						    $this->data[$map['field']]=$this->{$map['function']}($this->data[$map['field']],$meta_value,$map['params']);
						}else{
						    $this->data[$map['field_type']][$map['field']]=$this->{$map['function']}($this->data[$map['field']],$meta_value,$map['params']);
						}
					}else{
						if($map['field_type'] == "notice"){
							$this->data[$map['field']]=$meta_value;
						}else{
							$this->data[$map['field_type']][$map['field']] = $meta_value;
						}
					}
					break;
				}
			}
		}
// 		debug($this->data,false);
	}
	
	
	protected function dedoublonne($notice_id){
		global $pmb_notice_controle_doublons;
		
		$sign = new \notice_doublon();
		$signature=$sign->gen_signature($notice_id);
		if($pmb_notice_controle_doublons){
			$q = "select notice_id from notices where signature='".$signature."' and notice_id != ".$notice_id." limit 1";
			$res = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($res)) {
				$r=pmb_mysql_fetch_object($res);
				// doublon existe, on supprime la notice créée
				\notice::del_notice($notice_id);
				return $r->notice_id;
			}
		}
		pmb_mysql_query("update notices set signature = '".$signature."' where notice_id = ".$notice_id);
		return $notice_id;
	
	}
		
	protected function create_notice(){
		global $pmb_type_audit;
		global $webdav_current_user_name,$webdav_current_user_id;
		
		$ed_1 = $num_serie = 0;
		if($this->data['publisher']){
			$ed_1 = editeur::import(array('name'=>$this->data['publisher']));
		}
		
		if($this->data['serie']){
			$num_serie = serie::import($this->data['serie']);
		}	
		
		$ind_wew = $this->data['tit1']." ".$this->data['tit4'];
		$ind_sew = \strip_empty_words($ind_wew) ;
		
		if(!$this->data['date_parution'] && $this->data['year']){
			$this->data['date_parution'] = extraitdate($this->data['year']);
		}
			
		$query = "insert into notices set
				tit1 = '".addslashes($this->data['tit1'])."',".
				($this->data['code'] ? "code='".$this->data['code']."',":"").
				"ed1_id = '".$ed_1."',".
				($this->data['tit4'] ? "tit4 = '".addslashes($this->data['tit4'])."'," : "").
				($this->data['npages'] ? "npages = '".addslashes($this->data['npages'])."'," : "").
				($this->data['index_l'] ? "index_l = '".addslashes($this->data['index_l'])."'," : "")."
				year = '".$this->data['year']."',
				tparent_id = '".$num_serie."',
				niveau_biblio='m',
				niveau_hierar='0',
				statut = '".$this->config['default_statut']."',
				index_wew = '".addslashes($ind_wew)."',
				index_sew = '".addslashes($ind_sew)."',
				n_resume = '".addslashes($this->data['n_resume'])."',
				lien = '".addslashes($url)."',".
				($this->data['date_parution'] ? "date_parution = '".addslashes($this->data['date_parution'])."'," : "")."
				index_n_resume = '".addslashes(\strip_empty_words($this->data['n_resume']))."',".
				($this->data['thumbnail_content'] ? "thumbnail_url = 'data:image/png;base64,".base64_encode($this->data['thumbnail_content'])."',":"").
				"create_date = ".($this->data['create_date'] ? "'".addslashes($this->data['create_date'])."'": "sysdate()").",
				update_date = sysdate()";
		pmb_mysql_query($query);

		$notice_id = pmb_mysql_insert_id();

		//traitement audit
		if ($pmb_type_audit) {
			$query = "INSERT INTO audit SET ";
			$query .= "type_obj='1', ";
			$query .= "object_id='$notice_id', ";
			$query .= "user_id='$webdav_current_user_id', ";
			$query .= "user_name='$webdav_current_user_name', ";
			$query .= "type_modif=1 ";
			pmb_mysql_query($query);
		}
			
		if(count($this->data['authors'])){
			$i=0;
			foreach($this->data['authors'] as $author){
				$aut = array();
				if($author['file-as']){
					$infos = explode(",",$author['file-as']);
					$aut = array(
							'name' => $infos[0],
							'rejete' => $infos[1],
							'type' => 70
					);
				}
				if(!$aut['name']){
					$aut = array(
							'name' => $author['value'],
							'type' => 70
					);
				}
				$aut_id = \auteur::import($aut);
				if($aut_id){
					$query = "insert into responsability set
							responsability_author = '".$aut_id."',
							responsability_notice = '".$notice_id."',
							responsability_type = '0'";
					pmb_mysql_query($query);
					$i++;
				}
			}
		}
		if(count($this->data['co_authors'])){
			foreach($this->data['co_authors'] as $author){
				$aut = array();
				if($author['file-as']){
					$infos = explode(",",$author['file-as']);
					$aut = array(
							'name' => $infos[0],
							'rejete' => $infos[1],
							'type' => 70
					);
				}
				if(!$aut['name']){
					$aut = array(
							'name' => $author['value'],
							'type' => 70
					);
				}
				$aut_id = \auteur::import($aut);
				if($aut_id){
					$query = "insert into responsability set
							responsability_author = '".$aut_id."',
							responsability_notice = '".$notice_id."',
							responsability_type = '0',
							repsonsability_ordre = '".$i."'";
					pmb_mysql_query($query);
					$i++;
				}
			}
		}

		if(count($this->data['cp'])){
			foreach($this->data['cp'] as $cp_name => $values){
				if(is_array($values)){
					foreach($values as $value){
						$this->import_cp($notice_id, $cp_name, $value);
					}
				}else{
					$this->import_cp($notice_id, $cp_name, $values);
				}
			}
		}
		return $notice_id;
	}
	
	protected function import_cp($notice_id,$cp_name,$val){
		$req = " select idchamp, type, datatype from notices_custom where name='".$cp_name."'";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			$perso = pmb_mysql_fetch_object($res);
			if($perso->idchamp){
				if($perso->type == 'list'){
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($val)."' and notices_custom_champ=$perso->idchamp";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=$perso->idchamp";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($val)."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$req="SELECT 1 FROM notices_custom_values WHERE notices_custom_champ='".$perso->idchamp."' AND notices_custom_origine='".$notice_id."' AND notices_custom_".$perso->datatype."='".$value."'";
					if(($res=pmb_mysql_query($req)) && !pmb_mysql_num_rows($res)){//Pour éviter d'importer deux fois la même chose (c'était le cas en z39.50 ou connecteur lorsque l'on importe les notices une à une
						$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".$value."')";
						pmb_mysql_query($requete);
					}
				} else {
					$req="SELECT 1 FROM notices_custom_values WHERE notices_custom_champ='".$perso->idchamp."' AND notices_custom_origine='".$notice_id."' AND notices_custom_".$perso->datatype."='".addslashes($val)."'";
					if(($res=pmb_mysql_query($req)) && !pmb_mysql_num_rows($res)){//Pour éviter d'importer deux fois la même chose (c'était le cas en z39.50 ou connecteur lorsque l'on importe les notices une à une
						$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".addslashes($val)."')";
						pmb_mysql_query($requete);
					}
				}
			}
		}
	}
	protected function clean($str){
		$str = strtr($str, 'áàâäãåçéèêëíìîïñóòôöõúùûüıÿ', 'aaaaaaceeeeiiiinooooouuuuyy');
		return mb_strtoupper(strtr($str, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜİ', 'AAAAAACEEEEEIIIINOOOOOUUUUY'));
	}

}