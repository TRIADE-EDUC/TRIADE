<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_collections.class.php,v 1.29 2023/12/19 08:03:50 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once ($class_path . "/cms/cms_root.class.php");
require_once ($include_path . "/templates/cms/cms_collection.tpl.php");
require_once ($class_path . "/storages/storages.class.php");
require_once ($class_path . "/cms/cms_document.class.php");
require_once ($class_path . "/cms/cms_cache.class.php");

class cms_collections extends cms_root
{

    public $collections = array();

    public function __construct()
    {
        $this->fetch_datas();
    }

    protected function fetch_datas()
    {
        $this->collections = array();
        $query = "select id_collection from cms_collections order by collection_title asc";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->collections[] = new cms_collection($row->id_collection);
            }
        }
    }

    public function get_table($form_link = "")
    {
        global $msg, $charset;

        if (! $form_link) {
            $form_link = "./cms.php?categ=collection&sub=collection&action=edit";
        }

        $table = "
		<table>
			<tr>
				<th>" . $msg['cms_collection_title'] . "</th>
				<th>" . $msg['cms_collection_description'] . "</th>
				<th>" . $msg['cms_collection_nb_doc'] . "</th>
				<th></th>
			</tr>";
        for ($i = 0; $i < count($this->collections); $i ++) {
            $table .= "
			<tr class='" . ($i % 2 ? "odd" : "even") . "' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . ($i % 2 ? "odd" : "even") . "'\"  >
				<td onclick='document.location=\"" . $form_link . "&id=" . $this->collections[$i]->id . "\"' style='cursor: pointer'>" . htmlentities($this->collections[$i]->title, ENT_QUOTES, $charset) . "</td>
				<td>" . htmlentities($this->collections[$i]->description, ENT_QUOTES, $charset) . "</td>
				<td>" . htmlentities($this->collections[$i]->nb_doc, ENT_QUOTES, $charset) . "</td>
				<td><input type='button' class='bouton' onclick=\"document.location='./cms.php?categ=collection&sub=documents&collection_id=" . $this->collections[$i]->id . "'\" value='" . htmlentities($msg['cms_collection_edit_document'], ENT_QUOTES, $charset) . "'/></td>
			</tr>";
        }
        $table .= "
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton' value='" . $msg['cms_collection_add'] . "' onclick='document.location=\"" . $form_link . "&id=0\"'/>
		</div>";
        return $table;
    }

    public function process($action = "")
    {
        global $id;
        switch ($action) {
            case "edit":
                $collection = new cms_collection($id);
                print $collection->get_form();
                break;
            case "delete":
                $collection = new cms_collection($id);
                $collection->delete();
                $this->fetch_datas();
                print $this->get_table();
                break;
            case "save":
                $collection = new cms_collection();
                $collection->save_form();
                $this->fetch_datas();
                print $this->get_table();
                break;
            case "list":
            default:
                print $this->get_table();
                break;
        }
    }

    public function get_documents_form($selected = array())
    {
        global $msg, $charset;

        $list = "
		<div id='el2Child_0' class='row' movable='yes' title=\"" . htmlentities($msg['cms_documents_add'], ENT_QUOTES, $charset) . "\">
			<div class='row'>&nbsp;</div>
			<hr />
			<div id='collections_documents_form'>
				<script type='text/javascript'>
					function document_change_background(id){
						var doc = dojo.byId('document_'+id);
						if(doc.className == 'document_item'){
							doc.setAttribute('class','document_item document_item_selected');
						}else{
							doc.setAttribute('class','document_item');
						}
					}
					require(['dojo/ready', 'apps/pmb/PMBDojoxDialogSimple', 'apps/cms/CmsExpand'], function(ready, Dialog, CmsExpand) {
							ready(function() {
								openEditDialog = function(id){
									try{
										var dialog = dijit.byId('dialog_document');
									}catch(e){}
									if(!dialog){
										var dialog = new Dialog({title:'',id:'dialog_document'});
									}
									var path ='./ajax.php?module=cms&categ=documents&caller=editorial_form&action=get_form&id='+id;
									dialog.attr('href', path);
									dialog.startup();
									dialog.show();
								}
								var expand = new CmsExpand({expand_all_id :'expandall', collapse_all_id :'collapseall', context : 'collections_documents_form'});
							});
						});
					</script>
				<h3>" . htmlentities($msg['cms_documents_add']) . "</h3>
				<script src='./javascript/tablist.js'></script>
				<img id='expandall' style='border:0px' src='" . get_url_icon('expand_all.gif') . "'>
				<img id='collapseall' style='border:0px' src='" . get_url_icon('collapse_all.gif') . "'>";
        foreach ($this->collections as $collection) {
            $document_selected = $collection->get_documents_selected($selected);
            $data = array(
                "id" => $collection->id,
                "domId" => "collection" . $collection->id,
                "categ" => "collection",
                "action" => "get_documents_form",
                "params" => array(
                    "selected" => $selected
                )
            );
            $data = encoding_normalize::json_encode($data);

            $html = "
			<div class='row'></div>
            <div id='documents_selected_" . $collection->id . "'>";
            foreach ($document_selected as $doc) {
                $html .= "<input type='hidden' name='cms_documents_linked[]' value='" . $doc['id'] . "'/>";
            }
            $html .= "
            </div>
			<div id='collection" . $collection->id . "' class='notice-parent'>				
				<img data='" . $data . "' src='" . get_url_icon('plus.gif') . "' class='img_plus' name='imEx' id='collection" . $collection->id . "Img' title='" . htmlentities($msg['plus_detail'], ENT_QUOTES, $charset) . "' alt='" . htmlentities($msg['plus_detail'], ENT_QUOTES, $charset) . "' />
				<span class='notice-heada'>
					" . $collection->title . " (" . (count($document_selected) ? count($document_selected) . " / " : "") . $collection->nb_doc . " " . $msg['cms_document'] . ")" . "
				</span>
			</div>
			<div id='collection" . $collection->id . "Child' class='notice-child' style='margin-bottom:6px;display:none;width:94%'></div>";

            $list .= $html;
        }
        $list .= '</div>
		</div>';
        return $list;
    }

    public function get_collections_selector($name, $id)
    {
        global $msg, $charset;

        $select = "<select name='" . $name . "'>";
        foreach ($this->collections as $collection) {
            if ($collection->id == $id) {
                $selected = " selected='selected' ";
            } else
                $selected = "";
            $select .= "<option value='" . $collection->id . "'" . $selected . ">" . htmlentities($collection->title . " (" . $collection->nb_doc . " " . $msg['cms_document'] . ")", ENT_QUOTES, $charset) . "</option>";
        }
        $select .= "</select>";
        return $select;
    }
}

class cms_collection extends cms_root
{

    public $id = 0;

    public $title = "";

    public $description = "";

    public $num_parent = 0;

    public $num_storage = 0;

    public $nb_doc = 0;

    public $documents = array();

    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->fetch_datas_cache();
    }

    protected function fetch_datas_cache()
    {
        if ($tmp = cms_cache::get_at_cms_cache($this)) {
            $this->restore($tmp);
        } else {
            $this->fetch_datas();
            cms_cache::set_at_cms_cache($this);
        }
    }

    protected function restore($cms_object)
    {
        if (is_object($cms_object)) {
            foreach (get_object_vars($cms_object) as $propertieName => $propertieValue) {
                $this->{$propertieName} = $propertieValue;
            }
        }
    }

    public function fetch_datas()
    {
        $query = "select id_collection,collection_title, collection_description, collection_num_parent, collection_num_storage, count(id_document) as nb_doc from cms_collections left join cms_documents on document_type_object='collection' and document_num_object = id_collection where id_collection = " . $this->id . " group by id_collection";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $this->id = $row->id_collection;
            $this->title = $row->collection_title;
            $this->description = $row->collection_description;
            $this->num_parent = $row->collection_num_parent;
            $this->num_storage = $row->collection_num_storage;
            $this->nb_doc = $row->nb_doc;
        } else {
            $this->id = 0;
            $this->title = "";
            $this->description = "";
            $this->num_parent = 0;
            $this->nb_doc = 0;
        }
    }

    public function get_form($form_link = "")
    {
        global $cms_collection_form;
        global $msg, $charset;

        if (! $form_link) {
            $form_link = "./cms.php?categ=collection&sub=collection&action=save";
        }

        // lien pour la soumission du formulaire
        $form = str_replace("!!action!!", $form_link, $cms_collection_form);

        // id
        $form = str_replace("!!id!!", htmlentities($this->id, ENT_QUOTES, $charset), $form);
        // titre
        $form = str_replace("!!label!!", htmlentities($this->title, ENT_QUOTES, $charset), $form);
        // description
        $form = str_replace("!!comment!!", htmlentities($this->description, ENT_QUOTES, $charset), $form);

        // bouton supprimer
        if ($this->id) {
            $form = str_replace("!!form_title!!", $msg['cms_collection_edit'], $form);
            $form = str_replace("!!bouton_supprimer!!", "<input type='button' class='bouton' value=' " . $msg[63] . " ' onclick='confirmation_delete(\"&action=delete&id=" . $this->id . "\",\"" . htmlentities($this->title, ENT_QUOTES, $charset) . "\")' />", $form);
            $form .= confirmation_delete($form_link);
        } else {
            $form = str_replace("!!form_title!!", $msg['cms_collection_add'], $form);
            $form = str_replace("!!bouton_supprimer!!", "", $form);
        }

        $storages = new storages();
        $form = str_replace("!!storage_form!!", $storages->get_item_form($this->num_storage), $form);
        return $form;
    }

    public function save_form()
    {
        global $cms_collection_id, $cms_collection_title, $cms_collection_description, $cms_collection_num_parent, $storage_method;

        $this->id = intval($cms_collection_id);
        $this->title = stripslashes($cms_collection_title);
        $this->description = stripslashes($cms_collection_description);
        $this->num_parent = $cms_collection_num_parent;
        $this->num_storage = $storage_method;

        if ($this->id) {
            $query = "update cms_collections set ";
            $clause = "where id_collection = " . $this->id;
        } else {
            $query = "insert into cms_collections set ";
            $clause = "";
        }

        $query .= "
			collection_title = '" . addslashes($this->title) . "',
			collection_description = '" . addslashes($this->description) . "',
			collection_num_parent = '" . addslashes($this->num_parent) . "',
			collection_num_storage = '" . addslashes($this->num_storage) . "'";

        $result = pmb_mysql_query($query . $clause);
        if (! $this->id && $result) {
            $this->id = pmb_mysql_insert_id();
        }
        // $storages = new storages();
        // $storages->save_form("collection",$this->id);
    }

    public function delete()
    {
        global $msg;
        // on commence par vérifier si on a des documents dans la collection
        $query = "select id_document from cms_documents where document_type_object = 'collection' and document_num_object='" . $this->id . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            // oui, on affiche une erreur
            error_message($msg['540'], $msg['cms_collection_cant_delete_with_documents']);
        } else {
            $query = "delete from cms_collections where id_collection = " . $this->id;
            $result = pmb_mysql_query($query);
            if ($result) {
                return true;
            }
        }
        return false;
    }

    public function get_documents()
    {
        $this->documents = array();
        $query = "select id_document from cms_documents where document_type_object = 'collection' and document_num_object='" . $this->id . "' order by document_title, id_document";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->documents[] = new cms_document($row->id_document);
            }
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_documents_list()
    {
        global $msg, $charset;

        $this->get_documents();
        $list = "
		<div class='row' id='document_list'>";

        foreach ($this->documents as $document) {
            $list .= $document->get_item_render("openEditDialog");
        }
        $upload_max_filesize = ini_get('upload_max_filesize');
        if (empty($upload_max_filesize)) {
            $upload_max_filesize = 50000;
        } else {
            $upload_max_filesize = trim($upload_max_filesize);
            $last = strtolower($upload_max_filesize[strlen($upload_max_filesize) - 1]);
            $upload_max_filesize = (int) $upload_max_filesize;
            switch ($last) {
                // Le modifieur 'G' est disponible depuis PHP 5.1.0
                case 'g':
                    $upload_max_filesize *= 1024;
                case 'm':
                    $upload_max_filesize *= 1024;
                case 'k':
                    $upload_max_filesize *= 1024;
            }
        }
        $list .= "
            </div>
            <div class='row'>
                <input id='delete_batch_docnum' class='button' type='button' value='" . $msg['cms_document_delete_in_batch'] . "' onclick='delete_in_batch()'/>
            </div>
			<div id='dropTarget' class='dropTarget document_item'><p style='pointer-events: none;'>" . htmlentities($msg['drag_files_here'], ENT_QUOTES, $charset) . "</p></div>
			<link href='./javascript/dojo/snet/fileUploader/resources/uploader.css' rel='stylesheet' type='text/css'/>
			<script type='text/javascript'>
				require([
                        'dojo/_base/kernel',
                        'dojo/ready',
                        'snet/fileUploader/Uploader',
                        'apps/pmb/PMBDojoxDialogSimple', 
                        'dojo/request/xhr',
                        'dojo/_base/lang',
                    ], function(kernel, ready, Uploader, Dialog, xhr, lang) {
					ready(function() {
						var upl = new Uploader({
							url: './ajax.php?module=ajax&categ=storage&sub=upload&id=" . $this->num_storage . "&type=collection&id_collection=" . $this->id . "',
							dropTarget: 'dropTarget',
							maxKBytes: " . ($upload_max_filesize / 1024) . ",
							maxNumFiles: 10,
							append_div: 'document_list'
						});
						
						openEditDialog = function(id){
							try{
								var dialog = dijit.byId('dialog_document');
							}catch(e){}
							if(!dialog){
								var dialog = new Dialog({title:'',id:'dialog_document'});
							}
							var path ='./ajax.php?module=cms&categ=documents&action=get_form&id='+id;
							dialog.attr('href', path);     
							dialog.startup();
							dialog.show();
						}

                        delete_in_batch = function(){
                            if(confirm('" . $msg['cms_document_confirm_delete_in_batch'] . "')){
                                var checkboxes = document.querySelectorAll(\"input[class='delete_document']\");
                                var toDelete = '';
                                checkboxes.forEach(
                                    function(node){
                                        if(node.checked){
                                            if(toDelete){
                                                toDelete += ',' 
                                            }
                                            toDelete += node.value; 
                                        }
                                    }
                                );
                                if(toDelete){
                                    xhr.post('./ajax.php?module=cms&categ=documents&action=delete_in_batch&id=0', {
                        				data: {
                        					list_ids : toDelete,
                        				},
                        				handleAs: 'text'
                        			}).then(lang.hitch(this, function(content){
                                        if(content){
                                            alert(content);
                                        }
                                        window.location.reload();
                        			}));
                                }
                            }
                        }
					});
				});			
			</script>";

        return $list;
    }

    public function get_documents_selected($used)
    {
        $selected_list = array();
        $this->get_documents();
        foreach ($this->documents as $document) {
            if (in_array($document->get_id(), $used)) {
                $selected_list[] = array(
                    'id' => $document->get_id(),
                    'form' => $document->get_item_form(true)
                );
            }
        }
        return $selected_list;
    }

    public function get_documents_form($used)
    {
        global $msg, $charset;
        $this->get_documents();

        $form = "
		<div class='row document_list' id='document_list_" . $this->id . "'>";
        foreach ($this->documents as $document) {
            if (in_array($document->get_id(), $used)) {
                $selected = true;
            } else {
                $selected = false;
            }
            $form .= $document->get_item_form($selected);
        }
        $upload_max_filesize = ini_get('upload_max_filesize');
        if (! $upload_max_filesize) {
            $upload_max_filesize = 50000;
        } else {
            $upload_max_filesize = trim($upload_max_filesize);
            $last = strtolower($upload_max_filesize[strlen($upload_max_filesize) - 1]);
            switch ($last) {
                // Le modifieur 'G' est disponible depuis PHP 5.1.0
                case 'g':
                    $upload_max_filesize *= 1024;
                case 'm':
                    $upload_max_filesize *= 1024;
                case 'k':
                    $upload_max_filesize *= 1024;
            }
        }
        $form .= "
		</div>
		<div id='dropTarget_" . $this->id . "' class='document_item dropTarget'><p style='pointer-events: none;'>" . htmlentities($msg['drag_files_here'], ENT_QUOTES, $charset) . "</p></div>
		<link href='./javascript/dojo/snet/fileUploader/resources/uploader.css' rel='stylesheet' type='text/css'/>
		<script type='text/javascript'>
			require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader', 'apps/pmb/PMBDojoxDialogSimple'], function(kernel, ready, Uploader, Dialog) {
				ready(function() {
					var upl = new Uploader({
						url: './ajax.php?module=ajax&categ=storage&sub=upload&id=" . $this->num_storage . "&type=collection&id_collection=" . $this->id . "&from=form',
						dropTarget: 'dropTarget_" . $this->id . "',
						maxKBytes: " . ($upload_max_filesize / 1024) . ",
						maxNumFiles: 10,
						append_div: 'document_list_" . $this->id . "'
					});
				});
			});			
		</script>";
        return $form;
    }

    public function add_document($infos, $get_item_render = true, $from = '')
    {
        if (! trim($infos['title'])) {
            $infos['title'] = $infos['filename'];
        }

        $document = new cms_document();
        $document->set_title($infos['title'])
            ->set_filename($infos['filename'])
            ->set_mimetype($infos['mimetype'])
            ->set_filesize($infos['filesize'])
            ->set_vignette($infos['vignette'])
            ->set_url($infos['url'])
            ->set_path($infos['path'])
            ->set_create_date($infos['create_date'])
            ->set_num_storage($infos['num_storage'])
            ->set_type_object('collection')
            ->set_num_object($this->id);

        $document->save();

        if ($document->get_id()) {
            if ($get_item_render) {
                if ($from == "form") {
                    return $document->get_item_form(true);
                }
                return $document->get_item_render();
            }
            return true;
        }
        return false;
    }

    public function get_infos()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description
        );
    }
}



//TODO AR
