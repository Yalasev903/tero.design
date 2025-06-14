        <template>
        <div>
            <div class="home-grid-panel">
                <a href="/" target="_blank" class="inline-flex items-center text-blue-600 hover:underline text-lg font-medium mb-4">
                    <el-icon class="mr-1"><House /></el-icon>
                    Открыть главную страницу
                    </a>

            <SeoIndex
                v-model="form"
                title="SEO Home Grid"
                :apiUrl="'/api/admin/pages/homegrid-seo'"
            />
            <br />
            <div class="panel-buttons">
                <el-button type="primary" @click="addRow">
                <el-icon><Plus /></el-icon> Добавить строку
                </el-button>
                <el-button type="success" @click="saveGrid" :loading="saving">
                <el-icon><Check /></el-icon> Сохранить изменения
                </el-button>
            </div>

            <div class="grid-rows">
                <draggable
                v-model="gridRows"
                group="rows"
                animation="220"
                item-key="id"
                >
                <template #item="{ element: row, index: rowIdx }">
                    <div class="grid-row-wrap">
                    <div class="drag-row-overlay"></div>
                    <div class="row-header">
                        <span class="drag-row" title="Перетащить строку">
                        <el-icon><Menu /></el-icon>
                        </span>
                        <el-button
                        type="danger"
                        size="small"
                        circle
                        @click.stop="removeRow(rowIdx)"
                        title="Удалить строку"
                        >
                        <el-icon><Delete /></el-icon>
                        </el-button>
                    </div>
                    <div class="grid-row">
                        <draggable
                        v-model="row.items"
                        group="cols"
                        handle=".grid-item"
                        animation="180"
                        item-key="colIdx"
                        class="columns-draggable"
                        :style="{ display: 'flex', gap: '16px', width: '100%' }"
                        >
                        <template #item="{ element: col, index: colIdx }">
                            <div
                            class="grid-item"
                            :class="[
                                col.is_mobile ? 'grid-item-mobile' : 'grid-item-desktop',
                                col.media?.type ? 'grid-item-' + col.media.type : ''
                            ]"
                            >
                            <div
                                class="media-thumb"
                                @click="openPreview(col)"
                                @mousedown="onMouseDown"
                                @mouseup="onMouseUp"
                            >
                                <div v-if="col.media?.type === 'img' && col.media?.link">
                                <img
                                    :src="`/multimedia/${col.media.link}`"
                                    alt=""
                                    class="grid-img"
                                    @load="e => setPreviewImgSize(e, rowIdx, colIdx)"
                                />
                                </div>
                                <div v-else-if="col.media?.type === 'video' && col.media?.links?.length">
                                <video
                                    ref="allGridVideos"
                                    muted
                                    loop
                                    playsinline
                                    preload="auto"
                                    class="grid-video"
                                    :poster="`/multimedia/${col.media.poster || col.media.link}`"
                                    @loadedmetadata="e => setPreviewVideoSize(e, rowIdx, colIdx)"
                                    autoplay
                                >
                                    <source
                                    v-for="(link, i) in col.media.links || []"
                                    :key="i"
                                    :src="`/multimedia/${link.link}`"
                                    :type="link.mime || 'video/mp4'"
                                    />
                                </video>
                                </div>
                                <div v-else class="empty-media">
                                <el-icon><Picture /></el-icon>
                                </div>
                                <span class="edit-icon" @click.stop="openFileManager(rowIdx, colIdx)" title="Заменить медиа">
                                <el-icon><Edit /></el-icon>
                                </span>
                                <span class="media-name">
                                <el-icon v-if="col.media?.type === 'video'">
                                    <VideoCamera />
                                </el-icon>
                                <el-icon v-else>
                                    <Picture />
                                </el-icon>
                                {{ col.title || 'Без названия' }}
                                </span>
                            </div>
                            <div class="item-toolbar">
                                <el-button
                                size="small"
                                circle
                                :type="col.is_mobile ? 'primary' : 'default'"
                                @click="col.is_mobile = !col.is_mobile"
                                title="Показывать на мобильных"
                                >
                                <el-icon><Iphone /></el-icon>
                                </el-button>
                                <el-button
                                size="small"
                                type="danger"
                                circle
                                @click="removeCol(rowIdx, colIdx)"
                                title="Удалить колонку"
                                >
                                <el-icon><Delete /></el-icon>
                                </el-button>
                            </div>
                            </div>
                        </template>
                        </draggable>
                        <el-button
                        size="small"
                        circle
                        type="primary"
                        class="add-col-btn"
                        @click="addCol(rowIdx)"
                        title="Добавить колонку"
                        >
                        <el-icon><Plus /></el-icon>
                        </el-button>
                    </div>
                    </div>
                </template>
                </draggable>
            </div>
            </div>
        </div>
        <el-dialog
        v-model="previewVisible"
        title="Предпросмотр"
        width="50%"
        class="preview-dialog"
        :append-to-body="true"
        >
        <div class="preview-modal-content">
            <div v-if="previewSize.w && previewSize.h" class="media-size-modal">
            {{ previewSize.w }} × {{ previewSize.h }} px
            </div>

            <div v-if="previewItem?.type === 'img'">
            <img :src="`/multimedia/${previewItem.link}`" class="preview-img" />
            </div>

            <div v-else-if="previewItem?.type === 'video' && previewItem?.links?.length">
            <video
                class="preview-video"
                controls
                autoplay
                loop
                muted
                playsinline
                :poster="`/multimedia/${previewItem.poster || previewItem.link}`"
            >
                <source
                v-for="(link, i) in previewItem.links"
                :key="i"
                :src="`/multimedia/${link.link}`"
                :type="link.mime || 'video/mp4'"
                />
            </video>
            </div>

            <div v-else>
            <p>Нет данных для предпросмотра</p>
            </div>
        </div>
        </el-dialog>

    <!-- VueFinder -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal">
        <div class="finder-homegrid">
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
    </template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import draggable from 'vuedraggable'
import SeoIndex from '@/components/admin/SeoIndex.vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'
import {
  Plus,
  Check,
  Delete,
  Edit,
  Picture,
  Menu,
  Iphone,
  VideoCamera,
  House
} from '@element-plus/icons-vue'

// --- Переменные ---
const gridRows = ref([])
const saving = ref(false)
const form = ref({ seo_title: '', seo_description: '', seo_keywords: [] })

const previewVisible = ref(false)
const previewItem = ref({})

const showFileManager = ref(false)
const selectedCell = ref({ rowIdx: 0, colIdx: 0 })

const imgSizes = ref({})
const videoSizes = ref({})
const makeKey = (rowIdx, colIdx) => `${rowIdx}_${colIdx}`

// --- Загрузка данных ---
const loadGrid = async () => {
  const { data } = await axios.get('/api/admin/home-grid')
  gridRows.value = data
    .filter(r => Array.isArray(r) && r.length > 0)
    .map((row, i) => ({
      id: Date.now() + i,
      items: row
    }))
}

const loadSeo = async () => {
  const res = await axios.get('/api/admin/pages/homegrid-seo')
  form.value.seo_title = res.data.seo_title || ''
  form.value.seo_description = res.data.seo_description || ''
  form.value.seo_keywords = res.data.seo_keywords
    ? res.data.seo_keywords.split(',').map(s => s.trim())
    : []
}

// --- Grid управление ---
const addRow = () => {
  gridRows.value.unshift({
    id: Date.now() + Math.random(),
    items: []
  })
}

const removeRow = (rowIdx) => gridRows.value.splice(rowIdx, 1)

const addCol = (rowIdx) => {
  gridRows.value[rowIdx].items.push({
    project_id: null,
    media: {},
    is_mobile: false,
    title: 'Без названия',
    text2: '',
  })
}

const removeCol = (rowIdx, colIdx) => {
  gridRows.value[rowIdx].items.splice(colIdx, 1)
}

const saveGrid = async () => {
  saving.value = true
  try {
    const payload = gridRows.value.map(r => r.items)
    await axios.post('/api/admin/home-grid', { grid: payload })
    ElNotification({ title: 'Успешно', message: 'Сетка сохранена', type: 'success' })
    await loadGrid()
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Ошибка при сохранении', type: 'error' })
  }
  saving.value = false
}

// --- Медиа-файлы ---
const openFileManager = (rowIdx, colIdx) => {
      console.log('openFileManager', rowIdx, colIdx)
  selectedCell.value = { rowIdx, colIdx }
  showFileManager.value = true
}

const handleFileSelect = (items) => {
  if (!items.length) return
  const file = items[0]
  let type = 'img'
  if (file.mime?.includes('video')) type = 'video'

  const row = gridRows.value[selectedCell.value.rowIdx]
  const col = row.items[selectedCell.value.colIdx]

  const path = file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')

  if (type === 'img') {
    col.media = {
      type: 'img',
      link: path
    }
  } else {
    col.media = {
      type: 'video',
      poster: path,
      links: [{ link: path, mime: file.mime || 'video/mp4' }]
    }
  }

  showFileManager.value = false
}
const previewSize = ref({ w: null, h: null })

// --- Предпросмотр ---
const openPreview = async (col) => {
  previewItem.value = col.media || {}
  previewVisible.value = true

  // ждем рендер
  await nextTick()

  const el = document.querySelector('.preview-modal-content video, .preview-modal-content img')
  if (el?.tagName === 'IMG') {
    previewSize.value = { w: el.naturalWidth, h: el.naturalHeight }
  } else if (el?.tagName === 'VIDEO') {
    previewSize.value = { w: el.videoWidth, h: el.videoHeight }
  } else {
    previewSize.value = { w: null, h: null }
  }
}

const setPreviewImgSize = (e, rowIdx, colIdx) => {
  imgSizes.value[makeKey(rowIdx, colIdx)] = {
    w: e.target.naturalWidth,
    h: e.target.naturalHeight
  }
}

const setPreviewVideoSize = (e, rowIdx, colIdx) => {
  videoSizes.value[makeKey(rowIdx, colIdx)] = {
    w: e.target.videoWidth,
    h: e.target.videoHeight
  }
}

const getImgSize = (rowIdx, colIdx) =>
  imgSizes.value[makeKey(rowIdx, colIdx)] || { w: null, h: null }

const getVideoSize = (rowIdx, colIdx) =>
  videoSizes.value[makeKey(rowIdx, colIdx)] || { w: null, h: null }

// --- Инициализация ---
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
.add-col { margin-top: 7px; }
.finder-modal { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; }
.finder-container { width: 90%; height: 90%; background: #fff; border-radius: 8px; overflow: hidden; position: relative; }
.close-btn { position: absolute; top: 98px; right: 95px; background: #e00; color: #fff; padding: 5px 10px; border: none; border-radius: 6px; cursor: pointer; z-index: 10; }

.row-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 8px;
  background: #f2f2f2;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: grab;
  user-select: none;
}
.row-header:hover {
  background: #eaeaea;
}

.grid-rows {
  display: block;
  width: 100%;
}
.grid-row-wrap {
  position: relative;
  margin-bottom: 25px;
  border: 2px dashed #e3e3e3;
  border-radius: 12px;
  background: #f9f9fb;
  padding: 15px 8px 8px;
  cursor: grab;
}

.grid-row-wrap * {
  cursor: auto; /* отменяем grab внутри */
}

.drag-row-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
    pointer-events: none;
}

.vuedraggable-dragging {
  opacity: 0.9;
  transform: scale(1.01);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.grid-row {
  display: flex;
  flex-direction: row;
  gap: 16px;
  min-height: 150px;
  width: 100%;
}
.columns-draggable {
  display: flex;
  gap: 16px;
  width: 100%;
}

/* Иконка редактировать в верхнем правом углу превью */
.media-thumb {
  position: relative;
  width: 180px;
  height: 120px;
  border-radius: 10px;
  overflow: hidden;
  background: #eee;
  margin: 0 auto 10px auto;
  cursor: pointer;
  box-shadow: 0 3px 12px rgba(0,0,0,0.05);
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.media-thumb img,
.media-thumb video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.empty-media {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 38px;
  color: #ccc;
  background: #f5f5f7;
}
.edit-icon {
  position: absolute;
  top: 7px;
  right: 8px;
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.13);
  font-size: 18px;
  padding: 4px;
  color: #409EFF;
  cursor: pointer;
  transition: background 0.2s;
  z-index: 2;
}
.edit-icon:hover { background: #f3f7ff; }

.media-name {
  position: absolute;
  left: 0;
  bottom: 0;
  background: rgba(0,0,0,0.55);
  color: #fff;
  font-size: 13px;
  padding: 3px 12px 3px 7px;
  border-radius: 0 9px 0 0;
  display: flex;
  align-items: center;
  gap: 7px;
}

.item-toolbar {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 7px 0 2px 0;
}

.add-col-btn {
  margin-top: 4px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* drag-row area - иконка меню */
.drag-row {
  cursor: grab;
  font-size: 20px;
  color: #b1b1b1;
  margin-right: 11px;
  vertical-align: middle;
}

.drag-row-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
  cursor: grab;
}

.grid-item {
    position: relative;
    z-index: 10;
    cursor: grab;
  /* подсветка при drag можно добавить если хочешь */
}

/* Размеры над превью в сетке */
.media-size-above {
  position: absolute;
  top: 4px;
  left: 4px;
  background: rgba(0,0,0,0.45);
  color: #fff;
  font-size: 11px;
  padding: 1px 7px;
  border-radius: 6px;
  z-index: 5;
  pointer-events: none;
}

.preview-dialog .el-dialog__body {
  padding: 18px 28px 22px 28px;
  display: flex;
  flex-direction: column;
  align-items: center;
  overflow: auto;
}
.preview-modal-content {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.media-size-modal {
  margin-bottom: 6px;
  color: #757575;
  font-size: 15px;
  text-align: center;
  background: #f5f5f5;
  border-radius: 6px;
  padding: 2px 10px;
  display: inline-block;
}
.preview-img,
.preview-video {
  max-width: 100%;
  max-height: calc(100vh - 140px); /* чтобы не вылазило за экран */
  object-fit: contain;
  background: #000;
  border-radius: 10px;
  box-shadow: 0 3px 16px rgba(0, 0, 0, 0.18);
}
:deep(.el-select .el-tag) {
  background-color: transparent !important;
  border: 1px solid rgb(64, 255, 89) !important;
  color: #409EFF !important;
  font-weight: 500;
  border-radius: 6px;
}
@media (max-width: 900px) {
  .preview-img, .preview-video {
    max-width: 95vw;
    max-height: 45vh;
  }
  .preview-dialog .el-dialog__body {
    padding: 5vw 0;
  }
}
</style>
