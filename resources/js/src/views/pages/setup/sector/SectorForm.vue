<template>
	
	<div>
		<!-- ############ ACTION BUTTON ############  -->
		<div class="flex flex-wrap-reverse items-center data-list-btn-container">
			<!-- ADD NEW -->
			<div @click="goBack" class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
				<feather-icon icon="BackIcon" svgClasses="h-4 w-4" />
				<span class="ml-2 text-base text-default">BACK TO LIST</span>
			</div>
		</div><!-- ############ END ACTION BUTTON ############  -->
	
		<div class="vx-row">
			<!-- MULTIPLE COLUMNS-->
			<div class="vx-col w-full mb-base">
				<vx-card title="Form" code-toggler>
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label-placeholder="First Name" v-model="input25" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label-placeholder="Last Name" v-model="input26" />
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label-placeholder="City" v-model="input27" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label-placeholder="Country" v-model="input28" />
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col sm:w-1/2 w-full mb-2">
							<vs-input class="w-full" label-placeholder="Company" v-model="input29" />
						</div>
						<div class="vx-col sm:w-1/2 w-full mb-6">
							<vs-input type="email" class="w-full" label-placeholder="Email" v-model="input30" />
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
			<template slot="codeContainer">
	&lt;template&gt;
	&lt;div class=&quot;vx-row&quot;&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-2&quot;&gt;
		&lt;vs-input class=&quot;w-full&quot; label-placeholder=&quot;First Name&quot; v-model=&quot;input25&quot; /&gt;
		&lt;/div&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-2&quot;&gt;
		&lt;vs-input class=&quot;w-full&quot; label-placeholder=&quot;Last Name&quot; v-model=&quot;input26&quot; /&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	&lt;div class=&quot;vx-row&quot;&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-2&quot;&gt;
		&lt;vs-input class=&quot;w-full&quot; label-placeholder=&quot;City&quot; v-model=&quot;input27&quot; /&gt;
		&lt;/div&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-2&quot;&gt;
		&lt;vs-input class=&quot;w-full&quot; label-placeholder=&quot;Country&quot; v-model=&quot;input28&quot; /&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	&lt;div class=&quot;vx-row&quot;&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-2&quot;&gt;
		&lt;vs-input class=&quot;w-full&quot; label-placeholder=&quot;Company&quot; v-model=&quot;input29&quot; /&gt;
		&lt;/div&gt;
		&lt;div class=&quot;vx-col sm:w-1/2 w-full mb-6&quot;&gt;
		&lt;vs-input type=&quot;email&quot; class=&quot;w-full&quot; label-placeholder=&quot;Email&quot; v-model=&quot;input30&quot; /&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	&lt;div class=&quot;vx-row&quot;&gt;
		&lt;div class=&quot;vx-col w-full mb-6&quot;&gt;
		&lt;vs-checkbox class=&quot;inline-flex&quot; v-model=&quot;check7&quot;&gt;Remember Me&lt;/vs-checkbox&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	&lt;div class=&quot;vx-row&quot;&gt;
		&lt;div class=&quot;vx-col w-full&quot;&gt;
		&lt;vs-button class=&quot;mr-3 mb-2&quot;&gt;Submit&lt;/vs-button&gt;
		&lt;vs-button color=&quot;warning&quot; type=&quot;border&quot; class=&quot;mb-2&quot; @click=&quot;input25 = input26 = input27 = input28 = input29 = input30 = ''; check7 = false;&quot;&gt;Reset&lt;/vs-button&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	&lt;/template&gt;
			</template>
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
			this.$router.push('/setup/sector').catch(() => {})
		}
	}
}
</script>
