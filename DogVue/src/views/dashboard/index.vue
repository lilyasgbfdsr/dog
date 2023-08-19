<template>
  <div class="dashboard-container">
    <div v-loading.body="listLoading" class="clearfix">
      <el-row>
        <!-- 第一个大块 start -->
        <el-col v-for="(item, index) in list " :key="index" :span="12">
          <div class="widget-panel widget-style-2" :class="item.class">
            <i class="ion-eye">
              <img :src="qiNiuPath + item.path + '.svg'" class="images">
            </i>
            <h2 class="m-0 counter">{{ item.value }}</h2>
            <div>{{ item.title }}</div>
          </div>
        </el-col>
        <!-- 第一个大块 end -->
      </el-row>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { getList } from '@/api/index'

export default {
  name: 'Dashboard',
  data() {
    return {
      listLoading: false,
      qiNiuPath: 'https://cdn.bsybx.com/erp',
      list: [
        { 'title': '今日预约人数', 'field': 'reserve_today', 'value': 0, 'class': 'bg-info', path: '/today_count_customer' }
        // { 'title': '本月查询量', 'field': 'captcha_month', 'value': 0, 'class': 'bg-warning', path: '/month_new_customer' },
        // { 'title': '总查询量', 'field': 'captcha_click', 'value': 0, 'class': 'bg-success', path: '/today_count_work' },
        // { 'title': '数量', 'field': 'goods_count', 'value': 0, 'class': 'bg-danger', path: '/ready_finish_stock' }
      ]
    }
  },
  computed: {
    ...mapGetters([
      'name'
    ])
  },
  mounted() {
    this.getList()
  },
  methods: {
    // 获取列表数据
    getList() {
      // 开启加载层
      this.listLoading = true
      getList().then(response => {
        for (const item in response) {
          for (const i in this.list) {
            if (item === this.list[i].field) {
              this.list[i].value = response[item]
            }
          }
        }
        // 关闭加载层
        this.listLoading = false
      }).catch(() => {
        // 关闭加载层
        this.listLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard-container {
  background-color: #fff;
  min-height: 100vh;
  margin-top: -50px;
  padding: 100px 60px 0px;
}

.widget-style-2{
  width:90%;
  height:130px;
}
.widget-panel {
  padding: 40px 20px;
  border-radius: 4px;
  color: #ffffff;
  position: relative;
  margin-bottom: 20px;
}
.m-0 {
  margin: 0px !important;
  margin-bottom: 10px !important;
}
.widget-style-2 i {
  height: 130px;
  font-size: 10px;
  float: right;
  margin-top:-40px;
  margin-right: -20px;
  width:40%;
  color: #edf0f0;
  background: rgba(255, 255, 255, 0.2);
}
.images {
  max-width: 60px;
  max-height: 60px;
  width: 100%;
  height: 100%;
}
.ion-eye {
  padding-top: 40px;
  text-align: center;
}

.bg-info{
  background:#20a0ff;
}
.bg-success{
  background:#13ce66;
}
.bg-danger{
  background:#ff4949;
}
.bg-warning{
  background:#f7ba2a;
}
</style>
