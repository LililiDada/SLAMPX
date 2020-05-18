<template>
	<view>
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<view class="tips u-f-ajc" v-if="deviceList.length<1">
				<image src="../../static/tips.png" mode="widthFix"></image>
			</view>
			<view v-else>
				<uni-swipe-action>
					<uni-swipe-action-item v-for="(item,index) in deviceList" :options="options" :key="index" @change="swipeChange" @click="swipeClick($event,index,item.id)">
						<view class="cont u-f-ac">
							<device :item="item" @alterArea="alterArea(item,index)"></device>
						</view>
					</uni-swipe-action-item>
				</uni-swipe-action>
			</view>
			<view style="text-align: center;margin-top: 10px;" v-if="loaded && deviceList.length>6">数据已加载完毕~~</view>	
		</scroll-view>
		
	</view>
</template>

<script>
	import uniSwipeAction from '@/components/uni-swipe-action/uni-swipe-action.vue';
	import uniSwipeActionItem from '@/components/uni-swipe-action-item/uni-swipe-action-item.vue';
	import device from '@/components/device-list/device.vue';
	export default {
		components:{
			uniSwipeAction,
			uniSwipeActionItem,
			device
		},
		data() {
			return {
				refresh:false,
				page:1,
				num:6,
				region:0,
				options: [{
					text: '删除',  //滑动操作颜色
					style: {
						backgroundColor: 'rgb(255,58,49)'
					}
				}],
				deviceList:[],
				loaded:false, //判断数据是否加载完毕
			}
		},
		computed: {
			getHeight() {
				let height = uni.getSystemInfoSync().windowHeight
				return `height: ${height}px;`;
			}
		},
		methods: {
			swipeChange(e) {
				console.log('返回：', e);
			},
			swipeClick(e, index,id) {
				var that = this;
				let {
					content
				} = e
				if (content.text === '删除') {
					uni.showModal({
						title: '提示',
						content: '是否删除',
						success: async (res) => {
							if (res.confirm) {
								let [err,res] =await that.$http.post('/device/deletedevice',{
									id:id
								});
								if(res.statusCode == 200 ){
									if (index > -1) {  
									    that.deviceList.splice(index, 1);
									}
									uni.showToast({title:"删除成功"});
									return;
								}
								uni.showToast({title:"删除失败！",icon:'none'});
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
				} 
			
			},
			alterArea(e,index){
				console.log(e)
				var url = `../regoin-manage/regoin-manage?id=${e.id}&region_id=${e.area.id}&index=${index}`;
				uni.navigateTo({
					url:url
				})
			},
			// 封装需要的数据
			sort(list){
				if(list.length<6){
					 this.loaded = true;
					 uni.showToast({
					 	title:"数据已加载完毕~~",
					 	icon:"none"
					 })
				}
				var deviceList = [];
				list.forEach((item,index)=>{
					if(!item.region){
						item.region = {
							id:0,
							regionname:'未选择区域',
						}
					}
					
					// 权限之分
					if(!uni.getStorageSync('admin')){
						deviceList.push({
							id:item.id,
							deviceId:item.lamp_id,
							location:item.address,
							area:item.region
						})
					}else{
						deviceList.push({
							id:item.id,
							deviceId:item.id<10?`5121260000${item.id}`:`5121260000${item.id}`,
							location:item.address,
							area:item.region
						})
					}
					
					
				});
				return deviceList
			},
			// 下滑到底部
			async more(){
				if(this.loaded){ return;}
				this.page = this.page +1;
				let url = !this.region?`/device/getdevice/${this.page}/${this.num}`: `/device/regiondevice/${this.region}/${this.page}/${this.num}`
			    var [err,res] =await this.$http.get(url);
				var list = res.data.data.list;
				this.deviceList = this.deviceList.concat(this.sort(list));
			},
			
		},
		async onLoad(e){
			
			if(e.areaId) this.region = e.areaId;
			uni.showLoading({
				title:"数据加载中..."
			})
			let url = !this.region?`/device/getdevice/${this.page}/${this.num}`:`/device/regiondevice/${this.region}/${this.page}/${this.num}`
			var [err,res] =await this.$http.get(url);
			var list = res.data.data.list;
			this.deviceList = this.sort(list);
			uni.hideLoading()
		},
		 onShow(e) {
			
		},
	}
</script>

<style>
	scroll-view{
		padding: 20rpx 30rpx;
		box-sizing: border-box;
	}
	.tips{
		height: 100%;
		color: #494B68;
	}
	.tips>image{
		width: 65%;
	}
	.cont {
		flex: 1;
		/* height:95px; */
		/* line-height: 95px; */
		padding: 0 15px;
		position: relative;
		font-size: 15px;
		background-color: #FFFFFF;
		margin: 10px 0 10px;
		z-index: 9999;
		border-color: #C9C9C9;
		border-width: 1px;
		border-style: solid;
		border-radius: 10rpx;
	}
	
	.uni-swipe_button-group{
		margin: 10px 0;
		border-top-right-radius: 10rpx;
		border-bottom-right-radius: 10rpx;
	}
</style>
