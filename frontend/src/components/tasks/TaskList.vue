<template>
  <div>
    <!-- Header with filters and view toggle -->
    <v-card class="mb-4" elevation="1">
      <v-card-text>
        <v-row align="center">
          <v-col cols="12" md="3">
            <v-text-field
                v-model="searchQuery"
                label="Tìm kiếm nhiệm vụ"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="handleSearch"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="2">
            <v-select
                v-model="statusFilter"
                :items="statusItems"
                label="Trạng thái"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="handleFilter"
            ></v-select>
          </v-col>

          <v-col cols="12" md="2">
            <v-select
                v-model="priorityFilter"
                :items="priorityItems"
                label="Độ ưu tiên"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="handleFilter"
            ></v-select>
          </v-col>

          <v-col cols="12" md="2">
            <v-btn-toggle
                v-model="viewMode"
                mandatory
                @update:model-value="$emit('view-mode-change', $event)"
            >
              <v-btn value="list" icon="mdi-format-list-bulleted"></v-btn>
              <v-btn value="board" icon="mdi-view-column"></v-btn>
            </v-btn-toggle>
          </v-col>

          <v-col cols="12" md="3">
            <!-- 🔧 FIX: Sửa button tạo nhiệm vụ -->
            <v-btn
                color="primary"
                block
                @click="$emit('create')"
            >
              <template v-slot:prepend>
                <v-icon>mdi-plus</v-icon>
              </template>
              Tạo nhiệm vụ
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Task content based on view mode -->
    <div v-if="loading" class="text-center py-8">
      <LoadingSpinner text="Đang tải danh sách nhiệm vụ..." />
    </div>

    <div v-else-if="tasks.length === 0" class="text-center py-8">
      <v-icon size="64" color="grey-lighten-1">mdi-format-list-checks</v-icon>
      <p class="text-h6 text-grey-lighten-1 mt-4">Chưa có nhiệm vụ nào</p>
      <!-- 🔧 FIX: Sửa button ở đây cũng -->
      <v-btn color="primary" @click="$emit('create')">
        <template v-slot:prepend>
          <v-icon>mdi-plus</v-icon>
        </template>
        Tạo nhiệm vụ đầu tiên
      </v-btn>
    </div>

    <!-- List view -->
    <div v-else-if="viewMode === 'list'">
      <v-row>
        <v-col
            v-for="task in tasks"
            :key="task.id"
            cols="12"
            md="6"
            lg="4"
        >
          <TaskCard
              :task="task"
              @edit="$emit('edit', $event)"
              @delete="$emit('delete', $event)"
              @update-status="$emit('update-status', $event)"
          />
        </v-col>
      </v-row>
    </div>

    <!-- Board view -->
    <TaskDragDrop
        v-else
        :tasks="tasks"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
        @update-status="$emit('update-status', $event)"
        @update-order="$emit('update-order', $event)"
    />
  </div>
</template>

<!-- Script giữ nguyên -->

<script setup>
import { ref } from 'vue'
import TaskCard from './TaskCard.vue'
import TaskDragDrop from './TaskDragDrop.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const props = defineProps({
  tasks: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits([
  'create', 'edit', 'delete', 'update-status', 'update-order',
  'filter', 'search', 'view-mode-change'
])

const viewMode = ref('list')
const searchQuery = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')

const statusItems = [
  { title: 'Chờ làm', value: 'todo' },
  { title: 'Đang làm', value: 'in_progress' },
  { title: 'Hoàn thành', value: 'done' }
]

const priorityItems = [
  { title: 'Thấp', value: 'low' },
  { title: 'Trung bình', value: 'medium' },
  { title: 'Cao', value: 'high' }
]

const handleSearch = (query) => {
  emit('search', query)
}

const handleFilter = () => {
  emit('filter', {
    status: statusFilter.value,
    priority: priorityFilter.value
  })
}
</script>