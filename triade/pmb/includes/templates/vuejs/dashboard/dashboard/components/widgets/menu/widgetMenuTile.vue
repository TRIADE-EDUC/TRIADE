<template>
    <div class="widget-menus-tile">
        <div v-for="menu, index in widget.widgetSettings.menu" :key="index"
            class="widget-menu-tile"
            :style="{backgroundColor: menu.backgroundColor}"
            @click="openTab(menu.link)">
    
            <label :style="`color: ${helper.calculateTextColor(menu.backgroundColor)};`" class='center'>
                {{ menu.label }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["widget", "widget_type"],
        methods: {
            openTab: function (link) {
                window.open(link, "_blank");
            }
        },
        watch: {
            "widget.widgetSettings.menu" : function (oldVal, newVal){
                console.log(oldVal, newVal);
            }
        },
    }
</script>