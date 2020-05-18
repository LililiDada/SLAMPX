<template>
	<view>
		<!-- #ifndef APP-PLUS -->
		<uni-nav-bar  title="SLAMP" color="#ffffff" background-color="#6390C3" :border='false'/>
		<area-bar title="区域管理" @query='query'></area-bar>
		<!-- #endif -->
		
		
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<uni-swipe-action>
				<uni-swipe-action-item v-for="(item,index) in areaList" :options="options" :key="index" @change="swipeChange" @click="swipeClick($event,index)">
					<view class="cont u-f-ac">
						<view class="icon iconfont icon-tuichu1" :style="{'color':item.check?'#6390C3':'#666666'}" @click="check(index)"></view>
						<text @click="areaContent(item.id)">{{item.content}}</text>
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
	// #ifndef APP-PLUS
	import uniNavBar from "../../components/area/uni-nav-bar.vue";
	import areaBar from "../../components/area/area-bar.vue";
	// #endif
	import uniSwipeAction from '@/components/uni-swipe-action/uni-swipe-action.vue';
	import uniSwipeActionItem from '@/components/uni-swipe-action-item/uni-swipe-action-item.vue';
	import addButton from "../../components/area/add-button.vue";
	import uniPopup from "@/components/uni-popup.vue";
	import addArea from "../../components/area/add-area.vue";
	
	export default {
		components:{
			// #ifndef APP-PLUS
			uniNavBar,
			areaBar,
			// #endif
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
				pattern: {  // 添加按钮样式
					color: '#7A7E83',
					backgroundColor: '#fff',
					selectedColor: '#6390C3',
					buttonColor: '#6390C3'
				},
				horizontal: 'left',
				vertical: 'bottom',
				direction: 'horizontal',
				areaList: [{
					
					id: 10,
					content: '南区',
					check:false
				}, {
					id: 11,
					content: '北区',
					check:false
				}, {
					id: 12,
					content: '西区',
					check:false
				}]
			}
		},
		computed: {
			getHeight() {
				// #ifndef APP-PLUS
				let height = uni.getSystemInfoSync().windowHeight -uni.getSystemInfoSync().statusBarHeight - 69;
				return `height: ${height}px;`;
				// #endif
				
				// #ifdef APP-PLUS
				let Appheight = uni.getSystemInfoSync().windowHeight;
				;
				return `height: ${Appheight}px;`;
				// #endif
			}
		},
		methods: {
			// #ifndef APP-PLUS
			query(){
				console.log('确定');
			},
			// #endif
			
			swipeChange(e) {
				console.log('返回：', e);
			},
			swipeClick(e, index) {
				let {
					content
				} = e
				if (content.text === '删除') {
					uni.showModal({
						title: '提示',
						content: '是否删除',
						success: (res) => {
							if (res.confirm) {
								this.areaList.splice(index, 1)
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
				} 
			
			},
			areaContent(e){
				console.log(e)
			},
			
			check(e){
				this.areaList[e].check = !this.areaList[e].check;
				this.areaList.forEach((value,index)=>{
					if(index != e) value.check = false;
				})
			},
			add(){
				this.$refs.popup.open();
			},
			// 添加区域
			increaseArea(e){
				console.log(e)
				this.$refs.popup.close();
			},
			// 获取列表
			getList(page){
				let url = `region/getregion/${page}`
				var [err,res] = this.$http.get(url)
				return res;
			},
			// 下滑到底部
			async more(){
				if(this.loaded){ return;}
				this.page = this.page +1;
			    let list = await this.getList(this.page);
				
			}
		},
		async onLoad() {
			let list = await this.getList(this.page);
			console.log(list);
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
		padding: 20rpx 30rpx;
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
	}
	.cont>.icon{
		width: 50rpx;
		font-size: 40rpx;
		color: #666666;
	}
	.cont>text{
		flex: 1;
		padding-left: 15upx;
	}
</style>
