<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_cb_ui.class.php,v 1.4 2022/06/13 10:20:08 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once $class_path . '/onto/contribution/onto_contribution_datatype_small_text_ui.class.php';

/**
 * class onto_contribution_datatype_cb_ui
 */
class onto_contribution_datatype_cb_ui extends onto_contribution_datatype_small_text_ui
{
    
    public static function get_scripts()
    {
        $scripts = parent::get_scripts();
        $scripts .= self::get_script_cb();
        return $scripts;
    }

    public function get_script_cb()
    {
        global $base_path, $msg, $charset;

        return "
            var el = document.querySelector(\"input[id*='cb_0_value']\");

            el.addEventListener('blur', event => {
				var url = '" . $base_path . "/ajax.php?module=ajax&categ=contribution&sub=get_verfi_cb&cb='+el.value;
				fetch(url, {
					method : 'GET',
				}).then(response => {
					if (response.ok) {
                        response.text().then((valid)=> {
                            var input = document.querySelector(\"input[id*='cb_0_value']\");

                            var cb_img_valid = document.getElementById('cb_img_valid');
                            if(cb_img_valid){
                                cb_img_valid.remove()
                            }

                            var cb_text_valid = document.getElementById('cb_contribution');
                            if(cb_text_valid){
                                cb_text_valid.remove()
                            }

                            var hidden_input_cb = document.getElementById('hidden_input_cb');
                            if(!hidden_input_cb){
                                var hidden_input_cb = document.createElement('input');
                                hidden_input_cb.type = 'hidden';
                                hidden_input_cb.id = 'hidden_input_cb';
                                input.parentNode.insertBefore(hidden_input_cb, input.nextSibling);
                            }
                            
                            if(valid == 1 || input.value == ''){
                                var img = document.createElement('img');
                				img.setAttribute('id', 'cb_img_valid');
                				img.setAttribute('src', '" . $base_path . "/images/cross.png');
                				img.setAttribute('class', 'cb_contribution_img_cross');
                                input.parentNode.insertBefore(img, input.nextSibling);

                                var text = document.createElement('p');
                				text.setAttribute('id', 'cb_contribution');
                				text.setAttribute('class', 'cb_contribution_text');
                                text.innerText = '" . htmlentities($msg['contribution_code_barre_not_valid'], ENT_QUOTES, $charset) . "';
                                img.parentNode.insertBefore(text, img.nextSibling);

                				hidden_input_cb.setAttribute('value', false);
                            } else {
                                var img = document.createElement('img');
                				img.setAttribute('id', 'cb_img_valid');
                				img.setAttribute('src', '" . $base_path . "/images/tick.gif');
                				img.setAttribute('class', 'cb_contribution_img_tick');
                                input.parentNode.insertBefore(img, input.nextSibling);

                				hidden_input_cb.setAttribute('value', true);
                            }
					    });
					} else {
						console.log('fetch error');
					}
				}).catch(function(error) {
					console.log('error catch');
				});
            });
        ";
    }

    public static function get_validation_js($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $msg;

        return '{
			"message": "' . addslashes($property->get_label()) . '",
			"valid" : true,
			"nb_values": 0,
			"error": "",
			"values": new Array(),
			"check": function(){
				this.values = new Array();
				this.nb_values = 0;
				this.valid = true;
				var nodeOrder = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_new_order");
                if (nodeOrder) {
                    var order = nodeOrder.value;
    				for (var i=0; i<=order ; i++){
    					var label = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_"+i+"_value");
                        if(!label) continue;
    					var key = 0;
    					if((label.value != "") || (label.defaultValue != "")){
    						if(!this.values[key]){
    							this.values[key] = 0;
    						}
    						this.values[key]++;
    					    
    						if(this.nb_values < this.values[key]) {
    							this.nb_values = this.values[key];
    						}
    					}
    				}
                }

				if(this.nb_values < ' . $restrictions->get_min() . '){
					this.valid = false;
					this.error = "min";
				}

				if(' . $restrictions->get_max() . ' != -1 && this.nb_values > ' . $restrictions->get_max() . '){
					this.valid = false;
					this.error = "max";
				}
                
                var hidden_input_cb = document.getElementById("hidden_input_cb");
                if(hidden_input_cb && hidden_input_cb.value === false){
                    this.valid = false;
    				this.error = "cb_not_valid";
                }
                
				return this.valid;
			},
            
			"get_error_message": function(){
 				switch(this.error){
 					case "min" :
						this.message = "' . addslashes($msg['onto_error_no_minima']) . '";
						break;
					case "max" :
						this.message = "' . addslashes($msg['onto_error_too_much_values']) . '";
						break;
					case "cb_not_valid" :
						this.message = "' . addslashes($msg['contribution_code_barre_not_valid']) . '";
						break;
 				}
				this.message = this.message.replace("%s","' . addslashes($property->get_label()) . '");
				return this.message;
			}
		}';
    }

    public static function get_verfi_cb($cb)
    {
        $query = "SELECT * FROM exemplaires WHERE expl_cb = '" . $cb . "'";
        $result = pmb_mysql_query($query);
        return pmb_mysql_num_rows($result);
    }
} // end of onto_common_datatype_ui