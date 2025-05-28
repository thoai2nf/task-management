<template>
  <v-navigation-drawer app v-model="drawer">
    <v-list nav>
      <v-list-item
          v-for="item in menuItems"
          :key="item.title"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
      ></v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const drawer = ref(props.modelValue)

watch(() => props.modelValue, (newVal) => {
  drawer.value = newVal
})

watch(drawer, (newVal) => {
  emit('update:modelValue', newVal)
})

const menuItems = [
  {
    title: 'Dashboard',
    icon: 'mdi-view-dashboard',
    to: '/dashboard'
  },
  {
    title: 'Dự án',
    icon: 'mdi-folder-multiple',
    to: '/projects'
  }
]
</script>