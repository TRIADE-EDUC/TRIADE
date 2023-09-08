/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.js,v 1.3 2017-08-28 14:41:34 jpermanne Exp $ */

require(["dojo/request"], function(request) {
    function classementGen_save(object_type, object_id, url_callback){
        var id = 'classementGen_' + object_type + '_' + object_id;
        var classement = document.getElementById(id).value;
        request.post('./ajax.php?module=ajax&categ=classementGen&action=update',{
            data : {
                object_type: object_type,
                object_id : object_id,
                classement_libelle : classement
            }
        }).then(function(data){
            if (url_callback) {
                window.location=url_callback;
            }
        });
    }
    window.classementGen_save = classementGen_save;
});