<template>

    <div id="invoice-page">
        <!-- Poup Data Table -->
        <vs-popup fullscreen title="ពិដានថវិកាឆ្នាំ២០២២" :active.sync="popupActive">
          <popup></popup>
        </vs-popup><!--###End Poup Data Table-->

        <div class="flex flex-wrap items-center justify-between">
          <vx-input-group class="mb-base mr-3">
            <vs-input v-model="mailTo" :placeholder="$t('Search Area')" />

            <template slot="append">
              <div class="append-text btn-addon">
                <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">{{$t("Search")}}</vs-button>
              </div>
            </template>
          </vx-input-group>
          <div class="flex items-center">
            <vs-button class="mb-base mr-3" type="border" icon-pack="feather" icon="icon icon-download">{{$t('Download')}}</vs-button>
            <vs-button class="mb-base mr-3" icon-pack="feather" icon="icon icon-file" @click="printInvoice">{{$t("Print")}}</vs-button>
          </div>
        </div>

        <!-- កម្មវិធីចំណូល -->
        <div class="vx-row">
            <div id="popup-demo">
              <popup-fullscreen></popup-fullscreen>
            </div>

            <vs-navbar
                text-color="rgba(255,255,255,.6)"
                active-text-color="rgba(255,255,255,1)" 
                :type="type" 
                color="#F4F4F4"
                v-model="activeItem" 
                style="box-shadow: 1px 5px 15px;"
                class="p-2 mb-6">

                <div slot="title">
                  <vs-navbar-title>
                    <span>កម្មវិធីចំណូលប្រចាំត្រីមាស</span>
                  </vs-navbar-title>
                </div>

                <vs-navbar-item index="0">
                    <a href="#">សរុបកម្មវិធី</a>
                </vs-navbar-item>
            </vs-navbar>
            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card
                  title="ត្រីមាសទី១"
                  title-color="#fff"
                  card-background="#3fccca"
                  content-color="#fff"
                  code-toggler
                  @click="popupActive=true" color="primary">
                  <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី១ៈ ការគ្រប់គ្រងវិស្ស័យសេដ្ឋកិច្ច</b></h5></center>
              </vx-card>
            </div>
            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card
                  title="ត្រីមាសទី២"
                  title-color="#fff"
                  card-background="#cfb007"
                  content-color="#fff"
                  code-toggler @click="popupActive=true">
                  <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី២ៈ ការគ្រប់គ្រងវិស្ស័យហិរញ្ញវត្ថុ</b></h5></center>
              </vx-card>
            </div>

            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card
                  title="ត្រីមាសទី៣"
                  title-color="#fff"
                  card-background="#29a8d6"
                  content-color="#fff"
                  code-toggler @click="popupActive=true">
                  <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៣ៈ ការគ្រប់គ្រងចំណូលសាធារណៈ និងទ្រព្យសម្បត្តិ</b></h5></center>
              </vx-card>
            </div>

            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card
                  title="ត្រីមាសទី៤"
                  title-color="#fff"
                  card-background="#AFC817"
                  content-color="#fff"
                  code-toggler @click="popupActive=true">
                  <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៤ៈ ការគ្រប់គ្រងចំណាយសាធារណៈ និងប្រព័ន្ធហិរញ្ញវត្ថុ...</b></h5></center>
              </vx-card>
            </div>

        </div>
        <!-- កម្មវិធីចំណាយ -->
        <div class="vx-row">
              <div id="popup-demo">
                <popup-fullscreen></popup-fullscreen>
              </div>
  
              <vs-navbar
                  text-color="rgba(255,255,255,.6)"
                  active-text-color="rgba(255,255,255,1)" 
                  :type="type" 
                  color="#F4F4F4"
                  v-model="activeItem" 
                  style="box-shadow: 1px 5px 15px;"
                  class="p-2 mb-6">
  
                  <div slot="title">
                    <vs-navbar-title>
                      <span>កម្មវិធីចំណូលប្រចាំត្រីមាស</span>
                    </vs-navbar-title>
                  </div>
  
                  <vs-navbar-item index="0">
                      <a href="#">សរុបកម្មវិធី</a>
                  </vs-navbar-item>
              </vs-navbar>
              <!-- Background Color -->
              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                  <vx-card
                    title="ត្រីមាសទី១"
                    title-color="#fff"
                    card-background="#3fccca"
                    content-color="#fff"
                    code-toggler
                    @click="popupActive = true" color="primary">
                    <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី១ៈ ការគ្រប់គ្រងវិស្ស័យសេដ្ឋកិច្ច</b></h5></center>
                </vx-card>
              </div>
              <!-- Background Color -->
              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                  <vx-card
                    title="ត្រីមាសទី២"
                    title-color="#fff"
                    card-background="#cfb007"
                    content-color="#fff"
                    code-toggler @click="popupActive = true">
                    <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី២ៈ ការគ្រប់គ្រងវិស្ស័យហិរញ្ញវត្ថុ</b></h5></center>
                </vx-card>
              </div>
  
              <!-- Background Color -->
              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                  <vx-card
                    title="ត្រីមាសទី៣"
                    title-color="#fff"
                    card-background="#29a8d6"
                    content-color="#fff"
                    code-toggler @click="popupActive = true">
                    <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៣ៈ ការគ្រប់គ្រងចំណូលសាធារណៈ និងទ្រព្យសម្បត្តិ</b></h5></center>
                </vx-card>
              </div>
  
              <!-- Background Color -->
              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                  <vx-card
                    title="ត្រីមាសទី៤"
                    title-color="#fff"
                    card-background="#AFC817"
                    content-color="#fff"
                    code-toggler @click="popupActive = true">
                    <center><h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៤ៈ ការគ្រប់គ្រងចំណាយសាធារណៈ និងប្រព័ន្ធហិរញ្ញវត្ថុ...</b></h5></center>
                </vx-card>
              </div>
  
          </div>

    </div>
    
</template>

<script>
  import Popup from './Popup.vue'
  export default{
      data() {
          return {
            popupActive: false,
                colorx: '#5A8DEE',
              mailTo: "",
              companyDetails: {
                  name: 'Microsion Technologies Pvt. Ltd.',
                  addressLine1: '9 N. Sherwood Court',
                  addressLine2: 'Elyria, OH',
                  zipcode: '94203',
                  mailId: 'hello@pixinvent.net',
                  mobile: '+91 999 999 9999',
              },
              recipientDetails: {
                  fullName: 'Peter Stark',
                  addressLine1: '8577 West West Drive ',
                  addressLine2: 'Holbrook, NY',
                  zipcode: '90001',
                  mailId: 'peter@mail.com',
                  mobile: '+91 988 888 8888',
              },
              invoiceDetails: {
                  invoiceNo: '001/2019',
                  invoiceDate: 'Mon Dec 10 2018 07:46:00 GMT+0000 (GMT)',
              },
              invoiceData: {
                  tasks: [
                      {
                          id: 1,
                          task: 'Website Redesign',
                          hours: 60,
                          rate: 15,
                          amount: 90000,
                      },
                      {
                          id: 2,
                          task: 'Newsletter template design',
                          hours: 20,
                          rate: 12,
                          amount: 24000,
                      },
                  ],
                  subtotal: 114000,
                  discountPercentage: 5,
                  discountedAmount: 5700,
                  total: 108300,
              }
          }
      },
      computed: {

      },
      methods: {
        printInvoice() {
          window.print()
        }
      },
      components: {
        Popup
      },
      mounted() {
        this.$emit("setAppClasses", "invoice-page")
      }
  }
</script>

<style lang="scss">
  @media print {
    .invoice-page {
      * {
        visibility: hidden;
      }

      #content-area {
        margin: 0 !important;
      }

      #invoice-container,
      #invoice-container * {
        visibility: visible;
      }
      #invoice-container {
        position: absolute;
        left: 0;
        top: 0;
        box-shadow: none;
      }
    }
  }
</style>