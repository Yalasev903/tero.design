<template>
  <div>
    <div class="home-grid-panel">
      <h3>🏠 Home Grid</h3>

    <!-- SEO блок для Home Grid -->
    <SeoIndex
      v-model="form"
      title="SEO Home Grid"
      :apiUrl="'/api/admin/pages/homegrid-seo'"
    />
    <br/>
      <div class="panel-buttons">
        <el-button type="primary" @click="addRow">Добавить строку</el-button>
        <el-button type="success" @click="saveGrid" :loading="saving">Сохранить изменения</el-button>
      </div>

      <draggable
        v-model="gridRows"
        group="rows"
        handle=".drag-row"
        animation="220"
        item-key="rowIdx"
      >
        <template #item="{ element: row, index: rowIdx }">
          <div class="grid-row-wrap">
            <div class="row-header">
              <span class="drag-row" title="Перетащить строку">↕️</span>
              <el-button type="danger" size="small" icon="el-icon-delete" @click="removeRow(rowIdx)">Удалить строку</el-button>
            </div>
            <draggable
              :list="row"
              group="cols"
              handle=".drag-col"
              animation="180"
              class="grid-row"
              item-key="colIdx"
            >
              <template #item="{ element: col, index: colIdx }">
                <div
                  class="grid-item"
                  :class="[
                    col.is_mobile ? 'grid-item-mobile' : 'grid-item-desktop',
                    col.media?.type ? 'grid-item-' + col.media.type : ''
                  ]"
                >
                  <span class="drag-col" title="Перетащить ячейку">⬍</span>
                  <div class="preview-box" @click="openPreview(col)">
                    <template v-if="col.media?.type === 'img'">
                      <img :src="`/multimedia/${col.media.link}`" alt="" class="grid-img" />
                    </template>
                    <template v-else-if="col.media?.type === 'video'">
                      <video
                        muted loop playsinline preload="none"
                        class="grid-video"
                        :poster="`/multimedia/${col.media.poster || col.media.link}`"
                      >
                        <source
                          v-for="(link, i) in col.media.links || []"
                          :key="i"
                          :src="`/multimedia/${link.link}`"
                          :type="link.mime || 'video/mp4'"
                        />
                      </video>
                    </template>
                    <span class="zoom-icon" title="Просмотр">🔍</span>
                  </div>
                  <el-button
                    type="primary"
                    size="small"
                    icon="el-icon-upload"
                    @click="openFileManager(rowIdx, colIdx)"
                  >Медиа</el-button>
                  <el-button type="danger" size="small" @click="removeCol(rowIdx, colIdx)">Удалить</el-button>
                </div>
              </template>
            </draggable>
            <div class="add-col">
              <el-button type="primary" size="small" @click="addCol(rowIdx)">+ Колонка</el-button>
            </div>
          </div>
        </template>
      </draggable>
    </div>


    <!-- Модалка превью -->
    <el-dialog v-model="showPreview" width="900px" title="Превью">
      <img v-if="previewType === 'img'" :src="previewSrc" class="preview-img"/>
      <video v-else controls class="preview-video">
        <source :src="previewSrc" type="video/mp4" />
      </video>
    </el-dialog>

    <!-- VueFinder: файловый менеджер -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal">
        <div class="finder-container">
          <vue-finder
            id="vuefinder"
            :request="{
              baseUrl: '/api/vuefinder',
              adapter: 'local',
              xsrfHeaderName: 'X-XSRF-TOKEN'
            }"
            @select="handleFileSelect"
          />
          <button class="close-btn" @click="showFileManager = false">✖</button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import draggable from 'vuedraggable'
import SeoIndex from '@/components/admin/SeoIndex.vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'

const gridRows = ref([])
const saving = ref(false)

// Файловый менеджер
const showFileManager = ref(false)
const selectedCell = ref({ rowIdx: 0, colIdx: 0 })
const openFileManager = (rowIdx, colIdx) => {
  selectedCell.value = { rowIdx, colIdx }
  showFileManager.value = true
}
const handleFileSelect = (items) => {
  if (!items.length) return
  const file = items[0]
  // Простейшая автоопределялка типа
  let type = 'img'
  if (file.mime?.includes('video')) type = 'video'
  const col = gridRows.value[selectedCell.value.rowIdx][selectedCell.value.colIdx]
  if (type === 'img') {
    col.media = {
      type: 'img',
      link: file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
    }
  } else {
    col.media = {
      type: 'video',
      poster: file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, ''),
      links: [
        {
          link: file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, ''),
          mime: file.mime || 'video/mp4'
        }
      ]
    }
  }
  showFileManager.value = false
}

// Grid CRUD
const loadGrid = async () => {
  const { data } = await axios.get('/api/admin/home-grid')
  gridRows.value = data
}
const addRow = () => gridRows.value.push([])
const removeRow = (rowIdx) => gridRows.value.splice(rowIdx, 1)
const addCol = (rowIdx) => {
  gridRows.value[rowIdx].push({
    project_id: null,
    media: {},
    is_mobile: false,
    title: 'Без названия',
    text2: '',
  })
}
const removeCol = (rowIdx, colIdx) => gridRows.value[rowIdx].splice(colIdx, 1)
const saveGrid = async () => {
  saving.value = true
  try {
    await axios.post('/api/admin/home-grid', { grid: gridRows.value })
    ElNotification({ title: 'Успешно', message: 'Сетка сохранена', type: 'success' })
    loadGrid()
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Ошибка при сохранении', type: 'error' })
  }
  saving.value = false
}

// SEO
const form = ref({ seo_title: '', seo_description: '', seo_keywords: [] })
const loadSeo = async () => {
  const res = await axios.get('/api/admin/pages/homegrid-seo')
  form.value.seo_title = res.data.seo_title || ''
  form.value.seo_description = res.data.seo_description || ''
  form.value.seo_keywords = res.data.seo_keywords
    ? res.data.seo_keywords.split(',').map(s => s.trim())
    : []
}

// Превью
const showPreview = ref(false)
const previewSrc = ref('')
const previewType = ref('img')
const openPreview = (col) => {
  if (col.media?.type === 'video' && col.media.links?.length) {
    previewType.value = 'video'
    previewSrc.value = `/multimedia/${col.media.links[0].link}`
  } else {
    previewType.value = 'img'
    previewSrc.value = `/multimedia/${col.media.link}`
  }
  showPreview.value = true
}

onMounted(() => {
  loadGrid()
  loadSeo()
})
</script>

<style scoped>
.panel-buttons { display: flex; gap: 15px; margin-bottom: 20px; }
.grid-rows { display: flex; flex-direction: column; gap: 25px; }
.grid-row-wrap { margin-bottom: 18px; border: 2px dashed #e3e3e3; border-radius: 12px; background: #f9f9fb; padding: 15px 8px 8px; position: relative; }
.row-header { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.grid-row { display: flex; gap: 16px; min-height: 150px; }
.grid-item { border: 1px dashed #ccc; border-radius: 6px; min-width: 220px; max-width: 320px; padding: 10px; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; position: relative; }
.drag-row, .drag-col { cursor: grab; font-size: 18px; margin-right: 8px; color: #999; }
.preview-box { position: relative; width: 130px; height: 80px; border-radius: 8px; overflow: hidden; background: #eee; cursor: pointer; margin-bottom: 8px; }
.zoom-icon { position: absolute; bottom: 6px; right: 8px; background: #fff; color: #222; border-radius: 50%; font-size: 16px; padding: 2px 5px; }
.preview-img, .preview-video { width: 100%; max-width: 820px; height: auto; border-radius: 10px; margin: 0 auto; display: block; }
.add-col { margin-top: 7px; }
.finder-modal { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; }
.finder-container { width: 90%; height: 90%; background: #fff; border-radius: 8px; overflow: hidden; position: relative; }
.close-btn { position: absolute; top: 10px; right: 15px; background: #e00; color: #fff; padding: 5px 10px; border: none; border-radius: 6px; cursor: pointer; z-index: 10; }
</style>
