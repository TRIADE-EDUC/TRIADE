<template>
    <div>
        <div>
            <label class="widget-form-label" for="widget-refresh">
                {{ messages.get('dashboard', 'form_refresh_auto') }}
            </label>

            <input 
            type="checkbox" 
            id="widget-refresh" 
            @change="$forceUpdate()"
            v-model="settings.refresh.enabled">
        </div>
        <div v-if="settings.refresh.enabled" class="mb-s">
            <label class="widget-form-label" for="widget-interval-refresh">
                {{ messages.get('dashboard', 'refresh_interval_in_seconds') }}
            </label>
            <input 
                id="widget-interval-refresh" 
                type="number" 
                v-model="settings.refresh.value" 
                :min="$root.widget_refresh_time">
        </div>
    </div>
</template>

<script>
    export default {
        props: ["settings"],
        created: function() {
            if(!this.settings.refresh) {
                this.settings.refresh = {
                    enabled: false,
                    value: this.$root.widget_refresh_time
                };
            }
        }
    };
</script>