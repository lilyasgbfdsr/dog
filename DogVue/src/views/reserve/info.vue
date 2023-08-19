<template>
  <div class="app-container">
    <section style="padding-bottom:10px;">
      <el-row :gutter="24">
        <el-col :span="20">
          <el-date-picker
            v-model="listQuery.where.searchDate"
            type="daterange"
            align="right"
            unlink-panels
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="yyyy-MM-dd"
            value-format="yyyy-MM-dd"
            style="margin-right:10px;width:280px;"
          />
          <el-select
            v-model="listQuery.where.hour"
            class="filter-item"
            style="margin-right:10px;width:130px;"
            filterable
            clearable
            placeholder="预约时段"
          >
            <el-option
              v-for="item in timeArray"
              :key="item.id"
              :label="item.start_time + ' - ' + item.end_time"
              :value="item.id"
            />
          </el-select>
          <el-button type="primary" @click="handleSearch()">搜索</el-button>
          <el-button type="danger" @click="reserveUpdate()">添加</el-button>
        </el-col>
      </el-row>
    </section>

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
      <el-table-column min-width="220" label="预约日期" align="center">
        <template slot-scope="scope">
          <el-tag>{{ getTime(scope.row.int_time) || '--' }} {{ '星期' + scope.row.week }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column min-width="220" label="修改类型" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type == 1">整天不能预约</el-tag>
          <el-tag v-if="scope.row.type == 2" type="warning">时间段不能预约</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="预约场次" min-width="120" align="center">
        <template slot-scope="scope">
          <span v-for="item in timeArray" :key="item.id">
            <span v-if="item.id == scope.row.hour">
              <el-tag type="warning">{{ item.start_time }} - {{ item.end_time }}</el-tag>
            </span>
          </span>
        </template>
      </el-table-column>
      <el-table-column width="150" label="添加时间" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ new Date(scope.row.add_time*1000).toLocaleString('chinese', { hour12: false }) }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" min-width="100">
        <template slot-scope="scope">
          <el-button
            size="mini"
            type="danger"
            icon="el-icon-delete"
            @click="reserveOperate(scope.row.id)"
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

    <!-- 编辑客户名称&电话区域 start -->
    <el-dialog title="新增" :visible.sync="show" width="70%">
      <el-form ref="editCusNaData" :model="form" :rules="rules">
        <el-form-item label="预约日期">
          <!-- <el-radio-group v-model="form.date">
            <template v-for="(item, index) in days">
              <el-radio :key="index" :label="item" style="margin: 10px" />
            </template>
          </el-radio-group> -->
          <el-date-picker
            v-model="form.date"
            format="yyyy-MM-dd"
            value-format="yyyy-MM-dd"
            type="date"
            placeholder="选择日期"
          />
        </el-form-item>
        <el-form-item label="预约类型" prop="phone">
          <el-select
            v-model="form.type"
            class="filter-item"
            style="margin-right:10px;width:230px;"
            filterable
            clearable
            placeholder="选择场次"
          >
            <el-option
              v-for="item in typeArray"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item v-if="form.type == 2" label="预约场次" prop="phone">
          <el-select
            v-model="form.hour"
            class="filter-item"
            style="margin-right:10px;width:230px;"
            filterable
            clearable
            placeholder="选择场次"
          >
            <el-option
              v-for="item in timeArray"
              :key="item.id"
              :label="item.start_time + ' - ' + item.end_time"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="show = !show">取 消</el-button>
        <el-button type="primary" @click="updateSumit">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 编辑客户名称&电话区域 end -->
  </div>
</template>

<script>
import { check, infoList, infoStore, infoDelete } from '@/api/reserve'
import Count from '@/components/Count'
import Page from '@/components/Page'
import { getTime } from '@/utils/date'

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
      /** table表格变量 **/
      total: 0, // 总页数
      listLoading: true, // 列表数据loading加载:true表示显示,false表示隐藏
      listQuery: { // 列表分页数据
        page: 1, // 当前哪一页
        limit: 100, // 每页默认显示10条
        where: { // 查询条件
          searchDate: [],
          hour: ''
        }
      },
      tableData: [], // 列表初始化数据
      getTime,
      totayCount: 0,
      // timeArray: [
      //   { id: 1, name: '12:00 - 12:50', value: 0, count: 0 },
      //   { id: 2, name: '13:05 - 13:55', value: 0, count: 0 },
      //   { id: 3, name: '14:15 - 15:05', value: 0, count: 0 },
      //   { id: 4, name: '15:25 - 16:15', value: 0, count: 0 },
      //   { id: 5, name: '16:35 - 17:25', value: 0, count: 0 },
      //   { id: 6, name: '17:45 - 18:35', value: 0, count: 0 },
      //   { id: 7, name: '18:50 - 19:40', value: 0, count: 0 }
      // ],
      timeArray: [],
      statusArray: [
        { id: 1, name: '是' },
        { id: 2, name: '否' }
      ],
      typeArray: [
        { id: 1, name: '整天不能预约' },
        { id: 2, name: '时间段不能预约' }
      ],
      days: [],
      hours: [],
      show: false,
      form: {
        date: '',
        type: 2,
        hour: ''
      },
      rules: {
        date: [
          { required: true, message: '预约日期不能为空', trigger: 'blur' }
        ],
        type: [
          { required: true, message: '预约类型不能为空', trigger: 'blur' }
        ]
      },
      /** 时间 **/
      pickerOptions2: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      }
    }
  },
  created() {
    // const date = new Date()
    // const today = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
    // this.listQuery.where.searchDate = [today, today]
    this.fetchData()
  },
  methods: {
    handleSearch() {
      this.fetchData()
    },
    fetchData() {
      this.listLoading = true
      infoList(this.listQuery).then(response => {
        this.tableData = response.data
        this.total = response.total
        this.days = response.date
        this.timeArray = response.time_array
        this.listLoading = false
      })
    },
    reserveUpdate() {
      this.show = !this.show
      this.form.date = ''
      this.form.type = 2
      this.form.hour = ''
    },
    updateSumit() {
      console.log(this.form)
      this.$refs['editCusNaData'].validate(valid => {
        console.log(valid)
        if (valid) {
          if (this.form.type === 2 && !this.form.hour) {
            this.$notify.success({
              title: '提示',
              message: '请选择时间段',
              duration: 3000
            })
            return
          }
          infoStore(this.form).then(response => {
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
    reserveOperate(id) {
      const text = `是否删除该预约配置`
      this.$confirm(text, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        // 开启加载层
        this.listLoading = true
        infoDelete({ id }).then(response => {
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
    },
    reserveEditHander(id) {
      this.show = !this.show
      this.form.reserve_id = id
      this.form.date = ''
      this.form.hour = ''
      this.hours = []
    },
    dateChangeHandler(value) {
      this.hours = []
      this.form.hour = ''
      check({ date: value }).then(response => {
        const hoursArr = []
        response.map((item, index) => {
          const text = (item.data_type === 1) ? '(约满)' : (
            (item.desk === 1 || item.count <= 4) ? `(剩余${item.count}座位)` : '未约满'
          )
          const name = `${this.timeArray[index].name}${text}`
          hoursArr.push({
            id: item.play,
            name,
            data_type: item.data_type
          })
        })
        this.hours = hoursArr
      })
    }
  }
}
</script>
