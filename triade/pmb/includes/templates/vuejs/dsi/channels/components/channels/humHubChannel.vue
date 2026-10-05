<template>
    <div id="mail_channel">
        <div class="dsi-form-group">
            <label class="etiquette" for="humHubApiLink">{{ channelType.msg.humhub_api_link }}</label>
			<div class="dsi-form-group-content">
                <input id="humHubApiLink" type="text" v-model="channel.settings.humHubUrl" />
			</div>
		</div>
        <div class="dsi-form-group">
            <label class="etiquette" for="humHubApiKey">{{ channelType.msg.humhub_api_key }}</label>
			<div class="dsi-form-group-content">
                <input id="humHubApiKey" type="text" v-model="channel.settings.humHubApiKey" />
			</div>
		</div>
        <div v-if="containers.length">
            <div class="dsi-form-group">
                <label class="etiquette" for="humHubSpace">{{ channelType.msg.humhub_space }}</label>
                <div class="dsi-form-group-content">
                    <select id="humHubSpace" v-model="channel.settings.humHubSpace">
                        <option value="" disabled> {{channelType.msg.humhub_space_default}} </option>
                        <option v-for="container in containers" :key="container.id" :value="container.id">
                            {{ container.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="dsi-form-group">
                <label class="etiquette" for="humHubVisibility">{{ channelType.msg.humhub_visibility }}</label>
                <div class="dsi-form-group-content">
                    <input id="humHubVisibility" type="checkbox" v-model="channel.settings.visibility">
                </div>
            </div>
            <div class="dsi-form-group">
                <label class="etiquette" for="humHubPinned">{{ channelType.msg.humhub_pinned }}</label>
                <div class="dsi-form-group-content">
                    <input id="humHubPinned" type="checkbox" v-model="channel.settings.pinned">
                </div>
            </div>
            <div class="dsi-form-group">
                <label class="etiquette" for="humHubLockedComments">{{ channelType.msg.humhub_locked_comments }}</label>
                <div class="dsi-form-group-content">
                    <input id="humHubLockedComments" type="checkbox" v-model="channel.settings.lockedComments">
                </div>
            </div>
        </div>
        <div class="right" v-if="containers.length">
            <span>{{ channelType.msg.humhub_connected }}<i class="fa fa-check" aria-hidden="true"></i></span>
        </div>
        <div class="right" v-else>
            <span>{{ channelType.msg.humhub_not_connected }}<i class="fa fa-times" aria-hidden="true"></i></span>
        </div>
	</div>
</template>

<script>
export default {
    props : ['channel', "channelType"],
    data : function() {
        return {
            containers : []
        }
    },
    created : function() {
        this.initListners();
        if(! this.channel.settings.humHubUrl) {
            this.$set(this.channel.settings, "humHubUrl", "");
        }
        if(! this.channel.settings.humHubApiKey) {
            this.$set(this.channel.settings, "humHubApiKey", "");
        }
        if(! this.channel.settings.humHubSpace) {
            this.$set(this.channel.settings, "humHubSpace", "");
        }
        if(! this.channel.settings.visibility) {
            this.$set(this.channel.settings, "visibility", true);
        }
        if(! this.channel.settings.pinned) {
            this.$set(this.channel.settings, "pinned", false);
        }
        if(! this.channel.settings.lockedComments) {
            this.$set(this.channel.settings, "lockedComments", false);
        }

        this.getContainers();
    },
    methods : {
        getContainers : async function() {
            if(this.channel.id) {
                let response = await this.ws.get("Channels", "humhub/containers/" + this.channel.id);
                if(! response.error) {
                    this.$set(this, "containers", response);
                }
            }
        },
        initListners : function() {
            this.$root.$on('channelSaved', () => this.getContainers());
        }
    }
}
</script>