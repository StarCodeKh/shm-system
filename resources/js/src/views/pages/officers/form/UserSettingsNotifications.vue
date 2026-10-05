<template>
  <vx-card no-shadow>

    <div class="mb-base">
      <h6 class="mb-4">ការកំំណត់</h6>

      <div class="flex items-center mb-4">
        <vs-switch v-model="email" />
        <span class="ml-4">ផ្ញើរសារមកខ្ញុំប្រសិនបើមានអ្នកចូលក្នុងគណនី</span>
      </div>
      <!-- <div class="flex items-center mb-4">
        <vs-switch v-model="answer" />
        <span class="ml-4">Email me when someone answers on my form</span>
      </div>
      <div class="flex items-center mb-4">
        <vs-switch v-model="follow" />
        <span class="ml-4">Email me hen someone follows me</span>
      </div> -->

      <div class="flex items-center mb-4">
        <vs-switch v-model="incentive" />
        <span class="ml-4">ជូនដំណឹងប្រាក់ឧបត្ថម្ភ</span>
      </div>
      <div class="flex items-center mb-4">
        <vs-switch v-model="pending_account" />
        <span class="ml-4">បិទគណនីបណ្តោះអាសន្ន</span>
      </div>
      <!-- <div class="flex items-center">
        <vs-switch v-model="blog" />
        <span class="ml-4">Weekly blog digest</span>
      </div> -->
    </div>

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end mt-base">
      <vs-button class="ml-auto mt-2">រក្សាទុក</vs-button>
      <!-- <vs-button class="ml-4 mt-2" type="border" color="warning">Reset</vs-button> -->
    </div>

  </vx-card>
</template>

<script>
export default {
  data() {
    return {
      email: true,
      answer: true,
      follow: true,
      incentive: true,
      pending_account: false,
      blog: true,
    }
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>
