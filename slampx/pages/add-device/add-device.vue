<template>
	<view>
		<!-- 导航栏 -->
		<!-- <view class="status-bar" :style="{height : statusHeight + 'px'}"></view> -->
		<!-- <uni-nav-bar  title="新增设备" @clickLeft="getdevice" color="#ffffff" background-color="#6390C3"/> -->
		<!-- #ifndef APP-PLUS -->
			<view class="more-device u-f-ac">
				<view class="device-list u-f-ac" @tap="getdevice">
					<text>设备列表</text>
					<view class="device-icon icon iconfont icon-shebeiliebiao u-f-ajc"></view>
				</view>
			</view>
			
			
		<!-- #endif -->
		<!-- <view class="more-device u-f-ac">
			<view class="device-list u-f-ac" @tap="getdevice">
				<text>批量添加</text>
				<view class="device-icon icon iconfont icon-guanli1-copy u-f-ajc"></view>
			</view>
		</view> -->
		<view class="device-id u-f-ac device-wx">
			<image src="../../static/iconfont/device.png" mode=""></image>
			<text>设备ID</text>
			<input type="text" :value="deviceId" @input='deviceValue'/>
		</view>
		<view class="device-id u-f-ac">
			<image src="../../static/iconfont/imei.png" mode=""></image>
			<text>imei</text>
			<input type="text" :value="deviceIemi" @input='imeiValue'/>
		</view>
		<view class="region u-f-ac">
			<image src="../../static/iconfont/region.png" mode=""></image>
			<text>区域</text>
			<view class="picker u-f-ajc">
				<area-picker @picker='picker' ref='picker'></area-picker>
				<view class="icon iconfont icon-down-trangle"></view>
			</view>
			
		</view>
		<!-- 区域解释 -->
		<view class="tips u-f-ac">
			<text>区域自主添加，以批量控制区域，如学校南区、西区，不受街道约束</text>
			<view class="operation">
				<view  @tap="addRegion">添加</view>
				<view class="line"></view>
				<view @tap="toRegionList">管理</view>
			</view>
		</view>
		<!-- 确定按钮 -->
		<button type="default" class="add" plain="true" @click="addDevice">添加</button>
		
		
		<uni-popup ref='popup' type="bottom">
			<add-area @increaseArea='increaseArea'></add-area>
		</uni-popup>
	</view>
</template>
<script>
	let _self;
	// #ifndef APP-PLUS
	import uniNavBar from "../../components/uni-nav-bar/uni-nav-bar.vue"
	// #endif
	import uniPopup from "@/components/uni-popup.vue";
	import addArea from "../../components/area/add-area.vue";
	import areaPicker from "../../components/area/area-picker.vue";
	export default {
		components:{
			uniPopup,
			addArea,
			areaPicker,
			// #ifndef APP-PLUS
			uniNavBar
			// #endif
		},
		data() {
			return {
				deviceId:'',
				region:0,
				deviceIemi:'',
				// #ifndef APP-PLUS
				statusHeight:44
				// #endif
			}
		},
		methods: {
			// #ifndef APP-PLUS
			getdevice(){
				uni.navigateTo({
					url:"../device-list/device-list"
				})
			},
			// #endif
			// 获取输入的设备ID
			deviceValue(event){
				this.deviceId = event.target.value;
			},
			// 获取设备imei
			imeiValue(event){
				this.deviceIemi = event.target.value;
			},
			// 提示
			tips(title,icon){
				uni.showToast({
					title:title,
					icon:icon
				})
			},
			// 添加设备
			async addDevice(){
				if(!this.deviceId || !this.deviceIemi){
					_self.tips("设备ID或iemi不能为空！！",'none')
					return false;
				}
				uni.showLoading({
					title:"添加中..."
				})
				let url = `/devices/${this.deviceId}/datapoints?datastream_id=${this.config.latLong}`
				let [err,res] = await this.$onenet.get(url)
				if(res.data.errno==3){
					uni.hideLoading();
					return _self.tips("该设备不存在，请正确输入！！",'none');
				}
				
				let [imeierr,imeires] = await this.$onenet.get(`/devices/${this.deviceId}`);
				if(_self.deviceIemi != imeires.data.data.imsi_old[0]){
					uni.hideLoading();
					return _self.tips("请正确输入设备IMEI！！",'none');
				}  
				var latitude,longitude
				console.log(res.data.data.datastreams[0].datapoints[0].value)
				var loa = res.data.data.datastreams[0].datapoints[0].value.split(":");
				// 24.774008 ,  113.670639
				let [tperr,tpres]  =await this.$http.post('/device/adddevice',{
					imei:_self.deviceIemi,
					lamp_id:_self.deviceId,
					latitude:loa[1],
					longitude:loa[0],
					region_id:_self.region
				})
				uni.hideLoading();
				if(tpres&& !tpres.data.errorCode){
					_self.tips("添加成功！！",'success');
					
				}else{
					_self.tips("不可重复添加设备",'none');
				}
				_self.deviceIemi = '';
				_self.deviceId = '';
				console.log(err,res);
				console.log(this.region);
				console.log("添加设备");
			},
			// 添加区域弹窗
			addRegion(){
				this.$refs.popup.open();
			},
			// 添加区域
			increaseArea(e){
				console.log(e)
				this.region = e.id;
				this.$refs.picker.index = this.$refs.picker.modeList.length;
				this.$refs.picker.modeList.push(e);
				this.$refs.popup.close();
			},
			// 跳转区域列表
			toRegionList(){
				uni.navigateTo({
					url:'../area/area'
				})
			},
			picker(e){
				this.region = e;
			}
		},
		computed: {
			
		},
		onLoad() {
			_self = this;
		},
		onShow() {
			this.$refs.picker.getRegion();
			this.$refs.picker.index = 0;
			this.region = 0;
		},
		// 监听原生标题导航按钮点击事件
		onNavigationBarButtonTap(e){
			switch (e.index){
				case 0:
					uni.navigateTo({
						url:"../device-list/device-list"
					})
				break;
			}
		},
	}
</script>

<style>
	*{
		font-size: 28rpx;
	}
	/* #ifdef APP-PLUS */
		view{
			font-size: 30rpx;
		}
	/* #endif */
	
	.status-bar{
		width: 100vw;
		background-color: #6390C3;
	}	

	.device-id,.region{
		color: #494b68;
		width: 80vw;
		margin: 40upx auto 20upx;
		padding: 35upx 0;
		border-bottom: 1upx solid #C9C9C9;
	}
	.device-id>image,.region>image{
		width: 50upx;
		height: 50upx;
		padding-right: 10upx;
	}
	.device-id>text,.region>text{
		width: 100rpx;
	}
	.device-id>input,.picker{
		/* margin-left: 20upx; */
		flex: 1;
		text-align: center;
	}
	.picker>.icon{
		margin-left: 10rpx;
		font-size: 40rpx;
	}
	.region{
		margin: 0 auto 30upx;
	}
	.tips{
		width: 80vw;
		margin: 0 auto 70upx;
		color: #494b68;
	}
	.tips>text{
		width: 78%;
		padding: 2% 2% 2% 0;
		border-right: 1upx solid #C9C9C9;
	}
	.tips>view{
		flex: 1;
		text-align: center;
	}
	.line{
		width: 100%;
		height: 2upx;
		background-color: #C9C9C9;
		margin: 11upx 0;
	}
	button[plain],button[type=default][plain]{
		border:1upx solid #C9C9C9 ;
		/* border: none; */
	}
	.add{
		width: 80vw;
	}
	
	/* 跳转设备 */
	.more-device{
		width: 80vw;
		margin: 40upx auto 0upx;
		justify-content: flex-end;
	}
	.device-list{
		width: 250rpx;
		height: 70rpx;
		border-radius: 40rpx;
		box-shadow: 0 0 2px 2px rgba(238, 238, 238, 0.9);
		background-color: #FFFFFF;
		justify-content: flex-end;
		/* margin-top: 30rpx; */
	}
	.device-icon{
		width: 70rpx;
		height: 70rpx;
		border-radius: 50%;
		background-color: #6390C3;
		color: #FFFFFF;
		font-size: 40rpx;
		margin-left: 20rpx;
	}
	.device-wx{
		margin-top: 15rpx;
	}
</style>
