<template>
    <div v-if="display_formats.length">
        <div class="mb-s">
            <label class="widget-form-label" for="widget-display">
                {{ messages.get('dashboard', 'form_display_label') }}
            </label>
            <select id="widget-display" v-model="settings.display_format" required>
                <option value="" disabled>
                    {{ messages.get('dashboard', 'form_display_select') }}
                </option>
                <option v-for="display_format in display_formats" :value="display_format">
                    {{ messages.get('dashboard', 'form_display_' + display_format) }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["settings", "display_formats"],
        created: function() {
            if(!this.settings.display_format) {
                this.$set(this.settings, "display_format", "");
            }
        }
    }
</script>