<template>
    <div class="widget-indicators-list">
        <ul class="widget-indicator-list">
    
            <li v-for="(method, key) in widget.widgetSettings.methods" :key="key" :style="`color: ${method.backgroundColor};`">
                <label>
                    {{ method.label ? method.label : widget_type.msg[`${widget.widgetSettings.module}_${method.name}`] }}
                </label>

                <span class="widget-indicator-list-value">
                    {{ data[key] }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ["widget", "data", "widget_type"],
    };
</script>