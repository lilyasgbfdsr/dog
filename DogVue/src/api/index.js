import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/admin/index',
    method: 'post',
    data
  })
}
