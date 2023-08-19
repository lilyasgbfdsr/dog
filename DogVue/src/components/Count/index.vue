<template>
  <div style="color:rgb(44,162,252);margin-bottom: 10px;font-size: 14px">
    数据总计: {{ total }}(条)
  </div>
</template>

<script>
export default {
  name: 'Count',
  props: {
    total: {
      type: Number,
      default: () => 0
    }
  }
}
</script>
