<template>
  <div>
    <!-- Header with filters and search -->
    <v-card class="mb-4" elevation="1">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field
                v-model="searchQuery"
                label="Tìm kiếm dự án"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="handleSearch"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="3">
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

          <v-col cols="12" md="3">
            <v-select
                v-model="sortBy"
                :items="sortItems"
                label="Sắp xếp theo"
                variant="outlined"
                density="compact"
                @update:model-value="handleSort"
            ></v-select>
          </v-col>

          <v-col cols="12" md="2">
            <v-btn
                color="primary"
                block
                @click="$emit('create')"
            >
              <template v-slot:prepend>
                <v-icon>mdi-plus</v-icon>
              </template>
              Tạo mới
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Projects grid -->
    <div v-if="loading" class="text-center py-8">
      <LoadingSpinner text="Đang tải danh sách dự án..." />
    </div>

    <div v-else-if="projects.length === 0" class="text-center py-8">
      <v-icon size="64" color="grey-lighten-1">mdi-folder-outline</v-icon>
      <p class="text-h6 text-grey-lighten-1 mt-4">Chưa có dự án nào</p>
      <v-btn color="primary" @click="$emit('create')">
        <template v-slot:prepend>
          <v-icon>mdi-plus</v-icon>
        </template>
        Tạo dự án đầu tiên
      </v-btn>
    </div>

    <v-row v-else>
      <v-col
          v-for="project in projects"
          :key="project.id"
          cols="12"
          md="6"
          lg="4"
      >
        <ProjectCard
            :project="project"
            @edit="$emit('edit', $event)"
            @delete="$emit('delete', $event)"
        />
      </v-col>
    </v-row>

    <!-- Pagination -->
    <v-pagination
        v-if="pagination.last_page > 1"
        v-model="currentPage"
        :length="pagination.last_page"
        class="mt-4"
        @update:model-value="handlePageChange"
    ></v-pagination>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ProjectCard from './ProjectCard.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const props = defineProps({
  projects: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  pagination: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['create', 'edit', 'delete', 'filter', 'search', 'sort', 'page-change'])

const searchQuery = ref('')
const statusFilter = ref('')
const sortBy = ref('created_at_desc')

const currentPage = computed({
  get: () => props.pagination.current_page,
  set: (value) => handlePageChange(value)
})

const statusItems = [
  { title: 'Hoạt động', value: 'active' },
  { title: 'Không hoạt động', value: 'inactive' }
]

const sortItems = [
  { title: 'Mới nhất', value: 'created_at_desc' },
  { title: 'Cũ nhất', value: 'created_at_asc' },
  { title: 'Tên A-Z', value: 'title_asc' },
  { title: 'Tên Z-A', value: 'title_desc' }
]

const handleSearch = (query) => {
  emit('search', query)
}

const handleFilter = (status) => {
  emit('filter', { status })
}

const handleSort = (sort) => {
  const [field, direction] = sort.split('_')
  emit('sort', { sort_by: field, sort_direction: direction })
}

const handlePageChange = (page) => {
  emit('page-change', page)
}
</script>