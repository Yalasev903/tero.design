<script setup>
import { computed, inject } from 'vue'
import  Editor  from '@tinymce/tinymce-vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])

const inputSize = inject('inputSize', 'default')

const fontSize = computed(() => {
  switch (inputSize.value) {
    case 'mini': return '11px'
    case 'small': return '13px'
    case 'medium': return '15px'
    default: return '14px'
  }
})
</script>

<template>
  <Editor
    :model-value="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    api-key="zz5yzfkvutel7xxsey78ithzogwrudzczlqmwlyft73cupew"
    :init="{
      height: 320,
      menubar: true,
      branding: false,
      toolbar_mode: 'sliding',
      plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount emoticons',
      toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | emoticons charmap | link image media table | fullscreen code preview',
      content_style: `body { font-size: ${fontSize}; }`
    }"
  />
</template>
