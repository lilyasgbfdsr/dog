<template>
  <div class="app-container">
    <el-button type="primary" style="margin-bottom: 10px;" @click="addHandle()">{{ tableData.length > 0 ? '编辑' : '添加' }}</el-button>

    <Count :total="total" />

    <el-table
      v-loading="listLoading"
      v-loading.fullscreen.lock="fullScreenLoading"
      :data="tableData"
      element-loading-text="Loading"
      border
      fit
      highlight-current-row
    >
      <el-table-column align="center" label="序号" width="50">
        <template slot-scope="scope">
          {{ scope.$index + 1 }}
        </template>
      </el-table-column>
      <el-table-column label="预约金额" prop="price" min-width="90%" align="center" />
      <el-table-column width="150" label="添加时间" prop="created_at" align="center" />
      <el-table-column width="150" label="编辑时间" prop="updated_at" align="center" />
    </el-table>

    <Page
      style="padding: 10px 0;"
      :total="total"
      :page="listQuery.page"
      :limit="listQuery.limit"
    />

    <!-- 添加 start -->
    <el-dialog title="设置预约金额" :visible.sync="show" width="60%">
      <el-form ref="addTipFormRef" :model="form" :rules="rules">
        <el-form-item label="预约金额" prop="price">
          <el-input
            v-model="form.price"
            type="number"
            style="width: 40%;"
            placeholder="请填写预约金额"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="show = !show">取 消</el-button>
        <el-button type="primary" @click="addSubmitHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 添加 end -->
  </div>
</template>

<script>
import { configPrice, addConfigPrice } from '@/api/configPrice'
import Count from '@/components/Count'
import Page from '@/components/Page'

export default {
  name: 'Reserve',
  components: {
    Count,
    Page
  },
  data() {
    return {
      /** 全局变量 **/
      fullScreenLoading: false,
      /** 表单变量 */
      show: false,
      form: {
        price: 0
      },
      rules: {
        price: [
          { required: true, message: '预约金额不能为空', trigger: 'blur' }
        ]
      },
      /** 表格变量 */
      /** table表格变量 **/
      total: 0, // 总页数
      listLoading: true, // 列表数据loading加载:true表示显示,false表示隐藏
      listQuery: { // 列表分页数据
        page: 1, // 当前哪一页
        limit: 10
      },
      tableData: [] // 列表初始化数据
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    addHandle() {
      this.show = !this.show
      if (this.tableData.length) {
        this.form.price = this.tableData[0].price
      } else {
        this.form.price = 0
      }
    },
    async fetchData() {
      this.listLoading = true

      const response = await configPrice(this.listQuery)
      this.tableData = response.data
      this.total = response.total

      this.listLoading = false
    },
    reserveUpdate() {
      this.show = !this.show
    },
    addSubmitHandel() {
      this.$refs['addTipFormRef'].validate(valid => {
        if (valid) {
          addConfigPrice(this.form).then(response => {
            this.show = !this.show
            this.$notify.success({
              title: '修改成功',
              message: response,
              duration: 3000
            })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>
