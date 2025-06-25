<template>
  <div class="project-create-page">
    <h2 class="page-title">{{ isEditing ? 'Редактирование проекта' : 'Создание проекта' }}</h2>
    <SeoProject :title="'SEO для проекта'" :model="form" />
    <el-form :model="form" label-position="top" @submit.prevent="submit">
      <el-form-item label="Название проекта">
        <el-input v-model="form.title" :size="inputSize" />
      </el-form-item>

      <div class="editor-row">
        <el-form-item label="Описание 1" class="editor-item">
          <TinyEditor v-model="form.text1" />
        </el-form-item>
        <el-form-item label="Описание 2" class="editor-item">
          <TinyEditor v-model="form.text2" />
        </el-form-item>
      </div>

      <div class="panel-buttons">
        <el-button type="primary" @click="addRow"><el-icon><Plus /></el-icon> Добавить строку</el-button>
        <el-button type="success" @click="submit"><el-icon><Check /></el-icon> Сохранить проект</el-button>
      </div>

      <div class="grid-rows">
        <draggable v-model="gridRows" group="rows" animation="200" item-key="id">
          <template #item="{ element: row, index: rowIdx }">
            <div class="grid-row-wrap">
              <div class="row-header">
                <span class="drag-row"><el-icon><Menu /></el-icon></span>
                <el-button type="danger" size="small" circle @click="removeRow(rowIdx)">
                  <el-icon><Delete /></el-icon>
                </el-button>
                <!-- ✅ новый квадратный плюс -->
<el-dropdown
  class="add-col-dropdown"
  trigger="click"
  @command="type => handleAddCol(rowIdx, type)"
  :teleported="true"
>
  <template #default>
    <button class="add-col-btn" type="button">
      <el-icon><Plus /></el-icon>
    </button>
  </template>

  <template #dropdown>
    <el-dropdown-menu>
      <el-dropdown-item command="img"><el-icon><Picture /></el-icon>Изображение</el-dropdown-item>
      <el-dropdown-item command="video"><el-icon><VideoCamera /></el-icon>Видео</el-dropdown-item>
      <el-dropdown-item command="vr"><el-icon><View /></el-icon>VR</el-dropdown-item>
      <el-dropdown-item command="curtain"><el-icon><Connection /></el-icon>Шторка</el-dropdown-item>
    </el-dropdown-menu>
  </template>
</el-dropdown>



              </div>

              <div class="grid-row">
                <draggable
                  v-model="row.items"
                  group="cols"
                  handle=".grid-item"
                  animation="180"
                  item-key="colIdx"
                  class="columns-draggable"
                >
                  <template #item="{ element: col, index: colIdx }">
                    <div class="grid-item" :class="col.media?.type ? 'grid-item-' + col.media.type : ''">
                        <div class="media-thumb" @click="openPreview(col)">
                        <template v-if="col.media?.type === 'img'">
                            <img :src="'/multimedia/' + col.media.link" class="grid-img" />
                        </template>

                        <template v-else-if="col.media?.type === 'video'">
                            <video class="grid-video" autoplay muted loop playsinline preload="metadata"
                            :poster="col.media.poster ? '/multimedia/' + col.media.poster : undefined">
                            <source
                                v-for="(link, i) in col.media.links || []"
                                :key="i"
                                :src="'/multimedia/' + link.link"
                                :type="link.mime || 'video/mp4'" />
                            </video>
                        </template>

                        <template v-else-if="col.media?.type === 'vr'">
                            <iframe
                            class="grid-iframe"
                            :src="extractIframeSrc(col.media.link)"
                            :width="col.media.width"
                            :height="col.media.height"
                            frameborder="0"
                            allowfullscreen
                            allow="xr-spatial-tracking; gyroscope; accelerometer"
                            scrolling="no"
                            ></iframe>
                        </template>

                        <template v-else-if="col.media?.type === 'curtain'">
                            <div class="curtain-wrapper with-animation">
                            <img
                                class="curtain-img curtain-front"
                                :src="'/multimedia/' + col.media.images[0]"
                                alt="Curtain image 1"
                            />
                            <img
                                class="curtain-img curtain-back"
                                :src="'/multimedia/' + col.media.images[1]"
                                alt="Curtain image 2"
                            />
                            </div>
                        </template>

                        <template v-else>
                            <div class="empty-media">
                            <el-icon><Picture /></el-icon>
                            </div>
                        </template>

                        <span class="edit-icon" @click.stop="handleEditClick(rowIdx, colIdx, col.media?.type)">
                            <el-icon><Edit /></el-icon>
                        </span>
                        </div>
                      <span class="media-name">
                        <el-icon v-if="col.media?.type === 'video'"><VideoCamera /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'img'"><Picture /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'vr'"><View /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'curtain'"><Connection /></el-icon>
                      </span>

                      <div class="item-toolbar">
                        <el-button size="small" type="danger" circle @click="removeCol(rowIdx, colIdx)">
                          <el-icon><Delete /></el-icon>
                        </el-button>
                      </div>
                    </div>
                  </template>
                </draggable>

                <!-- Меню выбора типа медиа -->
                <!-- <el-dropdown trigger="click" @command="type => handleAddCol(rowIdx, type)">
                  <el-button size="small" circle type="primary" class="add-col-btn">
                    <el-icon><Plus /></el-icon>
                  </el-button>
                  <template #dropdown>
                    <el-dropdown-menu>
                      <el-dropdown-item command="img"><el-icon><Picture /></el-icon>Изображение</el-dropdown-item>
                      <el-dropdown-item command="video"><el-icon><VideoCamera /></el-icon>Видео</el-dropdown-item>
                      <el-dropdown-item command="vr"><el-icon><View /></el-icon>VR</el-dropdown-item>
                      <el-dropdown-item command="curtain"><el-icon><Connection /></el-icon>Шторка</el-dropdown-item>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown> -->
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </el-form>

    <!-- Модалка добавления и редактирования видео или изображения -->
    <el-dialog v-model="showMediaModal" :title="isEditingMedia ? 'Редактирование медиа' : 'Добавление медиа'" width="600px" :append-to-body="true">
    <el-form label-position="top">
        <el-form-item v-if="mediaType === 'img'" label="Изображение">
        <el-button @click="openFileManagerForModal">Выбрать изображение</el-button>
        <div v-if="modalMediaSize.w && modalMediaSize.h" class="media-size-modal" style="margin-bottom: 6px;">
            {{ modalMediaSize.w }} × {{ modalMediaSize.h }} px
        </div>
        <img v-if="modalMediaPreview" :src="`/multimedia/${modalMediaPreview}`" class="preview-img" style="margin-top: 10px;" />
        </el-form-item>

        <el-form-item v-else-if="mediaType === 'video'" label="Видео">
        <el-button @click="openFileManagerForModal">Выбрать видео</el-button>
        <div v-if="modalMediaPreview" style="margin-top: 10px;">
            <video autoplay muted loop playsinline controls style="max-width: 100%;">
            <source :src="`/multimedia/${modalMediaPreview}`" />
            </video>
        </div>
        </el-form-item>

        <el-form-item label="Название / alt">
        <el-input v-model="modalMediaTitle" placeholder="Название медиа (необязательно)" :size="inputSize" />
        </el-form-item>
    </el-form>

    <template #footer>
        <el-button @click="cancelMediaInsert">Отмена</el-button>
        <el-button type="primary" @click="confirmMediaInsert">Добавить</el-button>
    </template>
    </el-dialog>

    <!-- Модалка предпросмотра -->
    <el-dialog v-model="previewVisible" title="Предпросмотр" width="50%" :append-to-body="true">
    <div class="preview-modal-content">
        <div v-if="previewSize.w && previewSize.h" class="media-size-modal">
        {{ previewSize.w }} × {{ previewSize.h }} px
        </div>

        <img v-if="previewItem?.type === 'img'" :src="`/multimedia/${previewItem.link}`" class="preview-img" />

        <video v-else-if="previewItem?.type === 'video'" class="preview-video" controls autoplay loop muted playsinline
        :poster="`/multimedia/${previewItem.poster || previewItem.link}`">
        <source v-for="(link, i) in previewItem.links" :key="i" :src="`/multimedia/${link.link}`" />
        </video>

        <iframe v-else-if="previewItem?.type === 'vr'"
        class="preview-iframe"
        :src="extractIframeSrc(previewItem.link)"
        :width="previewItem.width || 800"
        :height="previewItem.height || 500"
        frameborder="0"
        allowfullscreen
        allow="xr-spatial-tracking; gyroscope; accelerometer"
        scrolling="no"
        ></iframe>

            <!-- Curtain-->
            <div v-else-if="previewItem?.type === 'curtain'" class="curtain-preview-container">
            <div class="curtain-preview">
                <img class="curtain-img curtain-front" :src="`/multimedia/${previewItem.images[0]}`" />
                <img class="curtain-img curtain-back" :src="`/multimedia/${previewItem.images[1]}`" />
                <div class="curtain-slider" ref="curtainSlider" @mousedown="startCurtainDrag" />
            </div>
            </div>

        <p v-else>Нет данных для предпросмотра</p>

        <el-input
        v-if="previewItem?.type === 'img'"
        v-model="previewItem.title"
        placeholder="Название / alt"
        :size="inputSize"
        style="margin-top: 16px; width: 100%; text-align: center;"
        />
    </div>
    </el-dialog>

    <!-- Модалка добавления VR -->
    <el-dialog v-model="showVrModal" title="Добавление VR-тура" width="600px" :append-to-body="true">
      <el-form label-position="top">
        <el-form-item label="HTML iframe код">
          <el-input
            type="textarea"
            v-model="vrIframeCode"
            rows="4"
             :size="inputSize"
            placeholder='<iframe src="..." width="..." height="..." ...></iframe>'
          />
        </el-form-item>

        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="Ширина">
              <el-input-number v-model="vrWidth" :min="100" :step="100"  :size="inputSize" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Высота">
              <el-input-number v-model="vrHeight" :min="100" :step="100" :size="inputSize" />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>

      <template #footer>
        <el-button @click="cancelVrInsert">Отмена</el-button>
        <el-button type="primary" @click="insertVrIframe">Добавить</el-button>
        <el-button @click="detectVrIframeSize">Определить размер</el-button>
      </template>
    </el-dialog>

    <!-- Модалка добавления шторки -->
    <el-dialog v-model="showCurtainModal" title="Добавление шторки" width="600px" :append-to-body="true">
    <div style="display: flex; flex-direction: column; gap: 20px">
        <div>
        <p><b>Первое изображение:</b></p>
<div v-if="curtainImage1" class="preview-img">
  <div v-if="curtainSize1.w && curtainSize1.h" style="margin-bottom: 4px; font-size: 13px; text-align: center;">
    {{ curtainSize1.w }} × {{ curtainSize1.h }} px
  </div>
  <img :src="'/multimedia/' + curtainImage1" style="max-width: 100%;" />
</div>
        <el-button @click="selectCurtainImage(1)">Выбрать изображение 1</el-button>
        <el-input
            v-model="curtainTitle1"
            placeholder="alt для первого изображения"
             :size="inputSize"
            style="margin-top: 10px"
            />
        </div>

        <div>
        <p><b>Второе изображение:</b></p>
<div v-if="curtainImage2" class="preview-img">
  <div v-if="curtainSize2.w && curtainSize2.h" style="margin-bottom: 4px; font-size: 13px; text-align: center;">
    {{ curtainSize2.w }} × {{ curtainSize2.h }} px
  </div>
  <img :src="'/multimedia/' + curtainImage2" style="max-width: 100%;" />
</div>
        <el-button @click="selectCurtainImage(2)">Выбрать изображение 2</el-button>
        <el-input
            v-model="curtainTitle2"
            placeholder="alt для второго изображения"
             :size="inputSize"
            style="margin-top: 10px"
            />
        </div>
    </div>

    <template #footer>
        <el-button @click="cancelCurtainInsert">Отмена</el-button>
        <el-button type="primary" @click="insertCurtain">Добавить шторку</el-button>
    </template>
    </el-dialog>

    <!-- FileManager -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal">
        <div class="finder-container">
          <vue-finder
            :request="{ baseUrl:'/api/vuefinder', adapter:'local', xsrfHeaderName:'X-XSRF-TOKEN' }"
            @select="handleFileSelect"
          />
          <button class="close-btn" @click="showFileManager = false">✖</button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import draggable from 'vuedraggable'
import TinyEditor from '@/components/admin/TinyEditor.vue'
import SeoProject from '@/components/admin/SeoProject.vue'
import Projects from './Projects.vue'
import { ElNotification } from 'element-plus'
import { Plus, Check, Delete, Edit, Picture, Menu, VideoCamera, View, Connection } from '@element-plus/icons-vue'
import { ElMessageBox } from 'element-plus'

const inputSize = inject('inputSize')

const curtainTitle1 = ref('')
const curtainTitle2 = ref('')

const route = useRoute(), router = useRouter()
const openTab = inject('openTab'), tabsMode = inject('tabsMode')

const isEditing = ref(false)
const projectId = ref(null)

const form = ref({
  title: '',
  text1: '',
  text2: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  multimedia_grid: []
})

const gridRows = ref([])
const selectedCell = ref(null)
const showFileManager = ref(false)
const previewVisible = ref(false)
const previewItem = ref({})


const showMediaModal = ref(false)
const mediaType = ref(null)
const modalMediaTitle = ref('')
const modalMediaPreview = ref('')
const modalMediaSize = ref({ w: null, h: null })
const isEditingMedia = ref(false)

// вызвать модалку
const openMediaModal = (type, isEdit = false) => {
  mediaType.value = type
  showMediaModal.value = true
  isEditingMedia.value = isEdit
  modalMediaTitle.value = ''
  modalMediaPreview.value = ''
  modalMediaSize.value = { w: null, h: null }
}

const openFileManagerForModal = () => {
  if (!selectedCell.value?.rowIdx && typeof pendingCurtainRowIdx.value === 'number') {
    selectedCell.value = { rowIdx: pendingCurtainRowIdx.value }
  }

  if (!selectedCell.value?.rowIdx && typeof pendingVrRowIdx.value === 'number') {
    selectedCell.value = { rowIdx: pendingVrRowIdx.value }
  }

  if (!selectedCell.value?.rowIdx && typeof selectedCell.value !== 'object') {
    console.warn('⚠ Нет строки для вставки, selectedCell не установлен корректно')
  }

  showFileManager.value = true
}

// VR
const showVrModal = ref(false)
const vrIframeCode = ref('')
const vrWidth = ref(1920)
const vrHeight = ref(1080)
const pendingVrRowIdx = ref(null)

// Curtain
const showCurtainModal = ref(false)
const pendingCurtainRowIdx = ref(null)
const curtainImage1 = ref('')
const curtainImage2 = ref('')
const selectingCurtainIndex = ref(null)
const curtainSize1 = ref({ w: null, h: null })
const curtainSize2 = ref({ w: null, h: null })

const curtainSlider = ref(null)

let isDraggingCurtain = false

const startCurtainDrag = (e) => {
  isDraggingCurtain = true
  document.addEventListener('mousemove', onCurtainDrag)
  document.addEventListener('mouseup', stopCurtainDrag)
}

const onCurtainDrag = (e) => {
  if (!isDraggingCurtain || !curtainSlider.value) return
  const parent = curtainSlider.value.parentElement
  const bounds = parent.getBoundingClientRect()
  const x = e.clientX - bounds.left
  const percent = Math.max(0, Math.min(100, (x / bounds.width) * 100))
  curtainSlider.value.style.left = `${percent}%`
  parent.style.setProperty('--clip-pos', `${percent}%`)
}

const stopCurtainDrag = () => {
  isDraggingCurtain = false
  document.removeEventListener('mousemove', onCurtainDrag)
  document.removeEventListener('mouseup', stopCurtainDrag)
}

// =====================
// Контентная логика
// =====================
const addRow = () => gridRows.value.push({ id: Date.now() + Math.random(), items: [] })
const removeRow = async (idx) => {
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
    gridRows.value.splice(idx, 1)
  } catch (_) {
    // Отмена — ничего не делаем
  }
}

const handleAddCol = (rowIdx, type = null) => {
  if (type === 'curtain') {
    selectedCell.value = null
    pendingCurtainRowIdx.value = rowIdx
    showCurtainModal.value = true
    return
  }

  if (type === 'vr') {
    selectedCell.value = null
    pendingVrRowIdx.value = rowIdx
    showVrModal.value = true
    return
  }

  // img / video → открываем модалку, но НЕ добавляем пока колонку
  mediaType.value = type
  isEditingMedia.value = false
  showMediaModal.value = true
  modalMediaTitle.value = ''
  modalMediaPreview.value = ''
  modalMediaSize.value = { w: null, h: null }

  selectedCell.value = { rowIdx, modal: true } // запоминаем строку
}

const removeCol = async (r, c) => {
  try {
    await ElMessageBox.confirm(
      'Вы уверены, что хотите удалить этот элемент?',
      'Подтверждение удаления',
      {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        type: 'warning',
      }
    )
    gridRows.value[r].items.splice(c, 1)
  } catch (_) {
    // Отмена — ничего не делаем
  }
}

const openFileManager = (r, c) => {
  selectedCell.value = { rowIdx: r, colIdx: c }
  showFileManager.value = true
}

const handleFileSelect = async (items) => {
  if (!items.length) return
  const file = items[0]
  const path = file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  const ext = path.split('.').pop()?.toLowerCase()
  const isImage = file.mime?.includes('image') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext)
  const isVideo = file.mime?.includes('video') || ['mp4', 'webm', 'mov'].includes(ext)

  // Шторка
  if (selectingCurtainIndex.value) {
    const target = selectingCurtainIndex.value === 1 ? curtainImage1 : curtainImage2
    const size = selectingCurtainIndex.value === 1 ? curtainSize1 : curtainSize2

    target.value = path
    await nextTick()
    const img = new Image()
    img.onload = () => size.value = { w: img.naturalWidth, h: img.naturalHeight }
    img.src = `/multimedia/${path}`
    selectingCurtainIndex.value = null
    showFileManager.value = false
    return
  }
console.log('>> FILE SELECTED:', JSON.stringify({ selectedCell: selectedCell.value }))

  // 🛠️ Модальное окно (если мы не редактируем существующую колонку напрямую)
  if (selectedCell.value?.modal && 'rowIdx' in selectedCell.value) {
    modalMediaPreview.value = path

    if (isImage) {
      const img = new Image()
      img.onload = () => modalMediaSize.value = { w: img.naturalWidth, h: img.naturalHeight }
      img.src = `/multimedia/${path}`
    }

    showFileManager.value = false
    return
  }

  // Редактирование уже существующей ячейки (через иконку "карандаш")
  if (
    selectedCell.value?.rowIdx !== undefined &&
    selectedCell.value?.colIdx !== undefined
  ) {
    const row = gridRows.value[selectedCell.value.rowIdx]
    const col = row.items[selectedCell.value.colIdx]

    if (isVideo) {
      col.media = {
        type: 'video',
        poster: '',
        links: [{ link: path, mime: file.mime || 'video/mp4' }]
      }
    } else if (isImage) {
      col.media = { type: 'img', link: path }
    } else {
      col.media = {}
    }

    showFileManager.value = false
    return
  }

  console.warn('⚠ Не удалось определить, куда вставлять файл')
}

const handleEditClick = (rowIdx, colIdx, type) => {
  selectedCell.value = { rowIdx, colIdx }

  if (type === 'img' || type === 'video') {
    mediaType.value = type
    isEditingMedia.value = true
    showMediaModal.value = true

    const col = gridRows.value[rowIdx].items[colIdx]
    modalMediaTitle.value = col.title || ''

    if (type === 'img') {
      modalMediaPreview.value = col.media.link
      const img = new Image()
      img.onload = () => modalMediaSize.value = { w: img.naturalWidth, h: img.naturalHeight }
      img.src = `/multimedia/${modalMediaPreview.value}`
    } else if (type === 'video') {
      modalMediaPreview.value = col.media.links?.[0]?.link || ''
    }

    return
  }

  if (type === 'vr') {
    const col = gridRows.value[rowIdx].items[colIdx]
    pendingVrRowIdx.value = rowIdx
    vrIframeCode.value = col.media.link || ''
    vrWidth.value = col.media.width || 1920
    vrHeight.value = col.media.height || 1080
    showVrModal.value = true
  }

  if (type === 'curtain') {
    const col = gridRows.value[rowIdx].items[colIdx]
    pendingCurtainRowIdx.value = rowIdx
    curtainImage1.value = col.media.images?.[0] || ''
    curtainImage2.value = col.media.images?.[1] || ''
    curtainTitle1.value = col.media.titles?.[0] || form.value.meta_title || ''
    curtainTitle2.value = col.media.titles?.[1] || form.value.meta_title || ''
    showCurtainModal.value = true

    if (curtainImage1.value) {
      const img1 = new Image()
      img1.onload = () => (curtainSize1.value = { w: img1.naturalWidth, h: img1.naturalHeight })
      img1.src = `/multimedia/${curtainImage1.value}`
    }

    if (curtainImage2.value) {
      const img2 = new Image()
      img2.onload = () => (curtainSize2.value = { w: img2.naturalWidth, h: img2.naturalHeight })
      img2.src = `/multimedia/${curtainImage2.value}`
    }
  }
}

const confirmMediaInsert = () => {
  const rowIdx = selectedCell.value?.rowIdx ?? null
  const colIdx = selectedCell.value?.colIdx ?? null

  console.log('>> confirmMediaInsert:', { rowIdx, colIdx, selectedCell: selectedCell.value })

  if (rowIdx === null || !gridRows.value[rowIdx]) {
    ElNotification({ title: 'Ошибка', message: 'Не выбрана строка для вставки', type: 'warning' })
    return
  }

  const col = {
    title: modalMediaTitle.value || '',
    media: {}
  }

  if (mediaType.value === 'img') {
    col.media = {
      type: 'img',
      link: modalMediaPreview.value
    }
  } else if (mediaType.value === 'video') {
    col.media = {
      type: 'video',
      poster: '',
      links: [{ link: modalMediaPreview.value, mime: 'video/mp4' }]
    }
  }

  if (typeof colIdx === 'number') {
    gridRows.value[rowIdx].items[colIdx] = col
  } else {
    gridRows.value[rowIdx].items.push(col)
  }

  showMediaModal.value = false
  isEditingMedia.value = false
  modalMediaPreview.value = ''
  modalMediaTitle.value = ''
}

const cancelMediaInsert = () => {
  showMediaModal.value = false
  isEditingMedia.value = false
  modalMediaPreview.value = ''
  modalMediaTitle.value = ''
}

// =====================
// VR
// =====================
const insertVrIframe = async () => {
  if (!vrIframeCode.value.trim()) {
    ElNotification({ title: 'Ошибка', message: 'Введите iframe-код перед добавлением', type: 'warning' })
    return
  }

  // Извлекаем src из iframe-кода, если это iframe
  const match = vrIframeCode.value.trim().match(/src=["']([^"']+)["']/)
  const cleanSrc = match ? match[1] : vrIframeCode.value.trim()

  const col = {
    title: '',
    media: {
      type: 'vr',
      link: cleanSrc,
      width: vrWidth.value,
      height: vrHeight.value
    }
  }

  if (
    selectedCell.value &&
    selectedCell.value.rowIdx !== null &&
    selectedCell.value.colIdx !== null
  ) {
    gridRows.value[selectedCell.value.rowIdx].items[selectedCell.value.colIdx] = col
  } else if (
    pendingVrRowIdx.value !== null &&
    gridRows.value[pendingVrRowIdx.value]
  ) {
    gridRows.value[pendingVrRowIdx.value].items.push(col)
  } else {
    ElNotification({ title: 'Ошибка', message: 'Не удалось определить строку для вставки VR', type: 'error' })
    return
  }

  showVrModal.value = false
  pendingVrRowIdx.value = null
  selectedCell.value = { rowIdx: null, colIdx: null }
  vrIframeCode.value = ''
  vrWidth.value = 1920
  vrHeight.value = 1080

  ElNotification({ title: 'Успешно', message: 'VR-тур сохранён', type: 'success' })
  await nextTick()
}

const cancelVrInsert = () => {
  showVrModal.value = false
  pendingVrRowIdx.value = null
  vrIframeCode.value = ''
  vrWidth.value = 1920
  vrHeight.value = 1080
}

const detectVrIframeSize = () => {
  if (!vrIframeCode.value) return
  const widthMatch = vrIframeCode.value.match(/width=["']?(\d+)/i)
  const heightMatch = vrIframeCode.value.match(/height=["']?(\d+)/i)
  if (widthMatch) vrWidth.value = parseInt(widthMatch[1])
  if (heightMatch) vrHeight.value = parseInt(heightMatch[1])
  ElNotification({ title: 'Размер определён', message: `Ширина: ${vrWidth.value}px, Высота: ${vrHeight.value}px`, type: 'success' })
}

// =====================
// Curtain
// =====================
const selectCurtainImage = (index) => {
  selectingCurtainIndex.value = index
  showFileManager.value = true
}

const insertCurtain = () => {
    console.log('--- CURTAIN INSERT ---')
    console.log('pendingCurtainRowIdx', pendingCurtainRowIdx.value)
    console.log('selectedCell', selectedCell.value)
    console.log('gridRows', JSON.stringify(gridRows.value, null, 2))

  if (!curtainImage1.value || !curtainImage2.value) {
    ElNotification({ title: 'Ошибка', message: 'Выберите оба изображения', type: 'warning' })
    return
  }

    const defaultTitle = form.value.meta_title || ''

const col = {
  title: '',
  media: {
    type: 'curtain',
    images: [curtainImage1.value, curtainImage2.value],
    titles: [curtainTitle1.value || form.value.meta_title, curtainTitle2.value || form.value.meta_title]
  }
}

  const rowIdx = selectedCell.value?.rowIdx ?? null
  const colIdx = selectedCell.value?.colIdx ?? null

  const isEditingExisting =
    rowIdx !== null &&
    colIdx !== null &&
    gridRows.value[rowIdx] &&
    Array.isArray(gridRows.value[rowIdx].items) &&
    gridRows.value[rowIdx].items[colIdx]

  if (isEditingExisting) {
    gridRows.value[rowIdx].items[colIdx] = col
  } else if (
    pendingCurtainRowIdx.value !== null &&
    gridRows.value[pendingCurtainRowIdx.value] &&
    Array.isArray(gridRows.value[pendingCurtainRowIdx.value].items)
  ) {
    gridRows.value[pendingCurtainRowIdx.value].items.push(col)
  } else {
    console.warn('Не удалось определить строку для вставки шторки')
    return
  }

  showCurtainModal.value = false
  curtainTitle1.value = ''
  curtainTitle2.value = ''
  curtainImage1.value = ''
  curtainImage2.value = ''
  curtainSize1.value = { w: null, h: null }
  curtainSize2.value = { w: null, h: null }
  pendingCurtainRowIdx.value = null
  selectedCell.value = { rowIdx: null, colIdx: null }

  ElNotification({ title: 'Успешно', message: 'Шторка сохранена', type: 'success' })
  nextTick(() => {
  console.log('ROWS AFTER CURTAIN INSERT:', JSON.stringify(gridRows.value, null, 2))
})

}

const cancelCurtainInsert = () => {
  showCurtainModal.value = false
  curtainTitle1.value = ''
  curtainTitle2.value = ''
  curtainImage1.value = ''
  curtainImage2.value = ''
  curtainSize1.value = { w: null, h: null }
  curtainSize2.value = { w: null, h: null }
  pendingCurtainRowIdx.value = null
}

// =====================
// Предпросмотр
// =====================
const previewSize = ref({ w: null, h: null })
const openPreview = async (col) => {
  previewItem.value = {
    ...col.media,
    title: col.title ?? ''
  }
  previewItem.value.__ref = col
  previewVisible.value = true

  await nextTick()

  const img = document.querySelector('.preview-modal-content img')
  const video = document.querySelector('.preview-modal-content video')

  if (img) {
    previewSize.value = { w: img.naturalWidth, h: img.naturalHeight }
  } else if (video) {
    video.addEventListener('loadedmetadata', () => {
      previewSize.value = { w: video.videoWidth, h: video.videoHeight }
    }, { once: true })
  } else {
    previewSize.value = { w: null, h: null }
  }
}

const extractIframeSrc = (html) => {
  if (!html) return ''
  if (html.includes('<iframe')) {
    const match = html.match(/src=["']([^"']+)["']/)
    return match ? match[1] : ''
  }
  return html // просто URL
}

// =====================
// Submit и Load
// =====================
const submit = async () => {
  try {
    form.value.multimedia_grid = gridRows.value.map(row =>
      row.items.map(col => {
        const base = {
          type: col.media?.type,
          title: col.title || ''
        }

        if (col.media?.type === 'img') return { ...base, link: col.media.link }
        if (col.media?.type === 'video') return { ...base, poster: col.media.poster || '', links: col.media.links || [] }
        if (col.media?.type === 'vr') return { ...base, link: col.media.link, width: col.media.width, height: col.media.height }
        if (col.media?.type === 'curtain') return {
        ...base,
        images: col.media.images,
        titles: col.media.titles || []
        }

        return base
      })
    )

    if (isEditing.value) {
      await axios.put(`/api/admin/projects/${projectId.value}`, form.value)
      ElNotification({ title: 'Успешно', message: 'Проект обновлён', type: 'success' })
    } else {
      await axios.post('/api/admin/projects', form.value)
      ElNotification({ title: 'Успешно', message: 'Проект создан', type: 'success' })
    }

    if (tabsMode?.value && openTab) {
      openTab({ path: '/projects/list', label: 'Список проектов', component: Projects })
    } else {
      router.push('/projects')
    }

  } catch (e) {
    ElNotification({ title: 'Ошибка', message: 'Не удалось сохранить проект', type: 'error' })
  }
}

const loadProject = async () => {
  if (route.name === 'EditProject') {
    isEditing.value = true
    projectId.value = route.params.id
    const { data } = await axios.get(`/api/admin/projects/${projectId.value}`)
    form.value = {
      ...data,
      multimedia_grid: data.multimedia_grid || []
    }

    gridRows.value = form.value.multimedia_grid.map(items => ({
      id: Date.now() + Math.random(),
      items: items.map(item => {
        const alt = item.title || item.description || item.link?.split('/').pop() || ''

        let link = item.link || ''
        let type = item.type


        if (item.type === 'curtain') {
        const first = item.first?.link || item.images?.[0]
        const last = item.last?.link || item.images?.[1]

        return {
            title: alt,
            media: {
                type: 'curtain',
                images: [first, last].filter(Boolean),
                titles: item.titles || []
                }
            }
        }

        // 🔧 если iframe — значит VR
        if (item.type === 'iframe') {
          type = 'vr'
          if (typeof link === 'string' && link.includes('<iframe')) {
            const match = link.match(/src=["']([^"']+)["']/)
            link = match ? match[1] : ''
          }
        }

        return {
          title: alt,
          media: {
            type,
            link,
            poster: item.poster || '',
            links: item.links || [],
            description: item.description || '',
            width: item.width || null,
            height: item.height || null,
            images: item.images || []
          }
        }
      })
    }))
  } else {
    // при создании
    isEditing.value = false
    projectId.value = null
    form.value = {
      title: '',
      text1: '',
      text2: '',
      meta_title: '',
      meta_description: '',
      meta_keywords: '',
      multimedia_grid: []
    }
    gridRows.value = []
  }
}

onMounted(loadProject)
watch(() => route.params.id, loadProject)
</script>

<style scoped>
.project-create-page {
  padding: 24px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
.page-title {
  font-size: 22px;
  margin-bottom: 24px;
  font-weight: 600;
}
.editor-row {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}
.editor-item {
  flex: 1;
}

.panel-buttons {
  display: flex;
  gap: 15px;
  margin: 20px 0;
}
.grid-rows {
  display: flex;
  flex-direction: column;
  gap: 25px;
}
.grid-row-wrap {
  margin-bottom: 18px;
  border: 2px dashed #e3e3e3;
  border-radius: 12px;
  background: #f9f9fb;
  padding: 15px 8px 8px;
  position: relative;
}
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
.drag-row {
  cursor: grab;
  font-size: 20px;
  color: #aaa;
}
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
.columns-draggable {
  display: flex;
  gap: 16px;
  width: 100%;
}
.grid-item {
  border: 1px dashed #ccc;
  border-radius: 6px;
  min-width: 220px;
  max-width: 320px;
  padding: 10px;
  background: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  position: relative;
}
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
.edit-icon:hover {
  background: #f3f7ff;
}
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
.finder-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 3000;
}
.finder-container {
  width: 90%;
  height: 90%;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: red;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
}
.preview-modal-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.preview-img,
.preview-video {
  max-width: 100%;
  max-height: calc(100vh - 140px);
  object-fit: contain;
  background: #000;
  border-radius: 10px;
  box-shadow: 0 3px 16px rgba(0, 0, 0, 0.18);
}
.grid-iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 10px;
}
.media-placeholder {
  width: 100%;
  height: 100%;
  background: #fafafa;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #aaa;
  font-size: 14px;
  text-align: center;
}
.media-placeholder .el-icon {
  font-size: 34px;
  margin-bottom: 4px;
}
.media-placeholder .media-label {
  font-size: 13px;
  font-weight: 500;
}
.vr-placeholder {
  background: #e8f5ff;
}
.curtain-placeholder {
  background: #fff6e6;
}
.preview-iframe {
  width: 100%;
  max-width: 100%;
  min-height: 400px;
  border-radius: 10px;
  box-shadow: 0 3px 16px rgba(0, 0, 0, 0.18);
  background: #000;
}
.curtain-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.curtain-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1.4s ease-in-out;
  will-change: transform;
}

/* В сетке — с анимацией */
.with-animation .curtain-front {
  z-index: 2;
  transform: translateX(0%);
  animation: slide-out 6s ease-in-out infinite alternate;
}
.with-animation .curtain-back {
  z-index: 1;
  transform: translateX(100%);
  animation: slide-in 6s ease-in-out infinite alternate;
}

/* В модалке — без анимации */
.preview-modal-content .curtain-front,
.preview-modal-content .curtain-back {
  animation: none !important;
  transform: none !important;
}

@keyframes slide-out {
  0% { transform: translateX(0%); }
  100% { transform: translateX(-100%); }
}

@keyframes slide-in {
  0% { transform: translateX(100%); }
  100% { transform: translateX(0%); }
}

.curtain-preview-container {
  width: 100%;
  max-width: 100%;
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  aspect-ratio: 16 / 9;
  background: #000;
  box-shadow: 0 3px 16px rgba(0,0,0,0.2);
}

.curtain-preview {
  width: 100%;
  height: 100%;
  position: relative;
  --clip-pos: 50%;
    display: block;
  background: transparent;
}

.curtain-preview .curtain-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.curtain-preview .curtain-back {
  z-index: 1;
}

.curtain-preview .curtain-front {
  z-index: 2;
  clip-path: polygon(0 0, var(--clip-pos) 0, var(--clip-pos) 100%, 0 100%);
}

.curtain-preview .curtain-slider {
  position: absolute;
  top: 0;
  bottom: 0;
  left: var(--clip-pos);
  width: 3px;
  background: #fff;
  z-index: 3;
  cursor: ew-resize;
  transform: translateX(-50%);
}
.add-col-btn {
  width: 36px;
  height: 36px;
  padding: 0;
  background-color: #409EFF;
  border: none;
  border-radius: 6px; /* ❗ квадрат с чуть скруглёнными углами */
  color: #fff;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s ease;
}
.add-col-btn:hover {
  background-color: #66b1ff;
}

@media (max-width: 768px) {
  .row-header .add-col-dropdown {
    margin-left: auto;
  }
}

</style>
