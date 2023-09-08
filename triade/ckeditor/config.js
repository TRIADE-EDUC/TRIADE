/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {

	// Define changes to default configuration here. For example:
	config.language = 'fr';
	config.filebrowserBrowseUrl = '/'+REPECOLE+'/kcfinder/browse.php?type=files';
   	config.filebrowserImageBrowseUrl = '/'+REPECOLE+'/kcfinder/browse.php?type=images';
   	config.filebrowserFlashBrowseUrl = '/'+REPECOLE+'/kcfinder/browse.php?type=flash';
   	config.filebrowserUploadUrl = '/'+REPECOLE+'/kcfinder/upload.php?type=files';
   	config.filebrowserImageUploadUrl = '/'+REPECOLE+'/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/'+REPECOLE+'/kcfinder/upload.php?type=flash';
	config.extraPlugins = 'scayt';
	
	if (colorGRAPH == '0') { config.uiColor = '#B2CADE'; }
	if (colorGRAPH == '1') { config.uiColor = '#FCE4BA'; }
	if (colorGRAPH == '2') { config.uiColor = '#CC9999'; }
	if (colorGRAPH == '3') { config.uiColor = '#99FF66'; }
	if (colorGRAPH == '4') { config.uiColor = '#FFE664'; }
	if (colorGRAPH == '5') { config.uiColor = '#B2CADE'; }
	if (colorGRAPH == '8') { config.uiColor = '#cba3c6'; }
	if (colorGRAPH == '10') { config.uiColor = '#739618'; }	

	config.toolbarGroups = [
    		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	    	{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' }
    	];
};
