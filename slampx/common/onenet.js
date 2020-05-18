// 引入
import config from './config.js';

export default{
	config:{
		baseUrl:config.onenetUrl,
		header:{
			'content-type':'application/json;charset=UTF-8',
			'api-key':'lODwUzE3zIsoIXUd4dSRav=UZkM=',
			'Content-Type':'application/x-www-from-urlencoded',
		},
		data:{},
		method:"GET",
		dataType:"json"
	},
	request(options={}){
		options.header = options.header || this.config.header;
		options.method = options.method || this.config.method;
		options.dataType = options.dataType || this.config.dataType;
		options.url = this.config.baseUrl + options.url;
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
}

