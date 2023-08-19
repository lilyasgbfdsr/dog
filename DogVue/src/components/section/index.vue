<template>
  <div>
    <el-col v-for="(item, index) in list" :key="index" :span="12">
      <div class="widget-panel widget-style-2" :class="item.class">
        <i class="ion-eye">
          <img :src="qiNiuPath + prefix + item.field + '.svg'" class="images">
        </i>
        <h2 class="m-0 counter">{{ item.value }}</h2>
        <div>{{ item.title }}</div>
      </div>
    </el-col>
  </div>
</template>

<script>
export default {
  name: 'Section',
  props: {
    list: {
      type: Object,
      default: () => {}
    }
  }
}
</script>

<style>

</style>
