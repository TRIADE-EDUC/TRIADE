<template>
	<div id="registrationDeleted" class="row">
		<h1 class="registration_intro" v-if="formdata.globals.opac_rgaa_active">
            {{ pmb.getMessage("animation", "animation_registration_deleted_success") }}
        </h1>
		<h2 class="registration_intro" v-else>
            {{ pmb.getMessage("animation", "animation_registration_deleted_success") }}
        </h2>
		<p>{{ pmb.getMessage("animation", "animation_registration_deleted_recap") }}</p>
		<br />
		<p>
			{{ pmb.getMessage("animation", "animation_registration_deleted_link_animation") }}
			<a :href="animation_link">{{ animation_name }}</a> 
		</p>
	</div>
</template>

<script>
	export default {
		props : ["pmb", "formdata"],
		
		computed: {
		    animation_link: function () {
		        if (this.formdata.animation.id) {
			        return "./index.php?lvl=animation_see&id=" + this.formdata.animation.id;
		        } else {
			        return "./index.php?lvl=animations_see";
		        }
		    },
		    animation_name: function () {
		        if (this.formdata.animation.name) {
			        return this.formdata.animation.name;
		        } else {
			        return this.pmb.getMessage("animation", "animations_list");
		        }
		    }
		}
	}
</script>