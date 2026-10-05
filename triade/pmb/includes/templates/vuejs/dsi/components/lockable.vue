<template>
    <div :class="locked ? 'component-disabled':''">
        <slot></slot>
    </div>
</template>

<script>
export default {
    props : ["locked"]
}
</script>