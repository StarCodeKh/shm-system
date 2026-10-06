<template>
	
	<div>
		<!-- ############ ACTION BUTTON ############  -->
		<div class="flex flex-wrap-reverse items-center data-list-btn-container">
			<!-- ADD NEW -->
			<div @click="goBack" class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
				<feather-icon icon="ArrowLeftIcon" svgClasses="h-4 w-4" />
				<span class="ml-2 text-base text-default">BACK TO LIST</span>
			</div>
		</div><!-- ############ END ACTION BUTTON ############  -->
	
		<div class="vx-row">
			<!-- MULTIPLE COLUMNS-->
			<div class="vx-col w-full mb-base">
				<vx-card title="Form">
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label="First Name" v-model="input25" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label="Last Name" v-model="input26" />
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label="City" v-model="input27" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label="Country" v-model="input28" />
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label="Company" v-model="input29" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-6">
							<vs-input type="email" class="w-full" label="Email" v-model="input30" />
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col w-full mb-6">
							<vs-checkbox class="inline-flex" v-model="check7">Remember Me</vs-checkbox>
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col w-full">
							<vs-button class="mr-3 mb-2">Save</vs-button>
							<vs-button color="warning" type="border" class="mb-2" @click="input25 = input26 = input27 = input28 = input29 = input30 = ''; check7 = false;">Reset</vs-button>
						</div>
					</div>
				</vx-card>
			</div>
		</div>
	</div>
</template>

<script>
export default{
	components: {
		
	},
	data() {
		return {
			check1: '',
			check2: '',
			check3: '',
			check4: '',
			check5: '',
			check6: '',
			check7: '',
			input1: '',
			input2: '',
			input3: '',
			input4: '',
			input5: '',
			input6: '',
			input7: '',
			input8: '',
			input9: '',
			input10: '',
			input11: '',
			input12: '',
			input13: '',
			input14: '',
			input15: '',
			input16: '',
			input17: '',
			input18: '',
			input19: '',
			input20: '',
			input21: '',
			input22: '',
			input23: '',
			input24: '',
			input25: '',
			input26: '',
			input27: '',
			input28: '',
			input29: '',
			input30: '',
		}
	},
	methods: {
		goBack(){
			this.$router.push('/setup/department').catch(() => {})
		}
	}
}
</script>
