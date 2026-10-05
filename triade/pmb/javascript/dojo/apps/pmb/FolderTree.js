// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FolderTree.js,v 1.2 2022/09/14 14:38:50 dbellamy Exp $


define([
    "dojo/_base/declare",
    "dijit/Tree",
    "dojo/topic",
    "dojo/_base/lang",
    'dojo/request/xhr',
    "dojo/when",
    'dijit/tree/ObjectStoreModel',
    'dojo/store/Observable',
    'apps/pmb/Store',
    "dojo/Deferred"
], function(declare, Tree, topic, lang, xhr, when, ObjectStoreModel, Observable, Store, Deferred) {


    // Dérivation du TreeNode 
    // permet d'injecter un tooltip HTML
    var MyTreeNode = declare(Tree._TreeNode, {
        _setLabelAttr: { node: "labelNode", type: "innerHTML" }
    });

    // Dérivation du Tree
    // pour tooltip HTML et l'envoi d'un event après une modification de l'arbre (pb de redimensionnement dans les frames)
    var FolderTree = declare([Tree], {
        showRoot: false,

        //Dérivation permettant de balancer un event pour cabler un resize du widget parent
        _startPaint: function(/*Promise|Boolean*/ p) {
            // summary:
            //		Called at the start of an operation that will change what's displayed.
            // p:
            //		Promise that tells when the operation will complete.  Alternately, if it's just a Boolean, it signifies
            //		that the operation was synchronous, and already completed.

            this._outstandingPaintOperations++;
            if (this._adjustWidthsTimer) {
                this._adjustWidthsTimer.remove();
                delete this._adjustWidthsTimer;
            }

            var oc = lang.hitch(this, function() {
                this._outstandingPaintOperations--;

                if (this._outstandingPaintOperations <= 0 && !this._adjustWidthsTimer && this._started) {
                    // Use defer() to avoid a width adjustment when another operation will immediately follow,
                    // such as a sequence of opening a node, then it's children, then it's grandchildren, etc.
                    this._adjustWidthsTimer = this.defer("_adjustWidths");
                }
                topic.publish("FoldersTree", "FoldersTree", "resize", {});
            });
            when(p, oc, oc);
        },

        // Pour avoir les tooltip HTML
        _createTreeNode: function(args) {
            return new MyTreeNode(args);
        },
    });

    // Classe "Proxy"
    // Je n'ai pas réussi à faire autrement que comme ça...
    return declare(null, {
        parameters: null,
        constructor: function(parameters) {
            this.parameters = parameters;
            // On commence par aller chercher les données de base
            xhr.get('./ajax.php?module=ajax&categ=folders_selector', {
                handleAs: 'json'
            }).then(lang.hitch(this, this.gotdata));
        },

        // Données récupérées
        gotdata: function(data) {
            
            store = new Store({
                data: data,
                getIdentity: function(object) {
                    return object.id
                }
            });
            store.getChildren = lang.hitch(this, this.getChildren);
            //l'observable permet de conserver le DOM synchrone au store après une modification
            this.store = new Observable(store);
            this.model = new ObjectStoreModel({
                store: new Observable(this.store),
                query: { type: 'root' }
            });
            this.tree = new FolderTree({
                // on rattache le modèle
                model: this.model,
                // callback au clic
                onClick: lang.hitch(this, this.onClick),
                // gestion d'un tooltip avec le détail du Folder
                onMouseOver: lang.hitch(this, this.showTooltip),
                onMouseOut: lang.hitch(this, this.hideTooltip),
            });
            // l'arbre est prêt, on l'annonce !
            // il est judicieux de faire le raccrochement au DOM et le startup en réponse à cet event
            topic.publish("FoldersTree", "FoldersTree", "ready", {});
            this.placeAt(this.parameters.att);
            this.tree.startup();
        },

        // Récupération des enfants
        getChildren: function(object) {
            
            switch (true) {

                case (object.type == 'root'):
                    return this.store.query({parent: object.id });
                    break;

                case (object.type == 'folder' && object.navigation != 1 ) :
                    return this.store.query({parent: object.id });
                    break;
                    
                default :
                    var deferred = new Deferred();
                    var url = './ajax.php?module=ajax&categ=folders_selector&folder_id=' + object.id ;
                    xhr.get(url, {
                        handleAs: 'json'
                    }).then(lang.hitch(self, function(data) {
                        //ajout dans le store
                        for (var i = 0; i < data.length; i++) {
                            this.store.add(data[i]);
                        }
                        //on retourne le résultat du deferred
                        deferred.resolve(this.store.query({parent: object.id }));
                    }));
                    // on retourne le promise
                    return deferred.promise;
            }
        },

        // Traitement du clic
        onClick: function(object, node, evt) {
            
            //recuperation id repertoire
            var folder_path = object.id;
            //recuperation libelle repertoire
            var folder_label = '';
            var node = this.store.get(object.parent);
            while(node.id != 0) {
                folder_label = node.name + '/' + folder_label;
                node = this.store.get(node.parent);
            }
            folder_label = folder_label + object.name + '/';
            //mise a jour formulaire
            try {
                document.getElementById('path').value = folder_label;
                document.getElementById('folder_path').value = folder_path;
                document.getElementById('id_rep').value = folder_path.split('_')[0];
            } catch(e) {}    
            
            // occultation frame
            try {
                var up_frame = document.getElementById('up_frame');
                up_frame.style.visibility="hidden";
                up_frame.style.display='none';
            } catch(e) {}
            

        },

        // Méthode proxy
        placeAt: function(domNode) {
            this.tree.placeAt(domNode);
        },

        // Méthode proxy
        startup: function() {
            this.tree.startup();
        },

        showTooltip: function(event) {
            var node = dijit.getEnclosingWidget(event.target);
            if (node.item) {
                var detail = node.item.name;
                if (node.item.type != "pagin") {
                    if (node.item.detail && node.item.detail != "\n<div class='details'>\n\t\t<table>\n\t\t</table>\t\n</div>") {
                        detail += node.item.detail.replace(/<a[^>]+>([^<]+)<\/a>/gim, "$1")
                    }
                    dijit.showTooltip(detail, node.labelNode);
                }
            }
        },

        hideTooltip: function(event) {
            var node = dijit.getEnclosingWidget(event.target);
            dijit.hideTooltip(node.labelNode);
        }
    });
});