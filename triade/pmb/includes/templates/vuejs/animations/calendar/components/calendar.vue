<template>
	<div id="calendarList">
		<table>
			<tbody>
				<tr>
					<th>{{ pmb.getMessage("animation", "admin_calendar") }}</th>
					<th>{{ pmb.getMessage("animation", "admin_calendar_color") }}</th>
				</tr>
				<tr v-for="(calendar, index) in calendarlist" :key="index" style="cursor: pointer" @click="edit(calendar.id_calendar)" @mouseover="hover = index" @mouseout="hover = -1" :class="[ index % 2 == 0 ? 'even' : 'odd', index == hover ? 'surbrillance' : '' ]">
					<td>
						<strong v-if="calendar.id_calendar == 1">{{ calendar.name }}</strong>
						<template v-else>{{ calendar.name }}</template>
					</td>
					<td>
						<template>{{ calendar.color }}</template>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="row">
	   		<input @click="newCalendar" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_add_calendar')"/>
		</div>
	</div>
</template>


<script>
	export default {
		props : ["pmb", "calendarlist"],
		
		data : function() {
			return {
				hover : -1
			}
		},
		
		methods : {
			newCalendar : function() {
				document.location = './admin.php?categ=animations&sub=calendar&action=add';
			},
			
			edit : function(id) {
				document.location = './admin.php?categ=animations&sub=calendar&action=edit&id=' + id;
			}
		}
	}
</script>