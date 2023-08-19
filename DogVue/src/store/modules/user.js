import { getInfo } from '@/api/user'
import { setGuid, getGuid, getToken, setToken, removeToken } from '@/utils/auth'
import { resetRouter } from '@/router'

const state = {
  guid: '',
  token: getToken(),
  name: '',
  avatar: ''
}

const mutations = {
  SET_GUID: (state, guid) => {
    state.guid = guid
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    // const { username, password } = userInfo
    return new Promise((resolve, reject) => {
      // login({ username: username.trim(), password: password }).then(response => {
      //   const { guid, token } = response
      //   commit('SET_GUID', guid)
      //   commit('SET_TOKEN', token)
      //   setGuid(guid)
      //   setToken(token)
      //   resolve()
      // }).catch(error => {
      //   reject(error)
      // })
      const guid = '88888888'
      const token = '88888888'
      commit('SET_GUID', guid)
      commit('SET_TOKEN', token)
      setGuid(guid)
      setToken(token)
      resolve()
    })
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo({ guid: getGuid(), token: state.token }).then(response => {
        const { name } = response
        if (!name) {
          reject('Verification failed, please Login again.')
        }
        commit('SET_NAME', name)
        commit('SET_AVATAR', '123')
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state }) {
    return new Promise((resolve, reject) => {
      commit('SET_TOKEN', '')
      removeToken()
      resetRouter()
      resolve()
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '')
      removeToken()
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

