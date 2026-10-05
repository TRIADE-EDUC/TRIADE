<template>
    <div class="filter">
        <div class="filter-group">
            <label :for="filterId" class="etiquette">{{ label }}</label>
            <input type="input" class="saisie-80em" :id="filterId" v-model="search" :placeholder="placeholder"
                ref="input" @input="change" @blur="change" @keyup.enter="change" />
        </div>
    </div>
</template>

<script>
let uid = 0;
export default {
    props: {
        label: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            required: false,
            default: () => { return ""; }
        },
    },
    data: function () {
        return {
            search: "",
            filterId: ""
        };
    },
    created: function () {
        this.filterId = `input_filter_${uid}`;
        uid++;
    },
    mounted: function () {
        if (this.$refs.input && this.$refs.input.form) {
            this.$refs.input.form.addEventListener('reset', () => this.reset())
        }
    },
    methods: {
        reset: function () {
            if (this.search != "") {
                this.search = "";
                this.change();
            }
        },
        change: function () {
            this.$emit('update', this.search);
        }
    }
}
</script>