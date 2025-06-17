<template>
  <div class="project-create-page">
    <h2 class="page-title">{{ isEditing ? 'Редактирование проекта' : 'Создание проекта' }}</h2>

    <el-form :model="form" label-position="top" @submit.prevent="submit">
      <el-form-item label="Название проекта">
        <el-input v-model="form.title" />
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
                      <div
                        class="media-thumb"
                        @click="openPreview(col)"
                        @mousedown="onMouseDown"
                        @mouseup="onMouseUp"
                      >
                        <template v-if="col.media?.type === 'img'">
                          <img
                            :src="'/multimedia/' + col.media.link"
                            :alt="col.title || 'Изображение'"
                            class="grid-img"
                          />
                        </template>

                        <template v-else-if="col.media?.type === 'video'">
                          <video
                            class="grid-video"
                            autoplay
                            muted
                            loop
                            playsinline
                            preload="metadata"
                            :poster="col.media.poster ? '/multimedia/' + col.media.poster : undefined"
                          >
                            <source
                              v-for="(link, i) in col.media.links || []"
                              :key="i"
                              :src="'/multimedia/' + link.link"
                              :type="link.mime || 'video/mp4'"
                            />
                          </video>
                        </template>

                        <div v-else class="empty-media">
                          <el-icon><Picture /></el-icon>
                        </div>

                        <span class="edit-icon" @click.stop="openFileManager(rowIdx, colIdx)">
                          <el-icon><Edit /></el-icon>
                        </span>
                      </div>

                      <span class="media-name">
                        <el-icon v-if="col.media?.type === 'video'"><VideoCamera /></el-icon>
                        <el-icon v-else-if="col.media?.type === 'img'"><Picture /></el-icon>
                      </span>

                      <div class="item-toolbar">
                        <el-button size="small" type="danger" circle @click="removeCol(rowIdx, colIdx)">
                          <el-icon><Delete /></el-icon>
                        </el-button>
                      </div>
                    </div>
                  </template>
                </draggable>

                <el-button size="small" circle type="primary" class="add-col-btn" @click="addCol(rowIdx)">
                  <el-icon><Plus /></el-icon>
                </el-button>
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </el-form>

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

        <div v-if="previewItem?.type === 'img' && previewItem.link">
          <img
            :src="`/multimedia/${previewItem.link}`"
            :alt="previewItem.title || 'Изображение'"
            class="preview-img"
          />
        </div>

        <div v-else-if="previewItem?.type === 'video' && previewItem.links?.length">
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

        <!-- Добавлено поле input только в модалке -->
        <el-input
          v-if="previewItem?.type === 'img'"
          v-model="previewItem.title"
          placeholder="Название / alt"
          style="margin-top: 16px; width: 100%; text-align: center;"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import draggable from 'vuedraggable'
import TinyEditor from '@/components/admin/TinyEditor.vue'
import Projects from './Projects.vue'
import { ElNotification } from 'element-plus'
import { Plus, Check, Delete, Edit, Picture, Menu, VideoCamera } from '@element-plus/icons-vue'

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
const selectedCell = ref({ rowIdx: 0, colIdx: 0 })
const showFileManager = ref(false)
const previewVisible = ref(false)
const previewItem = ref({})

const addRow = () => gridRows.value.push({ id: Date.now() + Math.random(), items: [] })
const removeRow = idx => gridRows.value.splice(idx, 1)
const addCol = rowIdx => gridRows.value[rowIdx].items.push({ media: {}, title: '' })
const removeCol = (r, c) => gridRows.value[r].items.splice(c, 1)

const openFileManager = (r, c) => {
  selectedCell.value = { rowIdx: r, colIdx: c }
  showFileManager.value = true
}

const handleFileSelect = (items) => {
  if (!items.length) return
  const file = items[0]
  const row = gridRows.value[selectedCell.value.rowIdx]
  const col = row.items[selectedCell.value.colIdx]

  const rawPath = file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  const ext = rawPath.split('.').pop()?.toLowerCase()
  const isVideo = file.mime?.includes('video') || ['mp4', 'webm', 'mov'].includes(ext)
  const isImage = file.mime?.includes('image') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext)

  if (isVideo) {
    col.media = {
      type: 'video',
      poster: '',
      links: [{ link: rawPath, mime: file.mime || 'video/mp4' }]
    }
  } else if (isImage) {
    col.media = { type: 'img', link: rawPath }
  } else {
    col.media = {}
  }

  showFileManager.value = false
}

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

const submit = async () => {
  try {
    form.value.multimedia_grid = gridRows.value.map(row =>
      row.items.map(col => ({ ...(col.media || {}), title: col.title || '' }))
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
        return {
          title: alt,
          media: {
            type: item.type,
            link: item.link || '',
            poster: item.poster || '',
            links: item.links || [],
            description: item.description || '',
            width: item.width || null,
            height: item.height || null
          }
        }
      })
    }))
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
  z-index: 1000;
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
</style>
