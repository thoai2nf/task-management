<template>
  <div class="loading-container">
    <v-progress-circular
        :size="size"
        :width="width"
        color="primary"
        indeterminate
    ></v-progress-circular>
    <div v-if="text" class="loading-text mt-2">{{ text }}</div>
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: [String, Number],
    default: 50
  },
  width: {
    type: [String, Number],
    default: 4
  },
  text: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.loading-text {
  font-size: 14px;
  color: #666;
}
</style>