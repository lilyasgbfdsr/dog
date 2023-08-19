import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/admin/reserveList',
    method: 'post',
    data
  })
}

export function reserveDelete(data) {
  return request({
    url: '/admin/reserveDelete',
    method: 'post',
    data
  })
}

export function reserveCheck(data) {
  return request({
    url: '/admin/reserveCheck',
    method: 'post',
    data
  })
}

export function reserveUpdate(data) {
  return request({
    url: '/admin/reserveUpdate',
    method: 'post',
    data
  })
}

export function reserveSign(data) {
  return request({
    url: '/admin/reserveSign',
    method: 'post',
    data
  })
}

export function check(data) {
  return request({
    url: '/admin/reserveCheck',
    method: 'post',
    data
  })
}

export function infoList(data) {
  return request({
    url: '/admin/infoList',
    method: 'post',
    data
  })
}

export function infoStore(data) {
  return request({
    url: '/admin/infoStore',
    method: 'post',
    data
  })
}

export function infoDelete(data) {
  return request({
    url: '/admin/infoDelete',
    method: 'post',
    data
  })
}
