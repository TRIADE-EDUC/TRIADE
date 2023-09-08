<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_view_django_by_categories.class.php,v 1.8 2016-09-20 10:25:41 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_view_django_by_categories extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "{% for category in categories %}
<div>
 <h3>{{category.title}}</h3>
  <ul>
   {% for watch in category.watches %}
    <li><a href='{{watch.rss_link}}' target='_blank'>{{watch.title}}</a></li>
   {% endfor %}
  </ul>
  <!-- Cascade pour la recursion....-->		
  {% for sub_category in category.children %}
   <div>
    <h4>{{sub_category.title}}</h4>
    <ul>
     {% for watch in sub_category.watches %}
      <li><a href='{{watch.rss_link}}' target='_blank'>{{watch.title}}</a></li>
     {% endfor %}
    </ul>
    <!-- Cascade pour la recursion....-->
    {% for sub_category2 in sub_category.children %}
	 <div>
      <h4>{{sub_category2.title}}</h4>
      <ul>
       {% for watch in sub_category2.watches %}
        <li><a href='{{watch.rss_link}}' target='_blank'>{{watch.title}}</a></li>
       {% endfor %}
      </ul>
      <!-- Cascade pour la recursion....-->
      {% for sub_category3 in sub_category2.children %}
				
      {% endfor %}
   </div>			
    {% endfor %}
   </div>
  {% endfor %}
</div>
{% endfor %}
<div>
 <h3>Hors Classement</h3>
 <ul>
   {% for watch in watches %}
    <li><a href='{{watch.rss_link}}' target='_blank'>{{watch.title}}</a></li>
   {% endfor %}
 </ul>
</div>";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_watcheslist_view_django_by_categories_link'>".$this->format_text($this->msg['cms_module_watcheslist_view_django_by_categories_build_watch_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("watch");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("watch");
		return parent::save_form();
	}
	
	public function render($datas){
		$newdatas = $new_datas['categories'] = array();
		//récupération des ids des classements de veilles...
		$categories = array();		
		for($i=0 ; $i<count($datas['watches']) ; $i++){
			if($datas['watches'][$i]['category']){
				$categories[] = $datas['watches'][$i]['category']['id']*1;
			}else{
				$newdatas['watches'][]=$datas['watches'][$i];
			}
			$datas['watches'][$i]['link'] = $this->get_constructed_link('watch',$datas['watches'][$i]['id']);
		}
		$categories = array_unique($categories);
		//on récupère les parents jusque la racine....
		$this->get_parent($categories);
		//on regénère une structure de données..;
		$newdatas['categories']= $this->set_children(0,$datas);
	
		return parent::render($newdatas);
	}
	
	protected function set_children($id,$watches){
		$categories = $category = array();
		if(is_array($this->categories) && count($this->categories)){
			foreach($this->categories as $id_category => $infos){
				if($infos['parent'] == $id){
					$category = array(
						'id' => $id_category,
						'title' => $this->categories[$id_category]['title']
					);
					for($i=0 ; $i<count($watches['watches']) ; $i++){
						if($watches['watches'][$i]['category'] && $watches['watches'][$i]['category']['id'] == $id_category){
							if(!isset($category['watches'])){
								$category['watches'] = array();
							}
							$category['watches'][] = $watches['watches'][$i];
						}
					}
					$children = $this->set_children($id_category,$watches);
					if(count($children)){
						$category['children'] = $children;
					}
					$categories[] = $category;
				}
			}
		}
		return $categories;
	}
	
	protected function get_parent($categories){
		global $dbh;
		if(is_array($categories) && count($categories)){
			$query = "select id_category, category_title, category_num_parent from docwatch_categories where id_category in ('".implode("','",$categories)."') order by category_title";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->categories[$row->id_category] = array(
						'title' => $row->category_title,
						'parent' => $row->category_num_parent
					);
					if($row->category_num_parent!= 0){
						$this->get_parent(array($row->category_num_parent));
					}
				}
			}
		}
	}
	
	public function get_format_data_structure(){	
		
		$datasource_watch = new cms_module_watch_datasource_watch();
		$datas = array(
			array(
				'var' => "categories",
				'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_categories_desc'],
 				'children' => array(
 					array(
 						'var' => "categories[i].id",
 						'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_categories_id_desc'],
 								
 					),
 					array(
 						'var' => "categories[i].title",
 						'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_categories_title_desc'],
 					),
 					array(
 						'var' => "categories[i].watches",
 						'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_categories_watches_desc'],
 						'children' => $this->prefix_var_tree(docwatch_watch::get_format_data_structure(),"categories[i].watches[j]")
 					),
 					array(
 						'var' => "categories[i].children",
 						'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_categories_children_desc']
 					)
				),
				array(
					'var' => "watches",
					'desc' => $this->msg['cms_module_watcheslist_view_django_by_categories_watches_desc'],
					'children' => $this->prefix_var_tree(docwatch_watch::get_format_data_structure(),"watches[i]")
				),
			)
		);
		$datas[0]['children'][2]['children'][] = array(
				'var' => "categories[i].watches[j].link",
				'desc'=> $this->msg['cms_module_watcheslist_view_django_by_categories_watch_link_desc']
		);
		
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}