<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatches.class.php,v 1.6 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/docwatch/docwatch_watch.class.php");

class docwatches extends scheduler_task {
		
	public function execution() {
		global $msg, $charset;
	
		if (SESSrights & DSI_AUTH) {
		    if($this->statut == scheduler_task::WAITING) {
		        $this->send_command(scheduler_task::RUNNING);
			}
			if($this->statut == scheduler_task::RUNNING) {
				if (method_exists($this->proxy, 'pmbesDocwatches_update')) {
					$docwatchesUpdated = $this->proxy->pmbesDocwatches_update();
					$this->update_progression(100);
					$this->report[] = "<tr><th colspan=4 >".$this->msg["planificateur_docwatches_title"]."</th></tr>";
					
					
					if (is_array($docwatchesUpdated)){
						foreach ($docwatchesUpdated as $idWatch=>$datasources){
							if(is_array($datasources)){
								$docwatch = new docwatch_watch($idWatch);								
								$this->report[] = "	<tr><td colspan=4 ><h1>".$docwatch->get_title()."</h1></td></tr>
													<tr>
														<td colspan=4 ><strong>".$this->msg["planificateur_docwatches_datasources_synced"]."</strong></td>
													</tr>
													<tr>
														<td><strong>".$this->msg["planificateur_docwatches_datasources_title"]."</strong></td>
														<td><strong>".$this->msg["planificateur_docwatches_datasources_last_date"]."</strong></td>
														<td><strong>".$this->msg["planificateur_docwatches_datasources_type"]."</strong></td>
														<td><strong>".$this->msg["planificateur_docwatches_datasources_ttl"]."</strong></td>
													</tr>";
								
								foreach ($datasources as $datasource){
									
									$query = "	SELECT datasource_type, datasource_title, datasource_ttl 
												FROM docwatch_datasources
												WHERE id_datasource =".$datasource['id'];
									
									$sql_result = pmb_mysql_query($query);
									
									if($sql_result){
										while($row=pmb_mysql_fetch_object($sql_result)){
											$this->report[] = "	<tr>
																	<td>".$row->datasource_title."</td>
																	<td>".htmlentities(formatdate($datasource['last_date'],1),ENT_QUOTES,$charset)."</td>
																	<td>".$msg['dsi_'.$row->datasource_type]."</td>
																	<td>".$row->datasource_ttl."</td>		
																</tr>";
										}
									}
								}
							}							
						}
					}
				}else {
					$this->add_function_rights_report("update","pmbesDocwatches");
				}
			}			
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}
