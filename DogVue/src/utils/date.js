// 获取时间
const getTime = function(time) {
  const date = new Date(parseInt(time) * 1000)
  const Y = date.getFullYear() + '-'
  const M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-'
  const D = date.getDate() + ' '
  return Y + M + D
  // const h = date.getHours() + ':'
  // const m = date.getMinutes() + ':'
  // const s = date.getSeconds()
  // return Y + M + D + fix(h, 2) + fix(m, 2) + fix(s, 2)
}

// function fix(num) {
//   return (parseInt(num) < 10) ? ('0' + num) : num
// }

export {
  getTime
}
