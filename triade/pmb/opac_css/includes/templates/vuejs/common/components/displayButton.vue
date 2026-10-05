<template>
    <button @click="displayElement()" class="bouton" type="button">
        <slot></slot>
    </button>
</template>


<script>
    export default {
        props : ['show'],
        methods : {
            displayElement : function (){
                this.$emit('toggle', !this.show);
            }
        }
    }
</script>