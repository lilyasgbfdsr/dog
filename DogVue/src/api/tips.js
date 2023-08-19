import request from '@/utils/request'

export function addTip(data) {
  return request({
    url: '/admin/tip',
    method: 'post',
    data
  })
}

export function tips(params) {
  return request({
    url: '/admin/tip',
    method: 'get',
    params
  })
}

export function deleteTip(data) {
  return request({
    url: '/admin/deleteTip',
    method: 'post',
    data
  })
}
