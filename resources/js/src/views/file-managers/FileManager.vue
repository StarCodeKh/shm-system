<template>
  <div id="upload-demo">
    <div id="tabs-demo">
      <vx-card title="បណ្ណាល័យឯកសារ">
        <!-- <upload-automatic></upload-automatic> -->
        <div class="mt-12">
          <vs-tabs alignment="fixed" :position="isSmallerScreen ? 'top' : 'left'">
            <vs-tab label="រូបភាព">
              <h4 class="mb-4">បណ្ណាល័យរូបភាព</h4>
              <div class="vx-row">
                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- <vs-navbar :type="type" v-model="activeItem" class="p-2">
                        <div slot="title">
                          <vs-navbar-title>
                            <span>ប្រភេទឯកសារ {{type}}</span>
                          </vs-navbar-title>
                        </div>

                        <vs-navbar-item index="0">
                          <a href="#">រូបភាព</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="1">
                          <a href="#">ឯកសារ</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="2">
                          <a href="#">វីដេអូ</a>
                        </vs-navbar-item>
                      </vs-navbar> -->
              </div>
            </vs-tab>

            <vs-tab label="ឯកសារ">
              <h4 class="mb-4">ឯកសារ</h4>
              <div class="vx-row">
                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-base">
                  <vx-card>
                    <div slot="no-body">
                      <img :src="card_1.img" alt="content-img" class="responsive card-img-top">
                    </div>
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                    <div class="flex justify-between flex-wrap">
                      <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0"
                        gradient-color-secondary="#CE9FFC">Download</vs-button>
                      <!-- <vs-button class="mt-4" type="border" color="#b9b9b9">View All</vs-button> -->
                    </div>
                  </vx-card>
                </div>

                <!-- <vs-navbar :type="type" v-model="activeItem" class="p-2">
                        <div slot="title">
                          <vs-navbar-title>
                            <span>ប្រភេទឯកសារ {{type}}</span>
                          </vs-navbar-title>
                        </div>

                        <vs-navbar-item index="0">
                          <a href="#">រូបភាព</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="1">
                          <a href="#">ឯកសារ</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="2">
                          <a href="#">វីដេអូ</a>
                        </vs-navbar-item>
                      </vs-navbar> -->
              </div>
            </vs-tab>
            <vs-tab label="វីដេអូ">
              <h4 class="mb-4">វីដេអូឯកសារ</h4>
              <div class="vx-row">
                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                  <vx-card>
                    <video-player ref="player" class="media-video-player card-video" :options="playerOptions" />
                    <h5 class="mb-2">{{ card_1.title }}</h5>
                    <p class="text-grey">{{ card_1.subtitle }}</p>
                    <p class="text-grey">{{ card_1.subtitle_2 }}</p>
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                  <vx-card>
                    <video-player ref="player" class="media-video-player card-video" :options="playerOptions" />
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                  <vx-card>
                    <video-player ref="player" class="media-video-player card-video" :options="playerOptions" />
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                  <vx-card>
                    <video-player ref="player" class="media-video-player card-video" :options="playerOptions" />
                  </vx-card>
                </div>

                <!-- Grid Image -->
                <div class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                  <vx-card>
                    <video-player ref="player" class="media-video-player card-video" :options="playerOptions" />
                  </vx-card>
                </div>
                <!-- <vs-navbar :type="type" v-model="activeItem" class="p-2">
                        <div slot="title">
                          <vs-navbar-title>
                            <span>ប្រភេទឯកសារ {{type}}</span>
                          </vs-navbar-title>
                        </div>

                        <vs-navbar-item index="0">
                          <a href="#">រូបភាព</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="1">
                          <a href="#">ឯកសារ</a>
                        </vs-navbar-item>

                        <vs-navbar-item index="2">
                          <a href="#">វីដេអូ</a>
                        </vs-navbar-item>
                      </vs-navbar> -->
              </div>
            </vs-tab>
          </vs-tabs>
        </div>
      </vx-card>
    </div>
  </div>
</template>

<script>
import UploadDefault from './UploadDefault.vue'
import UploadMultiple from './UploadMultiple.vue'
import UploadAutomatic from './UploadAutomatic.vue'

import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { videoPlayer } from 'vue-video-player'
import VxTimeline from "@/components/timeline/VxTimeline.vue"
import 'video.js/dist/video-js.css'

export default {
  data() {
    return {
      // card 1
      card_1: {},
      card_2: {},
      card_3: {},
      card_4: {},
      card_5: {},
      card_6: {},
      card_7: {},
      card_8: {},

      chatLog: [],
      chatMsgInput: '',

      card_9: {},

      timelineData: [
        {
          color: "primary",
          icon: "PlusIcon",
          title: "New Task Added",
          desc: "Bonbon macaroon jelly beans gummi bears jelly lollipop apple",
          time: "25 Days Ago"
        },
        {
          color: "warning",
          icon: "AlertCircleIcon",
          title: "Task Update Found",
          desc: "Cupcake gummi bears soufflé caramels candy",
          time: "15 Days Ago"
        },
        {
          color: "success",
          icon: "CheckIcon",
          title: "Task Finished",
          desc: "Candy ice cream cake. Halvah gummi bears",
          time: "20 mins ago"
        }
      ],

      name: '',
      email: '',
      msg: '',

      playerOptions: {},
      settings: { // perfectscrollbar settings
        maxScrollbarLength: 60,
        wheelSpeed: .60,
      },
    }
  },
  components: {
    UploadDefault,
    UploadMultiple,
    UploadAutomatic,
    VuePerfectScrollbar,
    videoPlayer,
    VxTimeline
  },
  mounted() {
    this.$refs.chatLogPS.$el.scrollTop = this.$refs.chatLog.scrollHeight;
  },
  created() {
    // Card 1
    this.$http.get("/api/users/pixinvent/product/1")
      .then((response) => { this.card_1 = response.data })
      .catch((error) => { console.log(error) })

    // Card 2
    this.$http.get("/api/users/pixinvent/product/sales/1")
      .then((response) => { this.card_2 = response.data })
      .catch((error) => { console.log(error) })

    // Card 3
    this.$http.get("/api/users/pixinvent/product/goals/1")
      .then((response) => { this.card_3 = response.data })
      .catch((error) => { console.log(error) })

    // Card 4
    this.$http.get("/api/users/89")
      .then((response) => { this.card_4 = response.data })
      .catch((error) => { console.log(error) })

    // Card 5
    this.$http.get("/api/users/58")
      .then((response) => { this.card_5 = response.data })
      .catch((error) => { console.log(error) })

    // Card 6
    this.$http.get("/api/users/16")
      .then((response) => { this.card_6 = response.data })
      .catch((error) => { console.log(error) })

    // Card 7
    this.$http.get("/api/place/18")
      .then((response) => { this.card_7 = response.data })
      .catch((error) => { console.log(error) })

    // Card 8
    this.$http.get("/api/place/76/weather")
      .then((response) => { this.card_8 = response.data })
      .catch((error) => { console.log(error) })

    // Chat Log
    this.$http.get("/api/chat/demo-1/log")
      .then((response) => { this.chatLog = response.data })
      .catch((error) => { console.log(error) })

    // Card 9
    this.$http.get("/api/item/12/review")
      .then((response) => { this.card_9 = response.data })
      .catch((error) => { console.log(error) })

    // Player Options
    this.$http.get("/api/card/card-basic/player-option")
      .then((response) => { this.playerOptions = response.data })
      .catch((error) => { console.log(error) })
  }
}
</script>

<style lang="scss">
#demo-basic-card {
  .overlay-card {
    .vx-card__collapsible-content {
      max-height: 485px;
    }
  }

  .chat-card-log {
    height: 360px;

    .chat-sent-msg {
      background-color: #f2f4f7 !important;
    }
  }

  .card-video {
    .video-js {
      height: 370px;
    }
  }
}
</style>
