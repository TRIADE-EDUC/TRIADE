<template>
	<div id="animationDaughterList">
		<div id="animationDaughter" class="notice-parent">
			<img id="animationDaughterImg" class="img_plus" name="imEx" :src='img.plus' onClick="expandBase('animationDaughter', true); return false;">
			<span class="notice-heada">{{ pmb.getMessage("animation", "animation_daughter_list") }} ({{ paginationChildrens.total }})</span>
		</div>
		<div id="animationDaughterChild" class="child" style="display: none;" v-if="0 < paginationChildrens.total">
			<!-- <animations :animations="animations" :pmb="pmb" :action="action" :formdata="formdata"></animations> -->
			<animations
				:parent-animation="parentAnimation"
				:pagination="paginationChildrens"
				:pmb="pmb"
				:action="action"
				:formdata="formdata"
			/>
		</div>
	</div>
</template>

<script>
	import animations from "./animations.vue";
	import messages from "../../../common/helper/Messages.js";

	export default {
		props : [
			"parentAnimation",
			"paginationChildrens",
			"img",
			"pmb",
			"action",
			"formdata"
		],
		components : {
			animations,
		},
        data: function () {
            return {
                msg: messages,
            }
        },
		ready : function() {
			this.action = "daughter";
		}
	}
</script>