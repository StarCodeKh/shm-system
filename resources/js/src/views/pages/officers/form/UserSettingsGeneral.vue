<template>
  <vx-card no-shadow>
    <!-- Info -->
    <!-- <vs-input class="w-full mb-base" label-placeholder="Username" v-model="username"></vs-input> -->
    <!-- <vs-input class="w-full" name="username" label-placeholder="Name" v-model="username" v-validate="'required'" />
    <span class="text-danger">{{ errors.first('username') }}</span>
    <vs-input class="w-full" name="name" label-placeholder="Name" v-model="name" v-validate="'required'"></vs-input>
    <span class="text-danger">{{ errors.first('name') }}</span>
    <vs-input class="w-full" name="email" label-placeholder="Email" v-model="email" v-validate="'required'"></vs-input>
    <span class="text-danger">{{ errors.first('email') }}</span> -->

    <!-- <vs-alert icon-pack="feather" icon="icon-info" class="h-full my-4" color="warning">
      <span>Your account is not verified. <a href="#" class="hover:underline">Resend Confirmation</a></span>
    </vs-alert> -->

    <!-- Img Row -->
    <div v-if="$route.params.userId" class="flex flex-wrap items-center mb-base">
      <vs-avatar :src="activeUserInfo.photoURL" size="70px" class="mr-4 mb-4" />
      <div>
        <vs-button class="mr-4 sm:mb-0 mb-2">Upload photo</vs-button>
        <vs-button type="border" color="danger">Remove</vs-button>
        <p class="text-sm mt-2">Allowed JPG, GIF or PNG. Max size of 800kB</p>
      </div>
    </div>
    <!-- tab 1 content -->
    <div class="vx-row">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="នាមត្រកូល" autocomplete="off" name="first_name" v-model="first_name" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('first_name') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="នាមខ្លួន" autocomplete="off" name="last_name" v-model="last_name" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('last_name') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="ជាអក្សរឡាតាំង" autocomplete="off" name="latin" v-model="latin" v-validate="'required'" class="w-full"/>
        <span class="text-danger">{{ errors.first('latin') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="អត្តលេខសម្គាល់" autocomplete="off" name="identity_no" v-model="identity_no" class="w-full"/>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ងារ</label>
        <v-select name="title" v-model="title" :options="titles" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('title') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">មុខតំណែង</label>
        <v-select name="position" v-model="position" :options="positions" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('position') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">អង្គភាព</label>
        <v-select name="entity" v-model="entity" :options="entities" v-validate="'required'" :dir="$vs.rtl ? 'rtl' : 'ltr'"/>
        <span class="text-danger">{{ errors.first('entity') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">តួនាទី</label>
        <v-select name="duty" v-model="duty" :options="duties" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('duty') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ផ្នែក</label>
        <v-select name="department" v-model="department" :options="departments" :dir="$vs.rtl ? 'rtl' : 'ltr'"/>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ធនាគារ</label>
        <v-select name="bank_name" v-model="bank_name" :options="banks" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('bank_name') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="បច្ចុប្បន្នលេខគណនី" name="updated_bank_acc" v-model="updated_bank_acc" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('updated_bank_acc') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="លេខកុងគណនេយ្យចាស់" name="bank_account_no" v-model="bank_account_no" class="w-full"/>
      </div>
      
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="email" label="អ៊ីម៉ែល" name="email"  v-model="email" class="w-full"/>
      </div>
      
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="លេខទូរស័ព្ទ" name="telephone_1" v-model="telephone_1" class="w-full"/>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="តំរៀបតាមលំដាប់" name="order_level" v-model="order_level" class="w-full"/>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <label class="text-sm">ស្ថានភាព</label>
        <div class="mt-2">
          <vs-radio v-model="status" vs-value="1" class="mr-4">សកម្ម</vs-radio>
          <vs-radio v-model="status" vs-value="0" class="mr-4">អសកម្ម</vs-radio>
        </div>
      </div>
    </div>

    <!-- <div class="vx-row" v-if="!$route.params.userId">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" autocomplete="off" label="លេខសម្ងាត់" name="password" v-model="password" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('password') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" label="បញ្ជាក់លេខសម្ងាត់" name="confirmPassword" v-model="confirmPassword" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('confirmPassword') }}</span>
      </div>
    </div> -->


    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button class="ml-auto mt-2" @click="saveGeneral()">Save Changes</vs-button>
      <!-- <vs-button class="ml-4 mt-2" type="border" color="warning">Reset</vs-button> -->
    </div>
  </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { Validator } from 'vee-validate';
import axios from "@/axios.js"
const dict = {
  custom: {
      first_name: {
        required: 'សំុបញ្ជូលនាមត្រកូន',
        alpha: "First name may only contain alphabetic characters"
      },
      last_name: {
        required: 'សំុបញ្ជូលនាមខ្លួន',
        alpha: "First name may only contain alphabetic characters"
      },
      latin: {
        required: 'សំុបញ្ជូលអក្សរឡាតាំង',
        alpha: "Latin may only contain alphabetic characters"
      },
      title: {
        required: 'សំជ្រើសរើសងារ',
        alpha: "First name may only contain alphabetic characters"
      },
      position: {
        required: 'សុំជ្រើសរើសមុខតំណែង',
        alpha: "First name may only contain alphabetic characters"
      },
      updated_bank_acc: {
        required: 'សូមបញ្ចូលបច្ចុប្បន្នគណនី',
        alpha: "Updated back account may only contain alphabetic characters"
      },
      // department: {
      //   required: 'សុំជ្រើសរើសផ្នែក',
      //   alpha: "First name may only contain alphabetic characters"
      // },
      duty: {
        required: 'សុំជ្រើសរើសតួនាទី',
        alpha: "First name may only contain alphabetic characters"
      },
      entity: {
        required: 'សុំជ្រើសរើសអង្គភាព',
        alpha: "First name may only contain alphabetic characters"
      },
      username: {
        required: 'សូមបំពេញឈ្មោះអ្នកប្រើប្រាស់',
        alpha: "First name may only contain alphabetic characters"
      },
      name: {
        required: 'សូមបំពេញឈ្មោះ',
        alpha: "First name may only contain alphabetic characters"
      },
      bank_name: {
        required: 'សូមជ្រើសរើសធនាគារ',
        alpha: "First name may only contain alphabetic characters"
      },
      password: {
        required: 'សូមបំពេញលេខសម្ងាត់',
        alpha: "First name may only contain alphabetic characters"
      },
      confirmPassword: {
        required: 'សូមបញ្ជាក់លេខសម្ងាត់',
        alpha: "First name may only contain alphabetic characters"
      },

  }
};
Validator.localize('en', dict);
export default {
  components: {
    'v-select': vSelect
  },
  data() {
    return {
      first_name: "",
      last_name: "",
      user_id: "",
      latin: "",
      identity_no: "",
      title: "",
      position: "",
      department: "",
      duty: "",
      entity: "",
      entity_id: "",
      bank_id: "",
      bank_name: "",
      bank_account_no: "",
      updated_bank_acc: "",
      email: "",
      telephone_1: "",
      order_level: 1,
      status: 1,
      titles: [],
      entities: [],
      duties: [],
      banks: [],
      departments: [],
      positions: [],
    }
  },
  watch: {
    bank_name(val){
        if(val.id){
          this.bank_id = val.id
        }
    }
  },
  created(){
    this.$vs.loading();
    setTimeout(() => {
      this.$vs.loading.close()
    }, 1000);

    this.getTitle();
    this.getEntity();
    this.getBank();
    this.getDuty();
    this.getDepartments();
    this.getPosition();

    if(this.$route.params.id){
      let _id = this.$route.params.id
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/incentive_officer/" + _id)
            .then((response) => {
            let _data = response.data.data
            this.first_name = _data.first_name
            this.last_name = _data.last_name
            this.user_id = _data.user_id
            this.identity_no = _data.identity_no
            this.latin = _data.latin
            this.title = _data.title
            this.position = _data.position
            this.department = _data.department
            this.duty = _data.duty
            this.duty_id = _data.duty_id
            this.entity = _data.entity
            this.entity_id = _data.entity_id
            this.bank_name = _data.bank_name
            this.bank_id = _data.bank_id
            this.bank_account_no = _data.bank_account_no
            this.updated_bank_acc = _data.updated_bank_acc
            this.email = _data.email
            this.telephone_1 = _data.telephone_1,
            this.order_level = _data.order_level,
            this.status = _data.status
        }).catch((error) => { 
          reject(error)
          this.$vs.notify({
            title: 'Message',
            text: "Error, while trying to do action or no permission allow!",
            iconPack: 'feather',
            icon: 'icon-check-circle',
            color: 'danger'
          })
        })
      })
    }
  },
  methods: {
    getTitle(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/title?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let titles = response["data"]["data"];
            for(let i = 0; i < titles.length; i++){
                const _data = {
                    id: titles[i]["id"],
                    label: titles[i]["name"]
                };
                this.titles.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getEntity(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/entity?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let entities = response["data"]["data"];
            for(let i = 0; i < entities.length; i++){
                const _data = {
                    id: entities[i]["id"],
                    label: entities[i]["name"]
                };
                this.entities.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getBank(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/bank?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let banks = response["data"]["data"];
            for(let i = 0; i < banks.length; i++){
                const _data = {
                    id: banks[i]["id"],
                    label: banks[i]["name"]
                };
                this.banks.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getDepartments(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/department?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let departments = response["data"]["data"];
            for(let i = 0; i < departments.length; i++){
                const _data = {
                    id: departments[i]["id"],
                    label: departments[i]["name"]
                };
                this.departments.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getDuty(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/duty?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let duties = response["data"]["data"];
            for(let i = 0; i < duties.length; i++){
                const _data = {
                    id: duties[i]["id"],
                    label: duties[i]["name"]
                };
                this.duties.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getPosition(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/position?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let positions = response["data"]["data"];
            for(let i = 0; i < positions.length; i++){
                const _data = {
                    id: positions[i]["id"],
                    label: positions[i]["name"]
                };
                this.positions.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    letterTypeList() {
      return [
      'លិខិតផ្ទៃក្នុង',
      'លិខិតចេញ',
      'លិខិតចូល'
      ]
    },
    saveGeneral(){
      this.$validator.validateAll().then(result => {
        if(result){
          this.$vs.loading();
          console.log("=====", this.department);
          let _department
          if(this.department == null){
            _department = this.department;
          }else{
            _department = this.department.label;
          }
          console.log("Here is value of department", this.department);
          // return false;
          let _data = {
            first_name: this.first_name,
            last_name: this.last_name,
            user_id: this.user_id,
            identity_no: this.identity_no,
            latin: this.latin,
            title: this.title.label ? this.title.label : this.title,
            position: this.position.label ? this.position.label : this.position,
            department: _department,
            duty: this.duty.label ? this.duty.label : this.duty,
            duty_id: this.duty.id ? this.duty.id : this.duty_id,
            entity: this.entity.label ? this.entity.label : this.entity,
            entity_id: this.entity.id ? this.entity.id : this.entity_id,
            bank_id: this.bank_id,
            bank_name: this.bank_name.label ? this.bank_name.label : this.bank_name,
            bank_account_no: this.bank_account_no,
            updated_bank_acc: this.updated_bank_acc,
            email: this.email,
            telephone_1: this.telephone_1,
            order_level: this.order_level,
            status: this.status
          }
          if(this.$route.params.id){
            let _id = this.$route.params.id;
            return new Promise((resolve, reject) => {
                axios.put("/api/v1/incentive_officer/" + _id, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/officer/list').catch(() => {})
                }).catch((error) => { 
                  reject(error)
                  this.$vs.notify({
                    title: 'Message',
                    text: "មិនអាចធ្វើបច្ចុប្បន្នភាពបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                  })
                })
            })
          }else{
            return new Promise((resolve, reject) => {
              axios.post("/api/v1/incentive_officer", _data)
                .then((response) => {
                  console.log("message", response);
                this.$vs.notify({
                  title: 'Message',
                  text: response.data.message,
                  iconPack: 'feather',
                  icon: 'icon-check-circle',
                  color: 'primary'
                })
                this.$router.push('/officer/list').catch(() => {})
              }).catch((error) => { 
                reject(error)
                this.$vs.notify({
                  title: 'Message',
                  text: "មិនអាចរក្សាទុកទិន្នន័យបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                  iconPack: 'feather',
                  icon: 'icon-check-circle',
                  color: 'danger'
                })
              })
            })
          }
        }else{
          this.$vs.loading.close()
        }
      });
    }
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>
