<template>
	<div>
		<h2>{{ data.messages.name }}</h2>
		<p>{{ data.messages.description }}</p>
	
		<form class="form-admin thumbnail-small-form" action="" method="POST" @submit.prevent="add">
			<h3>{{ data.messages.add_link }}</h3>
			<div class="form-contenu">
				<div class="row">
                    <div class="colonne4">
                       <label class="etiquette" for="new_link_name">{{ data.messages.link_name }}</label>
                    </div>
                    <div class="colonne_suite">
                       <input class="saisie-50em" type="text" name="new_link_name" id="new_link_name" required autocomplete="off" v-model="new_link_name" />
                    </div>
                </div>
				<div class="row">
                    <div class="colonne4">
                       <label class="etiquette" for="new_link_url">{{ data.messages.link_url }}</label>
                    </div>
                    <div class="colonne_suite">
                       <input class="saisie-50em" type="text" name="new_link_url" id="new_link_url" required autocomplete="off" v-model="new_link_url" />
                    </div>
                </div>
                <div class="row">
					<button class="bouton" type="submit">
						{{ messages.get("common", "more") }}
					</button>
				</div>

				<div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="curl_timeout">{{ data.messages.form_curl_timeout }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="number" name="curl_timeout" id="curl_timeout" v-model="curl_timeout" />
                    </div>
                </div>
			</div>
		</form>
		
		<h3>{{ data.messages.links_order }}</h3>
		<table class="links-table">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(link, index) in links" :key="index"
					:class="['link-item', index%2 ? 'odd' : 'even', hover == index ? 'surbrillance' : '']"
					@mouseover="hover = index" @mouseout="hover = null">
					<td class="link-item-name">
						{{link.name}}
					</td>
					<td class="link-item-url">
						{{link.url}}
					</td>
					<td class="link-item-action">
						<button :class="['bouton up', upDisabled(index) ? 'disabled' : '']" type="button" @click="down(index)" :disabled="upDisabled(index)">
							<img :src="images.get('top-arrow.png')" :alt="messages.get('common', 'up')">
						</button>
						<button :class="['bouton down', downDisabled(index) ? 'disabled' : '']" type="button" @click="up(index)" :disabled="downDisabled(index)">
							<img :src="images.get('bottom-arrow.png')" :alt="messages.get('common', 'down')">
						</button>
						<button class="bouton" type="button" @click="remove(index)" :title="messages.get('common', 'remove')">
							{{ messages.get("common", "remove_short") }}
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="row">
		    <button class="bouton btnCancel" type="button" @click="cancel">
		        {{ messages.get("common", "cancel") }}
		    </button>
		    <button class="bouton" type="submit" @click="submit">
		        {{ messages.get("common", "submit") }}
		    </button>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["data"],
		data: function () {
			return {
			    hover: null,
				links : [],
				new_link_name : "",
				new_link_url : "",
				curl_timeout : 5,
			}
		},
		created: function() {
			this.links = this.helper.cloneObject(this.data.parameters);
			//changement de format de donnees
			if (this.data.parameters.links) {
            	this.links = this.helper.cloneObject(this.data.parameters.links);
            }
			if (this.data.parameters.curl_timeout) {
            	this.curl_timeout = this.helper.cloneObject(this.data.parameters.curl_timeout);
            }
		},
		methods: {
		    cancel: function () {
		        this.$emit('cancel');
		    },
		    downDisabled: function(index) {
		        return index == this.links.length - 1;
		    }, 
		    upDisabled: function(index) {
		        return index == 0;
		    }, 
		    cancel: function () {
		        this.$emit('cancel');
		    },
		    submit: async function () {
		    	let values = [{
                    "links": this.links,
                    "curl_timeout": this.curl_timeout,
                }]; 
                let response = await this.ws.post(this.data.entityType, "externallinks/save", {values: values});
				if (response.error) {
				    if (response.errorMessage) {
				        console.error(response.errorMessage);
				    }
					this.notif.error(this.messages.get("common", "failed_save"));
				} else {
					this.notif.info(this.messages.get("common", "success_save"));
				}
		    },
		    add: function () {
		        this.links.push({
		        	"name" : this.new_link_name,
		        	"url" : this.new_link_url,
		    	});
		        this.new_link_name = "";
		        this.new_link_url = "";
		    },
		    remove: function (index) {
		        this.links.splice(index, 1);
		    },
		    up: function (index) {
		        this.links.splice(index+2, 0, this.links[index])
		        this.links.splice(index, 1)
		    },
		    down: function (index) {
		        this.links.splice(index-1, 0, this.links[index])
		        this.links.splice(index+1, 1)
		    }
		}
	}
</script>