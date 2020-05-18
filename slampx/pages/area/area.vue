<template>
	<view>
		
		
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<uni-swipe-action>
				<uni-swipe-action-item v-for="(item,index) in areaList" :options="options" :key="index" @change="swipeChange" @click="swipeClick($event,index,item.id)">
					<view class="cont u-f-ac">
						<view class="icon iconfont icon-tuichu1"></view>
						<text @click="areaContent(item.id)">{{item.regionname}}</text>
					</view>
				</uni-swipe-action-item>
			</uni-swipe-action>
		</scroll-view>
		
		
		<!-- 添加按钮 -->
		<add-button @add='add'></add-button>
		
		<!-- 添加弹出层 -->
		<uni-popup ref='popup' type="bottom">
			<add-area @increaseArea='increaseArea'></add-area>
		</uni-popup>
	</view>
</template>

<script>
	import uniSwipeAction from '@/components/uni-swipe-action/uni-swipe-action.vue';
	import uniSwipeActionItem from '@/components/uni-swipe-action-item/uni-swipe-action-item.vue';
	import addButton from "../../components/area/add-button.vue";
	import uniPopup from "@/components/uni-popup.vue";
	import addArea from "../../components/area/add-area.vue";
	
	export default {
		components:{
			uniSwipeAction,
			uniSwipeActionItem,
			addButton,
			uniPopup,
			addArea
		},
		data() {
			return {
				topHeight:55,
				page:1,
				options: [{
					text: '删除',  //滑动操作颜色
					style: {
						backgroundColor: 'rgb(255,58,49)'
					}
				}],
				areaList: []
			}
		},
		computed: {
			getHeight() {
			
				let Appheight = uni.getSystemInfoSync().windowHeight;
				return `height: ${Appheight}px;`;
			}
		},
		methods: {
			
			swipeChange(e) {
				console.log(e)
			},
			async swipeClick(e, index,id) {
				var that = this; 
				let {content} = e
				if (content.text === '删除') {
					uni.showModal({
						title: '提示',
						content: '是否删除',
						success:async function(res){
							if (res.confirm) {
								let [err,res] =await that.$http.post('/region/deleteregion',{
									id:id
								});
								if(res.statusCode == 200 ){
									if (index > -1) {  
									    that.areaList.splice(index, 1);
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
			areaContent(e){
				console.log(e)
				uni.navigateTo({
					url:'../device-list/device-list?areaId=' + e
				})
			},
			add(){
				this.$refs.popup.open();
			},
			// 添加区域
			increaseArea(e){
				this.areaList.unshift({
					id:e.id,
					regionname:e.regionname
				});
				this.$refs.popup.close();
			},
			// 下滑到底部
			async more(){
				this.page = this.page ++;
			    let url = `/region/getregion/${page}`;
			    var [err,res] =await this.$http.get(url);
				this.areaList.push(res.data.data.list); 
			}
		},
		async onShow() {
			let url = `/region/getregion/${this.page}`;
			var [err,res] =await this.$http.get(url);
			this.areaList = res.data.data.list
		},
		// 监听原生标题导航按钮点击事件
		onNavigationBarButtonTap(e){
			console.log(e);
			switch (e.index){
				case 0:
					console.log('确定')
				break;
			}
		},
	}
</script>

<style>
	scroll-view{
		padding: 20rpx 30rpx 20rpx;
		box-sizing: border-box;
	}
	.cont {
		flex: 1;
		height: 60px;
		line-height: 60px;
		padding: 0 15px;
		position: relative;
		background-color: #fff;
		font-size: 15px;
		border-bottom-color: #C9C9C9;
		border-bottom-width: 1px;
		border-bottom-style: solid;
		color: #494B68;
	}
	
	.cont>.icon{
		width: 58rpx;
		font-size: 47rpx;
		color: #6390C3;
	}
	.cont>text{
		flex: 1;
		padding-left: 15upx;
	}
</style>
