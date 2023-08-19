import request from '@/utils/request'

export function addDateTime(data) {
  return request({
    url: '/admin/dateTime',
    method: 'post',
    data
  })
}

export function dateTime(params) {
  return request({
    url: '/admin/dateTime',
    method: 'get',
    params
  })
}

export function deleteDateTime(data) {
  return request({
    url: '/admin/deleteDateTime',
    method: 'post',
    data
  })
}

export function editDateTime(data) {
  return request({
    url: '/admin/editDateTime',
    method: 'post',
    data
  })
}
