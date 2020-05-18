import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false

// 挂载全局的方法
import config from "./common/config.js"
Vue.prototype.config = config

import lib from "./common/lib.js"
Vue.prototype.lib = lib

import chat from "./common/chat.js"
Vue.prototype.$chat = chat

import request from "./common/request.js"
Vue.prototype.$http = request

import onenet from "./common/onenet.js"
Vue.prototype.$onenet = onenet

App.mpType = 'app'

const app = new Vue({
    ...App,
	config,
	lib,
})
app.$mount()
