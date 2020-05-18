// 引入
import config from './config.js';

export default{
	config:{
		baseUrl:config.webUrl,
		header:{
			'content-type':'application/json;charset=UTF-8',
			// 'content-type':'application/x-www-from-urlencoded',
		},
		data:{},
		method:"GET",
		dataType:"json"
	},
	request(options={}){
		var token = uni.getStorageSync("token")
		options.header = options.header || this.config.header;
		options.method = options.method || this.config.method;
		options.dataType = options.dataType || this.config.dataType;
		options.url = this.config.baseUrl + options.url;
		if(token) options.header.token = token;
		return uni.request(options);
	},
	get(url,data,option={}){
		option.url = url;
		option.data = data;
		option.method = 'GET';
		return this.request(option);
	},
	post(url,data,option={}){
		option.url = url;
		option.data = data;
		option.method = 'POST';
		return this.request(option);
	},
	// 错误处理
	errorCheck(err,res,errfun = false,resfun = false){
		if(err){
			// 如果 errfun 是一个 "function" (函数)，则执行errfun这个函数
			typeof errfun === 'function' && errfun();
				uni.showToast({
					title:'加载失败,请检查网络',
					icon:'none'
				})
			return false;
		}
		if(res.data.errorCode){
			typeof resfun === 'function' && resfun();
				uni.showToast({title: res.data.msg,icon:'none'});
			return false;
		}
	},
	
	
}

