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
      <span v-if="activeUserInfo.photo != null">
        <vs-avatar :src="activeUserInfo.photo" size="70px" class="mr-4 mb-4" />
      </span>
      <span v-else>
        <vs-avatar :src="noImage" size="70px" class="mr-4 mb-4" />
      </span>
      <div>
        <vs-button class="mr-4 sm:mb-0 mb-2">Upload photo</vs-button>
        <vs-button type="border" color="danger">Remove</vs-button>
        <p class="text-sm mt-2">Allowed JPG, GIF or PNG. Max size of 800kB</p>
      </div>
    </div>
    <!-- tab 1 content -->
    <div class="vx-row">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="នាមត្រកូល និង នាមខ្លួន" autocomplete="off" name="fullname" v-model="fullname" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('fullname') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="ឈ្មោះជាអក្សរឡាតាំង" autocomplete="off" name="latin" v-model="latin" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('latin') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ងារ</label>
        <v-select name="title" v-model="title" :options="titles" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('title') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">នាយកដ្ឋាន</label>
        <v-select name="entity" v-model="department" :options="departments" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('department') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">អង្គភាព</label>
        <v-select name="entity" v-model="entity" :options="entities" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('entity') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">តំណែង</label>
        <!-- <vs-input label="តំណែង" name="position" v-model="position" class="w-full" v-validate="'required'"/> -->
        <v-select name="position" v-model="position" :options="positions" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('position') }}</span>
      </div>
      
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">តួនាទី និងសិទ្ធ</label>
        <!-- <vs-input label="តួនាទី" name="duty"  v-model="duty" class="w-full" v-validate="'required'"/> -->
        <v-select name="role" v-model="role" :options="roles" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('role') }}</span>
      </div>
      
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="លេខទំនាក់ទំនង" name="telephone_1" v-model="telephone_1" class="w-full"/>
      </div>
    </div>
    <!-- tab 1 content -->
    <div class="vx-row">

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="email" label="អ៊ីម៉ែល" name="email"  v-model="email" class="w-full"/>
      </div>
      
      <!-- <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="ឈ្មោះអ្នកប្រើប្រាស់ប្រព័ន្ឋ" autocomplete="off" name="username" v-model="username" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('username') }}</span>
      </div> -->
    </div>

    <div class="vx-row" v-if="!$route.params.userId">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" autocomplete="off" label="លេខសម្ងាត់" name="password" v-model="password" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('password') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" label="បញ្ជាក់លេខសម្ងាត់" name="confirmPassword" v-model="confirmPassword" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('confirmPassword') }}</span>
      </div>
    </div>

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button class="ml-auto mt-2" @click="saveGeneral()">រក្សាទុក</vs-button>
      <!-- <vs-button class="ml-4 mt-2" type="border" color="warning">Reset</vs-button> -->
    </div>
  </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { Validator } from 'vee-validate';
import apiConfig from "@/apiConfig.js"
import axios from "@/axios.js";
const dict = {
    custom: {
        title: {
          required: 'សំជ្រើសរើសងារ',
          alpha: "First name may only contain alphabetic characters"
        },
        latin: {
          required: 'សុំបញ្ជូលឈ្មោះឡាតាំង',
          alpha: "First name may only contain alphabetic characters"
        },
        position: {
          required: 'សុំជ្រើសរើសមុខងារ',
          alpha: "First name may only contain alphabetic characters"
        },
        department: {
          required: 'សុំជ្រើសរើសផ្នែក',
          alpha: "First name may only contain alphabetic characters"
        },
        role: {
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
      title: {"value": 1, "label": "Label"},
      role: "",
      latin: "",
      department: "",
      entity: "",
      username: "",
      telephone_1: "",
      telephone_2: "",
      fullname: "",
      email: "",
      company: "",
      password: "",
      confirmPassword: "",
      titles: [],
      entities: [],
      departments: [],
      position: "",
      positions: [],
      roles: [],
      noImage: require("@assets/images/no-account.png"),
      uri: "?offset=0&limit=10&sort=id&order=desc&page_number=1",
      
    }
  },
  created(){
    this.$vs.loading();
    setTimeout(() => {
      this.$vs.loading.close()
    }, 2000);
    // this.getRole()
    // this.getTitle()
    // this.getEntity()
    // this.getDepartment()
    // this.getPosition()
    if(this.$route.params.userId){
      let _userId = this.$route.params.userId
      return new Promise((resolve, reject) => {
        axios.post(apiConfig._apiUser + "/profile")
            .then((response) => {
            let _data = response.data.data
            this.title = _data.data.title
            this.titles = _data.title
            this.role = {label: _data.data.role, value: _data.data.role}
            this.roles = _data.role
            this.position = {label: _data.data.position, value: _data.data.position}
            this.positions = _data.position
            this.duty = _data.duty
            this.latin = _data.data.latin
            this.position = {label: _data.data.position, value: _data.data.position}
            this.department = {label: _data.data.department, value: _data.data.department}
            this.departments = _data.departments
            this.entity = {label: _data.data.entity.code+"-"+_data.data.entity.name_kh, value: _data.data.entity.id}
            this.entities = _data.entities
            this.username = _data.data.username
            this.telephone_1 = _data.data.telephone_1
            this.telephone_2 = _data.data.telephone_2
            this.fullname = _data.data.fullname
            this.email = _data.data.email
            this.company = _data.data.company
            // this.password = _data.password
            // this.role = this.roles.find(i => i.id == _data.role_id);
            
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
    letterTypeList() {
      return [
      'លិខិតផ្ទៃក្នុង',
      'លិខិតចេញ',
      'លិខិតចូល'
      ]
    },
    getTitle(){
      return new Promise((resolve, reject) => {
        axios.get(apiConfig._apiTitle + this.uri)
            .then((response) => {
            let titles = response["data"]["data"];
            for(let i = 0; i < titles.length; i++){
                const _data = {
                    id: titles[i]["id"],
                    label: titles[i]["name_kh"]
                };
                this.titles.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getEntity(){
      return new Promise((resolve, reject) => {
        axios.get(apiConfig._apiEntity + this.uri)
            .then((response) => {
            let entities = response["data"]["data"];
            for(let i = 0; i < entities.length; i++){
                const _data = {
                    id: entities[i]["id"],
                    label: entities[i]["name_en"]
                };
                this.entities.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getDepartment(){
      return new Promise((resolve, reject) => {
        axios.get(apiConfig._apiDepartment + this.uri)
            .then((response) => {
            let departments = response["data"]["data"];
            for(let i = 0; i < departments.length; i++){
                const _data = {
                    id: departments[i]["id"],
                    label: departments[i]["name_en"]
                };
                this.departments.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getPosition(){
      return new Promise((resolve, reject) => {
        axios.get(apiConfig._apiPosition + this.uri)
            .then((response) => {
            let positions = response["data"]["data"];
            for(let i = 0; i < positions.length; i++){
                const _data = {
                    id: positions[i]["id"],
                    label: positions[i]["name_en"]
                };
                this.positions.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getRole(){
      return new Promise((resolve, reject) => {
        axios.get(apiConfig._apiRole + this.uri)
            .then((response) => {
            let roles = response["data"]["data"];
            for(let i = 0; i < roles.length; i++){
                const _data = {
                    id: roles[i]["id"],
                    label: roles[i]["name"]
                };
                this.roles.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    saveGeneral(){
      this.$vs.loading();
      this.$validator.validateAll().then(result => {
        if(result){
          let _data = [
            {name: this.name},
            {latin: this.latin},
            {username: this.username},
            {email: this.email},
            {fullname: this.fullname.label ? this.fullname.label : this.fullname},
            {title: this.title.label ? this.title.label : this.title},
            {position: this.position.label ? this.position.label : this.position},
            {role_id: this.role.label ? this.role.label : this.role},
            {role: this.role.value ? this.role.value : this.role},
            {department: this.department.label ? this.department.label : this.department},
            {entity_id: this.entity.value ? this.entity.value : this.entity},
            {telephone_1: this.telephone_1},
            {telephone_2: this.telephone_2},
            {password: this.password}
          ];
          if(this.$route.params.userId){
            let _userId = this.$route.params.userId;
            return new Promise((resolve, reject) => {
                axios.put(apiConfig._apiUser + "/" + _userId, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$vs.loading.close();
                    // this.$router.push('/user/list').catch(() => {})
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
          }else{
            return new Promise((resolve, reject) => {
                axios.post("/api/auth/signup", _data)
                    .then((response) => {
                      console.log("message", response);
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    // this.$router.push('/user/list').catch(() => {})
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
