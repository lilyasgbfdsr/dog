import request from '@/utils/request'

export function configPrice(params) {
  return request({
    url: '/admin/configPrice',
    method: 'get',
    params
  })
}

export function addConfigPrice(data) {
  return request({
    url: '/admin/configPrice',
    method: 'post',
    data
  })
}
