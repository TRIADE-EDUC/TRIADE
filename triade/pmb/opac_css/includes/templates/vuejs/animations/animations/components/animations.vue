<template>
	<div id="listAnim">
		<h3>{{ pmb.getMessage("animation", "animation_registration_list_animation") }}</h3>
		<table >
			<thead>
				<tr>
					<th>{{ pmb.getMessage("animation", "animation_children_multi_registration") }}</th>
					<th>{{ pmb.getMessage("animation", "animation_children_title") }}</th>
					<th>{{ pmb.getMessage("animation", "animation_children_date") }}</th>
					<th>{{ pmb.getMessage("animation", "animation_children_animation_quota_available") }}</th>
				</tr>
			</thead>
			<tbody>
				<template v-if="animations.length">
					<tr v-for="(anim, index) in animations" :key='index' @mouseover="hover = index " @mouseout="hover = -1 " 
					   :class="[ index%2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '' ]">
						<td>
							<div class="center">
								<template v-if="anim.event.dateExpired">
									<input type="checkbox" disabled="true" :value="anim.id" :title="pmb.getMessage('animation', 'animation_date_expired')"/>
								</template>
								<template v-else-if="anim.emprAlreadyRegistred">
									<input type="checkbox" disabled="" :value="anim.id"/>
								</template>
								<template v-else>
									<input type="checkbox" v-model="animationsSelected" :value="anim.id" @change="changeAnimationsSelected"/>
								</template>
							</div>
						</td>
						<td>
		 					{{ anim.name }}
						</td>
						<td>
							<template v-if="anim.event">
								{{ anim.event.startDate }} 
								<template v-if="'00:00' !== anim.event.startHour">{{ anim.event.startHour}}</template>
							</template>
							<template v-if="!formdata.animation.event.duringDay">
								<br />
								{{ anim.event.endDate }} 
								<template v-if="'00:00' !== anim.event.endHour">{{ anim.event.endHour}}</template>
							</template>
						</td>
						<td>
							<template v-if='anim.hasChildrens'>
								{{ pmb.getMessage("animation", "form_search_NA") }}
							</template>
							<template v-else-if="anim.allQuotas.animationQuotas.global ">
								<template v-if="typeof anim.allQuotas.availableQuotas.global !== 'undefined' ">
									{{ anim.allQuotas.availableQuotas.global }} / {{ anim.allQuotas.animationQuotas.global  }} 
								</template>
							</template>
							<template v-else>
								{{ pmb.getMessage("animation", "form_search_illimited_quotas") }}
							</template>
						</td>
					</tr>
				</template>
				<template v-else-if="action != 'list'">
					<tr>
						<td colspan="10" style='text-align: center'>{{ pmb.getMessage("animation", "animation_no_linked_animation") }}</td>
					</tr>
				</template>
				<template v-else>
					<tr>
						<td colspan="10" style='text-align: center'>{{ pmb.getMessage("animation", "animation_no_coming_animation") }}</td>
					</tr>
				</template>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		props : ["animations", "pmb","formdata"],
		
		data: function () {
			return {
			    action: "view",
				hover : -1,
				animationsSelected : [],
			}
		},
		created :function(){
			if (this.formdata && this.formdata.animationsSelected){
				this.animationsSelected = this.formdata.animationsSelected;
			}
		},
		methods : {
		    changeAnimationsSelected: function ()  {
		        this.$emit('input', this.animationsSelected);
		    }
		}
	}
</script>