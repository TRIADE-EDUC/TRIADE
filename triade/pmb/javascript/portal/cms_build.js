dojo.require('dojo.parser');
dojo.require('dijit.form.Button');
dojo.require('dojox.layout.ContentPane');
dojo.require('dojo.request.xhr');
dojo.require('dojo.topic');

var registry = dojo.require('dijit/registry');
var PMBDialog = dojo.require('apps/pmb/PMBDialog');
var PMBDojoxDialog = dojo.require('apps/pmb/PMBDojoxDialog');
var PMBDojoxDialogSimple = dojo.require('apps/pmb/PMBDojoxDialogSimple');
var ContentPane = dojo.require('dojox/layout/ContentPane');
var loaderActive = false;

function cms_clean_cache() {
    dojo.request.xhr(window.location.href + '&action=clean_cache').then(function(data) {
		dojo.topic.publish('dGrowl', pmbDojo.messages.getMessage('portal', 'cms_clean_cache_done'));
    });
}

function cms_clean_img_cache() {
    dojo.request.xhr(window.location.href + '&action=clean_cache_img').then(function(data) {
		dojo.topic.publish('dGrowl', pmbDojo.messages.getMessage('portal', 'cms_clean_cache_img_done'));
    });
}

function showLoader() {
	if (!loaderActive) {			
        window.dispatchEvent(new Event("showLoader"));
        loaderActive = true;
	}
}

function hiddenLoader() {
	if (loaderActive) {
		setTimeout(() => {
	        window.dispatchEvent(new Event("hiddenLoader"));
	        loaderActive = false;
	    }, 750);
    }
}

function cms_build_load_module(module, action, id, title) {
	destroyWidget();
	
	if (!title) {
		title = "";
	}

	if (!module.match('cms_module_')) {
		module = 'cms_module_' + module;
	}

	//définition du post !
	var post_datas = '&callback=window.cms_build_save_new_module';
	post_datas += '&cancel_callback=window.cms_build_cancel_new_module';
	post_datas += '&hidden_delete_button=1';
	post_datas += '&cms_build_info=' + document.getElementById('cms_build_info').value;

	//creates a new dialog
	var contentPane = dijit.byId('new_cms_build_container');
	if (!contentPane) {
		contentPane = new ContentPane({
			executeScripts: true,
			id: 'new_cms_build_container',
			onLoad: () => {
				hiddenLoader();
			}
		});
	}
	contentPane.placeAt(document.getElementById('add_frame_container'), 'first')

	var to_duplicate = 0;
	if (action == 'get_form_duplicate') {
		action = 'get_form';
		to_duplicate = 1;
	}

	showLoader();
	dojo.xhrPost({
		url: './ajax.php?module=cms&categ=module&elem=' + module + '&action=' + action + '&id=' + id,
		postData: post_datas,
		handelAs: 'text/html',
		load: (data) => {
			contentPane.set('content', data);
			if (to_duplicate) {
				document.getElementById('cms_module_common_module_id').value = '';
			}
		}
	});
}

function edit_module(module, id) {
	destroyWidget();
	
	var action = 'get_form';
	if (!module.match('cms_module_')) {
		module = 'cms_module_' + module;
	}

	if (id.match('cms_module_')) {
		id = id.replace(/cms_module_[^_]+_/, '');
	}
	id = parseInt(id);
	if (!id || isNaN(id) || id == 0) {
		return;
	}

	//définition du post !
	var post_datas = '&callback=window.cms_build_save_module';
	post_datas += '&cancel_callback=window.cms_build_cancel_module';
	post_datas += '&delete_callback=window.cms_build_delete_module';
	post_datas += '&cms_build_info=' + document.getElementById('cms_build_info').value;

	//creates a new dialog
	var contentPane = dijit.byId('cms_build_container');
	if (!contentPane) {
		contentPane = new ContentPane({
			executeScripts: true,
			id: 'cms_build_container',
			onLoad: () => {
				hiddenLoader();
			}
		});
	}
	contentPane.placeAt(document.getElementById('frame_container'), 'first')

	var to_duplicate = 0;
	if (action == 'get_form_duplicate') {
		action = 'get_form';
		to_duplicate = 1;
	}

	showLoader();
	dojo.xhrPost({
		url: './ajax.php?module=cms&categ=module&elem=' + module + '&action=' + action + '&id=' + id,
		postData: post_datas,
		handelAs: 'text/html',
		load: (data) => {
			contentPane.set('content', data);
			if (to_duplicate) {
				document.getElementById('cms_module_common_module_id').value = '';
			}
		}
	});
}

function cms_build_cancel_new_module() {
    window.dispatchEvent(new Event("cancel_new_module"));
}

function cms_build_cancel_module() {
    window.dispatchEvent(new Event("cancel_module"));
}

function cms_build_delete_callback() {
	destroyWidget();
}

function cms_build_delete_module() {
    window.dispatchEvent(new Event("delete_module"));
}

function cms_build_save_new_module(data) {
    window.dispatchEvent(new CustomEvent("save_new_module", {detail: {...data}}));
}

function cms_build_save_module(data) {
	publish_save(data);
}

function publish_save(data) {
    window.dispatchEvent(new CustomEvent("save_module", {detail: {...data}}));
}

function destroyWidget() {
	
	const DELETE_WIDGETS = [
		'datasource_form',
		'view_form',
		'cms_build_container',
		'new_cms_build_container',
	];
	var widgets = new Array();
	
	registry.forEach(widget => {
		if (widget.id.match('cms_module_') || DELETE_WIDGETS.includes(widget.id)) {
			widgets.push(widget.id);
			widget.destroyRecursive();
			widget.destroy();
		}
	})
	
	for (let i = 0; i < widgets.length; i++) {
		registry.remove(widgets[i]);
	}
}

