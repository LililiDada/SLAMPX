<template>
	<view>
		<map  style="width: 100vw;height: 70vh;"  :latitude="latitude" :longitude="longitude" :markers="markers" @markertap='showdetail' scale="18" show-location v-if="show" id="maps">
			<cover-view class="add u-f-ajc" @click="addDevice">
				<!-- <cover-view class="add-icon icon iconfont icon-add"></cover-view> -->
				<cover-image src="../../static/add.png" class="add-img"></cover-image>
			</cover-view>
		</map>
		<view class="record u-f-ac u-f-jsa">
			<view class="lamp_status">
				<view :style="[{margin: lampStatusList.length-1 == index?'':marginBottom}]" v-for="(item,index) in lampStatusList" :key='index'>
					<lamp-status :item="item"></lamp-status>
				</view>
			</view>
			<view class="lamp_count u-f-ajc u-f-column">
				<text>路灯总数</text>
				<text>{{deviceTotal}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	import lampStatus from  "../../components/map/lamp-status.vue";
	export default {
		components:{
			lampStatus,
		},
		data() {
			return {
				show:false,
				inputInfo:'请输入',
				inputModel:'',
				deviceTotal:0,  //路灯总数
				lampStatusList:[{
					id:1,
					activeColor:'#009f3c',
					width:uni.upx2px(150),
					borderRadius:20,
					handleColor:"transparent",
					status:"正常路灯",
					percent:'15%',
					number:15
				},{
					id:2,
					activeColor:'#f4ea2a',
					width:uni.upx2px(150),
					borderRadius:20,
					handleColor:"transparent",
					status:"关闭路灯",
					percent:'15%',
					number:15
				},{
					id:3,
					activeColor:'#d81e06',
					width:uni.upx2px(150),
					borderRadius:20,
					handleColor:"transparent",
					status:"故障路灯",
					percent:'15%',
					number:15
				}],
				inputFocus:false,
				windowWidth:0,
				windowHeight:0,
				latitude:24.77327253,
				longitude: 113.67080133,
				marginBottom:'0 0 25rpx 0',
				noraml:{
					content:'',//文本
					color:'#ffffff',//文字颜色
					fontSize:14,//文本大小
					borderRadius: 4,
					borderWidth: 1,
					borderColor: '#36b599',
					bgColor:'#36b599',//背景颜色
					display:'ALWAYS',//常显
					padding:2
				},
				normalImg:'../../static/normal.png',
				abnormalImg:'../../static/abnormal.png',
				abnormal:{
					content:'',
					color:'#ffffff',//文字颜色
			 　　   fontSize:14,//文本大小
			 　　   borderRadius:2,//边框圆角
				    borderColor:'#ff7442',
			　　    bgColor:'#ff7442',//背景颜色
			 　　   display:'ALWAYS',//常显
					padding:2
				},
				markers: [
					{
						"id": 3,
						"title":'123',
						"latitude": 24.766771,
						"longitude": 113.657915,
						"width": 40,
						"height": 40,
						"iconPath": "../../static/normal.png"
					}
					
				],
				
			}
		},
		methods: {
			showdetail(e){
				uni.navigateTo({
				    url: '/pages/record/record?des_id=' +  e .markerId
				});
				
			},
			
			// 添加设备
			addDevice(){
				uni.navigateTo({
				    url: '/pages/add-device/add-device'
				});
			},
			
			percentage(num, total) {
			    if (num == 0 || total == 0){
			        return 0;
			    }
			    return (Math.round(num / total * 1000) / 10.00);// 小数点后一位百分比
			}

		},
		async onShow() {
			
			
			this.show = false;
			uni.showLoading({title: '正在加载...'});
			let that = this;
			uni.getSystemInfo({
			    success: function (res) {
					that.windowWidth = res.windowWidth
					that.windowHeight = res.windowHeight;
			    }
			});
			let [err,res] =await this.$http.get('/device');
			console.log(res)
			var data =  res.data.data
			var total = data.faultCount + data.runCount;
			that.deviceTotal = total;
			let list = data.list;
			var marker = [];
			
			// 权限之分
			if(!uni.getStorageSync('admin')){
				if(!list.length){
					uni.getLocation({
					    type: 'wgs84',
					    success: function (res) {
					        that.longitude = res.longitude;
					        that.latitude = res.latitude;
					    }
					});
				}else{
					that.latitude=list[0].latitude;
					that.longitude=list[0].longitude;
				}
			}
			
			
			list.forEach(function(item,index){
				let data = {
					id:Number(item.id),
					latitude: item.latitude,
					longitude:  item.longitude,
					width:40,
					height:40,
				}
					
				if(item.status){
					data.iconPath = that.normalImg;
					marker.push(data);
					return;
				}
				data.iconPath = that.abnormalImg;
				marker.push(data);
			})
			that.markers = marker
			that.show = true;
			that.lampStatusList.forEach((item,index)=>{
				switch(item.id){
					case 1:
					    var percent = that.percentage(data.runCount,total);
						var width = 150*percent/100;
						that.$set(that.lampStatusList[index],'number', data.runCount);
						that.$set(that.lampStatusList[index],'percent', percent);
						that.$set(that.lampStatusList[index],'width', uni.upx2px(width));
					break;
					case 2:
						var percent = that.percentage(data.onlineCount,total);
						var width = 150*percent/100;
						that.$set(that.lampStatusList[index],'number', data.onlineCount);
						that.$set(that.lampStatusList[index],'percent', percent);
						that.$set(that.lampStatusList[index],'width', uni.upx2px(width));
					break;
					case 3:
						var percent = that.percentage(data.faultCount,total);
						var width = 150*percent/100;
						that.$set(that.lampStatusList[index],'number', data.faultCount);
						that.$set(that.lampStatusList[index],'percent', percent);
						that.$set(that.lampStatusList[index],'width', uni.upx2px(width));
					break;
				}
				uni.hideLoading();
			})
			
		}
	}
</script>

<style>
	.record{
		height: 30vh;
	}
	.lamp_count{
		width: 25vw;
		height: 155rpx;
		background-color: #FFFFFF;
		box-shadow: 0 0 2px 2px rgba(153, 153, 153, 0.1);
		border-radius: 10rpx;
		font-size: 27rpx;
	}
	.lamp_count>text:nth-of-type(1){
		display: inline-block;
		padding-bottom: 20rpx;
	}
	.add{
		width: 90rpx;
		height: 90rpx;
		border-radius: 50%;
		background-color: #FFFFFF;
		color: #494b68;
		line-height: 120rpx;
		box-shadow: 0 0 2px 2px rgba(238, 238, 238, 1);
		position: absolute;
		margin: auto;
		bottom: 20rpx;
		left: 0;
		right: 0;
	}
	.add-icon{
		font-size: 50rpx;
	}
	.add-img{
		width: 40rpx;
		height: 40rpx;
	}
</style>