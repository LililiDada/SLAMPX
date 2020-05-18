<template>
	<view class="content">
		<view class="bgcolor"></view>
		<image class="logo" src="/static/login3.png"  mode="aspectFit"></image>
		<view class="login">
			<view>
				<view class="icon iconfont icon--yonghu"></view>
				<input type="text" v-model="username" placeholder="用户名" placeholder-class="input-place"/>	
			</view>
			<view class="password">
				<view class="icon iconfont icon-mima"></view>
				<input type="text" v-model="password" placeholder="密码" placeholder-class="input-place" password="true"/>	
			</view>
			<button  plain :loading="loading" :class="{'user-set-btn-disable':disabled}" 
			 @tap="submit" :disabled="disabled">登录</button>
		</view>
	</view>
	
</template>

<script>
	export default {
		data() {
			return {
				username: '',
				password:'',
				disabled:true
			}
		},
		watch:{
			username(val){
				this.OnBtnChange();
			},
			password(val){
				this.OnBtnChange();
			},
		},
		async onLoad() {
			
			// var Tomorrow = new Date();
			//  Tomorrow.setTime(Tomorrow.getTime()+24*60*60*1000);
			//  var TomorrowDate = Tomorrow.getFullYear()+"/" + (Tomorrow.getMonth()+1) + "/" + Tomorrow.getDate();
			//  console.log(TomorrowDate)
			// var Tomorrow = new Date().setTime(new Date().getTime()+24*60*60*1000)
			// var TomorrowYear = Tomorrow.getFullYear()
			// // var TomorrowDate = `${Tomorrow.getFullYear()}/${Tomorrow.getMonth()+1}/${Tomorrow.getDate()}`
			// console.log(TomorrowDate);
			// let netUrl = `/nbiot?imei=869976031494728&obj_id=3341&obj_inst_id=2&mode=2`;
			// let [neterr,netres] = await this.$onenet.post(netUrl,{
			// 	data:[{
			// 		res_id:5528,
			// 		val:'19:00-12:00,19:00-12:00,19:00-12:00'
			// 	}]
			// })
			
			// let netUrl = `https://api.heclouds.com/nbiot?imei=869976031494728&obj_id=3333&obj_inst_id=0&mode=2`;
			
			// var [neterr,netres] = await uni.request({
			// 	url:netUrl,
			// 	header:{
			// 		// 'content-type':'application/json;charset=UTF-8',
			// 		'api-key':'lODwUzE3zIsoIXUd4dSRav=UZkM=',
			// 		'Content-Type':'application/x-www-from-urlencoded',
			// 	},
			// 	data:{
			// 		'data':[{
			// 			res_id:5506,
			// 			type:2,
			// 			val:1585711146,
			// 		}]
			// 	},
			// 	method:"POST",
			// 	dataType:"json",
			// })
			// var url=`/devices/587768654/datapoints`;
			// let [err,res] = await this.$onenet.get(url,{
			// 	datastream_id:'3303_1_5700',
			// 	limit:500
			// })
			// console.log(res.data.data.datastreams[0].datapoints);
			// res.data.data.datastreams[0].datapoints.forEach((item,index)=>{
			// 	console.log(index);
			// })
			// console.log(neterr,netres.data)
		},
		methods: {
			// 改变按钮状态
			OnBtnChange(){
				if (this.username && this.password){
					this.disabled=false; return;
				}
				this.disabled=true;
			},
			// 登录
			async submit(){
				uni.showLoading({
					title:"登录中"
				})
				let [err,res] = await this.$http.post('/user/login',{
					username:this.username,
					password:this.password,
				});
				if(err) {
					uni.hideLoading();
					uni.showToast({title:'登录失败！！',icon:'none'}); 
					return;
				};
				if(res.data.errorCode){
					uni.hideLoading();
					uni.showToast({title:'用户名或密码错误！！',icon:'none'}); 
					return;
				}
				console.log(res)
				if(res.data.data.status == 2){
					uni.setStorageSync('admin', true);
				}else{
					uni.setStorageSync('admin', false);
				}
				uni.setStorageSync('token', res.data.data.token);
				uni.setStorageSync('user_scene',res.data.data.scene);
				uni.setStorageSync('user_scene_id',res.data.data.scene_id);
				uni.setStorageSync('user_plain',res.data.data.plain);
				uni.setStorageSync('user_plain_id',res.data.data.plain_id);
				uni.hideLoading();
				uni.showToast({
					title:'登录成功',
					duration:500
				})
				setTimeout(()=>{
					uni.switchTab({
					    url: '/pages/map/map'
					});
				},500)
				
			},
			
		}
	}
</script>

<style>
	.bgcolor{
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #6390c3;
		z-index: -1;
	}
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		
	}

	.logo {
		width: 430rpx;
		margin-top: 100rpx;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 105rpx;
	}

	.iconfont{
		color: #FFFFFF;
		font-size: 40rpx;
		padding: 0 20rpx;
	}
	.login>view{
		display: flex;
		flex-direction: row;
		align-items: center;
		padding-bottom: 20rpx;
		border-bottom:1rpx solid #FFFFFF ;
	}
	.login>view>input{
		color: #FFFFFF;
		font-size: 33rpx;
		width: 500rpx;
		height: 50rpx;
	}
	.input-place{
		color: #FFFFFF;
	}
	.password{
		margin-top: 45rpx;
	}
	.login>button{
		border:1rpx solid #FFFFFF;
		color: #FFFFFF;
		margin-top: 70rpx;
	}
	.user-set-btn-disable{
		background: #F4F4F4!important;
		border: 1rpx solid #EEEEEE!important;
		color: #909090!important;
	}
</style>
