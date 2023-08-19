import Cookies from 'js-cookie'

const TokenKey = 'vue_admin_template_token'
const GuidKey = 'guid'

export function getGuid() {
  return Cookies.get(GuidKey)
}

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setGuid(guid) {
  return Cookies.set(GuidKey, guid)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}

export function removeToken() {
  return Cookies.remove(TokenKey)
}
