// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUniverseController.js,v 1.25.2.1.2.3 2025/04/11 09:55:47 dgoron Exp $


define(["dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/on",
    "dojo/query",
    "dojo/dom-class",
    "dojo/dom-form",
    "dojo/dom-attr",
    "dojo/request/xhr",
    'dojo/io-query',
], function (declare, lang, dom, on, query, domClass, domForm, domAttr, xhr, ioQuery) {
    return declare(null, {
        memoryNodes: null,
        links: null,
        search_field: null,
        selectedLink: null,
        universeQuery: null,
        segmentsValues: null,

        constructor: function (universeQuery) {
            this.search_field = document.getElementsByName('user_query')[0];
            this.links = query('.search_universe_segments_row');
            if (document.readyState === 'loading') {
            	window.addEventListener("load", (event) => {
                    this.addUniverseEvents();
                    this.segmentsValues = new Array();
                    this.displayNbResultsInSegments();
                });
            } else {
            	this.addUniverseEvents();
                this.segmentsValues = new Array();
                this.displayNbResultsInSegments();
            }
        },

        removeSelected: function () {
            this.links.forEach(link => {
                domClass.remove(link, 'selected');
            });
        },
        setWaitingIcon: function () {
            this.links.forEach(link => {
                let resultP = query('.segment_nb_results', link)[0];
                resultP.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            });
        },
        setUniverseHistory: function (data) {
            let promise = new Promise(lang.hitch(this, function (resolve, reject) {
                if (data.user_query) {
                    dom.byId('last_query').value = data.user_query.replace(/'/g, "\'");
                }
                let dynamicParams = "";
                if (data.dynamic_params) {
                    dynamicParams = data.dynamic_params;
                }

                xhr("./ajax.php?module=ajax&categ=search_universes&sub=search_universe&action=rec_history&id=" + data.universe_id + dynamicParams, {
                    data: data,
                    handleAs: "json",
                    method: 'POST',
                }).then((response) => {
                    if (response) {
                        let historyNode = dom.byId('search_index');
                        if (historyNode && response.search_index) {
                            historyNode.value = response.search_index;
                        }
                        this.links.forEach(segment => {
                            this.updateSegmentsLinks(segment);
                        });
                        resolve(true);
                    }
                });
            }));
            return promise;
        },
        addUniverseEvents: function () {
            let form = dom.byId('search_universe_input');
            if (form) {
                on(form, 'submit', lang.hitch(this, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.universeFormSubmit(form);
                }));
            }

            //si on a une valeur par défaut (provenant de l'historique), on poste les formulaires des segments;
            let last_query = dom.byId('last_query');
            if (last_query && last_query.value) {
                this.universeFormSubmit(form, true, true);
            }
        },

        setUserQuery: function (newValue) {
            let user_query = dom.byId('user_query');
            if (user_query) {
                user_query.value = newValue;
            }
        },

        universeFormSubmit: function (form, setHistory = true, fromHistory = false) {
            let defaultSegment = dom.byId('default_segment').value;
            let data = JSON.parse(domForm.toJson(form.id));
            let promise = null;

            if (setHistory) {
                promise = this.setUniverseHistory(data);
            } else {
                promise = Promise.resolve(true);
            }

            /**
             * Si il y a un segment par defaut
             * on affiche la page du segement
			 * sans chercher les résultats dans les autres
             */
            let last_query = dom.byId('last_query').value;
            let universe_user_rmc = dom.byId('universe_user_rmc');
            if (parseInt(defaultSegment) && ((last_query && last_query != "*") || (universe_user_rmc && universe_user_rmc.value && !fromHistory))) {
                let default_segment_url = dom.byId("default_segment_url");
                if (default_segment_url && default_segment_url.value) {
                    form.action = default_segment_url.value;
                    form.submit();
                }
                return true;
            }
            
            this.setWaitingIcon();

            let user_query = dom.byId('last_query').value;
            if (universe_user_rmc && universe_user_rmc.value) {
                user_query = universe_user_rmc.value;
            }

            this.links.forEach(link => {
                let segmentId = domAttr.get(link, 'data-segment-id');
                let universeId = domAttr.get(link, 'data-universe-id');
                let dynamicField = domAttr.get(link, 'data-segment-dynamic-field');
                dynamicField = parseInt(dynamicField);
                let resultP = query('.segment_nb_results', link)[0];

                let storage_user_query = sessionStorage.getItem('universe_' + universeId + '_segment_' + segmentId + '_query_' + user_query);

                if (sessionStorage.getItem('universe_' + universeId + '_segment_' + segmentId + '_nb_' + user_query) != null && (storage_user_query != null && storage_user_query == user_query)) {
                    resultP.innerHTML = '(' + sessionStorage.getItem('universe_' + universeId + '_segment_' + segmentId + "_nb_" + user_query) + ')';

                    sessionStorage.setItem('universe_' + universeId + '_segment_' + segmentId + '_last_query', sessionStorage.getItem('universe_' + universeId + '_segment_' + segmentId + "_nb_" + user_query));
                } else {
                    data.segment_id = domAttr.get(link, 'data-segment-id');
                    xhr(form.action, {
                        data: data,
                        handleAs: "json",
                        method: 'POST',
                    }).then(lang.hitch(this, function (response) {
                        if (response) {
                            resultP.innerHTML = '(' + response.nb_result + ')';
                            if (!dynamicField) {
                                sessionStorage.setItem('universe_' + universeId + '_segment_' + segmentId + "_nb_" + user_query, response.nb_result);
                                sessionStorage.setItem('universe_' + universeId + '_segment_' + segmentId + '_query_' + user_query, user_query);

                                sessionStorage.setItem('universe_' + universeId + '_segment_' + segmentId + '_last_query', response.nb_result);
                            }
                        }
                    }));
                }
            });
            if (universe_user_rmc && universe_user_rmc.value) {
                if (document.location.hash) {
                    document.location.hash = ""
                }
                document.location.hash = "#search_universe_segments_list";
            }
        },

        displayResult: function () {
            dom.byId('search_universe_result_container').innerHTML = '';
            if (this.segmentsValues.results) {
                dom.byId('search_universe_result_container').innerHTML = '<h3>' + this.segmentsValues.label + '</h3>' + this.segmentsValues.results;
                collapseAll();
            }
        },

        updateSegmentsLinks: function (segment) {
            let searchIndex = dom.byId('search_index').value;
            if (segment) {
                let segmentLink = query('a', segment)[0];
                let url = domAttr.get(segmentLink, "href");
                let searchParams = url.substring(url.indexOf("?") + 1);
                let queryObject = ioQuery.queryToObject(searchParams);
                queryObject.search_index = searchIndex;
                url = url.split('?')[0] + "?" + ioQuery.objectToQuery(queryObject);

                domAttr.set(segmentLink, "href", url);
            }
        },

        displayNbResultsInSegments: function () {
            //attention en cas de rmc provenant des segments
            let user_query = dom.byId('user_query');
            let user_rmc = dom.byId('user_rmc');
            let last_query = dom.byId('last_query');

            if ((!last_query || !last_query.value) && (!user_rmc || !user_rmc.value) && (!user_query || !user_query.value)) {
                let form = dom.byId('search_universe_input');
                if (form) {
                    on.emit(form, "submit", {
                        bubbles: true,
                        cancelable: true
                    });
                }
            }
            if (user_query && user_query.value == "*") {
                user_query.value = "";
            }
        },
    });
});