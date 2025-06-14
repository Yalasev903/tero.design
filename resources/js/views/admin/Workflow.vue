<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import draggable from 'vuedraggable'
import TinyEditor from '@/components/admin/TinyEditor.vue'
import SeoServices from '@/components/admin/SeoServices.vue'
import { ElNotification } from 'element-plus'
import { Edit, EditPen, Delete, Rank } from '@element-plus/icons-vue'

// Workflow модель
const model = ref({
  col_poster: '',
  col_poster_alt: '',
  col_video: '',
  col_description: ''
})

const posterDimensions = ref('')
const videoDimensions = ref('')
const expanded = ref(null)
const showFileManager = ref(false)
const currentType = ref(null)

// FAQ
const faqList = ref([])
const showFaqModal = ref(false)
const isEditingFaq = ref(false)
const faqForm = ref({ col_id: null, col_question: '', col_answer: '' })

const posterSrc = computed(() => model.value.col_poster ? `/multimedia/${model.value.col_poster}` : '')
const videoSrc = computed(() => model.value.col_video ? `/multimedia/${model.value.col_video}` : '')

// --- Файл-менеджер ---
const openManager = (type) => {
  currentType.value = type
  showFileManager.value = true
}

const handleSelect = (items) => {
  if (!items.length) return
  const path = items[0].path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  model.value[currentType.value] = path
  showFileManager.value = false
  nextTick(() => {
    if (currentType.value === 'col_poster') updatePosterDimensions()
    if (currentType.value === 'col_video') updateVideoDimensions()
  })
}

// --- Размеры ---
const updatePosterDimensions = () => {
  const img = new Image()
  img.onload = () => {
    posterDimensions.value = `${img.naturalWidth}x${img.naturalHeight}px`
  }
  img.src = posterSrc.value
}
const updateVideoDimensions = () => {
  const video = document.querySelector('.js-video')
  if (video) {
    video.onloadedmetadata = () => {
      videoDimensions.value = `${video.videoWidth}x${video.videoHeight}px`
    }
  }
}

// --- Загрузка ---
const loadWorkflow = async () => {
  const { data } = await axios.get('/api/admin/workflow')
  model.value = data
  await nextTick()
  updatePosterDimensions()
  updateVideoDimensions()
}
const loadFaq = async () => {
  const { data } = await axios.get('/api/admin/faq')
  faqList.value = data
}

// --- Сохранение ---
const saveWorkflow = async () => {
  try {
    await axios.post('/api/admin/workflow', model.value)
    ElNotification({ title: 'Успешно', message: 'Сохранено', type: 'success' })
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Не удалось сохранить', type: 'error' })
  }
}
const saveFaq = async () => {
  try {
    await axios.post('/api/admin/faq/save-all', faqList.value)
    ElNotification({ title: 'Успешно', message: 'FAQ сохранены', type: 'success' })
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Не удалось сохранить FAQ', type: 'error' })
  }
}
const deleteFaq = async (id) => {
  try {
    await axios.delete(`/api/admin/faq/${id}`)
    faqList.value = faqList.value.filter(f => f.col_id !== id)
    ElNotification({ title: 'Удалено', message: 'Вопрос удалён', type: 'success' })
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Ошибка удаления', type: 'error' })
  }
}

// --- FAQ Модалка ---
const openFaqModal = (faq = null) => {
  if (faq) {
    isEditingFaq.value = true
    faqForm.value = { ...faq }
  } else {
    isEditingFaq.value = false
    faqForm.value = { col_id: null, col_question: '', col_answer: '' }
  }
  showFaqModal.value = true
}
const closeFaqModal = () => showFaqModal.value = false
const submitFaq = async () => {
  const { col_question, col_answer } = faqForm.value

  const cleanQuestion = col_question.trim()
  const cleanAnswer = col_answer.trim().replace(/<(.|\n)*?>/g, '').trim()

  if (!cleanQuestion || !cleanAnswer) {
    ElNotification({ title: 'Ошибка', message: 'Заполните вопрос и ответ', type: 'warning' })
    return
  }

  if (isEditingFaq.value) {
    const index = faqList.value.findIndex(f => f.col_id === faqForm.value.col_id)
    if (index !== -1) {
      faqList.value[index].col_question = cleanQuestion
      faqList.value[index].col_answer = faqForm.value.col_answer
      ElNotification({ title: 'Изменено', message: 'Вопрос обновлён', type: 'success' })
    }
  } else {
    try {
      const { data } = await axios.post('/api/admin/faq', {
        col_question: cleanQuestion,
        col_answer: faqForm.value.col_answer,
        position: faqList.value.length
      })

      faqList.value.push(data)

      ElNotification({ title: 'Добавлено', message: 'Вопрос добавлен', type: 'success' })
    } catch {
      ElNotification({ title: 'Ошибка', message: 'Не удалось добавить', type: 'error' })
    }
  }

  showFaqModal.value = false
}

const toggleExpand = (type) => {
  expanded.value = expanded.value === type ? null : type
}

onMounted(() => {
  axios.get('/sanctum/csrf-cookie')
  loadWorkflow()
  loadFaq()
})
</script>

<template>
  <div class="page-layout">
    <div class="left-column">
      <SeoServices title="SEO для Workflow" pageName="workflow" />

      <el-card style="margin-top: 30px;">
        <template #header>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">FAQ</h3>
            <el-button type="primary" @click="openFaqModal()">
              <el-icon><Edit /></el-icon> Добавить
            </el-button>
          </div>
        </template>

        <draggable
          v-model="faqList"
          item-key="col_id"
          :animation="200"
          :ghost-class="'dragging'"
          :cancel="'.faq-actions, .faq-actions *'"
        >
          <template #item="{ element }">
            <div class="faq-row">
              <span class="drag-handle" title="Перетащить"><el-icon><Rank /></el-icon></span>
              <div class="faq-id">ID: {{ element.col_id }}</div>
              <div class="faq-question">{{ element.col_question }}</div>
              <div class="faq-actions">
                <el-button size="small" type="primary" @click="openFaqModal(element)">
                  <el-icon><EditPen /></el-icon>
                </el-button>
                <el-button size="small" type="danger" @click="deleteFaq(element.col_id)">
                  <el-icon><Delete /></el-icon>
                </el-button>
              </div>
            </div>
          </template>
        </draggable>

        <div class="save-button">
          <el-button type="success" @click="saveFaq">Сохранить</el-button>
        </div>
      </el-card>
    </div>

    <div class="right-column">
      <h3>🎬 Видео Workflow</h3>

      <div class="media-box" :class="{ expanded: expanded === 'poster' }" @click="toggleExpand('poster')">
        <div class="media-label">Постер ({{ posterDimensions }})</div>
        <img :src="posterSrc" :alt="model.col_poster_alt || 'Постер'" />
        <div class="overlay"><span class="upload-icon" @click.stop="openManager('col_poster')">📤 Заменить</span></div>
      </div>
      <el-input v-model="model.col_poster_alt" placeholder="Alt постера" style="margin-top: 10px" />
      <p class="filename">{{ model.col_poster }}</p>

      <div class="media-box" :class="{ expanded: expanded === 'video' }" @click="toggleExpand('video')">
        <div class="media-label">Видео ({{ videoDimensions }})</div>
        <video class="js-video" :key="videoSrc" autoplay loop muted playsinline>
          <source :src="videoSrc" :type="'video/' + videoSrc.split('.').pop()" />
        </video>
        <div class="overlay"><span class="upload-icon" @click.stop="openManager('col_video')">📤 Заменить</span></div>
      </div>
      <p class="filename">{{ model.col_video }}</p>

      <div class="editor-wrap">
        <h4>Описание</h4>
        <TinyEditor v-model="model.col_description" />
      </div>

      <div class="save-button">
        <el-button type="success" @click="saveWorkflow">Сохранить</el-button>
      </div>
    </div>

    <!-- VueFinder -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal" @click.self="showFileManager = false">
        <div class="finder-container">
          <vue-finder
            :request="{ baseUrl: '/api/vuefinder', adapter: 'local', xsrfHeaderName: 'X-XSRF-TOKEN' }"
            @select="handleSelect"
          />
          <button class="close-btn" @click="showFileManager = false">✖</button>
        </div>
      </div>
    </teleport>

    <!-- Модалка FAQ -->
    <el-dialog
    v-model="showFaqModal"
    :title="isEditingFaq ? 'Редактировать вопрос' : 'Добавить вопрос'"
    width="600px"
    destroy-on-close
    >
    <el-form :model="faqForm" label-position="top">
        <el-form-item label="Вопрос">
        <el-input v-model="faqForm.col_question" type="textarea" placeholder="Введите вопрос" />
        </el-form-item>
        <el-form-item label="Ответ">
        <TinyEditor v-model="faqForm.col_answer" />
        </el-form-item>
    </el-form>
    <template #footer>
        <el-button @click="closeFaqModal">Отмена</el-button>
        <el-button type="primary" @click="submitFaq">
        {{ isEditingFaq ? 'Сохранить' : 'Добавить' }}
        </el-button>
    </template>
    </el-dialog>
  </div>
</template>

<style scoped>
.page-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.left-column {
  width: 50%;
}
.right-column {
  width: 50%;
  background: white;
  padding: 20px;
  border-radius: 8px;
}
.faq-id {
  font-weight: bold;
  font-size: 13px;
  color: #999;
  margin-right: 15px;
  width: 50px;
}
.faq-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  border-bottom: 1px solid #eaeaea;
  background: #fff;
  transition: background 0.2s ease;
}
.faq-row:hover {
  background: #f9f9f9;
}
.drag-handle {
  cursor: move;
  display: flex;
  align-items: center;
  margin-right: 12px;
  color: #999;
}
.faq-question {
  flex: 1;
  font-size: 14px;
}
.faq-actions {
  display: flex;
  gap: 6px;
}
.dragging {
  background-color: #eef;
  opacity: 0.9;
}
.media-box {
  position: relative;
  margin-top: 20px;
  cursor: pointer;
  border: 2px dashed #ccc;
  border-radius: 6px;
  overflow: hidden;
  width: 100%;
  max-width: 360px;
  aspect-ratio: 16 / 9;
  transition: all 0.4s ease;
  background: #f9f9f9;
}
.media-box.expanded {
  max-width: 720px;
  aspect-ratio: 16 / 9;
}
.media-box img,
.media-box video {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 6px;
  display: block;
  background: #000;
}
.media-label {
  position: absolute;
  top: 5px;
  left: 8px;
  background: rgba(0,0,0,0.6);
  color: #fff;
  padding: 2px 8px;
  font-size: 13px;
  z-index: 2;
  border-radius: 4px;
}
.overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.media-box:hover .overlay {
  opacity: 1;
}
.upload-icon {
  font-size: 14px;
  background: rgba(0, 0, 0, 0.7);
  padding: 6px 12px;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
}
.filename {
  font-size: 13px;
  color: #555;
  margin: 5px 0 15px;
  word-break: break-word;
}
.editor-wrap {
  margin-top: 30px;
}
.save-button {
  margin-top: 20px;
  text-align: right;
}
.finder-modal {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.finder-container {
  width: 90%;
  height: 90%;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #e00;
  color: #fff;
  padding: 5px 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  z-index: 10;
}
</style>
