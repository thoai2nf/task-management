<template>
  <v-card class="mb-4" elevation="2">
    <v-card-title class="d-flex justify-space-between align-center">
      <span>{{ project.title }}</span>
      <v-chip
          :color="statusColor"
          size="small"
          variant="flat"
      >
        {{ statusText }}
      </v-chip>
    </v-card-title>

    <v-card-text>
      <p v-if="project.description" class="text-body-2 mb-2">
        {{ project.description }}
      </p>

      <div class="d-flex align-center text-caption text-medium-emphasis">
        <v-icon size="small" class="me-1">mdi-clock-outline</v-icon>
        {{ formatDate(project.created_at) }}

        <v-spacer></v-spacer>

        <v-icon size="small" class="me-1">mdi-format-list-checks</v-icon>
        {{ project.tasks?.length || 0 }} nhiệm vụ
      </div>
    </v-card-text>

    <v-card-actions>
      <v-btn
          color="primary"
          variant="text"
          :to="`/projects/${project.id}`"
      >
        Xem chi tiết
      </v-btn>

      <v-spacer></v-spacer>

      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn
              icon="mdi-dots-vertical"
              v-bind="props"
              variant="text"
              size="small"
          ></v-btn>
        </template>

        <v-list>
          <v-list-item @click="$emit('edit', project)">
            <template v-slot:prepend>
              <v-icon>mdi-pencil</v-icon>
            </template>
            <v-list-item-title>Chỉnh sửa</v-list-item-title>
          </v-list-item>

          <v-list-item @click="$emit('delete', project)">
            <template v-slot:prepend>
              <v-icon color="error">mdi-delete</v-icon>
            </template>
            <v-list-item-title class="text-error">Xóa</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { computed } from 'vue'
import { formatDate } from '@/utils/helpers'

const props = defineProps({
  project: {
    type: Object,
    required: true
  }
})

defineEmits(['edit', 'delete'])

const statusColor = computed(() => {
  return props.project.status === 'active' ? 'success' : 'grey'
})

const statusText = computed(() => {
  return props.project.status === 'active' ? 'Hoạt động' : 'Không hoạt động'
})
</script>