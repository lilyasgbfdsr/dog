<template>
  <quill-editor
    ref="myQuillEditor"
    :content="content"
    :options="editorOption"
    @blur="onEditorBlur($event)"
    @focus="onEditorFocus($event)"
    @change="onEditorChange($event)"
  />
</template>

<script>
import Vue from 'vue'
import VueQuillEditor from 'vue-quill-editor'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(VueQuillEditor)

const toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'], // toggled buttons:加粗，斜体，下划线，删除线
  // ['blockquote', 'code-block'], // 引用，代码块
  // [{ 'header': 1 }, { 'header': 2 }], // custom button values:标题，键值对的形式；1、2表示字体大小
  // [{ 'list': 'ordered' }, { 'list': 'bullet' }], // 列表
  // [{ 'script': 'sub' }, { 'script': 'super' }], // superscript/subscript:上下标
  // [{ 'indent': '-1' }, { 'indent': '+1' }], // outdent/indent:缩进
  // [{ 'direction': 'rtl' }], // text direction:文本方向
  // [{ 'size': ['small', false, 'large', 'huge'] }], // custom dropdown:字体大小
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }], // 几级标题
  [{ 'color': [] }, { 'background': [] }], // dropdown with defaults from theme:字体颜色，字体背景颜色
  // [{ 'font': [] }], // 字体
  [{ 'align': [] }], // 对齐方式
  ['clean'] // remove formatting button:清除字体样式
  // ['image','video']                                //上传图片、上传视频
]

export default {
  name: 'Quill',
  props: {
    content: {
      type: String,
      default: () => ''
    }
  },
  data() {
    return {
      editorOption: {
        modules: {
          toolbar: toolbarOptions
        }
      }
    }
  },
  methods: {
    onEditorBlur() { // 失去焦点事件
    },
    onEditorFocus() { // 获得焦点事件
    },
    onEditorChange({ editor, html, text }) { // 内容改变事件
      this.$emit('getContent', html)
    }
  }
}
</script>
