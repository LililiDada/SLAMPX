import Config from './config.js';
import $http from './request.js';

export default{
	// socket地址
	url:Config.websocketUrl,
	// 连接状态,连接完后会返回客户端,会用当前登录用户id去绑定$client_id,绑定失败就让其断开,可以有效防止恶意连接websocket
	IsOpen:false,
	// SocketTask
	SocketTask:false,
	// 是否上线,把控用户身份(会员ID绑定客户端ID,检验用户身份,通过则绑定)
	IsOnline:false,
	
	// 连接
	Open(){
		if(this.IsOpen) return;  //防止重复连接
		this.SocketTask = uni.connectSocket({
			url:this.url,
			complete:(e)=> {}
		});
		if(!this.SocketTask) return;
		// 监听开启
		this.SocketTask.onOpen((e)=>{
			this.IsOpen = true; 
			console.log(e,this.IsOpen)
		})
		this.Message();
		// 监听关闭
		this.SocketTask.onClose((e)=>{
			this.IsOpen = false;
			this.SocketTask = false;
			console.log(e,this.IsOpen)
		});
		// 监听错误
		this.SocketTask.onError((e)=>{
			this.IsOpen = false;
			this.SocketTask = false;
			console.log(e,this.IsOpen)
		})
	},
	// 用户绑定
	UserBind(client_id){
		$http.post('/message/bind',{
			type:'bind',
			client_id:client_id
		}).then(data=>{
			let [err,res] = data;
			console.log(res);
			// 错误处理
			if(!$http.errorCheck(err,res)){
				// 退出登录,重新连接等处理
				return;
			}
			// 成功
			return this.resultUserBind(res.data.data)
		})
	},
	// 关闭连接
	Close(){
		if(this.IsOpen){
			this.SocketTask.close();
		}
	},
	// 用户绑定结果
	resultUserBind(res){
		if(res.status && res.type == 'bind'){
			// 修改状态为上线
			this.IsOnline = true;
			return;
		}
		// 绑定失败,断开连接
		uni.showToast({ title: res.msg, icon:"none" });
		this.SocketTask.close();
	},
	// 监听信息
	Message(){
		this.SocketTask.onMessage((e)=>{
			// 字符串转json
			console.log(e);
			let res
			var reg_Hello = RegExp(/Hello/);
			var reg_login= RegExp(/login/);
			if(reg_Hello.test(e.data)){
				res={
					type:'bind',
					data:e.data.substr(e.data.lastIndexOf(' ')+1,20)
				}
			}else if(reg_login.test(e.data)){
				return;
			}else{
				res = JSON.parse(e.data);
			}
			console.log(res)
			// 绑定返回结果
			if(res.type && res.type == 'bind'){
				return this.UserBind(res.data);
			} 
			// 全局通知接口
			uni.$emit('message',res)
		})
	}
	
}