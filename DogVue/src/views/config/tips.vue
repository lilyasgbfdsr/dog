<template>
  <div class="app-container">
    <el-button type="primary" style="margin-bottom: 10px;" @click="addHandle()">添加</el-button>

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
      <el-table-column label="预约文案" prop="tip" min-width="90%" align="left" />
      <el-table-column label="排序" width="110" prop="sort" align="center" />
      <el-table-column width="150" label="添加时间" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" min-width="40">
        <template slot-scope="scope">
          <el-button
            size="mini"
            type="danger"
            icon="el-icon-delete"
            @click="delOperate(scope.row.id)"
          />
        </template>
      </el-table-column>
    </el-table>

    <Page
      style="padding: 10px 0;"
      :total="total"
      :page="listQuery.page"
      :limit="listQuery.limit"
    />

    <!-- 添加 start -->
    <el-dialog title="添加预约文案" :visible.sync="show" width="60%">
      <el-form ref="addTipFormRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="预约场次" prop="tip">
          <el-input
            v-model="form.tip"
            type="textarea"
            style="width: 80%;"
            placeholder="选择场次"
          />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input
            v-model="form.sort"
            type="number"
            style="width: 40%;"
            placeholder="请填写排序"
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
import { tips, addTip, deleteTip } from '@/api/tips'
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
        tip: '',
        sort: 0
      },
      rules: {
        tip: [
          { required: true, message: '预约文案不能为空', trigger: 'blur' }
        ],
        sort: [
          { required: true, message: '排序不能为空', trigger: 'blur' }
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
    },
    async fetchData() {
      this.listLoading = true

      const response = await tips(this.listQuery)
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
          addTip(this.form).then(response => {
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
    },
    delOperate(id) {
      const text = `是否删除`
      this.$confirm(text, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        // 开启加载层
        this.listLoading = true
        deleteTip({ id }).then(response => {
          // 关闭加载层
          this.listLoading = false
          this.$notify.success({
            title: '信息提示',
            message: response,
            duration: 3000
          })
          this.fetchData()
        }).catch(() => {
          // 关闭加载层
          this.listLoading = false
        })
      }).catch(() => {
        this.$notify.info({
          title: '信息提示',
          message: '已取消此操作',
          duration: 3000
        })
      })
    }
  }
}
</script>
