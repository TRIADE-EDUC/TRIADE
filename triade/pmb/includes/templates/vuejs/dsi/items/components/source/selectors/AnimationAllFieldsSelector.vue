<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group-m">
            <div class="dsi-form-group-content">
                <label for="tlc">{{ msg.tlc }}</label>
                <input name="tlc" v-model="data.search" />
            </div>
        </div>
        <div>
            <slot name="trySearch"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default: () => {
                return {
                    search: ""
                }
            }
        },
        msg: {
            type: Object
        }
    }
}
</script>
