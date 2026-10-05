<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_collections.class.php,v 1.11 2023/08/17 09:47:53 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once ($class_path . "/cms/cms_root.class.php");
require_once ($include_path . "/templates/cms/cms_collection.tpl.php");
require_once ($class_path . "/storages/storages.class.php");
require_once ($class_path . "/cms/cms_document.class.php");

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
        global $msg;

        $list = "
		<div class='row'>&nbsp;</div>
		<hr />
		<script>
			function document_change_background(id){
				var doc = dojo.byId('document_'+id);
				if(doc.className == 'document_item'){
					doc.setAttribute('class','document_item document_item_selected');
				}else{
					doc.setAttribute('class','document_item');
				}
			}
		</script>
		<h3>" . htmlentities($msg['cms_documents_add']) . "</h3>";
        foreach ($this->collections as $collection) {
            $coll_form = "<div class='row'>&nbsp;</div>";
            $coll_form = $collection->get_documents_form($selected);
            $coll_form .= "<div class='row'>&nbsp;</div>";
            $list .= gen_plus('collection' . $collection->id, $collection->title . " (" . $collection->nb_doc . " " . $msg['cms_document'] . ")", $coll_form);
        }
        return $list;
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
        $query = "select id_collection,collection_title, collection_description, collection_num_parent, collection_num_storage, count(id_document) as nb_doc from cms_collections left join cms_documents on document_type_object='collection' and document_num_object = id_collection where id_collection = '" . $this->id . "' group by id_collection";
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
        // TODO vérification des documents
        $query = "delete from cms_collections where id_collection = " . $this->id;
        $result = pmb_mysql_query($query);
        if ($result) {
            return true;
        }
        return false;
    }

    public function get_documents()
    {
        $this->documents = array();
        $query = "select id_document from cms_documents where document_type_object = 'collection' and document_num_object='" . $this->id . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->documents[] = new cms_document($row->id_document);
            }
        }
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
			<div id='dropTarget' class='dropTarget document_item'><p style='pointer-events: none;'>" . htmlentities($msg['drag_files_here'], ENT_QUOTES, $charset) . "</p></div>
			<link href='./javascript/dojo/snet/fileUploader/resources/uploader.css' rel='stylesheet' type='text/css'/>
			<script>
				require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader', 'dojox/widget/DialogSimple'], function(kernel, ready, Uploader, Dialog) {
					ready(function() {
						var upl = new Uploader({
							url: './ajax.php?module=ajax&categ=storage&sub=upload&id=" . $this->num_storage . "&type=collection&id_collection=" . $this->id . "',
							dropTarget: 'dropTarget',
							maxKBytes: " . $upload_max_filesize . ",
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
					});
				});			
			</script>";

        return $list;
    }

    public function get_documents_form($used)
    {
        global $msg, $charset;
        $this->get_documents();

        $form = "
		<div class='row document_list' id='document_list_" . $this->id . "'>";
        foreach ($this->documents as $document) {
            if (in_array($document->id, $used)) {
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
		<script>
			require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader', 'dojox/widget/DialogSimple'], function(kernel, ready, Uploader, Dialog) {
				ready(function() {
					var upl = new Uploader({
						url: './ajax.php?module=ajax&categ=storage&sub=upload&id=" . $this->num_storage . "&type=collection&id_collection=" . $this->id . "',
						dropTarget: 'dropTarget_" . $this->id . "',
						maxKBytes: " . $upload_max_filesize . ",
						maxNumFiles: 10,
						append_div: 'document_list_" . $this->id . "'
					});
				});
			});			
		</script>";
        return $form;
    }

    public function add_document($infos, $get_item_render = true)
    {
        $result = "";

        $query = "insert into cms_documents set 
			document_title = '" . addslashes($infos['title']) . "',
			document_filename = '" . addslashes($infos['filename']) . "',
			document_mimetype = '" . addslashes($infos['mimetype']) . "',
			document_filesize = '" . addslashes($infos['filesize']) . "',	
			document_vignette = '" . addslashes($infos['vignette']) . "',	
			document_url = '" . addslashes($infos['url']) . "',
			document_path = '" . addslashes($infos['path']) . "',
			document_create_date = '" . addslashes($infos['create_date']) . "',	
			document_num_storage = '" . intval($infos['num_storage']) . "',
			document_type_object = 'collection',
			document_num_object = '" . $this->id . "'	
		";
        if (pmb_mysql_query($query)) {
            if ($get_item_render) {
                $document = new cms_document(pmb_mysql_insert_id());
                $result = $document->get_item_render();
            } else {
                $result = true;
            }
        } else {
            $result = false;
        }
        return $result;
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