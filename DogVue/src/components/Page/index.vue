<template>
  <el-pagination
    :current-page.sync="page"
    background
    :page-size="limit"
    :total="total"
    :page-sizes="[10,20,30, 50, 100]"
    layout="total, sizes, prev, pager, next, jumper"
    @size-change="handleSizeChange"
    @current-change="handleCurrentChange"
  />
</template>

<script>
export default {
  name: 'Page',
  props: {
    page: {
      type: Number,
      default: 1
    },
    limit: {
      type: Number,
      default: 10
    },
    total: {
      type: Number,
      default: 0
    }
  },
  methods: {
    handleSizeChange(val) {
      this.$parent.listQuery.limit = val
      this.$parent.fetchData()
    },
    handleCurrentChange(val) {
      this.$parent.listQuery.page = val
      this.$parent.fetchData()
    }
  }
}
</script>
