<script setup>
import  Editor  from '@tinymce/tinymce-vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
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
      toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | emoticons charmap | link image media table | fullscreen code preview'
    }"
  />
</template>
