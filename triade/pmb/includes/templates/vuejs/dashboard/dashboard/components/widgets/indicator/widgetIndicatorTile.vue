<template>
    <div class="widget-indicators-tile">
        <div 
            v-for="(method, key) in widget.widgetSettings.methods" 
            class="widget-indicator-tile"
            :style="`background-color: ${method.backgroundColor}`">
    
            <span 
                class="widget-indicator-tile-value"
                :style="`color: ${helper.calculateTextColor(method.backgroundColor)};`">
    
                {{ data[key] }}
            </span>
            <label :style="`color: ${helper.calculateTextColor(method.backgroundColor)};`">
                {{ method.label ? method.label : widget_type.msg[`${widget.widgetSettings.module}_${method.name}`] }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["widget", "data", "widget_type"],
    };
</script>