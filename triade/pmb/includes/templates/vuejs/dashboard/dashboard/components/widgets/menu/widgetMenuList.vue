<template>
    <div class="widget-menu">
        <ul class="widget-menu-list">
            <li v-for="menu, index in widget.widgetSettings.menu"  :key="index" >
                <a :href="menu.link" target="_blank" class="widget-menu-link">
                    {{ menu.label}}
                    <i class="fa fa-external-link" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ["widget", "widget_type"]
    }
</script>