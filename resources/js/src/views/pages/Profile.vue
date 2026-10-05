
<template>
    <div id="profile-page">

        <!-- PROFILE HEADER -->
        <div class="profile-header">
            <div class="relative">
                <div class="cover-container rounded-t-lg">
                    <img :src="user_info.cover_img" alt="user-profile-cover" class="responsive block">
                </div>
                <div class="profile-img-container pointer-events-none">
                    <div>
                        <vs-avatar class="user-profile-img" :src="user_info.profile_img" size="85px" />
                    </div>
                    <div class="profile-actions pointer-events-auto flex">
                        <vs-button @click="editProfile()" icon-pack="feather" radius icon="icon-edit-2"></vs-button>
                        <vs-button @click="editSetting()" icon-pack="feather" radius icon="icon-settings" class="ml-2 lg:ml-4"></vs-button>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end flex-wrap profile-header-nav p-6">

                <div class="block sm:hidden">
                    <feather-icon @click="isNavOpen = !isNavOpen" icon="AlignJustifyIcon" v-show="!isNavOpen" class="vx-navbar-toggler cursor-pointer" />
                    <feather-icon icon="XIcon" v-show="isNavOpen" @click="isNavOpen = !isNavOpen" class="vx-navbar-toggler cursor-pointer" />
                </div>
                <div :class="isNavOpen ? 'block': 'hidden'" class="w-full flex-grow sm:flex sm:items-center sm:w-auto">
                  <div class="text-md sm:flex-grow">
                    <ul class="sm:flex justify-around mt-8 w-full md:mt-0 md:ml-auto md:w-3/4">
                      <li class="p-2 sm:p-0"><router-link to="/pages/profile">ព័ត៌មានផ្ទាល់ខួ្លន</router-link></li>
                      <li class="p-2 sm:p-0"><router-link to="/pages/profile">ព័ត៌មានប្រាក់ឧបត្ថម្ភ</router-link></li>
                      <li class="p-2 sm:p-0"><router-link to="/pages/profile">ការប្លាស់ប្តូរគណនី៍ធនាគា</router-link></li>
                      <li class="p-2 sm:p-0"><router-link to="/pages/profile">របាយការណ៍ការងារ</router-link></li>
                    </ul>
                  </div>
                </div>
            </div>
        </div>

        <!-- COL AREA -->
        <div class="vx-row">
            <!-- COL 1 -->
            <div class="vx-col w-full lg:w-1/4">
                <!-- ABOUT CARD -->
                <vx-card title="ព័ត៌មានលម្អិត" class="mt-base">
                    <!-- ACTION SLOT -->
                    <template slot="actions">
                        <feather-icon icon="MoreHorizontalIcon"></feather-icon>
                    </template>

                    <!-- USER BIO -->
                    <!-- <div class="user-bio">
                        <p>Tart I love sugar plum I love oat cake. Sweet roll caramels I love jujubes. Topping cake wafer.</p>
                    </div> -->

                    <!-- OTEHR DATA -->
                    <div class="mt-5">
                        <h6>ឈ្មោះ: យ៉ន ម៉ាលឹមឆេង</h6>
                    </div>

                    <div class="mt-5">
                        <h6>តួនាទី: មន្ត្រីជំនាញជាន់ខ្ពស់</h6>
                    </div>

                    <div class="mt-5">
                        <h6>លេខទំនាក់ទំនង:​ ០៨១ ៣៣៤ ៤៥៥</h6>
                    </div>

                    <div class="mt-5">
                        <h6>អ៊ីម៉ែល: user@pfm.gov.kh</h6>
                    </div>

                    <div class="mt-5">
                        <h6>អាស័យដ្ឋាន: ផ្ទះ១៨ ផ្លូវ១៤ សង្កាត់ចោមចៅ ខ័ណ្ឌពោសែនជ័យរាជធានីភ្នំពេញ។</h6>
                    </div>

                    <div class="mt-5">
                        <h6>Website: www.pfm.gov.</h6>
                    </div>

                    <div class="social-links flex mt-4">
                        <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="FacebookIcon"></feather-icon>
                        <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="TwitterIcon"></feather-icon>
                        <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="InstagramIcon"></feather-icon>
                    </div>
                </vx-card>
                <!-- TWITER FEEDS -->
                <!-- <vx-card title="Twitter Feeds" class="mt-base">
                    <ul class="twitter-feeds-list">
                        <li class="twitter-feed" :class="{'mt-8': index}" v-for="(feed, index) in twitterFeeds" :key="feed.id">
                            <div class="twitter-feed-header flex items-center">
                                <vs-avatar class="m-0" :src="feed.authorAvatar" size="35px" />
                                <div class="leading-tight ml-3">
                                    <p class="feed-author font-semibold">{{ feed.authorDisplayName }}</p>
                                    <span class="flex items-center"><small>@{{ feed.authorUsername }}</small><feather-icon class="ml-1" icon="CheckIcon" svgClasses="h-3 w-3 bg-primary rounded-full text-white"></feather-icon></span>
                                </div>
                            </div>

                            <p class="mt-4">{{ feed.content }}</p>
                            <div class="tags-container" v-if="feed.tags.length">
                                <span v-for="tag in feed.tags" :key="tag" id="tag" class="mr-2 text-primary">#{{tag}}</span>
                            </div>
                            <small class="mt-3 inline-block">{{ feed.time | date(true) }}</small>
                        </li>
                    </ul>
                </vx-card> -->
            </div>

            <!-- COL 2 -->
            <div class="vx-col w-full lg:w-1/2">
              <vx-card title="ប្រតិបត្តិការថ្មីៗ" class="mt-base">
                
                <!-- data-list -->
                <vs-table ref="table" multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="products">

                  <template slot="thead">
                    <!-- <vs-th sort-key="name">គោត្តនាម និង នាម</vs-th> -->
                    <vs-th sort-key="role">តួនាទី</vs-th>
                    <vs-th sort-key="bank">ធនាគារ</vs-th>
                    <vs-th sort-key="bank_acc">លេខគណនីចាស់</vs-th>
                    <vs-th sort-key="bank_acc">លេខគណនីថ្មី</vs-th>
                    <vs-th sort-key="status">ស្ថានភាព</vs-th>
                    <vs-th sort-key="total_incentive">ប្រាក់ឧបត្ថម្ភសរុប</vs-th>
                  </template>

                    <template slot-scope="{data}">
                      <tbody>
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                          <!-- <vs-td>
                            <p class="product-name font-medium truncate" @click.stop="viewOffierDetail(tr)">{{ tr.name }}</p>
                          </vs-td> -->

                          <vs-td>
                            <p class="product-role" @click.stop="viewOffierDetail(tr)">{{ tr.role }}</p>
                          </vs-td>

                          <vs-td>
                            <p class="product-bank" @click.stop="viewOffierDetail(tr)">{{ tr.bank | title }}</p>
                          </vs-td>

                          <vs-td>
                            <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">{{ tr.bank_acc_no }}</vs-chip>
                          </vs-td>

                          <vs-td>
                            <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">{{ tr.bank_acc_no }}</vs-chip>
                          </vs-td>

                          <vs-td>
                            <vs-chip :color="getOrderStatusColor('success')" class="product-order-status">{{ tr.status }}</vs-chip>
                            <!-- <vs-progress :percent="Number(tr.popularity)" :color="getPopularityColor(Number(tr.popularity))" class="shadow-md" /> -->
                            <!-- <p class="product-bank">{{ tr.status }}</p> -->
                          </vs-td>

                          <vs-td>
                            <p class="product-price">{{ tr.price }} ៛</p>
                          </vs-td>

                        </vs-tr>
                      </tbody>
                    </template>
                </vs-table>

                <div class="vx-row">
                  <div class="vx-col w-full">
                      
                  </div>
                </div>
              </vx-card>  

              <vx-card title="ប្រវត្តនៃការប្លាស់ប្តូរគណនី" class="mt-base">
                <!-- data-list -->
                <vs-table ref="table" multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="products">

                  <template slot="thead">
                    <!-- <vs-th sort-key="name">គោត្តនាម និង នាម</vs-th> -->
                    <vs-th sort-key="role">តួនាទី</vs-th>
                    <vs-th sort-key="bank">ធនាគារ</vs-th>
                    <vs-th sort-key="bank_acc">លេខគណនីចាស់</vs-th>
                    <vs-th sort-key="bank_acc">លេខគណនីថ្មី</vs-th>
                  </template>

                    <template slot-scope="{data}">
                      <tbody>
                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                          <!-- <vs-td>
                            <p class="product-name font-medium truncate" @click.stop="viewOffierDetail(tr)">{{ tr.name }}</p>
                          </vs-td> -->

                          <vs-td>
                            <p class="product-role" @click.stop="viewOffierDetail(tr)">{{ tr.role }}</p>
                          </vs-td>

                          <vs-td>
                            <p class="product-bank" @click.stop="viewOffierDetail(tr)">{{ tr.bank | title }}</p>
                          </vs-td>

                          <vs-td>
                            <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">{{ tr.bank_acc_no }}</vs-chip>
                          </vs-td>

                          <vs-td>
                            <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">{{ tr.bank_acc_no }}</vs-chip>
                          </vs-td>

                        </vs-tr>
                      </tbody>
                    </template>
                </vs-table>
              </vx-card>

              <vx-card title="របាយការណ៍ការងារ" class="mt-base">
                ពិពណ៌នា
                <div class="vx-row">
                  <div class="vx-col w-full">
                      
                  </div>
                </div>
              </vx-card> 

              <vx-card title="ការងារពេញចិត្ត" class="mt-base">
                ពិពណ៌នា
                <div class="vx-row">
                  <div class="vx-col w-full">
                      
                  </div>
                </div>
              </vx-card>  
              <!-- <vx-card class="mt-base" v-for="(post, index) in userPosts" :key="index">
                  <div>
                      <div class="post-header flex justify-between mb-4">
                          <div class="flex items-center">
                              <div>
                                  <vs-avatar class="m-0" :src="userLatestPhotos[0]" size="45px"></vs-avatar>
                              </div>
                              <div class="ml-4">
                                  <h6>{{ post.author }}</h6>
                                  <small>{{ post.time | date(true) }} at {{ post.time | time }}</small>
                              </div>
                          </div>
                          <div class="flex">
                              <feather-icon class="ml-4" icon="HeartIcon" :svgClasses="{'text-danger fill-current stroke-current': post.isLiked}"></feather-icon>
                          </div>
                      </div>

                      <div class="post-content">
                          <p>{{ post.text }}</p>
                      </div>
                      <div class="post-media-container mb-6 mt-4">
                          <ul class="flex post-media-list">
                              <li class="post-media m-1 w-full" v-for="(media, mediaIdex) in post.media.slice(0, 2)" :key="mediaIdex">
                                  <img class="responsive rounded" :src="media.img" alt="user-upload" v-if="mediaType(media) == 'image'">
                                  <video-player ref="player" class="media-video-player" :options="playerOptions(media)" v-else-if="mediaType(media) == 'video'" v-once />
                                  <span class="text-lg text-primary" v-else>Unknown Media Type</span>
                              </li>
                          </ul>
                          <span class="flex justify-end" v-if="post.media.length > 2">
                              <a class="inline-flex items-center text-sm" href=""><span>View All</span> <feather-icon :icon="$vs.rtl ? 'ChevronsLeftIcon' : 'ChevronsRightIcon'" svgClasses="h-4 w-4"></feather-icon></a>
                          </span>
                      </div>

                      <div>
                          <div class="flex justify-between">
                              <div class="flex items-center">
                                  <div class="flex items-center"><feather-icon class="mr-2" icon="HeartIcon" svgClasses="h-5 w-5"></feather-icon> <span>{{ post.likes }}</span></div>
                                  <ul class="users-liked user-list ml-3 sm:ml-6">
                                      <li v-for="(user, userIndex) in post.usersLiked" :key="userIndex">
                                          <vx-tooltip :text="user.name" position="bottom">
                                              <vs-avatar :src="user.img" size="30px" class="border-2 border-white border-solid -m-1"></vs-avatar>
                                          </vx-tooltip>
                                      </li>
                                  </ul>
                                  <small class="ml-2">+{{ post.likes - 5 }} more</small>
                              </div>
                              <div class="flex items-center"><feather-icon class="mr-2" icon="MessageSquareIcon" svgClasses="h-5 w-5"></feather-icon> <span>{{ post.comments }}</span></div>
                          </div>
                          <div class="comments-container mt-4">
                              <ul class="user-comments-list">
                                  <li v-for="(commentedUser, commentIndex) in post.usersCommented.slice(0, 2)" :key="commentIndex" class="commented-user flex items-center mb-4">
                                      <div class="mr-3"><vs-avatar class="m-0" :src="commentedUser.img" size="30px" /></div>
                                      <div class="leading-tight">
                                          <p class="font-medium">{{ commentedUser.author }}</p>
                                          <span class="text-xs">{{ commentedUser.comment }}</span>
                                      </div>
                                      <div class="ml-auto">
                                          <div class="flex">
                                              <feather-icon icon="HeartIcon" svgClasses="h-4 w-4" class="mr-2 cursor-pointer"></feather-icon>
                                              <feather-icon icon="MessageSquareIcon" svgClasses="h-4 w-4" class="cursor-pointer"></feather-icon>
                                          </div>
                                      </div>
                                  </li>
                              </ul>
                              <span class="flex justify-end" v-if="post.usersCommented.length > 2">
                                  <a class="inline-flex items-center text-sm" href=""><span>View All</span> <feather-icon :icon="$vs.rtl ? 'ChevronsLeftIcon' : 'ChevronsRightIcon'" svgClasses="h-4 w-4"></feather-icon></a>
                              </span>
                          </div>
                          <div class="post-comment">
                              <vs-textarea label="Add Comment" class="mb-2" v-model="post.commentbox" />
                              <vs-button size="small">Post Comment</vs-button>
                          </div>
                      </div>
                  </div>
              </vx-card> -->
            </div>

            <!-- COL 3 -->
            <div class="vx-col w-full lg:w-1/4">
              <vx-card title="សំណើរ" class="mt-base">
                <!-- ACTION SLOT -->
                <!-- <template slot="actions">
                    <feather-icon icon="MoreHorizontalIcon"></feather-icon>
                </template> -->
                ពិពណ៌នា
              </vx-card>

              <!-- PAGES SUGGESTION -->
              <vx-card title="សង្ខេបការងារ" class="mt-base">
                <!-- CARD 8: Activity Timeline -->
                <vx-card title="សកម្មភាពពេលវេលា">
                    <vx-timeline :data="timelineData" />
                </vx-card>
                <!-- <ul class="page-suggestions-list">
                    <li class="page-suggestion flex items-center mb-4" v-for="page in suggestedPages" :key="page.index">
                        <div class="mr-3"><vs-avatar class="m-0" :src="page.img" size="35px" /></div>
                        <div class="leading-tight">
                            <p class="font-medium">{{ page.title | capitalize }}</p>
                            <span class="text-xs">{{ page.type | capitalize }}</span>
                        </div>
                        <div class="ml-auto">
                            <div class="flex">
                                <feather-icon icon="StarIcon" svgClasses="h-4 w-4" class="mr-2 cursor-pointer"></feather-icon>
                            </div>
                            <span class="flex bg-primary rounded p-2 text-white"><feather-icon icon="UserPlusIcon" svgClasses="w-4 h-4"></feather-icon></span>
                        </div>
                    </li>
                </ul> -->
              </vx-card>
              
              <!-- សមិទ្ធកម្មការងារ -->
              <vx-card title="សមិទ្ធកម្មការងារ" class="mt-base">
                <div class="vx-row pt-2">
                  <!-- <div class="vx-col w-1/2 sm:w-1/2 md:w-1/3 xl:1/4" v-for="(img, index) in userLatestPhotos" :key="index">
                    <img :src="img" alt="latest-upload" class="rounded mb-4 user-latest-image responsive">
                  </div> -->
                </div>
              </vx-card>

              <!-- <vx-card title="Polls" class="mt-base">
                  <ul class="polls-list">
                      <li class="poll" v-for="poll in polls" :key="poll.id">
                          <h6 class="poll-title">{{ poll.title }}</h6>
                          <ul class="poll-options-result">
                              <li class="poll-option mt-6" v-for="option in poll.options" :key="option.value">
                                  <div class="flex">
                                      <vs-radio v-model="userPoll" :vs-value="option.value">{{ option.text | capitalize }}</vs-radio>
                                      <span class="block ml-auto">{{ option.voted }}%</span>
                                  </div>
                                  <vs-progress :percent="option.voted"></vs-progress>
                                  <ul class="users-voted user-list mt-2">
                                      <li v-for="(user, userIndex) in option.usersVoted" :key="userIndex">
                                          <vx-tooltip :text="user.name" position="bottom">
                                              <vs-avatar :src="user.avatar" size="30px" class="border-2 border-white border-solid -m-1"></vs-avatar>
                                          </vx-tooltip>
                                      </li>
                                  </ul>
                              </li>
                          </ul>
                          <vs-button class="mt-5 w-full">Vote Now</vs-button>
                      </li>
                  </ul>
              </vx-card> -->
            </div>
        </div>
    </div>
</template>

<script>
import { videoPlayer } from 'vue-video-player'
import 'video.js/dist/video-js.css'
import VxTimeline from "@/components/timeline/VxTimeline"
import moduleDataList from "@/store/data-list/moduleDataList.js"

export default {
  data() {
    return {
      selected: [],
      // products: [],
      itemsPerPage: 10,
      isMounted: false,

      // Data Sidebar
      addNewDataSidebar: false,
      sidebarData: {},
      isNavOpen: false,
      userPoll: '',
      user_info: {
        profile_img: require("@assets/images/profile/user-uploads/limcheng.png"),
        cover_img: require("@assets/images/profile/user-uploads/cover.jpg"),
      },
      mediaExtensions: { img: ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'exif', 'tiff'], video: ['avi', 'flv', 'wmv', 'mov', 'mp4', '3gp'] },
      suggestedFriends: [
        { name: 'Carissa Dolle', avatar: require("@assets/images/portrait/small/avatar-s-5.jpg"),  mutualFriends: 6 },
        { name: 'Liliana Pecor', avatar: require("@assets/images/portrait/small/avatar-s-6.jpg"),  mutualFriends: 3 },
        { name: 'Isidra Strunk', avatar: require("@assets/images/portrait/small/avatar-s-7.jpg"),  mutualFriends: 2 },
        { name: 'Gerald Licea',  avatar: require("@assets/images/portrait/small/avatar-s-8.jpg"),  mutualFriends: 1 },
        { name: 'Kelle Herrick', avatar: require("@assets/images/portrait/small/avatar-s-9.jpg"),  mutualFriends: 1 },
        { name: 'Cesar Lee',     avatar: require("@assets/images/portrait/small/avatar-s-10.jpg"), mutualFriends: 1 },
        { name: 'John Doe',      avatar: require("@assets/images/portrait/small/avatar-s-11.jpg"), mutualFriends: 1 },
        { name: 'Tonia Seabold', avatar: require("@assets/images/portrait/small/avatar-s-12.jpg"), mutualFriends: 1 },
      ],
      userLatestPhotos: [
        require("@assets/images/profile/user-uploads/user-01.jpg"),
        require("@assets/images/profile/user-uploads/user-02.jpg"),
        require("@assets/images/profile/user-uploads/user-04.jpg"),
        require("@assets/images/profile/user-uploads/user-03.jpg"),
        require("@assets/images/profile/user-uploads/user-05.jpg"),
        require("@assets/images/profile/user-uploads/user-06.jpg"),
        require("@assets/images/profile/user-uploads/user-07.jpg"),
        require("@assets/images/profile/user-uploads/user-08.jpg"),
        require("@assets/images/profile/user-uploads/user-09.jpg")
      ],
      userPosts: [{
          author     : 'Leeanna Alvord',
          time       : 'Mon Dec 12 2018 07:46:05 GMT+0000 (GMT)',
          isLiked    : true,
          text       : 'I love jujubes wafer pie ice cream tiramisu. Chocolate I love pastry pastry sesame snaps wafer. Pastry topping biscuit lollipop topping I love lemon drops sweet roll bonbon. Brownie donut icing.',
          media      : [{ img: require("@assets/images/profile/post-media/2.jpg") }],
          likes      : 145,
          comments   : 77,
          usersLiked : [
            { name: 'Trina Lynes',       img: require("@assets/images/portrait/small/avatar-s-1.jpg") },
            { name: 'Lilian Nenez',      img: require("@assets/images/portrait/small/avatar-s-2.jpg") },
            { name: 'Alberto Glotzbach', img: require("@assets/images/portrait/small/avatar-s-3.jpg") },
            { name: 'George Nordick',    img: require("@assets/images/portrait/small/avatar-s-4.jpg") },
            { name: 'Vennie Mostowy',    img: require("@assets/images/portrait/small/avatar-s-5.jpg") },
          ],
          commentbox : '',
          usersCommented: [
            {
              comment : 'orthoplumbate morningtide naphthaline exarteritis',
              author  : 'Kitty Allanson',
              img     : require("@assets/images/portrait/small/avatar-s-6.jpg"),
              time    : 'Mon Dec 10 2018 08:56:05 GMT+0000 (GMT)'
            },
            {
              comment : 'blockiness pandemy metaxylene speckle coppy',
              author  : 'Jeanie Bulgrin',
              img     : require("@assets/images/portrait/small/avatar-s-8.jpg"),
              time    : 'Mon Dec 10 2018 08:55:00 GMT+0000 (GMT)'
            },
          ]
        },
        // {
        //   author     : 'Leeanna Alvord',
        //   time       : 'Mon Dec 11 2018 08:05:05 GMT+0000 (GMT)',
        //   isLiked    : false,
        //   text       : 'Candy jelly beans powder brownie biscuit. Jelly marzipan oat cake cake. Cupcake I love wafer cake. Halvah I love powder jelly I love cheesecake cotton candy tiramisu brownie.',
        //   media      : [{ img: require("@assets/images/profile/post-media/25.jpg") }],
        //   likes      : 276,
        //   comments   : 105,
        //   usersLiked : [
        //     { name: 'Lai Lewandowski',   img: require("@assets/images/portrait/small/avatar-s-6.jpg")  },
        //     { name: 'Elicia Rieske',     img: require("@assets/images/portrait/small/avatar-s-7.jpg")  },
        //     { name: 'Darcey Nooner',     img: require("@assets/images/portrait/small/avatar-s-8.jpg")  },
        //     { name: 'Julee Rossignol',   img: require("@assets/images/portrait/small/avatar-s-10.jpg") },
        //     { name: 'Jeffrey Gerondale', img: require("@assets/images/portrait/small/avatar-s-9.jpg")  },
        //   ],
        //   commentbox: '',
        //   usersCommented: [
        //     {
        //       comment : 'I love cupcake danish jujubes sweet.',
        //       author  : 'Darcey Nooner',
        //       img     : require("@assets/images/portrait/small/avatar-s-8.jpg"),
        //       time    : 'Mon Dec 11 2018 09:56:05 GMT+0000 (GMT)'
        //     },
        //     {
        //       comment : 'Wafer I love brownie jelly bonbon tart apple pie',
        //       author  : 'Lai Lewandowski',
        //       img     : require("@assets/images/portrait/small/avatar-s-6.jpg"),
        //       time    : 'Mon Dec 10 2018 09:50:00 GMT+0000 (GMT)'
        //     },
        //   ]
        // },
        // {
        //   author     : 'Leeanna Alvord',
        //   time       : 'Mon Dec 10 2018 12:05:05 GMT+0000 (GMT)',
        //   isLiked    : false,
        //   text       : 'Wafer I love brownie jelly bonbon tart. Candy jelly beans powder brownie biscuit. Jelly marzipan oat cake cake.',
        //   media      : [{ sources: [{ type: "video/mp4", src: "http://vjs.zencdn.net/v/oceans.mp4" }], poster: 'https://goo.gl/xcCsDd' }],
        //   likes      : 269,
        //   comments   : 98,
        //   usersLiked : [
        //     { name: 'Vennie Mostowy',  img: require("@assets/images/portrait/small/avatar-s-5.jpg")  },
        //     { name: 'Elicia Rieske',   img: require("@assets/images/portrait/small/avatar-s-7.jpg")  },
        //     { name: 'Julee Rossignol', img: require("@assets/images/portrait/small/avatar-s-10.jpg") },
        //     { name: 'Darcey Nooner',   img: require("@assets/images/portrait/small/avatar-s-8.jpg")  },
        //     { name: 'Elicia Rieske',   img: require("@assets/images/portrait/small/avatar-s-7.jpg")  },
        //   ],
        //   commentbox: '',
        //   usersCommented: [
        //     {
        //       comment : 'I love cupcake danish jujubes sweet.',
        //       author  : 'Darcey Nooner',
        //       img     : require("@assets/images/portrait/small/avatar-s-8.jpg"),
        //       time    : 'Mon Dec 11 2018 09:56:05 GMT+0000 (GMT)'
        //     },
        //     {
        //       comment : 'Wafer I love brownie jelly bonbon tart apple pie',
        //       author  : 'Lai Lewandowski',
        //       img     : require("@assets/images/portrait/small/avatar-s-6.jpg"),
        //       time    : 'Mon Dec 10 2018 09:50:00 GMT+0000 (GMT)'
        //     },
        //   ]
        // }
      ],
      suggestedPages: [
        { img: require("@assets/images/profile/pages/page-09.jpg"), title: 'Rockose',      type: 'Company'          },
        { img: require("@assets/images/profile/pages/page-08.jpg"), title: 'The Devil\'s', type: 'Clothing Store'   },
        { img: require("@assets/images/profile/pages/page-03.jpg"), title: 'The Magician', type: 'Public Figure'    },
        { img: require("@assets/images/profile/pages/page-02.jpg"), title: 'AC/DC',        type: 'Music'            },
        { img: require("@assets/images/profile/pages/page-07.jpg"), title: 'eat hard',     type: 'restaurant / bar' },
        { img: require("@assets/images/profile/pages/page-04.jpg"), title: 'B4B',          type: 'Beauty Store'     },
        { img: require("@assets/images/profile/pages/page-05.jpg"), title: 'Kylie Jenner', type: 'Public Figure'    },
        { img: require("@assets/images/profile/pages/page-01.jpg"), title: 'RDJ',          type: 'Actor'            },
        { img: require("@assets/images/profile/pages/page-06.jpg"), title: 'Taylor Swift', type: 'Music'            },
      ],
      polls: [{
        id: 59,
        title: 'Who is the best actor in Marvel Cinematic Universe?',
        options: [{
            text       : 'RDJ',
            value      : 'rdj',
            voted      : 58,
            usersVoted : [
              { name: 'Tonia Seabold',  avatar: require("@assets/images/portrait/small/avatar-s-12.jpg") },
              { name: 'Carissa Dolle',  avatar: require("@assets/images/portrait/small/avatar-s-5.jpg") },
              { name: 'Kelle Herrick',  avatar: require("@assets/images/portrait/small/avatar-s-9.jpg") },
              { name: 'Len Bregantini', avatar: require("@assets/images/portrait/small/avatar-s-10.jpg") },
              { name: 'John Doe',       avatar: require("@assets/images/portrait/small/avatar-s-11.jpg") },
              { name: 'Tonia Seabold',  avatar: require("@assets/images/portrait/small/avatar-s-12.jpg") },
              { name: 'Dirk Fornili',   avatar: require("@assets/images/portrait/small/avatar-s-2.jpg") },
            ]
          },
          {
            text       : 'Chris Hemsworth',
            value      : 'chris hemsworth',
            voted      : 16,
            usersVoted : [
              { name: 'Liliana Pecor',      avatar: require("@assets/images/portrait/small/avatar-s-6.jpg") },
              { name: 'Kasandra Nalevanko', avatar: require("@assets/images/portrait/small/avatar-s-1.jpg") },
            ]
          },
          {
            text       : 'mark ruffalo',
            value      : 'mark ruffalo',
            voted      : 8,
            usersVoted : [
              { name: 'Lorelei Lacsamana', avatar: require("@assets/images/portrait/small/avatar-s-4.jpg") },
            ]
          },
          {
            text       : 'Chris Evans',
            value      : 'chris evans',
            voted      : 16,
            usersVoted : [
              { name: 'Jeanie Bulgrin', avatar: require("@assets/images/portrait/small/avatar-s-8.jpg") },
              { name: 'Graig Muckey',   avatar: require("@assets/images/portrait/small/avatar-s-3.jpg") },
            ]
          },
        ]

      }],
      twitterFeeds: [
        {
          authorAvatar      : require("@assets/images/portrait/small/avatar-s-12.jpg"),
          authorUsername    : 'tiana59',
          authorDisplayName : 'Tiana Vercetti',
          content           : 'I love cookie chupa chups sweet tart apple pie chocolate bar. Jelly-o oat cake chupa chups.',
          tags              : ['js', 'vuejs'],
          time              : 'Mon Dec 12 2018 07:46:05 GMT+0000 (GMT)',
        },
        {
          authorAvatar      : require("@assets/images/portrait/small/avatar-s-12.jpg"),
          authorUsername    : 'tiana59',
          authorDisplayName : 'Tiana Vercetti',
          content           : 'Carrot cake cake gummies I love I love tiramisu. Biscuit marzipan cookie lemon drops.',
          tags              : ['python'],
          time              : 'Mon Dec 11 2018 01:05:05 GMT+0000 (GMT)',
        },
        {
          authorAvatar      : require("@assets/images/portrait/small/avatar-s-12.jpg"),
          authorUsername    : 'tiana59',
          authorDisplayName : 'Tiana Vercetti',
          content           : 'I love cookie chupa chups sweet tart apple pie chocolate bar. Jelly-o oat cake chupa chups .',
          tags              : [],
          time              : 'Mon Dec 10 2018 03:33:05 GMT+0000 (GMT)',
        },
        {
          authorAvatar      : require("@assets/images/portrait/small/avatar-s-12.jpg"),
          authorUsername    : 'tiana59',
          authorDisplayName : 'Tiana Vercetti',
          content           : 'Muffin candy caramels. I love caramels tiramisu jelly. Pie I love wafer. Chocolate cake lollipop tootsie roll cake.',
          tags              : ['django', 'vuejs'],
          time              : 'Mon Dec 9 2018 08:47:05 GMT+0000 (GMT)',
        },
      ],
      wasSidebarOpen: null,
      timelineData: [
        {
          color: "primary",
          icon: "PlusIcon",
          title: "ធ្វើសន្និសិទ្ទ",
          desc: "ពិពណ៌នា ការងារ",
          time: "25 mins Ago"
        },
        {
          color: "warning",
          icon: "MailIcon",
          title: "បើកការប្រជុំប្រចាំឆ្នាំ",
          desc: "ពិពណ៌នា ការងារ",
          time: "15 Days Ago"
        },
        {
          color: "danger",
          icon: "UsersIcon",
          title: "ការងារចុះទៅក្រៅប្រទេស",
          desc: "ពិពណ៌នា ការងារ",
          time: "20 days ago"
        },
        {
          color: "success",
          icon: "LayoutIcon",
          title: "បើកការប្រជុំប្រចាំឆ្នាំ",
          desc: "ពិពណ៌នា ការងារ",
          time: "25 days ago"
        },
        {
          color: "primary",
          icon: "TvIcon",
          title: "បើកការប្រជុំប្រចាំឆ្នាំ",
          desc: "ពិពណ៌នា ការងារ",
          time: "28 days ago"
        }
      ],
    }
  },
  computed: {
    currentPage() {
      if(this.isMounted) {
        return this.$refs.table.currentx
      }
      return 0
    },
    products() {
      return this.$store.state.dataList.products
    },
    queriedItems() {
      return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
    },
    mediaType() {
      return (media) => {
        if (media.img) {
          const ext = media.img.split('.').pop();
          if (this.mediaExtensions.img.includes(ext)) return 'image'
        } else if (media.sources && media.poster) {
          // other validations
          return 'video'
        }
      }
    },
    playerOptions() {
      return (media) => {
        return {
          height: '360',
          fluid: true,
          autoplay: false,
          muted: true,
          language: 'en',
          playbackRates: [0.7, 1.0, 1.5, 2.0],
          sources: media.sources,
          poster: media.poster,
        }
      }
    }
  },
  methods: {
    addNewData() {
      this.sidebarData = {}
      this.toggleDataSidebar(true)
    },
    deleteData(id) {
      this.$store.dispatch("dataList/removeItem", id).catch(err => { console.error(err) })
    },
    editData(data) {
      // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
      this.sidebarData = data
      this.toggleDataSidebar(true)
    },
    getOrderStatusColor(status) {
      if(status == 'on_hold') return "warning"
      if(status == 'delivered') return "success"
      if(status == 'canceled') return "danger"
      return "primary"
    },
    getPopularityColor(num) {
      if(num > 90) return "success"
      if(num >70) return "primary"
      if(num >= 50) return "warning"
      if(num < 50) return "danger"
      return "primary"
    },
    toggleDataSidebar(val=false) {
      this.addNewDataSidebar = val
    },
    viewOffierDetail(offierId){
      this.$router.push('/pages/profile')
    },
    loadContent() {
      this.$vs.loading({
        background: this.backgroundLoading,
        color: this.colorLoading,
        container: "#button-load-more-posts",
        scale: 0.45
      })
      setTimeout(() => {
        this.$vs.loading.close("#button-load-more-posts > .con-vs-loading")
      }, 3000);
    },
    editProfile(){
      console.log(localStorage.getItem("accessToken"));
      this.$router.push("/apps/user/user-edit/268")
    },
    editSetting(){
      this.$router.push("/pages/user-settings").catch(() => {})
    }
  },
  components: {
    videoPlayer,
    VxTimeline
  },
  mounted() {
    this.wasSidebarOpen = this.$store.state.reduceButton;
    this.$store.commit('TOGGLE_REDUCE_BUTTON', true)
    this.isMounted = true;
  },
  created() {
    if(!moduleDataList.isRegistered) {
      this.$store.registerModule('dataList', moduleDataList)
      moduleDataList.isRegistered = true
    }
    this.$store.dispatch("dataList/fetchDataListIncentiveItems")
  },
  beforeDestroy() {
    if (!this.wasSidebarOpen) this.$store.commit('TOGGLE_REDUCE_BUTTON', false)
  }
}

</script>


<style lang="scss">
  @import "@sass/vuexy/pages/profile.scss";
  #data-list-list-view {
    font-family: var(--font-base);
    .vs-con-table {

      /*
        Below media-queries is fix for responsiveness of action buttons
        Note: If you change action buttons or layout of this page, Please remove below style
      */
      @media (max-width: 689px) {
        .vs-table--search {
          margin-left: 0;
          max-width: unset;
          width: 100%;

          .vs-table--search-input {
            width: 100%;
          }
        }
      }

      @media (max-width: 461px) {
        .items-per-page-handler {
          display: none;
        }
      }

      @media (max-width: 341px) {
        .data-list-btn-container {
          width: 100%;

          .dd-actions,
          .btn-add-new {
            width: 100%;
            margin-right: 0 !important;
          }
        }
      }

      .product-name {
        max-width: 23rem;
      }

      .vs-table--header {
        display: flex;
        flex-wrap: wrap;
        margin-left: 1.5rem;
        margin-right: 1.5rem;
        > span {
          display: flex;
          flex-grow: 1;
        }

        .vs-table--search{
          padding-top: 0;

          .vs-table--search-input {
            padding: 0.9rem 2.5rem;
            font-size: 1rem;

            &+i {
              left: 1rem;
            }

            &:focus+i {
              left: 1rem;
            }
          }
        }
      }

      .vs-table {
        border-collapse: separate;
        border-spacing: 0 1.3rem;
        padding: 0 1rem;

        tr{
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.05);
            td{
              padding: 20px;
              &:first-child{
                border-top-left-radius: .5rem;
                border-bottom-left-radius: .5rem;
              }
              &:last-child{
                border-top-right-radius: .5rem;
                border-bottom-right-radius: .5rem;
              }
            }
            td.td-check{
              padding: 20px !important;
            }
        }
      }

      .vs-table--thead{
        th {
          padding-top: 0;
          padding-bottom: 0;

          .vs-table-text{
            text-transform: uppercase;
            font-weight: 600;
          }
        }
        th.td-check{
          padding: 0 15px !important;
        }
        tr{
          background: none;
          box-shadow: none;
        }
      }

      .vs-table--pagination {
        justify-content: center;
      }
    }
  }
</style>
