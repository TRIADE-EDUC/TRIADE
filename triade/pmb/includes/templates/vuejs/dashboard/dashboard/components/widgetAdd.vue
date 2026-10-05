<template>
    <div class="widget-add">
        
        <!-- Titre -->
        <h2>{{ messages.get('dashboard', 'add_widget_list') }}</h2>

        <!-- Bouton de fermeture -->
        <button type="button" class="bouton widget-add-close" @click="$emit('close')">
            <span class="visually-hidden">{{ messages.get('common', 'close') }}</span>
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>

        <div class="widget-add-content">
            <!-- Bouton de création d'un widget -->
            <button type="button" class="bouton" @click="$emit('editWidget', 0, 'widget')">
                {{ messages.get('dashboard', 'add_widget_create') }}
            </button>

            <!-- Barre de recherche de widgets -->
            <label for="widget-search" class="visually-hidden">{{ messages.get('dashboard', 'add_widget_search') }}</label>
            <input 
                id="widget-search"
                type="text" 
                class="widget-add-search"
                v-model="searchWidget" 
                :placeholder="messages.get('dashboard', 'add_widget_search')">

            <div v-if="!searchWidgets.length" class="widget-add-no-widget">
                <span>{{ messages.get('dashboard', 'add_widget_empty_search') }}</span>
            </div>

            <!-- Liste des widgets -->
            <div v-if="myWidgets.length">
                <h3>{{ messages.get('dashboard', 'add_widget_my_list') }}</h3>
                <div class="widget-add-items">
                    <div v-for="widget in myWidgets" class="widget-add-item"
                        :data-widget-id="widget.idWidget" 
                        @drag="drag" 
                        @dragend="dragend" 
                        draggable="true" 
                        unselectable="on">
    
                        <!-- Boutons d'actions -->
                        <div v-if="current_user == widget.numUser" class="dashboard-widget-add-actions">
                            <button 
                                class="dashboard-button" 
                                :title="messages.get('common', 'edit')"
                                @click="editWidget(widget.idWidget)">
                                
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span>
                            {{ widget.widgetName }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="sharedWidgets.length">
                <h3>{{ messages.get('dashboard', 'add_widget_shared_list') }}</h3>
                <div class="widget-add-items">
                    <div v-for="widget in sharedWidgets" class="widget-add-item"
                        :data-widget-id="widget.idWidget" 
                        @drag="drag" 
                        @dragend="dragend" 
                        draggable="true" 
                        unselectable="on">

                        <!-- Boutons d'actions -->
                        <div v-if="current_user == widget.numUser" class="dashboard-widget-add-actions">
                            <button 
                                class="dashboard-button" 
                                :title="messages.get('common', 'edit')"
                                @click="editWidget(widget.idWidget)">
                                
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </button>
                        </div>

                        {{ widget.widgetName }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    // Position de la souris
    let mousePos = {
        "x": 0,
        "y": 0
    };

    // Position du widget
    let dragPos = {
        "x": 0,
        "y": 0, 
        "w": 2, 
        "h": 2, 
        "i": null
    };

    export default {
        props: ["dashboard", "widgets", "current_user"],
        mounted: function() {

            // Ajout d'un évènement pour enregistrer la position de la souris
            document.addEventListener("dragover", function(e) {
                mousePos.x = e.clientX;
                mousePos.y = e.clientY;
            }, false);
        },
        data: function() {
            return {
                dragActive: false, // Drag actif
                searchWidget: "", // Recherche de widget
                colNum: 24
            }
        },
        computed: {

            /**
             * Liste des widgets disponibles.
             *
             * @return {array}
             */
            availableWidgets: function() {
                if(this.dashboard['widgets']) {
                    return this.widgets.filter(
                        widget => !this.dashboard['widgets'].find(
                            w => w.idWidget == widget.idWidget
                        )
                    );
                }

                return this.widgets;
            },

            /**
             * Liste des widgets correspondant à la recherche.
             *
             * @return {array}
             */
            searchWidgets: function() {
                return this.availableWidgets.filter(
                    widget => widget.widgetName.toLowerCase().indexOf(this.searchWidget.toLowerCase()) !== -1
                );
            },

            myWidgets: function() {
                return this.searchWidgets.filter(widget => widget.numUser == this.current_user);
            },

            sharedWidgets: function() {
                return this.searchWidgets.filter(widget => widget.widgetShareable && widget.numUser != this.current_user);
            }
        },
        methods: {

            /**
             * Vérifie si la souris est dans la grille.
             *
             * @return {boolean}
             */
            mouseInGrid: function() {
                const parentRect = document.getElementById('dashboard-grid').getBoundingClientRect();
                return (
                    (mousePos.x > parentRect.left && mousePos.x < parentRect.right) &&
                    (mousePos.y > parentRect.top && mousePos.y < parentRect.bottom)
                );
            },

            /**
             * Ajoute un nouvel élément à la grille.
             * 
             * @return {void}
             */
            addElementToGrid() {
                // Si la souris est dans la grille et que le drag n'est pas actif
                if (this.mouseInGrid() && this.dashboard.layout.findIndex(item => item.i === 'drop') === -1) {
                    const newElement = {
                        x: (this.dashboard.layout.length * 2) % (this.colNum || 12),
                        y: this.dashboard.layout.length + (this.colNum || 12),
                        w: 2,
                        h: 2,
                        i: 'drop'
                    }
                    this.dashboard.layout.push(newElement);
                }
            },

            /**
             * Gére l'événement de début de drag
             * 
             * @return {void}
             */
            handleDragEvent() {
                const gridLayout = this.$parent.$refs.dashboardLayout.$refs.grid_layout;
                const index = this.dashboard.layout.findIndex(item => item.i === 'drop');

                // Si le drag n'est pas actif
                if (index !== -1) {
                    if(gridLayout.$children[this.dashboard.layout.length]) {
                        gridLayout.$children[this.dashboard.layout.length].$refs.item.style.display = "none";
                    }

                    const element = gridLayout.$children[index];
                    const parentRect = document.getElementById('dashboard-grid').getBoundingClientRect();

                    element.dragging = {
                        "top": mousePos.y - parentRect.top,
                        "left": mousePos.x - parentRect.left
                    };

                    const newPos = element.calcXY(mousePos.y - parentRect.top, mousePos.x - parentRect.left);

                    if (this.mouseInGrid()) {
                        gridLayout.dragEvent('dragstart', 'drop', newPos.x, newPos.y, 2, 2);
                        dragPos.i = String(index);
                        dragPos.x = this.dashboard.layout[index].x;
                        dragPos.y = this.dashboard.layout[index].y;
                    } else {
                        gridLayout.dragEvent('dragend', 'drop', newPos.x, newPos.y, 2, 2);
                        this.dashboard.layout = this.dashboard.layout.filter(obj => obj.i !== 'drop');
                    }
                }
            },

            /**
             * Gére l'événement de fin de drag
             * 
             * @return {void}
             */
            handleDragEndEvent(e) {
                const gridLayout = this.$parent.$refs.dashboardLayout.$refs.grid_layout;

                if (this.mouseInGrid()) {
                    gridLayout.dragEvent('dragend', 'drop', dragPos.x, dragPos.y, 1, 1);
                    this.dashboard.layout = this.dashboard.layout.filter(obj => obj.i !== 'drop');

                    //const uniqueId = this.getUniqueId();

                    this.$nextTick(() => {
                        const position = {
                            x: dragPos.x,
                            y: dragPos.y,
                            w: 2,
                            h: 2,
                            minW: 2,
                            minH: 2,
                            i: e.target.getAttribute("data-widget-id"),
                            // id: e.target.getAttribute("data-widget-id"),
                        };

                        this.dashboard.layout.push(position);
                        gridLayout.dragEvent('dragend', e.target.getAttribute("data-widget-id"), dragPos.x, dragPos.y, 1, 1);

                        const widget = this.getWidgetById(e.target.getAttribute("data-widget-id"));
                        if(widget) {
                            widget.dashboardWidgetSettings = widget.widgetSettings;
                            widget.dashboardWidgetSettings.position = position;

                            this.dashboard.widgets.push(widget);
                        }
    
                        if(gridLayout.$children[this.dashboard.layout.length]) {
                            gridLayout.$children[this.dashboard.layout.length].$refs.item.style.display = "block";
                        }
                    });
                }
            },

            /**
             * Initier la fonctionnalité de glisser.
             * 
             * @return {void}
             */
            drag() {
                this.dragActive = true;
                this.setOpacity();

                this.addElementToGrid();
                this.handleDragEvent();
            },

            /**
             * Initier la fonctionnalité de déposer.
             * 
             * @return {void}
             */
            dragend(e) {
                this.handleDragEndEvent(e);
                this.dragActive = false;

                this.setOpacity();
            },

            /**
             * Rend le formulaire d'ajout de widget transparent.
             *
             * @return {void}
             */
            setOpacity: function() {
                const widgetAdd = document.querySelector(".widget-add");
                if(this.dragActive) {
                    widgetAdd.classList.add("widget-add-opacity");
                    return;
                }

                widgetAdd.classList.remove("widget-add-opacity");
            },

            /**
             * Génére un identifiant unique.
             *
             * @return {any} L'identifiant unique généré.
             */
            getUniqueId: function() {
                return Math.floor(Math.random() * Date.now());
            },

            /**
             * Récupère un widget par son identifiant.
             *
             * @param {integer} id - L'identifiant du widget
             * @return {object} Le widget avec l'identifiant correspondant
             */
            getWidgetById: function(id) {
                return this.widgets.find(widget => widget.idWidget == id);
            },

            /**
             * Modifier un widget.
             *
             * @param {integer} id - L'identifiant du widget à modifier
             * @return {void} 
             */
            editWidget: function(id) {
                this.$emit("editWidget", id, "widget");
            }
        }
    }
</script>