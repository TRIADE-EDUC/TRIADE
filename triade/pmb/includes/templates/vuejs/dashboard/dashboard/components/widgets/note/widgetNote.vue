<template>
    <div 
        class="dashboard-item-note"
        :style="'background-color: ' + widget.dashboardWidgetSettings.backgroundColor">

        <!-- <textarea 
            v-model="widget.dashboardWidgetSettings.content" 
            :readonly="!editable"
            :class="editMode ? 'dashboard-item-note-textarea' : 'dashboard-item-note-textarea editable'"
            
            @blur="save"
            @input="isModified = true">
        </textarea> -->
        <editor 
            :content="widget.widgetSettings.content" 
            :editable="!editMode"
            ref="editor"
            @blur="save"
            @changeContent="changeContent">
        </editor>

        <div v-show="!editMode" class="dashboard-item-actions">
            <button 
                v-show="isModified"
                type="button" 
                class="dashboard-button" 
                :title="messages.get('common', 'submit')"
                @click="save">

                <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button 
                type="button" 
                class="dashboard-button" 
                :title="messages.get('common', 'refresh')"
                @click="refresh(false, true)">

                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import tinymce from 'tinymce';
    import editor from '@/dashboard/components/editor.vue';

    export default {
        props: ["widget", "editMode", "current_user"],
        components: {
            editor
        },
        data: function() {
            return {
                isModified: false
            }
        },
        mounted: function () {
            if(this.widget.dashboardWidgetSettings && this.widget.dashboardWidgetSettings.refresh.enabled) {
                const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                setTimeout(this.refresh, refresh * 1000);
            }
        },
        computed: {
        
            /**
             * Retourne l'editabilite de la note
             *
             * @return {bool}
             */
            editable: function() {
                
                if(this.editMode) {
                    return false;
                }
                if(this.widget.numUser==this.current_user) {
                    return true;
                }
                if(this.widget.widgetEditable==1) {
                    return true;
                }
                return false;
            }
        },
        methods: {
            /**
             * Enregistre le contenu du widget.
             */
            save: function () {
                if(this.isModified) {
                    this.$root.$emit("saveWidget", this.widget);
                    this.$nextTick(() => {
                        this.isModified = false;
                    });
                }
            },
            refresh: function (loop = true, loader = false) {
                if(this.$refs.editor) {
                    const editor = this.$refs.editor.getEditor();
                    if(editor && !editor.hasFocus()) {
                        this.$root.$emit("refreshWidget", this.widget, loader);
                    }
                }

                if(this.widget.dashboardWidgetSettings.refresh.enabled && loop) {
                    const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                    setTimeout(this.refresh, refresh * 1000);
                }
            },
            changeContent: function (content) {
                this.isModified = true;
                this.$set(this.widget.widgetSettings, "content", content);
            }
        }
    };
</script>