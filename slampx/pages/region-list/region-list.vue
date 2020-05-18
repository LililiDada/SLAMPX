<template>
	<view>
		<!-- #ifndef APP-PLUS -->
		<uni-nav-bar  title="SLAMP" color="#ffffff" background-color="#6390C3" :border='false'/>
		<area-bar title="正常模式管理" @query='query'></area-bar>
		<!-- #endif -->
		
		
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<uni-swipe-action>
				<uni-swipe-action-item v-for="(item,index) in brightList" :options="options" :key="index" @change="swipeChange" @click="swipeClick($event,index,item.id)">
					<view class="cont u-f-ac" @click="check(index)">
						<view class="icon iconfont icon-xuanzhong1" :style="{'color':item.check?'#6390C3':'#666666'}"></view>
						<view :style="{'color':item.check?'#6390C3':'#666666'}">
							<text>时间段:{{item.list[0].start_time}}</text>
							<text>:{{item.list[0].start_min}}-</text>
							<text>{{item.list[0].end_time}}</text>
							<text>:{{item.list[0].end_min}}</text>
							<text style="margin-left: 10rpx;">亮度:{{item.list[0].light}}</text>
						</view>
					</view>
				</uni-swipe-action-item>
			</uni-swipe-action>
			<view style="text-align: center;margin-top: 10px;" v-if="loaded && brightList.length>13">数据已加载完毕~~</view>	
		</scroll-view>
		
		<!-- 添加按钮 -->
		<add-button @add='add'></add-button>
		
		<!-- 修改弹出层 -->
		<uni-popup ref='alterpopup' type="center">
			<add-scene @close='alterclose' @increaseScene='alterScene' :start="start" :end="end" :inputValue='inputValue' :sceneId='sceneId' v-if="hackReset" :add="false"></add-scene>
		</uni-popup>
		
		<!-- 添加弹出层 -->
		<uni-popup ref='popup' type="center">
			<add-scene @close='close' @increaseScene='increaseScene'  ref="addScene" v-if='hackReset' :plain='true'></add-scene>
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
	import addScene from "../../components/scene/add-scene.vue";
	
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
			addScene
		},
		data() {
			return {
				hackReset:true,
				topHeight:55,
				sceneId:0,
				start:[1,0],
				end:[0,4],
				inputValue:10,
				modeId:0,
				alterIndex:0,  //修改brightListd的第几个
				page:1,
				num:15,
				loaded:false,   //判断数据是否加载完毕
				options: [{
					text: '修改',  //滑动操作颜色
					style: {
						backgroundColor: '#6390C3'
					}
				},{
					text: '删除',  //滑动操作颜色
					style: {
						backgroundColor: 'rgb(255,58,49)'
					}
				}],
				brightList:[]
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
				var pages = getCurrentPages();
				var prepage = pages[pages.length - 2];
				if(prepage.route == "pages/record/record"){
					this.deviceBind(prepage);
					return;
				}
				this.bindScene();
			},
			// #endif
			// 单个管理
			async deviceBind(prepage){
				var exit = false;
				var that = this;
				this.brightList.forEach(async function(item,value){
					if(item.check == true){
						var unAlter = false
						exit = true;
						if(that.modeId == item.id){
							prepage.$vm.spectacleList=item.list
							prepage.$vm.plain_id=item.id;
							unAlter = true;
							return uni.navigateBack();
						}
						if(!unAlter){
							console.log("执行了")
							var [err,res] = await that.$http.post('/device/bindplain',{
								id:prepage.data.des_id,
								plain_id:item.id
							})
							console.log('res',res)
							if(err){
								uni.showToast({title:"保存失败",icon:"none"});
								return;
							}
							prepage.$vm.spectacleList=item.list
							prepage.$vm.plain_id=item.id
							uni.showToast({title:"成功保存",icon:"success"});
						}
					    uni.navigateBack();
					}
					
				})
				console.log('exit',exit)
				if(!exit){
					var [errs,ress] = await that.$http.post('/device/bindplain',{
						id:prepage.data.des_id,
						plain_id:0
					})
					if(errs){
						uni.showToast({title:"保存失败",icon:"none"});
						return;
					}
					console.log('ress',ress);
					prepage.$vm.spectacleList=[]
					prepage.$vm.plain_id= 0
					uni.navigateBack();
				}
				
			},
			
			
			// 批量管理
			async bindScene(){
				var exit = false;
				var that = this;
				this.brightList.forEach( async function(item,value){
					if(item.check == true){
						exit = true;
						if(that.modeId == item.id){
							await uni.setStorageSync('user_plain',item.list);
							await uni.showToast({title:"成功保存",icon:"success"});
							return uni.navigateBack()
						}
						var [err,res] = await that.$http.post('/user/bindplain',{
							plain_id:item.id
						})
						console.log('res',res)
						if(err){
							uni.showToast({title:"保存失败",icon:"none"});
							return;
						}
						await uni.setStorageSync('user_plain',item.list);
						await uni.setStorageSync('user_plain_id',item.id);
					    uni.showToast({title:"成功保存",icon:"success"});
				        uni.navigateBack();
					}
				})
				if(exit){ return;}
				var [errs,ress] = await this.$http.post('/user/bindplain',{
					plain_id:0
				})
				if(errs){
					uni.showToast({title:"保存失败",icon:"none"});
					return;
				}
				console.log('ress',ress);
				uni.setStorageSync('user_plain',[]);
				uni.setStorageSync('user_plain_id',0);
				uni.navigateBack();
			},
			
			navBack(){
				var pages = getCurrentPages();
				 var prepage = pages[pages.length - 2]; //上一个页面
				 var data
				 this.brightList.forEach(value=>{
					 if(value.check) data = value
				 })
				 prepage.$vm.spectacleList.push(data);
				 uni.navigateBack();
				console.log(prepage.$vm.spectacleList)
			},
			swipeChange(e) {
				console.log('返回：', e);
			},
			swipeClick(e,index,id) {
				var that = this;
				let {
					content
				} = e
				if (content.text === '删除') {
					uni.showModal({
						title: '提示',
						content: '是否删除',
						success: async function (res) {
							if (res.confirm) {
								console.log(id)
								let [err,res] =await that.$http.post('/mode/delete',{
									id:id
								});
								if(res.statusCode == 200 ){
									if (index > -1) {  
									    that.brightList.splice(index,1)
									}
									uni.showToast({title:"删除成功,请按保存后退出",icon:'none'});
									return;
								}
								uni.showToast({title:"删除失败！",icon:'none'});
								// this.brightList.splice(index, 1)
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
				}else if(content.text === '修改'){
					this.hackReset = false;
					var that = this;
					that.start[0]=parseInt(that.brightList[index].list[0].start_time);
					that.start[1]=parseInt(that.brightList[index].list[0].start_min/10);
					that.end[0]=parseInt(that.brightList[index].list[0].end_time);
					that.end[1]=parseInt(that.brightList[index].list[0].end_min/10);
					that.inputValue = that.brightList[index].list[0].light;
					that.sceneId = that.brightList[index].id;
					this.alterIndex = index;
					console.log(that.start)
					// 重新创建子标签
					this.$nextTick(() => {
					  this.hackReset = true;
					});
					this.$refs.alterpopup.open();
				}
			
			},
			
			check(e){
				this.brightList[e].check = !this.brightList[e].check;
				this.brightList.forEach((value,index)=>{
					if(index != e) value.check = false;
				})
			},
			add(){
				this.hackReset = false;
				this.$nextTick(() => {
				  this.hackReset = true;
				});
				this.$refs.popup.open();
			},
			// 修改普通场景
			async alterScene(e){
				var data={
					light:e.light,
					start_time:e.start_time<10?`0${e.start_time}`:e.start_time,
					start_min:e.start_min<10?`0${e.start_min}`:e.start_min,
					end_time:e.end_time<10?`0${e.end_time}`:e.end_time,
					end_min:e.end_min<10?`0${e.end_min}`:e.end_min,
				}
				var [err,res] =await this.$http.post('/adjust/alter',{
					alterlist:JSON.stringify(data),
					id:this.brightList[this.alterIndex].list[0].id
				})
				console.log(err,res)
				this.$set(this.brightList[this.alterIndex].list,0,data,true);
				uni.showToast({title:'修改成功,请按保存后退出',icon:"none"});
				console.log(data)
				this.$refs.alterpopup.close();
			},
			// 关闭修改
			alterclose(){
				this.$refs.alterpopup.close();
				
			},
			// 增加普通场景
			increaseScene(e){
				this.brightList.forEach(item=>{
					item.check = false;
				})
				console.log(e)
				var data = {
					id:e.id,
					check:true,
					list:[{
						start_time:e.start_time,
						start_min:e.start_min,
						light:e.light,
						end_time:e.end_time,
						end_min:e.end_min
					}]
				}
				this.brightList.unshift(data);
				console.log(this.bright)
				this.$refs.popup.close();
			},
			// 关闭增加弹幕
			close(){
				this.$refs.popup.close();
			},
			// 下滑到底部加载更多
			async more(){
				if(this.loaded){ return;}
				let page = this.page ++;
			    let url = `/mode/3/getlist/${this.page}/${this.num}`;
			    var [err,res] =await this.$http.get(url);
				this.brightList = this.brightList.concat(this.format(res.data.data.list,this.modeId)); 
			},
			
			// 获取数据格式
			format(list,id){
				console.log(this.loaded);
				if(list.length<15){
					 this.loaded = true;
					 uni.showToast({
					 	title:"数据已加载完毕~~",
					 	icon:"none"
					 })
				}
				var brightList=[]
				list.forEach((item,index)=>{
					var data={}
					if(item.id == id){data.check = true;}
					else{data.check = false;}
					data.id = item.id;
					data.list = item.adjust ;
					brightList.push(data);
				})
				return brightList;
			}
		},
		async onLoad(e) {
			uni.showLoading({title:"加载中"})
			this.modeId = e.id;
			var url = `/mode/3/getlist/${this.page}/${this.num}`
			var [err,res] =await this.$http.get(url);
			this.brightList = this.format(res.data.data.list, e.id)
			console.log(this.brightList);
			uni.hideLoading();
			console.log(this.modeId,e.id);
		},
		// 监听原生标题导航按钮点击事件
		onNavigationBarButtonTap(e){
			console.log(e);
			switch (e.index){
				case 0:
					var pages = getCurrentPages();
					var prepage = pages[pages.length - 2];
					if(prepage.route == "pages/record/record"){
						this.deviceBind(prepage);
						return;
					}
					this.bindScene();
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
		width: 58rpx;
		font-size: 48rpx;
		color: #666666;
	}
	.cont>text{
		flex: 1;
		padding-left: 15upx;
	}
</style>
