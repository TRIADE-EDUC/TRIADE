<template>
    <div class="loader" :style="style">
        <i class="fa fa-spinner fa-spin fa-3x fa-fw icons" style="color:#babdb6;"></i>
        <span class="sr-only">Loading...</span>
    </div>
</template>

<script>
export default {
    name : "loader",
    props : ['height'],
    computed : {
        style : function() {
            return {
                "height" : this.height + "px"
            }
        }
    }
}
</script>

<style scoped>
.loader {
    display : flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}
</style>