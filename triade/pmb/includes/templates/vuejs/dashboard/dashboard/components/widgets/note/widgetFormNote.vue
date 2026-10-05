<template>
    <div>
        <form-refresh :settings="settings"></form-refresh>
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>

        <div>
            <label class="widget-form-label" for="widget-note-bgcolor">
                {{ messages.get('common', 'background_color_label') }}
            </label>
            <input type="color" id="widget-note-bgcolor" v-model="settings.backgroundColor">
        </div>
        
        <div v-if="widget.widgetEditable || widget.numUser == current_user">
            <label class="widget-form-label" for="widget-note-content">
                {{ messages.get('common', 'content') }}
            </label>
            <editor 
                :content="widget.widgetSettings.content" 
                :editable="true"
                @changeContent="changeContent">
            </editor>
        </div>
    </div>
</template>

<script>
    import editor from '@/dashboard/components/editor.vue';
    import formRefresh from "../common/formRefresh.vue";
    import formDisplay from "../common/formDisplay.vue";

    export default {
        props: ["widget", "from", "current_user", "widget_type"],
        components: {
            editor,
            formRefresh,
            formDisplay
        },
        created: function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.backgroundColor){
                    this.widget.widgetSettings.backgroundColor = "#aaa";
                }
                if(!this.widget.widgetSettings.content){
                    this.widget.widgetSettings.content = "";
                }
            }
        },
        computed: {
            settings: function() {
                if(this.from == 'widget') {
                    return this.widget.widgetSettings;
                }

                return this.widget.dashboardWidgetSettings;
            }
        },
        methods: {
            changeContent: function (content) {
                this.$set(this.widget.widgetSettings, "content", content);
            }
        }
    };
</script>