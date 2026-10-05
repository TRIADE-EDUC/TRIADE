<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_locking.tpl.php,v 1.7 2019/05/27 14:27:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $entity_locked_form, $entity_unload_script, $save_error_message;
global $entity_polling_script, $msg, $current_module, $base_path, $charset;

$timer_lock_script = "
    require(['dojo/topic', 'dojo/dom'], function(topic, dom) {
        var lockingTime = !!entity_locking_time!!;
        if((lockingTime - (new Date().getTime()/1000)) > 0){
            var time = new Date(lockingTime*1000);
            topic.publish('dGrowl', '<div class=\'entity_lock_timer\' id=\'entity_lock_timer\'></div>', {sticky:true});
            var padVal = function(val){
                return val.length < 2 ? [0,val].join('') : val;
            }
            dom.byId('entity_lock_timer').innerHTML = showTime();    
            setInterval(function() {
                dom.byId('entity_lock_timer').innerHTML = showTime();    
            },1000);
        }
        function showTime (){
            var currentDate = new Date();
            var currentTime = currentDate.getTime();
            var diff = time.getTime() - currentTime;
            if (diff > 0) {
                var durationLeft = new Date(diff);
                var days = diff > 86400000 ? (diff - (diff % 86400000)) / 86400000 + ' " .$msg['abonnements_periodicite_unite_jour']. " ' : '';
                var hours = durationLeft.getUTCHours().toString();
                var minutes = durationLeft.getUTCMinutes().toString();
                var seconds = durationLeft.getUTCSeconds().toString();
                    
                var timerContent = days + padVal(hours)+':'+padVal(minutes)+':'+padVal(seconds);
                dom.byId('entity_lock_timer').innerHTML = timerContent;
                return timerContent;
            }else{
                topic.publish('LockingTimer', 'lockingTimerOver');
                return '00:00:00';
            }
        }
    });
";

$entity_locked_form = "
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3>".$msg['entity_currently_locked_title']."</h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='left'>
            <p id='entity_locked_message'>".$msg['entity_currently_locked']."<span id='entity_lock_timer'></span></p>
        </div>
        <div id='availability_container' class='right'>
            <img data-type='unavailable' src='".$base_path."/images/sauv_failed.png'/>
        </div>
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' id='lock_return_button' onclick='history.go(-1);' type='button' value='".$msg['654']."'  />
	</div>
    <div class='right' id='edit_button_container'></div>
<div class='row'></div>
</form>
<script type='text/javascript'>
    require(['dojo/topic', 
'dojo/_base/lang', 
'dojo/dom-construct', 
'dojo/dom', 
'dojo/ready', 
'dojo/request/xhr',
'dojo/on',
'dijit/registry'], function(topic, lang, domConstruct, dom, ready, xhr, on, registry){
        ready(function(){
            if(typeof registry.byId('content_infos') != 'undefined'){
                domConstruct.destroy('lock_return_button');
            }
            setInterval(function(){
                 xhr('./ajax.php?module=ajax&categ=entity_locking&sub=check', {
                    method:'POST',
                    data: 'entity_id=!!entity_id!!&entity_type=!!entity_type!!&user_id=!!entity_locked_userid!!',
                    handleAs: 'json',
                }).then(function(response){
                    if(response.status){
                        if(document.querySelector('div[id=\"availability_container\"] > img[data-type=\"unavailable\"')){
                            domConstruct.empty('availability_container');
                            domConstruct.create('img', {'data-type': 'available', src: '".$base_path."/images/sauv_succeed.png'}, 'availability_container', 'last');
                            dom.byId('entity_locked_message').innerHTML = '".htmlentities($msg['entity_unlocked'], ENT_QUOTES, $charset)."';
                            if(dom.byId('refresh_button')){
                                domConstruct.destroy('refresh_button');
                            }
                            if(typeof registry.byId('content_infos') != 'undefined'){
                                domConstruct.create('input', {type: 'button', id:'refresh_button', class:'bouton', value: '".$msg['actualiser']."'}, 'edit_button_container');
                                on(dom.byId('refresh_button'), 'click', function(){
                                    registry.byId('content_infos').refresh();
                                }); 
                            }else{
                                domConstruct.create('input', {type: 'submit', id:'refresh_button', class:'bouton', value: '".$msg['actualiser']."'}, 'edit_button_container');
                            }
                        }
                    }else{
                        if(document.querySelector('div[id=\"availability_container\"] > img[data-type=\"available\"')){
                             domConstruct.empty('availability_container');
                             domConstruct.create('img', {'data-type': 'unavailable', src: '".$base_path."/images/sauv_failed.png'}, 'availability_container', 'last');
                             dom.byId('entity_locked_message').innerHTML = response.message;
                        }
                    }
                });
            }, !!entity_locked_refresh_time!!);
//             }, 1000);
        });
    });

</script>
";

$entity_unload_script = "
<script type='text/javascript'>

    require(['dojo/ready', 'dojo/request/xhr', 'dojo/on', 'dojo/topic', 'dojo/dom'], function(ready, xhr, on, topic, dom) {
        ready(function() {
        
            window.addEventListener('unload', function(event){
                var xhrReq = new XMLHttpRequest();
                xhrReq.open('POST', './ajax.php?module=ajax&categ=entity_locking&sub=unlock_entity&entity_id=!!entity_id!!&entity_type=!!entity_type!!&user_id=!!entity_locked_userid!!', false);
                xhrReq.setRequestHeader('Content-Type', 'text/plain;charset=UTF-8');
                xhrReq.send();
                xhrReq.onreadystatechange = function(e) {
                    if (xhr.readyState === 4) {
                      if (xhr.status === 200) {
                      
                        }
                    }
                }
            });   
                
           ".$timer_lock_script."
        });
    });
</script>
";

$entity_polling_script = "
<script type='text/javascript'>
    require(['dojo/ready', 'dojo/request/xhr', 'dojo/on', 'dojo/topic', 'dojo/dom'], function(ready, xhr, on, topic, dom) {
        ready(function() {
            setInterval(function(){
                xhr('./ajax.php?module=ajax&categ=entity_locking&sub=poll', {
                    method:'POST',
                    data: 'entity_id=!!entity_id!!&entity_type=!!entity_type!!&user_id=!!entity_locked_userid!!',
                });
            },!!entity_locked_refresh_time!!);            
        });
    });
</script>
";
    
    

$save_error_message = "
    <form class='form-$current_module' name='search' method='post' action='!!action!!'>
        <h3>".$msg['locked_entity_save_error']."</h3>
        <div class='form-contenu'>
        	<div class='row'>
        		<p>".$msg['entity_currently_locked']."<span id='entity_lock_timer'></span></p>
        	</div>
        </div>
        <div class='row'>
        	<div class='left'>
        		<input class='bouton' onclick='history.go(-1);' type='button' value='".$msg['654']."'  />
        	</div>
        <div class='row'></div>
    </form>
    <script type='text/javascript'>".$timer_lock_script."</script>
";

?>