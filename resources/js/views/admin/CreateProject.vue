<template>
  <div class="project-create-page">
    <h2 class="page-title">{{ isEditing ? 'Редактирование проекта' : 'Создание проекта' }}</h2>

       <a
        v-if="isEditing && projectId"
        :href="`/project/${projectId}`"
        target="_blank"
        class="inline-flex items-center text-blue-600 hover:underline text-lg font-medium mb-4"
        style="margin-bottom: 16px; display: inline-flex; align-items: center;"
        >
        <el-icon style="margin-right: 6px;"><View /></el-icon> Открыть проект
        </a>


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
        <div class="drag-row-overlay"></div>

        <div class="grid-row-container">
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
                    <!-- Media content -->
                    <template v-if="col.media?.type === 'img'">
                      <img
                        class="grid-img"
                        :key="col.media?.__v || colIdx"
                        :src="buildMediaUrl(col.media.link) + (col.media?.__v ? '?v=' + col.media.__v : '')"
                        />
                    </template>

                    <template v-else-if="col.media?.type === 'video'">
                        <video
                        class="grid-video"
                        :key="col.media?.__v || colIdx"
                        autoplay muted loop playsinline preload="metadata"
                        :poster="col.media.poster ? buildMediaUrl(col.media.poster) + (col.media.__v ? '?v=' + col.media.__v : '') : undefined"
                        >
                        <source
                            v-for="(link, i) in col.media.links || []"
                            :key="link.link + '_' + i"
                            :src="buildMediaUrl(link.link) + (col.media.__v ? '?v=' + col.media.__v : '')"
                            :type="link.mime || 'video/mp4'"
                        />
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
                        <img class="curtain-img curtain-front" :src="buildMediaUrl(col.media.images[0])" />
                        <img class="curtain-img curtain-back" :src="buildMediaUrl(col.media.images[1])" />
                      </div>
                    </template>

                    <template v-else>
                      <div class="empty-media"><el-icon><Picture /></el-icon></div>
                    </template>

                    <!-- Overlay: Edit icon, file type icon, delete -->
                    <div class="media-overlay">
                      <span class="edit-icon" @click.stop="handleEditClick(rowIdx, colIdx, col.media?.type)">
                        <el-icon><Edit /></el-icon>
                      </span>

                      <div class="media-tools">
                        <el-icon v-if="col.media?.type === 'video'"><VideoCamera /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'img'"><Picture /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'vr'"><View /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'curtain'"><Connection /></el-icon>

                        <el-button
                          size="small"
                          type="danger"
                          circle
                          @click.stop="removeCol(rowIdx, colIdx)"
                          title="Удалить элемент"
                        >
                          <el-icon><Delete /></el-icon>
                        </el-button>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </draggable>
          </div>

          <div class="row-actions-inline">
            <el-button
              type="danger"
              size="small"
              circle
              @click.stop="removeRow(rowIdx)"
              title="Удалить строку"
            >
              <el-icon><Delete /></el-icon>
            </el-button>

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
        <img
            v-if="modalMediaPreview"
            :key="modalMediaPreview + '__' + modalMediaSize.w + 'x' + modalMediaSize.h + '__' + Date.now()"
            :src="buildMediaUrl(modalMediaPreview) + '?v=' + Date.now()"
            class="preview-img"
            style="margin-top: 10px;" />
        </el-form-item>

        <el-form-item v-else-if="mediaType === 'video'" label="Видео">
                    <div v-if="modalMediaSize.w && modalMediaSize.h" class="media-size-modal" style="margin-bottom: 6px;">
            {{ modalMediaSize.w }} × {{ modalMediaSize.h }} px
        </div>
        <el-button @click="openFileManagerForModal">Выбрать видео</el-button>
        <div v-if="modalMediaPreview" style="margin-top: 10px;">
            <video
                :key="modalMediaPreview"
                autoplay muted loop playsinline controls
                style="max-width: 100%;"
                >
                <source :src="buildMediaUrl(modalMediaPreview) + '?v=' + Date.now()" />
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

        <img
            v-if="previewItem?.type === 'img' && previewItem.link"
            :src="buildMediaUrl(previewItem.link) + '?v=' + Date.now()"
            class="preview-img"
            />

        <video
        v-else-if="previewItem?.type === 'video'"
        :key="previewItem.links?.[0]?.link || Date.now()"
        class="preview-video"
        controls
        autoplay
        loop
        muted
        playsinline
        :poster="previewItem.poster || previewItem.link ? buildMediaUrl(previewItem.poster || previewItem.link) + '?v=' + Date.now() : undefined">
        <source
            v-for="(link, i) in previewItem.links"
            :key="i"
            :src="buildMediaUrl(link.link) + '?v=' + Date.now()"
            :type="link.mime || 'video/mp4'" />
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
                <img class="curtain-img curtain-front"  :src="buildMediaUrl(previewItem.images[0])" />
                <img class="curtain-img curtain-back" :src="buildMediaUrl(previewItem.images[1])" />
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
        <!-- Предпросмотр -->
        <div v-if="vrIframeCode.trim()" style="margin-top: 16px">
        <p style="font-weight: 500; margin-bottom: 6px;">Предпросмотр VR-тура:</p>

        <div v-if="vrLoadError" style="color: red; margin-bottom: 8px">
            ⚠️ Не удалось загрузить VR-тур. Проверьте путь или содержимое архива (index.htm).
        </div>

        <iframe
            ref="vrPreviewIframe"
            :src="extractIframeSrc(vrIframeCode) + '?v=' + Date.now()"
            :width="Math.min(vrWidth, 500)"
            :height="Math.min(vrHeight, 300)"
            frameborder="0"
            allow="autoplay; fullscreen; xr-spatial-tracking; gyroscope; accelerometer"
            style="border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); max-width: 100%;"
            @error="vrLoadError = true"
            @load="vrLoadError = false"
        ></iframe>
        </div>

        <!-- Форма -->
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
                <el-input-number v-model="vrWidth" :min="100" :step="100" :size="inputSize" />
                </el-form-item>
            </el-col>
            <el-col :span="12">
                <el-form-item label="Высота">
                <el-input-number v-model="vrHeight" :min="100" :step="100" :size="inputSize" />
                </el-form-item>
            </el-col>
            </el-row>

            <el-form-item label="Загрузить архив VR-тура (.zip)">
                <!-- Скрытый input -->
                <input
                    ref="vrZipInput"
                    type="file"
                    accept=".zip"
                    style="display: none"
                    @change="onVrZipChange"
                />

                <!-- Видимая кнопка -->
                <el-button
                    @click="vrZipInput?.click()"
                    :disabled="vrUploading"
                    style="margin-top: 8px"
                >
                    Выбрать архив
                </el-button>

                <!-- Кнопка загрузки -->
                <el-button
                    @click="uploadVrZip"
                    type="primary"
                    :disabled="!vrZipFile"
                    style="margin-left: 12px; margin-top: 8px"
                    :loading="vrUploading"
                >
                    Загрузить архив
                </el-button>

                <!-- Показываем имя выбранного файла -->
                <div v-if="vrZipFile" style="margin-top: 6px; font-size: 13px; color: #666;">
                    📦 {{ vrZipFile.name }}
                </div>
                </el-form-item>
        </el-form>

        <!-- Футер -->
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
  <img :src="buildMediaUrl(curtainImage1)" style="max-width: 100%;" />
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
  <img :src="buildMediaUrl(curtainImage2)" style="max-width: 100%;" />
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
defineOptions({
  name: 'CreateProject'
})
import { ref, onMounted, watch, nextTick, inject, onActivated } from 'vue'
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

const resetForm = () => {
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
  isEditing.value = false
  projectId.value = null
}

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
const originalMedia = ref(null)

const vrZipFile = ref(null)
const vrLoadError = ref(false)
const vrPreviewIframe = ref(null)
const vrUploading = ref(false)
const vrZipInput = ref(null)

const fileExists = async (path) => {
  try {
    await axios.head(path)
    return true
  } catch {
    return false
  }
}

const buildMediaUrl = (link) => {
  if (!link) return ''

  // Уже абсолютный URL
  if (link.startsWith('http')) return link

  // Уже начинается с multimedia/ — ок
  if (link.startsWith('multimedia/')) return '/' + link

  // Добавим префикс
  return '/multimedia/' + link
}

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
  const rawPath = file.path.replace(/^local:\/\//, '').replace(/^\/+/, '')
  const fullPath = rawPath

  const ext = fullPath.split('.').pop()?.toLowerCase()
  const isImage = file.mime?.includes('image') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext)
  const isVideo = file.mime?.includes('video') || ['mp4', 'webm', 'mov'].includes(ext)

  // 🧩 Шторка
  if (selectingCurtainIndex.value) {
    const target = selectingCurtainIndex.value === 1 ? curtainImage1 : curtainImage2
    const size = selectingCurtainIndex.value === 1 ? curtainSize1 : curtainSize2

    target.value = fullPath
    await nextTick()
    const img = new Image()
    img.onload = () => size.value = { w: img.naturalWidth, h: img.naturalHeight }
    img.src = buildMediaUrl(fullPath)
    selectingCurtainIndex.value = null
    showFileManager.value = false
    return
  }

  // 📦 Модалка вставки нового медиа
  if (selectedCell.value?.modal && 'rowIdx' in selectedCell.value) {
    modalMediaPreview.value = fullPath

    if (isImage) {
      const img = new Image()
      img.onload = () => {
        modalMediaSize.value = { w: img.naturalWidth, h: img.naturalHeight }
        modalMediaTitle.value = getFilename(fullPath) // или ''
      }
      img.src = buildMediaUrl(fullPath)
    } else if (isVideo) {
      const video = document.createElement('video')
      video.preload = 'metadata'
      video.src = buildMediaUrl(fullPath)
      video.onloadedmetadata = () => {
        modalMediaSize.value = { w: video.videoWidth, h: video.videoHeight }
        modalMediaTitle.value = getFilename(fullPath) // или ''
      }
    }

    showFileManager.value = false
    return
  }

  // ✏️ Редактирование существующего элемента
  if (selectedCell.value?.rowIdx !== undefined && selectedCell.value?.colIdx !== undefined) {
    const row = gridRows.value[selectedCell.value.rowIdx]
    const col = row.items[selectedCell.value.colIdx]

    if (isImage) {
      const img = new Image()
      img.onload = () => {
        const updated = {
          type: 'img',
          link: fullPath,
          title: modalMediaTitle.value || col.media?.title || '',
          width: img.naturalWidth,
          height: img.naturalHeight,
          __v: Date.now()
        }

        col.media = updated

        modalMediaPreview.value = fullPath
        modalMediaTitle.value = updated.title
        modalMediaSize.value = { w: img.naturalWidth, h: img.naturalHeight }
      }
      img.src = buildMediaUrl(fullPath)

    } else if (isVideo) {
      const video = document.createElement('video')
      video.preload = 'metadata'
      video.src = buildMediaUrl(fullPath)
      video.onloadedmetadata = () => {
        const updated = {
          type: 'video',
          poster: col.media?.poster || '',
          links: [{ link: fullPath, mime: file.mime || 'video/mp4' }],
          title: modalMediaTitle.value || col.media?.title || '',
          width: video.videoWidth,
          height: video.videoHeight,
          __v: Date.now()
        }

        col.media = updated

        modalMediaPreview.value = fullPath
        modalMediaTitle.value = updated.title
        modalMediaSize.value = { w: video.videoWidth, h: video.videoHeight }
      }
    }

    showFileManager.value = false
    return
  }

  console.warn('⚠ Не удалось определить, куда вставлять файл')
}

const handleEditClick = async (rowIdx, colIdx, type) => {
  const row = gridRows.value[rowIdx]
  const col = row?.items?.[colIdx]

  if (!col || !col.media) {
    console.warn('❌ Элемент не найден для редактирования')
    return
  }

  selectedCell.value = { rowIdx, colIdx }
  mediaType.value = col.media.type || type || null
  isEditingMedia.value = true
  originalMedia.value = JSON.parse(JSON.stringify(col.media))

  if (mediaType.value === 'img') {
    modalMediaPreview.value = col.media.link
    modalMediaTitle.value = col.media.title ?? ''

    const img = new Image()
    img.onload = () => {
      modalMediaSize.value = { w: img.naturalWidth, h: img.naturalHeight }
      showMediaModal.value = true
    }
    img.onerror = () => {
      showMediaModal.value = true
    }
    img.src = buildMediaUrl(modalMediaPreview.value)

  } else if (mediaType.value === 'video') {
    modalMediaPreview.value = col.media?.links?.[0]?.link || ''
    modalMediaTitle.value = col.media.title ?? ''

    const video = document.createElement('video')
    video.preload = 'metadata'
    video.onloadedmetadata = () => {
      modalMediaSize.value = { w: video.videoWidth, h: video.videoHeight }
      showMediaModal.value = true
    }
    video.onerror = () => {
      showMediaModal.value = true
    }
    video.src = buildMediaUrl(modalMediaPreview.value)

  } else if (mediaType.value === 'vr') {
    vrIframeCode.value = col.media.link || ''
    vrWidth.value = col.media.width || 1920
    vrHeight.value = col.media.height || 1080
    showVrModal.value = true

  } else if (mediaType.value === 'curtain') {
    const images = col.media.images || []
    const titles = col.media.titles || []

    curtainImage1.value = images[0]?.replace(/^\/?multimedia\//, 'multimedia/') || ''
    curtainImage2.value = images[1]?.replace(/^\/?multimedia\//, 'multimedia/') || ''
    curtainTitle1.value = titles[0] || ''
    curtainTitle2.value = titles[1] || ''

    const img1 = new Image()
    img1.onload = () => curtainSize1.value = { w: img1.naturalWidth, h: img1.naturalHeight }
    img1.src = buildMediaUrl(curtainImage1.value)

    const img2 = new Image()
    img2.onload = () => curtainSize2.value = { w: img2.naturalWidth, h: img2.naturalHeight }
    img2.src = buildMediaUrl(curtainImage2.value)

    showCurtainModal.value = true
  }
}

const confirmMediaInsert = () => {
  const rowIdx = selectedCell.value?.rowIdx ?? null
  const colIdx = selectedCell.value?.colIdx ?? null

  if (rowIdx === null || !gridRows.value[rowIdx]) {
    ElNotification({ title: 'Ошибка', message: 'Не выбрана строка для вставки', type: 'warning' })
    return
  }

  if (!modalMediaPreview.value || !modalMediaSize.value.w || !modalMediaSize.value.h) {
    ElNotification({
      title: 'Ошибка',
      message: 'Файл не выбран или не удалось определить размеры',
      type: 'warning'
    })
    return
  }

  const fullPath = modalMediaPreview.value
  const ext = fullPath.split('.').pop()?.toLowerCase() || ''
  const timestamp = Date.now()

  let mimeType = 'video/mp4'
  if (ext === 'webm') mimeType = 'video/webm'
  else if (ext === 'mov') mimeType = 'video/quicktime'

  const media =
    mediaType.value === 'img'
      ? {
          type: 'img',
          link: fullPath,
          title: modalMediaTitle.value || '',
          width: modalMediaSize.value.w,
          height: modalMediaSize.value.h,
          __v: timestamp
        }
      : mediaType.value === 'video'
      ? {
          type: 'video',
          poster: '',
          links: [{ link: fullPath, mime: mimeType }],
          title: modalMediaTitle.value || '',
          width: modalMediaSize.value.w,
          height: modalMediaSize.value.h,
          __v: timestamp
        }
      : {}

  if (isEditingMedia.value && typeof colIdx === 'number') {
    const existing = gridRows.value[rowIdx].items[colIdx]
    if (mediaType.value === 'video' && existing.media?.poster) {
      media.poster = existing.media.poster
    }

    gridRows.value[rowIdx].items[colIdx] = {
      ...existing,
      media,
      __v: Date.now()
    }
  } else {
    gridRows.value[rowIdx].items.push({
      media,
      __v: timestamp
    })
  }

  showMediaModal.value = false
  isEditingMedia.value = false
  modalMediaPreview.value = ''
  modalMediaTitle.value = ''
  modalMediaSize.value = { w: 0, h: 0 }
}

const onVrZipChange = (e) => {
  const file = e.target.files?.[0]
  if (file && file.type === 'application/zip') {
    vrZipFile.value = file
  } else {
    vrZipFile.value = null
    ElNotification({ title: 'Ошибка', message: 'Выберите файл .zip', type: 'warning' })
  }
}

const uploadVrZip = async () => {
  if (!vrZipFile.value || !projectId.value) return

  const formData = new FormData()
  formData.append('zip', vrZipFile.value)

  try {
    vrUploading.value = true

    const { data } = await axios.post(`/api/admin/project/${projectId.value}/upload-vr`, formData)
    vrIframeCode.value = `<iframe src="${data.iframe_src}" width="100%" height="600" frameborder="0" allowfullscreen></iframe>`
    detectVrIframeSize()

    ElNotification({ title: 'Успешно', message: 'VR-тур загружен и готов к вставке', type: 'success' })

    vrZipFile.value = null
    vrZipInput.value.value = ''
  } catch (e) {
    const msg = e?.response?.data?.message || 'Ошибка загрузки VR'
    ElNotification({ title: 'Ошибка', message: msg, type: 'error' })
  } finally {
    vrUploading.value = false
  }
}

const cancelMediaInsert = () => {
  if (
    isEditingMedia.value &&
    selectedCell.value?.rowIdx !== undefined &&
    selectedCell.value?.colIdx !== undefined &&
    originalMedia.value
  ) {
    const row = gridRows.value[selectedCell.value.rowIdx]
    const col = row.items[selectedCell.value.colIdx]

    // 🔁 Откат к исходному media
    col.media = JSON.parse(JSON.stringify(originalMedia.value))
  }

  // Сброс состояний
  showMediaModal.value = false
  isEditingMedia.value = false
  modalMediaPreview.value = ''
  modalMediaTitle.value = ''
  modalMediaSize.value = { w: 0, h: 0 }
  originalMedia.value = null
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

  const widthMatch = vrIframeCode.value.match(/width=["']([^"']+)["']/i)
  const heightMatch = vrIframeCode.value.match(/height=["']([^"']+)["']/i)

  let parsedWidth = widthMatch ? widthMatch[1].trim() : null
  let parsedHeight = heightMatch ? heightMatch[1].trim() : null

  // Если указано в %, или не число — ставим дефолт
  if (!parsedWidth || isNaN(parseInt(parsedWidth)) || parsedWidth.includes('%')) {
    parsedWidth = 1920
  } else {
    parsedWidth = parseInt(parsedWidth)
  }

  if (!parsedHeight || isNaN(parseInt(parsedHeight)) || parsedHeight.includes('%')) {
    parsedHeight = 1080
  } else {
    parsedHeight = parseInt(parsedHeight)
  }

  vrWidth.value = parsedWidth
  vrHeight.value = parsedHeight

  ElNotification({
    title: 'Размер определён',
    message: `Ширина: ${vrWidth.value}px, Высота: ${vrHeight.value}px`,
    type: 'success'
  })
}
// =====================
// Curtain
// =====================
const selectCurtainImage = (index) => {
  selectingCurtainIndex.value = index
  showFileManager.value = true
}

const insertCurtain = () => {
  if (!curtainImage1.value || !curtainImage2.value) {
    ElNotification({ title: 'Ошибка', message: 'Выберите оба изображения', type: 'warning' })
    return
  }

  const col = {
    title: '',
    media: {
      type: 'curtain',
      images: [curtainImage1.value, curtainImage2.value],
      titles: [curtainTitle1.value || '', curtainTitle2.value || '']
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
    gridRows.value[pendingCurtainRowIdx.value]
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
    // Подготовка структуры мультимедиа
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

    let res
    const isEdit = isEditing.value

    if (isEdit) {
      res = await axios.put(`/api/admin/project/${projectId.value}`, form.value)
      ElNotification({ title: 'Успешно', message: 'Проект обновлён', type: 'success' })
    } else {
      // 🆕 Создание проекта
      res = await axios.post('/api/admin/project', form.value)

      const folderPath = res.data.folder
      const newProjectId = res.data.project.id

      sessionStorage.setItem('project-folder', folderPath)

      projectId.value = newProjectId
      isEditing.value = true

      await loadProject()

      ElNotification({
        title: `Проект создан: ${res.data.project.title}`,
        message: `Папка создана: ${folderPath}`,
        type: 'success',
        duration: 8000
      })
    }

    // 📦 Перемещённые файлы
    if (res?.data?.moved_files?.length) {
      ElNotification({
        title: 'Файлы перемещены',
        message: `Перемещено файлов: ${res.data.moved_files.length}`,
        type: 'success',
        duration: 8000
      })
    }

    // ⚠️ Ошибки перемещения
    if (res?.data?.errors?.length) {
      res.data.errors.forEach(msg => {
        ElNotification({
          title: 'Ошибка перемещения файла',
          message: msg,
          type: 'warning',
          duration: 8000
        })
      })
    }

    // ✅ Переход на список (если tabsMode выключен — можно остаться)
    if (tabsMode?.value && openTab) {
      openTab({ path: '/project/list', label: 'Список проектов', component: Projects })
    } else {
      router.push('/project')
    }

  } catch (e) {
    const message = e?.response?.data?.message || 'Не удалось сохранить проект'
    const errors = e?.response?.data?.errors || []

    ElNotification({ title: 'Ошибка', message, type: 'error' })

    if (Array.isArray(errors)) {
      errors.forEach(err => {
        ElNotification({
          title: 'Деталь',
          message: err,
          type: 'error',
          duration: 8000
        })
      })
    }
  }
}

const loadProject = async () => {
  const id = route.params.id ?? null
  projectId.value = id

  if (route.name === 'EditProject' && id) {
    isEditing.value = true
    const { data } = await axios.get(`/api/admin/project/${id}`)
    form.value = {
      ...data,
      multimedia_grid: data.multimedia_grid || []
    }

    if (data.folder) {
      sessionStorage.setItem('project-folder', data.folder)
    }
  } else {
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

    const folderFromStorage = sessionStorage.getItem('project-folder')?.replace(/^multimedia\//, '')
    if (!folderFromStorage) {
      console.warn('[❗ Папка проекта не определена — прерываем загрузку]')
      return
    }
  }

  const getFilename = (path) => {
    if (!path || typeof path !== 'string') return ''
    return path.split('/').pop()?.trim() || ''
  }

  const tryPathsInOrder = async (rawPath) => {
    if (!rawPath || typeof rawPath !== 'string') return ''
    const projectFolder = sessionStorage.getItem('project-folder')?.replace(/^multimedia\//, '').replace(/\/$/, '')
    const normalizedPath = rawPath.replace(/^multimedia\//, '').replace(/^\/+/, '')
    const filename = normalizedPath.split('/').pop()

    const possiblePaths = [
      `multimedia/${normalizedPath}`,
      projectFolder ? `multimedia/${projectFolder}/${filename}` : null,
      `multimedia/${filename}`
    ].filter(Boolean)

    for (const fullPath of possiblePaths) {
      if (await fileExists('/' + fullPath)) return fullPath
    }

    return ''
  }

  gridRows.value = await Promise.all(form.value.multimedia_grid.map(async items => {
    const rowItems = await Promise.all(items.map(async item => {
      const alt = item.title ?? item.description ?? getFilename(item.link) ?? ''
      let type = item.type
      let link = item.link || ''

      if (type === 'iframe') {
        type = 'vr'
        if (typeof link === 'string' && link.includes('<iframe')) {
          const match = link.match(/src=["']([^"']+)["']/)
          link = match ? match[1] : ''
        }
      }

      if (type === 'curtain') {
        const images = item.images || []
        const resolvedImages = await Promise.all(images.map(img => tryPathsInOrder(img)))
        return {
          media: {
            type: 'curtain',
            images: resolvedImages,
            titles: item.titles || [],
            debug_path: resolvedImages
          }
        }
      }

      if (type === 'img') {
        const resolvedLink = await tryPathsInOrder(link)
        return {
          media: {
            type: 'img',
            link: resolvedLink,
            title: alt,
            debug_path: resolvedLink
          }
        }
      }

      if (type === 'video') {
        const resolvedPoster = await tryPathsInOrder(item.poster)
        const links = await Promise.all((item.links || []).map(async video => {
          const resolved = await tryPathsInOrder(video.link)
          return { ...video, link: resolved }
        }))
        return {
          media: {
            type: 'video',
            poster: resolvedPoster,
            links,
            title: alt,
            debug_path: {
              poster: resolvedPoster,
              links: links.map(v => v.link)
            }
          }
        }
      }

      if (type === 'vr') {
        return {
          media: {
            type: 'vr',
            link,
            width: item.width || null,
            height: item.height || null,
            title: alt,
            debug_path: link
          }
        }
      }

      // fallback
      const fallback = getFilename(link)
      return {
        media: {
          type,
          link: fallback,
          title: alt,
          debug_path: fallback
        }
      }
    }))

    return {
      id: Date.now() + Math.random(),
      items: rowItems.filter(i => i.media?.link || i.media?.type === 'curtain' || i.media?.type === 'vr')
    }
  }))
}

watch(form, (newVal) => {
  sessionStorage.setItem('create-project-form', JSON.stringify(newVal))
}, { deep: true })

onMounted(() => {
  const isEdit = route.name === 'EditProject' || !!route.params.id || !!route.query.id

  if (isEdit) {
    loadProject()
  } else {
    const wasAlreadyOpened = sessionStorage.getItem('was-create-opened')
    if (wasAlreadyOpened) {
      // Загружаем черновик
      const saved = sessionStorage.getItem('create-project-form')
      if (saved) {
        try {
          const data = JSON.parse(saved)
          form.value = data
          gridRows.value = (data.multimedia_grid || []).map(row =>
            ({
              id: Date.now() + Math.random(),
              items: row.map(col => ({ ...col }))
            })
          )
        } catch (e) {
          resetForm()
        }
      } else {
        resetForm()
      }
    } else {
      resetForm()
    }

    sessionStorage.setItem('was-create-opened', 'true')
  }
})

// При возврате кэшированной вкладки
onActivated(() => {
  const isEdit = route.name === 'EditProject' || !!route.params.id || !!route.query.id

  if (isEdit) {
    const wasAlreadyOpened = sessionStorage.getItem('was-create-opened')
    if (!wasAlreadyOpened) {
      resetForm()
      sessionStorage.setItem('was-create-opened', 'true')
    }
    // если была открыта — ничего не делаем
  }
})


watch(
  () => route.fullPath,
  (newPath, oldPath) => {
    // 📌 Если route.id поменялся — загружаем
    const newId = route.params.id
    const oldId = projectId.value

    if (newId && newId !== oldId) {
      loadProject()
      return
    }

    // Если перешли на создание (без ID)
    if (!newId && oldId) {
      resetForm()
      sessionStorage.removeItem('create-project-form')
      sessionStorage.setItem('was-create-opened', 'true')
    }
  }
)
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

.media-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 6px;
  z-index: 2;
  pointer-events: none;
}

.edit-icon {
  align-self: flex-start;
  background: rgba(0, 0, 0, 0.45);
  color: #fff;
  font-size: 16px;
  border-radius: 6px;
  padding: 4px;
  pointer-events: auto;
  cursor: pointer;
  transition: background 0.2s;
}
.edit-icon:hover {
  background: rgba(0, 0, 0, 0.65);
}

.media-tools {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  background: rgba(0, 0, 0, 0.45);
  padding: 4px 8px;
  border-radius: 6px;
  align-self: flex-end;
  pointer-events: auto;
}
.media-tools .el-icon {
  color: white;
}

.media-name {
  position: absolute;
  left: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  color: #fff;
  font-size: 12px;
  padding: 2px 8px 2px 6px;
  border-radius: 0 9px 0 0;
  display: flex;
  align-items: center;
  gap: 6px;
  pointer-events: none;
  width: 100%;
  justify-content: center;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.item-toolbar {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 7px 0 2px 0;
}

.grid-row-container {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  width: 100%;
}
.row-actions-inline {
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: center;
  justify-content: flex-start;
  padding-left: 12px;
  padding-top: 4px;
}
.drag-row-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
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

.media-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  padding: 0 4px;
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
