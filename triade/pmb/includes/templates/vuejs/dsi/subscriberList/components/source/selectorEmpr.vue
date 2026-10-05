<template>
    <div class="dsi-form-group">
        <label class="etiquette" for="search-empr-input"></label>
        <div class="dsi-form-group-content">
            <input type="text"
                name="search-empr-input"
                v-model="userInput"
                :placeholder="messages.get('dsi', 'subscriber_list_selector_simple_search')"
                @input="$emit('updateRMC', userInput)" @keypress.enter.prevent="$root.$emit('startImport', from)">
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: String,
            default: function() {
                return "";
            }
        },
        from: {
            type: String,
            default: function() {
                return "";
            }
        },
    },
    data: function() {
        return {
            userInput: this.data
        }
    }
}
</script>