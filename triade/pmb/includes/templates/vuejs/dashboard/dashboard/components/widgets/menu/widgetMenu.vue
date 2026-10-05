<template>
    <div class="widget-menu height-100 ui-flex ui-flex-column ">
        <span class="widget-menu-label">{{ widget.widgetName }}</span>

        <component 
            v-if="widget.dashboardWidgetSettings.display_format" 
            :is="`widget-menu-${widget.dashboardWidgetSettings.display_format}`" 
            :widget="widget"
            :widget_type="widget_type">
        </component>
    </div>
</template>

<script>
    import widgetMenuTile from './widgetMenuTile.vue';
    import widgetMenuList from './widgetMenuList.vue';

    export default {
        props: ["widget", "editMode", "current_user", "widget_type"],
        components: {
            widgetMenuTile,
            widgetMenuList
        },
    }
</script>