<template>
  <div class="app-container">
    <el-card class="box-card" style="margin-bottom:10px;">
      <el-badge :value="todayCount" class="item" style="margin-right:30px;">
        <el-button size="small" type="primary" plain>今日人数</el-button>
      </el-badge>
      <template v-for="(item, index) in dateTimeArr">
        <template v-if="todayCheck">
          <el-badge :key="index" :value="item.sum" style="margin-right:30px;">
            <el-button v-if="item.sum >= 18 || (item.glt_count === 0 && item.sum >= 16)" size="small" type="danger" plain>{{ item.start_time }} - {{ item.end_time }}（{{ item.count }}桌）</el-button>
            <el-button v-else size="small" type="success" plain>{{ item.start_time }} - {{ item.end_time }}（{{ item.count }}桌）</el-button>
          </el-badge>
        </template>
        <template v-else>
          <el-badge :key="index" :value="item.sum" style="margin-right:30px;">
            <el-button size="small" type="primary" plain>{{ item.start_time }} - {{ item.end_time }}（{{ item.count }}桌）</el-button>
          </el-badge>
        </template>
        <div v-if="index == 2" :key="item.name" style="height: 20px" />
      </template>
    </el-card>

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
            style="margin-right:10px;width:200px;"
            filterable
            clearable
            placeholder="预约时段"
          >
            <el-option
              v-for="item in dateTimeArr"
              :key="item.id"
              :label="item.start_time + ' - ' + item.end_time"
              :value="item.id"
            />
          </el-select>
          <!-- <el-select
            v-model="listQuery.where.status"
            class="filter-item"
            style="margin-right:10px;width:80px;"
            filterable
            clearable
            placeholder="付款"
          >
            <el-option
              v-for="item in statusArray"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select> -->
          <el-button type="primary" @click="handleSearch()">搜索</el-button>
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
      <!-- <el-table-column align="center" label="序号" width="50">
        <template slot-scope="scope">
          {{ scope.$index + 1 }}
        </template>
      </el-table-column> -->
      <el-table-column min-width="220" label="预约日期" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.operate_status == 2" type="danger">取</el-tag>
          <el-tag>{{ getTime(scope.row.int_time) || '--' }} {{ '星期' + scope.row.week }}</el-tag>
          <el-tag v-if="scope.row.time_out" type="info">{{ scope.row.date_time }}</el-tag>
          <el-tag v-else type="warning">{{ scope.row.date_time }}</el-tag>
        </template>
      </el-table-column>
      <!-- <el-table-column label="预约场次" min-width="120" align="center">
        <template slot-scope="scope">

        </template>
      </el-table-column> -->
      <el-table-column label="预约姓名" min-width="80" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.check_status == 2" @click="reserveOperate(scope.row.reserve_id, 'sign')">{{ scope.row.name || '--' }}</span>
          <span v-else style="color:red;">
            <el-tag type="warning">签</el-tag>
            {{ scope.row.name || '--' }}
          </span>
        </template>
      </el-table-column>
      <el-table-column label="预约电话" min-width="80" align="center">
        <template slot-scope="scope">
          {{ scope.row.telephone || '--' }}
        </template>
      </el-table-column>
      <el-table-column label="预约人数" min-width="60" align="center">
        <template slot-scope="scope">
          <el-tag type="info">{{ scope.row.number }}</el-tag>
        </template>
      </el-table-column>
      <!-- <el-table-column label="预约费用" min-width="80" align="center">
        <template slot-scope="scope">
          <el-tag type="success">{{ scope.row.order_price || '--' }}</el-tag>
        </template>
      </el-table-column> -->
      <el-table-column label="实际费用" min-width="60" align="center">
        <template slot-scope="scope">
          <el-tag type="warning">{{ scope.row.total_fee / 100 || '--' }}</el-tag>
        </template>
      </el-table-column>
      <!-- <el-table-column label="支付状态" width="110" prop="created_at" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status === 1">已支付</el-tag>
          <el-tag v-else-if="scope.row.status === 2" type="warning">未支付</el-tag>
          <el-tag v-else type="info">未知</el-tag>
        </template>
      </el-table-column> -->
      <el-table-column width="150" label="下单时间" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ new Date(scope.row.add_time*1000).toLocaleString('chinese', { hour12: false }) }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" min-width="100">
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.operate_status != 2"
            size="mini"
            type="primary"
            icon="el-icon-edit"
            @click="reserveEditHander(scope.row.reserve_id)"
          />
          <el-button
            v-if="scope.row.operate_status != 2"
            size="mini"
            type="danger"
            icon="el-icon-delete"
            @click="reserveOperate(scope.row.reserve_id, 'del')"
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
    <el-dialog title="编辑" :visible.sync="show" width="70%">
      <el-form ref="editCusNaData" :model="form" :rules="rules">
        <el-form-item label="预约日期">
          <el-radio-group v-model="form.date" @change="dateChangeHandler(form.date)">
            <template v-for="(item, index) in days">
              <el-radio :key="index" :label="item" style="margin: 10px" />
            </template>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="预约场次" prop="phone">
          <el-select
            v-model="form.hour"
            class="filter-item"
            style="margin-right:10px;width:230px;"
            filterable
            clearable
            placeholder="选择场次"
          >
            <el-option
              v-for="item in hours"
              :key="item.id"
              :label="item.name"
              :value="item.id"
              :disabled="item.data_type == 1 || item.data_type == 2"
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
import { getList, reserveDelete, reserveSign, check, reserveUpdate } from '@/api/reserve'
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
          hour: '',
          status: 1
        }
      },
      tableData: [], // 列表初始化数据
      getTime,
      todayCount: 0,
      // timeArray: [
      //   { id: 1, name: '12:00 - 12:50', value: 0, count: 0 },
      //   { id: 2, name: '13:05 - 13:55', value: 0, count: 0 },
      //   { id: 3, name: '14:15 - 15:05', value: 0, count: 0 },
      //   { id: 4, name: '15:25 - 16:15', value: 0, count: 0 },
      //   { id: 5, name: '16:35 - 17:25', value: 0, count: 0 },
      //   { id: 6, name: '17:45 - 18:35', value: 0, count: 0 },
      //   { id: 7, name: '18:50 - 19:40', value: 0, count: 0 }
      // ],
      dateTimeArr: [],
      statusArray: [
        { id: 1, name: '是' },
        { id: 2, name: '否' }
      ],
      days: [],
      hours: [],
      show: false,
      form: {
        reserve_id: 0,
        date: '',
        hour: ''
      },
      rules: {
        reserve_id: [
          { required: true, message: '预约id不能为空', trigger: 'blur' }
        ],
        date: [
          { required: true, message: '预约日期不能为空', trigger: 'blur' }
        ],
        hour: [
          { required: true, message: '预约时间段不能为空', trigger: 'blur' }
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
  computed: {
    todayCheck() {
      const today = this.getDate()
      console.log(today)
      if (this.listQuery.where.searchDate[0] === today && this.listQuery.where.searchDate[1] === today) {
        return true
      } else {
        return false
      }
    }
  },
  created() {
    const today = this.getDate()
    this.listQuery.where.searchDate = [today, today]
    this.fetchData()
  },
  methods: {
    getDate() {
      const date = new Date()
      const month = (date.getMonth() < 9) ? `0${date.getMonth() + 1}` : date.getMonth() + 1
      return date.getFullYear() + '-' + month + '-' + date.getDate()
    },
    handleSearch() {
      this.fetchData()
    },
    async fetchData() {
      this.listLoading = true

      const response = await getList(this.listQuery)
      this.tableData = response.data
      this.total = response.total
      this.dateTimeArr = response.list
      this.days = response.date
      this.todayCount = response.count

      // this.timeArray[0].value = response.list[1] || 0
      // this.timeArray[1].value = response.list[2] || 0
      // this.timeArray[2].value = response.list[3] || 0
      // this.timeArray[3].value = response.list[4] || 0
      // this.timeArray[4].value = response.list[5] || 0
      // this.timeArray[5].value = response.list[6] || 0
      // this.timeArray[6].value = response.list[7] || 0

      // this.timeArray[0].count = response.count[1] || 0
      // this.timeArray[1].count = response.count[2] || 0
      // this.timeArray[2].count = response.count[3] || 0
      // this.timeArray[3].count = response.count[4] || 0
      // this.timeArray[4].count = response.count[5] || 0
      // this.timeArray[5].count = response.count[6] || 0
      // this.timeArray[6].count = response.count[7] || 0

      if (this.todayCheck) {
        // const checkList = await check({ date: this.listQuery.where.searchDate[0] })
        // this.timeArray[0].is_full = checkList[0].data_type === 1
        // this.timeArray[1].is_full = checkList[1].data_type === 1
        // this.timeArray[2].is_full = checkList[2].data_type === 1
        // this.timeArray[3].is_full = checkList[3].data_type === 1
        // this.timeArray[4].is_full = checkList[4].data_type === 1
        // this.timeArray[5].is_full = checkList[5].data_type === 1
      }

      this.listLoading = false
    },
    reserveUpdate() {
      this.show = !this.show
    },
    updateSumit() {
      this.$refs['editCusNaData'].validate(valid => {
        if (valid) {
          reserveUpdate(this.form).then(response => {
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
    reserveOperate(id, type) {
      const text = `是否${type === 'del' ? '取消预约' : '签到'}`
      this.$confirm(text, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        // 开启加载层
        let request = null
        if (type === 'del') {
          request = reserveDelete({ reserve_id: id })
        } else {
          request = reserveSign({ reserve_id: id })
        }
        this.listLoading = true
        request.then(response => {
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
          const text = (item.data_type === 2) ? '(场次禁用)' : (
            (item.data_type === 1) ? '(约满)' : (
              item.count <= 5 ? `(剩余${item.count}座位)` : '未约满'
            )
          )
          // const name = `${this.timeArray[index].name}${text}`
          const name = `${item.time}${text}`
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
