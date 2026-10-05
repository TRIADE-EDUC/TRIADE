<template>
    <div id="dashboard-grid">
        
        <!-- Placement des widgets -->
        <grid-layout
            :layout.sync='dashboard.layout'
            :col-num="24"
            :row-height="30"
            :is-draggable="editMode"
            :is-resizable="editMode"
            :is-mirrored="false"
            :vertical-compact="false"
            :margin="[5, 5]"
            :use-css-transforms="false"
            :cols="{ lg: 24, md: 16, sm: 8, xs: 4, xxs: 1 }"
            :responsive="true"
            :autoSize="true"
            ref="grid_layout">
    
            <!-- Affichage des widgets -->
            <grid-item v-for="(item, index) in dashboard.layout" 
                :key="index"
                :x="item.x"
                :y="item.y"
                :w="item.w"
                :h="item.h"
                :minW="item.minW"
                :minH="item.minH"
                :i="item.i">
    
                <!-- Affichage du widget -->
                <div class="dashboard-item">

                    <!-- Boutons d'actions -->
                    <div v-if="editMode" class="dashboard-item-actions">
                        <button 
                            class="dashboard-button" 
                            :title="messages.get('common', 'edit')"
                            @click="editItem(item.i)">
                            
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </button>
                        <button 
                            class="dashboard-button" 
                            :title="messages.get('common', 'remove')" 
                            @click="removeItem(item.i)">
    
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
    
                    <component 
                        v-if="dashboard.layout.length && item.i !== 'drop'" 
                        :is="'widget-' + getTypeById(item.i)" 
                        :widget="getWidgetById(item.i)"
                        :editMode="editMode"
                        :current_user="current_user"
                        :widget_type="getWidgetType(getTypeById(item.i))">
                        >
                    </component>
                </div>
            </grid-item>
        </grid-layout>
    </div>
</template>

<script>
    import { GridLayout, GridItem } from "vue-grid-layout"
    import WidgetNote from "./widgets/note/widgetNote.vue"
    import WidgetCounter from "./widgets/counter/widgetCounter.vue"
    import WidgetIndicator from "./widgets/indicator/widgetIndicator.vue"
    import WidgetRss from "./widgets/rss/widgetRss.vue"
    import WidgetAlert from "./widgets/alert/widgetAlert.vue"
    import WidgetStat from "./widgets/stat/widgetStat.vue"
    import WidgetMenu from "./widgets/menu/widgetMenu.vue"
    //import WidgetSnake from "./widgets/snake/widgetSnake.vue"

    export default {
        props: ["dashboard", "editMode", "current_user", "widget_types"],
        components: {
            GridLayout,
            GridItem,
            WidgetNote,
            WidgetCounter,
            WidgetIndicator,
            WidgetRss,
            WidgetAlert,
            WidgetStat,
            WidgetMenu
            //WidgetSnake
        },
        methods: {

            /**
             * Récupère un widget par son id.
             *
             * @param {type} id - L'id du widget
             * @return {object} Le widget avec l'id correspondant
             */
            getWidgetById: function (id) {
                return this.dashboard.widgets.find(widget => {
                    return widget.numWidget == id;
                });
            },

            getTypeById: function (id) {
                let widget = this.dashboard.widgets.find(widget => {
                    return widget.idWidget == id;
                });

                return widget && widget.widgetType ? widget.widgetType : "";
            },
            getWidgetType: function (type) {
                return this.widget_types.find(widgetType => {
                    return widgetType.type == type;
                });
            },

            /**
             * Supprime un élément de la mise en page du tableau de bord.
             *
             * @param {any} i - Indice à supprimer
             * @return {void} 
             */
            removeItem: function (i) {
                let index = this.dashboard.layout.map(item => item.i).indexOf(i);
                this.dashboard.layout.splice(index, 1);
                this.dashboard.widgets.splice(index, 1);
            },

            /**
             * Editer un widget.
             *
             * @param {type} id - Identifiant du widget
             * @return {void}
             */
            editItem: function (id) {
                this.$emit("editWidget", id, "dashboard");
            },

            /**
             * Récupère un widget par son identifiant.
             *
             * @param {any} id - L'identifiant du widget
             * @return {object} Le widget avec l'identifiant correspondant
             */
            getWidgetById: function (id) {
                return this.dashboard.widgets.find(widget => widget.idWidget == id);
            }
        }
    }
</script>