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
                        item-key="id"
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
                                    :key="col.media?.__v || col.id"
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
                                <span class="edit-icon" @click.stop="openEditModal(rowIdx, colIdx)" title="Редактировать медиа">
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
<el-dropdown trigger="click" @command="type => openMediaModal(type, rowIdx)">
  <el-button type="primary">
    <el-icon><Plus /></el-icon>
  </el-button>
  <template #dropdown>
    <el-dropdown-menu>
      <el-dropdown-item command="img">Изображение</el-dropdown-item>
      <el-dropdown-item command="video">Видео</el-dropdown-item>
    </el-dropdown-menu>
  </template>
</el-dropdown>
                    </div>
                    </div>
                </template>
                </draggable>

<el-dialog v-model="showMediaModal" title="Добавление медиа" width="600px" :append-to-body="true">
  <el-form label-position="top">
    <el-form-item label="Выберите проект">
      <el-select v-model="selectedProjectId" placeholder="Проект" style="width: 100%">
        <el-option
        v-for="p in projects"
        :key="p.id"
        :label="`${p.title} (${p.usageCount})`"
        :value="p.id"
        />
      </el-select>
    </el-form-item>

    <el-form-item v-if="mediaType === 'img'" label="Изображение">
      <el-button @click="openFileManagerForModal">Выбрать изображение</el-button>
      <div v-if="modalMediaSize.w && modalMediaSize.h" class="media-size-modal" style="margin-bottom: 6px;">
        {{ modalMediaSize.w }} × {{ modalMediaSize.h }} px
        </div>
      <div v-if="selectedPath" class="preview-img">
        <img :src="`/multimedia/${selectedPath}`" style="max-width: 100%; margin-top: 10px" />
      </div>
    </el-form-item>

          <el-form-item
          v-if="mediaType === 'img' && showMediaModal"
          label="SEO описание изображения">
        <el-input
            v-model="mediaDescription"
            placeholder="Введите описание для alt / SEO"
            type="textarea"
            rows="2"
        />
        </el-form-item>

    <el-form-item v-if="mediaType === 'video'" label="Видео">
      <el-button @click="openFileManagerForModal">Выбрать видео</el-button>
      <div v-if="modalMediaSize.w && modalMediaSize.h" class="media-size-modal" style="margin-bottom: 6px;">
        {{ modalMediaSize.w }} × {{ modalMediaSize.h }} px
        </div>
      <div v-if="selectedPath" class="preview-img">
        <video autoplay loop muted playsinline style="width: 100%; margin-top: 10px"
          :src="`/multimedia/${selectedPath}`" />
      </div>
    </el-form-item>
  </el-form>

  <template #footer>
    <el-button @click="cancelMediaInsert">Отмена</el-button>
    <el-button type="primary" @click="insertMedia">Добавить</el-button>
  </template>
</el-dialog>
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
import { ElMessageBox } from 'element-plus'

// --- Переменные ---
const gridRows = ref([])
const saving = ref(false)
const form = ref({ seo_title: '', seo_description: '', seo_keywords: [] })

const previewVisible = ref(false)
const previewItem = ref({})

const showFileManager = ref(false)
const selectedCell = ref({ rowIdx: 0, colIdx: 0 })
const modalMediaSize = ref({ w: null, h: null })

const imgSizes = ref({})
const videoSizes = ref({})
const makeKey = (rowIdx, colIdx) => `${rowIdx}_${colIdx}`

const mediaDescription = ref('')

const isEditingModal = ref(false)
const editingTarget = ref({ rowIdx: null, colIdx: null })

const openEditModal = (rowIdx, colIdx) => {
  const cell = gridRows.value[rowIdx].items[colIdx]

  selectedProjectId.value = cell.project_id
  if (cell.media?.type === 'img') {
  selectedPath.value = cell.media.link
  mediaDescription.value = cell.media?.description || ''
} else if (cell.media?.type === 'video') {
  selectedPath.value = cell.media.links?.[0]?.link || ''
  mediaDescription.value = ''
}

  mediaType.value = cell.media?.type || 'img'
  modalMediaSize.value = { w: null, h: null }

  editingTarget.value = { rowIdx, colIdx }
  isEditingModal.value = true
  showMediaModal.value = true
  loadProjects()
}

// --- Загрузка данных ---
const loadGrid = async () => {
  try {
    gridRows.value = []

    const { data } = await axios.get('/api/admin/home-grid')
    const flatGrid = data || []

    const grouped = {}

    flatGrid.forEach((item, index) => {
      const rowIdx = item.row_number ?? 0
      const colIdx = item.col_number ?? 0

      if (!grouped[rowIdx]) grouped[rowIdx] = []

      grouped[rowIdx][colIdx] = {
        id: `cell_${rowIdx}_${colIdx}_${item.project_id ?? 'null'}_${Math.random().toString(36).substring(2, 8)}`,
        project_id: item.project_id,
        media: typeof item.media === 'string'
          ? JSON.parse(item.media || '{}')
          : (item.media || {}),
        is_mobile: item.is_mobile || false,
        title: item.title || 'Без названия',
        text2: item.text2 || ''
      }
    }) // ← закрыли forEach ✅

    // Теперь правильно инициализируем gridRows
    gridRows.value = Object.keys(grouped)
      .sort((a, b) => +a - +b)
      .map(rowIdx => ({
        id: `row_${rowIdx}`,
        items: Object.keys(grouped[rowIdx])
          .sort((a, b) => +a - +b)
          .map(colIdx => grouped[rowIdx][colIdx])
      }))
  } catch (e) {
    console.error('Ошибка при загрузке home-grid', e)
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось загрузить сетку',
      type: 'error'
    })
  }
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

const removeRow = async (rowIdx) => {
  try {
    await ElMessageBox.confirm(
      'Вы уверены, что хотите удалить всю строку с контентом?',
      'Подтверждение удаления',
      {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        type: 'warning',
      }
    )
    gridRows.value.splice(rowIdx, 1)
  } catch (_) {
    // Отменено — ничего не делаем
  }
}

const addCol = (rowIdx) => {
  gridRows.value[rowIdx].items.push({
    project_id: null,
    media: {},
    is_mobile: false,
    title: 'Без названия',
    text2: '',
  })
}

const removeCol = async (rowIdx, colIdx) => {
  try {
    await ElMessageBox.confirm(
      'Вы уверены, что хотите удалить колонку?',
      'Подтверждение удаления',
      {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        type: 'warning',
      }
    )
    gridRows.value[rowIdx].items.splice(colIdx, 1)
  } catch (_) {
    // Отменено — ничего не делаем
  }
}

const saveGrid = async () => {
  saving.value = true

  try {
    const payload = []

    // 🔥 Сохраняем точный порядок: верхняя строка — row_number = 0
    gridRows.value.forEach((row, rowIdx) => {
      row.items.forEach((item, colIdx) => {
        payload.push({
          row_number: rowIdx, // ← важен порядок строк
          col_number: colIdx, // ← и порядок колонок внутри
          project_id: item.project_id || null,
          media: item.media || {},
          is_mobile: item.is_mobile || false,
          title: item.title || '',
          text2: item.text2 || ''
        })
      })
    })

    if (payload.length === 0) {
      ElNotification({
        title: 'Ошибка',
        message: 'Сетка пуста',
        type: 'warning'
      })
      saving.value = false
      return
    }

    await axios.post('/api/admin/home-grid', { grid: payload })

    ElNotification({
      title: 'Успешно',
      message: 'Сетка сохранена',
      type: 'success'
    })

  console.log('→ ПЕРЕД СОХРАНЕНИЕМ:', JSON.stringify(gridRows.value, null, 2))
    // Загружаем заново, чтобы синхронизировать структуру
    await loadGrid()
  } catch (e) {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось сохранить сетку',
      type: 'error'
    })
    console.error(e)
  }

  saving.value = false
  console.log('← ПОСЛЕ ЗАГРУЗКИ:', JSON.stringify(gridRows.value, null, 2))
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
  const path = file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  selectedPath.value = path
  showFileManager.value = false

  const isVideo = file.mime?.includes('video') || path.match(/\.(mp4|webm|ogg)$/i)

  // 🛠 если showMediaModal открыт → вставка в модалке "добавить медиа"
  if (showMediaModal.value) return

  // иначе — это просто замена в ячейке
  const { rowIdx, colIdx } = selectedCell.value

  if (typeof rowIdx === 'undefined' || typeof colIdx === 'undefined') {
    console.warn('Не указаны индексы для замены')
    return
  }

  const row = gridRows.value[rowIdx]
  const col = row.items[colIdx]

  col.media = isVideo
    ? {
        __v: Date.now(),
        type: 'video',
        poster: path,
        links: [{ link: path, mime: file.mime || 'video/mp4' }]
      }
    : {
        __v: Date.now(),
        type: 'img',
        link: path
      }

  ElNotification({
    title: 'Обновлено',
    message: isVideo ? `Видео заменено: ${path}` : `Изображение заменено: ${path}`,
    type: 'success'
  })

  // авторазмеры
  nextTick(() => {
    setTimeout(() => {
      const el = document.querySelector('.el-dialog__body .preview-img img, .el-dialog__body .preview-img video')
      if (!el) return
      if (el.tagName === 'IMG') {
        modalMediaSize.value = {
          w: el.naturalWidth,
          h: el.naturalHeight
        }
      } else if (el.tagName === 'VIDEO') {
        el.addEventListener('loadedmetadata', () => {
          modalMediaSize.value = {
            w: el.videoWidth,
            h: el.videoHeight
          }
        }, { once: true })
      }
    }, 150)
  })
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


//col
const showMediaModal = ref(false)
const mediaType = ref('img')
const selectedProjectId = ref(null)
const selectedPath = ref('')
const targetRowIdx = ref(0) // по умолчанию 0 строка
const projects = ref([])

const openMediaModal = (type, rowIdx) => {
  mediaType.value = type

if (typeof rowIdx === 'undefined') {
  ElNotification({
    title: 'Ошибка',
    message: 'Сначала добавьте строку',
    type: 'warning'
  })
  return
}
targetRowIdx.value = rowIdx


  showMediaModal.value = true
  loadProjects()
}


const openFileManagerForModal = () => {
  showFileManager.value = true
}

const cancelMediaInsert = () => {
  showMediaModal.value = false
  selectedPath.value = ''
  selectedProjectId.value = null
  mediaType.value = 'img'
  modalMediaSize.value = { w: null, h: null }
  isEditingModal.value = false
  editingTarget.value = { rowIdx: null, colIdx: null }
  mediaDescription.value = ''
}

const insertMedia = async () => {
  if (!selectedPath.value) {
    ElNotification({ title: 'Ошибка', message: 'Выберите файл', type: 'warning' })
    return
  }

  const project = projects.value.find(p => p.id === selectedProjectId.value)

  const media =
    mediaType.value === 'img'
      ? {
        type: 'img',
        link: selectedPath.value,
        description: mediaDescription.value.trim()
        }
      : {
          type: 'video',
          poster: selectedPath.value,
          links: [{
            link: selectedPath.value,
            mime: selectedPath.value.endsWith('.webm')
              ? 'video/webm'
              : 'video/mp4'
          }]
        }

  if (isEditingModal.value) {
    // Режим редактирования
    const { rowIdx, colIdx } = editingTarget.value
    const cell = gridRows.value[rowIdx].items[colIdx]
    cell.project_id = selectedProjectId.value
    cell.title = project?.title || 'Без названия'
    cell.media = {
  ...JSON.parse(JSON.stringify(media)),
  __v: Date.now()
}

    ElNotification({ title: 'Обновлено', message: 'Контент обновлён', type: 'success' })
  } else {
    // Режим добавления
    const rowIdx = targetRowIdx.value
    const newCol = {
      id: `cell_${rowIdx}_${Date.now()}_${Math.random().toString(36).substring(2, 8)}`,
      project_id: selectedProjectId.value || null,
      title: project?.title || 'Без проекта',
      media: JSON.parse(JSON.stringify(media)),
      is_mobile: false
    }

    gridRows.value[rowIdx].items.push(newCol)
    ElNotification({ title: 'Добавлено', message: 'Контент добавлен', type: 'success' })
  }

  cancelMediaInsert()
}

const loadProjects = async () => {
  try {
    const { data } = await axios.get('/api/admin/projects')

    // Подсчёт вхождений каждого project_id в текущей сетке
    const usageMap = {}

    gridRows.value.forEach(row => {
      row.items.forEach(col => {
        if (col.project_id) {
          usageMap[col.project_id] = (usageMap[col.project_id] || 0) + 1
        }
      })
    })

    // Добавляем счётчик к каждому проекту
    projects.value = data.map(p => ({
      ...p,
      usageCount: usageMap[p.id] || 0
    }))

    console.log('Проекты с подсчётом:', projects.value)
  } catch (e) {
    console.error('Ошибка при загрузке проектов:', e)
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось загрузить проекты',
      type: 'error'
    })
  }
}

onMounted(() => {
  loadGrid()
  loadSeo()
  loadProjects()
})
</script>

<style scoped>
.panel-buttons { display: flex; gap: 15px; margin-bottom: 20px; }
.grid-rows { display: flex; flex-direction: column; gap: 25px; }
.grid-row-wrap { margin-bottom: 18px; border: 2px dashed #e3e3e3; border-radius: 12px; background: #f9f9fb; padding: 15px 8px 8px; position: relative; }
.row-header { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.grid-row {
  display: flex;
  flex-direction: row;
  gap: 16px;
  min-height: 150px;
  width: 100%;
  overflow-x: auto;           /* 🔥 Горизонтальный скролл */
  padding-bottom: 8px;        /* 🔽 отступ снизу под скролл */
  scrollbar-width: thin;      /* для Firefox */
  scrollbar-color: #bbb transparent;
}

.grid-row::-webkit-scrollbar {
  height: 8px;
}

.grid-row::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}
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

.preview-img video,
.preview-img img {
  max-width: 100%;
  max-height: 50vh;
  object-fit: contain;
  border-radius: 8px;
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
