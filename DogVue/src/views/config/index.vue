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
      <el-table-column label="预约时间段" prop="tip" min-width="90%" align="center">
        <template slot-scope="scope">
          <el-tag type="info">{{ scope.row.start_time }} - {{ scope.row.end_time }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="排序字段" prop="sort" min-width="60" align="center" />
      <el-table-column label="开始时间" prop="created_at" min-width="100" align="center" />
      <el-table-column label="结束时间" prop="updated_at" min-width="100" align="center" />
      <el-table-column align="center" label="操作" min-width="40">
        <template slot-scope="scope">
          <el-button
            size="mini"
            type="primary"
            icon="el-icon-edit"
            @click="editOperate(scope.row)"
          />
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
    <el-dialog title="添加预约时间段" :visible.sync="show" width="40%">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="开始时间段" prop="start_time">
          <el-time-picker
            v-model="form.start_time"
            format="HH:mm"
            value-format="HH:mm"
            placeholder="开始时间段"
          />
        </el-form-item>
        <el-form-item label="结束时间段" prop="end_time">
          <el-time-picker
            v-model="form.end_time"
            format="HH:mm"
            value-format="HH:mm"
            placeholder="结束时间段"
          />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input
            v-model="form.sort"
            type="number"
            style="width: 220px;"
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

    <!-- 编辑 start -->
    <el-dialog title="编辑预约时间段" :visible.sync="editShow" width="40%">
      <el-form ref="editFormRef" :model="editForm" :rules="editRules" label-width="100px">
        <el-form-item label="开始时间段" prop="start_time">
          <el-time-picker
            v-model="editForm.start_time"
            format="HH:mm"
            value-format="HH:mm"
            placeholder="开始时间段"
          />
        </el-form-item>
        <el-form-item label="结束时间段" prop="end_time">
          <el-time-picker
            v-model="editForm.end_time"
            format="HH:mm"
            value-format="HH:mm"
            placeholder="结束时间段"
          />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input
            v-model="editForm.sort"
            type="number"
            style="width: 220px;"
            placeholder="请填写排序"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editShow = !editShow">取 消</el-button>
        <el-button type="primary" @click="editSubmitHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 编辑 end -->
  </div>
</template>

<script>
import { dateTime, addDateTime, editDateTime, deleteDateTime } from '@/api/dateTimes'
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
        start_time: undefined,
        end_time: undefined,
        sort: 0
      },
      rules: {
        start_time: [
          { required: true, message: '预约开始时间段', trigger: 'blur' }
        ],
        end_time: [
          { required: true, message: '预约结束时间段', trigger: 'blur' }
        ],
        sort: [
          { required: true, message: '排序不能为空', trigger: 'blur' }
        ]
      },
      editShow: false,
      editForm: {
        id: 0,
        start_time: undefined,
        end_time: undefined,
        sort: 0
      },
      editRules: {
        start_time: [
          { required: true, message: '预约开始时间段', trigger: 'blur' }
        ],
        end_time: [
          { required: true, message: '预约结束时间段', trigger: 'blur' }
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
      this.$nextTick(() => {
        this.$refs['formRef'].resetFields()
        this.$refs['formRef'].clearValidate()
      })
    },
    async fetchData() {
      this.listLoading = true

      const response = await dateTime(this.listQuery)
      this.tableData = response.data
      this.total = response.total

      this.listLoading = false
    },
    addSubmitHandel() {
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          addDateTime(this.form).then(response => {
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
    editOperate(row) {
      this.editShow = !this.show
      this.editForm.id = row.id
      this.editForm.sort = row.sort
      this.editForm.start_time = row.start_time
      this.editForm.end_time = row.end_time
      this.$nextTick(() => {
        this.$refs['editFormRef'].clearValidate()
      })
    },
    editSubmitHandel() {
      this.$refs['editFormRef'].validate(valid => {
        if (valid) {
          editDateTime(this.editForm).then(response => {
            this.editShow = !this.editShow
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
        deleteDateTime({ id }).then(response => {
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
