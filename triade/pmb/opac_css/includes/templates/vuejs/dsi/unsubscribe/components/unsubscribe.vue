<template>
    <div class="row">
        <span v-if="diffusion">{{ this.messages.get('dsi', 'unsubscribe_success').replace("!!diffusion_name!!", diffusion.settings.opacName ? diffusion.settings.opacName : diffusion.name) }}</span>
        <span v-else>{{ this.messages.get('dsi', 'unsubscribe_diffusion_deleted')}}</span>
    </div>
</template>

<script>
export default {
    name : 'unsubscribe',
    props : ['diffusion']
}
</script>