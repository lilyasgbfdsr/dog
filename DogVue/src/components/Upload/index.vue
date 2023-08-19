<template>
  <Fragment>
    <el-upload
      v-if="!isMoreImg"
      accept="image/jpeg, image/png, image/bmp"
      :on-success="onSuccess"
      :data="dataObj"
      :action="host"
      :before-upload="beforeUpload"
      :show-file-list="isMoreImg"
      class="avatar-uploader"
      :on-error="onError"
    >
      <img v-if="imageUrl" :src="imageUrl" class="avatar">
      <i v-else class="el-icon-plus avatar-uploader-icon" />
    </el-upload>
    <el-upload
      v-else
      ref="elUpload"
      :file-list="imgList"
      :action="host"
      :data="dataObj"
      :before-upload="beforeUpload"
      :on-preview="handlePictureCardPreview"
      :on-remove="handleRemove"
      :on-success="onSuccess"
      :on-error="onError"
      list-type="picture-card"
      accept="image/jpeg, image/png, image/bmp"
    >
      <i class="el-icon-plus" />
    </el-upload>
    <div v-if="isMoreImg" id="uplaodImages" v-viewer="{movable: true, url: getUrl}">
      <img v-for="(item, index) in imgList" :key="'img' + index" :src="item.url">
    </div>
  </Fragment>
</template>
<script>
import { getToken } from '@/api/oss'
import { Fragment } from 'vue-fragment'
import { map, filter } from 'underscore'
import 'viewerjs/dist/viewer.css'
import Viewer from 'v-viewer'
import Vue from 'vue'
Vue.use(Viewer)
export default {
  name: 'Upload',
  components: {
    Fragment
  },
  props: {
    value: {
      type: [Array, String],
      default: () => null
    },
    isMoreImg: { // 是否多图上传
      type: Boolean,
      default: () => false
    },
    isEdit: {
      type: Boolean,
      default: () => false
    }
  },
  data() {
    return {
      host: '',
      uploading: false,
      keyUid: {},
      dataObj: {
        OSSAccessKeyId: null,
        policy: null,
        Signature: null,
        key: null,
        success_action_status: 200
      }
    }
  },
  computed: {
    imgList() {
      if (!this.isEdit) {
        return []
      }
      if (this.value && typeof this.value === 'object') {
        return map(this.value, item => {
          console.log(123123)
          return {
            name: item,
            url: process.env.VUE_APP_GETOSSHOST + item
          }
        })
      }
      return []
    },
    imageUrl() {
      if (this.value && typeof this.value === 'string') {
        return process.env.VUE_APP_GETOSSHOST + this.value
      }
      return null
    }
  },
  watch: {
    value(e) {
      if (!e && this.isMoreImg) {
        this.$refs.elUpload.clearFiles()
      }
    }
  },
  methods: {
    getUrl(image) {
      return image.src
    },
    handlePictureCardPreview({ uid, name }) {
      let keyString = this.keyUid[uid]
      if (!keyString) {
        keyString = name
      }
      const viewer = document.getElementById('uplaodImages').$viewer
      viewer.view(this.value.indexOf(keyString))
    },
    handleRemove({ uid, name }) { // 移除上传图片
      let keyString = this.keyUid[uid]
      if (!keyString) {
        keyString = name
      }
      const arr = filter(this.value, item => {
        return item !== keyString
      })
      this.$emit('input', arr)
    },
    onSuccess(str, { uid }) {
      if (!this.isMoreImg) {
        this.$emit('input', this.dataObj.key)
      } else {
        let arr = []
        if (this.value && typeof this.value === 'object') {
          arr = arr.concat(this.value)
        }
        arr.push(this.dataObj.key)
        this.keyUid[uid] = this.dataObj.key // 存储uid与上传图像关系 用于删除使用
        this.$emit('input', arr)
      }
      this.uploading = false
    },
    onError() {
      this.uploading = false
    },
    beforeUpload(file) {
      if (this.uploading) {
        this.$message.error('请等待当前上传完成')
        return
      }
      const isLt2M = file.size / 1024 / 1024 < 5
      // 判断上传大小
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过 5MB!')
        return
      }
      this.uploading = true
      return new Promise((resolve, reject) => {
        getToken({ prefix: 'goods' }).then(res => {
          this.dataObj.key = res.dir
          this.dataObj.policy = res.policy
          this.dataObj.Signature = res.signature
          this.dataObj.OSSAccessKeyId = res.accessid
          this.host = res.host
          resolve(true)
        }).catch(() => {
          this.uploading = false
          reject(false)
        })
      })
    }
  }
}
</script>
<style>
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 148px;
  height: 148px;
  line-height: 148px;
  text-align: center;
}
.avatar {
  width: 148px;
  height: 148px;
  display: block;
}
#uplaodImages {
  display: none;
}
</style>
