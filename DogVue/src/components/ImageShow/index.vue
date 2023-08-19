<template>
  <div class="ImgViewer">
    <viewer ref="viewer" :options="options" :images="images" class="viewer" @inited="inited">
      <template slot-scope="scope">
        <el-row v-if="alignCenter" :gutter="20" type="flex" justify="center">
          <el-col v-for="src in scope.images" :key="src" :span="colSpan">
            <el-image :style="styles" :src="cndUrl + src" class="image" />
          </el-col>
        </el-row>
        <el-row v-else :gutter="20">
          <el-col v-for="src in scope.images" :key="src" :span="colSpan">
            <el-image :style="styles" :src="cndUrl + src" class="image" />
          </el-col>
        </el-row>
      </template>
    </viewer>
  </div>
</template>
<script>
import 'viewerjs/dist/viewer.css'
import Viewer from 'v-viewer/src/component.vue'
export default {
  name: 'ImageShow',
  components: {
    Viewer
  },
  props: {
    images: {
      type: [Array, Object],
      default: () => []
    },
    colSpan: {
      type: [Number],
      default: () => 3
    },
    alignCenter: {
      type: Boolean,
      default: () => false
    },
    styles: {
      type: String,
      default: () => ''
    }
  },
  data() {
    return {
      cndUrl: process.env.VUE_APP_GETOSSHOST,
      options: {
        zIndex: 20000 // 解决与饿了么对话框高度冲突的问题
      }
    }
  },
  methods: {
    inited(viewer) {
      this.$viewer = viewer
    }
    // show() {
    //   this.$viewer.show()
    // }
  }
}
</script>
<style lang="scss">
.ImgViewer {
  .image {
    display: block;
    width: 100%;
    height: 150px;
    cursor: zoom-in;
  }
}
</style>
