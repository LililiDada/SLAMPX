<template>
	<view>
		<!-- #ifndef APP-PLUS -->
		<uni-nav-bar  title="SLAMP" color="#ffffff" background-color="#6390C3" :border='false'/>
		<area-bar title="区域管理" @query='query'></area-bar>
		<!-- #endif -->
		
		
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<uni-swipe-action>
				<uni-swipe-action-item v-for="(item,index) in areaList"  :key="index" @change="swipeChange" @click="swipeClick($event,index)">
					<view class="cont u-f-ac u-f-jsb">
						<view class="u-f-ac" :style="{'color':item.check?'#6390C3':'#666666'}" @click="check(index)">
							<view class="icon iconfont icon-tuichu1" :style="{'color':item.check?'#6390C3':'#666666'}"></view>
							<text>{{item.regionname}}</text>
						</view>
						<view class="icon iconfont icon-gengduo" @click="areaContent(item.id)"></view>
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
				id:0,
				region_id:0,
				preindex : 0,
				options: [{
					text: '删除',  //滑动操作颜色
					style: {
						backgroundColor: 'rgb(255,58,49)'
					}
				}],
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
				return `height: ${Appheight}px;padding-top:10px;padding-bottom:10px`;
				// #endif
			}
		},
		methods: {
			// #ifndef APP-PLUS
			query(){
				this.alterRegion();
			},
			// #endif
			async alterRegion(){
				var that = this;
				var ok = false;
				var region_id,regionname;
				this.areaList.forEach(item=>{
					if(item.check){
						regionname = item.regionname
						region_id=item.id;
						ok = true;
						return;
					}
					if(ok) return;
					regionname = '未选择区域'
					region_id=0;
				})
				if(region_id == this.region_id){
					uni.showToast({
						title:'修改成功',
						duration:500
					})
					setTimeout(()=>{
						uni.navigateBack();
					},500)
					return;
				}
				var [err,res] = await this.$http.post('/device/bindregion',{
					id:that.id,
					region_id:region_id
				})
				if(res.statusCode == 200){
					var pages = getCurrentPages();
					var prepage = pages[pages.length - 2];
					console.log(this.preindex)
					console.log(prepage.$vm.deviceList[this.preindex])
					this.$set(prepage.$vm.deviceList[this.preindex].area,'regionname',regionname);
					this.$set(prepage.$vm.deviceList[this.preindex].area,'id',region_id);
					uni.showToast({
						title:'修改成功',
						duration:500
					})
					
				}else{
					uni.showToast({
						title:'修改失败',
						duration:500
					})
				}
				setTimeout(()=>{
					uni.navigateBack();
				},500)
			},
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
				uni.navigateTo({
					url:'../device-list/device-list?areaId=' + e
				})
			},
			
			check(e){
				this.areaList[e].check = !this.areaList[e].check;
				this.areaList.forEach((value,index)=>{
					if(index != e) value.check = false;
				})
			},
			add(){
				console.log('增加');
				this.$refs.popup.open();
			},
			// 添加区域
			increaseArea(e){
				this.areaList.forEach(item => {
					item.check = false
				});
				this.areaList.unshift({
					id:e.id,
					regionname:e.regionname,
					check:true,
				});
				this.$refs.popup.close();
			},
			// 封装
			regionList(list,id){
				let areaList =[]
				list.forEach((item,index)=>{
					if(item.id == id){
						item.check = true;
					}
					else{
						item.check = false;
					}
					areaList.push(item);
				})
				return areaList;
			},
			// 下滑到底部
			async more(){
				this.page = this.page +1;
			    let url = `/region/getregion/${this.page}`;
			    var [err,res] =await this.$http.get(url,this.region_id );
				
			}
		},
		async onLoad(e) {
			this.id = e.id;
			this.region_id = e.region_id;
			this.preindex = e.index;
			let url = `/region/getregion/${this.page}`;
			var [err,res] =await this.$http.get(url);
			this.areaList = this.regionList(res.data.data.list,e.region_id)
		},
		// 监听原生标题导航按钮点击事件
		onNavigationBarButtonTap(e){
			console.log(e);
			switch (e.index){
				case 0:
					this.alterRegion();
				break;
			}
		},
	}
</script>

<style>
	scroll-view{
		padding: 70rpx 30rpx 20rpx;
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
		color: #C0C0C0;
		width: 32rpx;
	}
	.cont>view:nth-of-type(1){
		flex: 1;
	}
	.cont>view>.icon{
		width: 58rpx;
		font-size: 48rpx;
		color: #666666;
	}
	.cont>text{
		flex: 1;
		padding-left: 15upx;
	}
</style>
