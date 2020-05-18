<template>
	<view>
		<!-- #ifndef APP-PLUS -->
		<uni-nav-bar  title="SLAMP" color="#ffffff" background-color="#6390C3" :border='false'/>
		<area-bar title="节能模式管理" @query='query'></area-bar>
		<!-- <view class="seat"></view> -->
		<!-- #endif -->
		
		
<!-- 		<scroll-view scroll-y="true" :style="getHeight">
			<uni-swipe-action>
				<uni-swipe-action-item v-for="(item,index) in brightList" :options="options" :key="index" @change="swipeChange($event)" @click="swipeClick($event,index)">
					<view class="cont u-f-ac">
						<view class="icon iconfont icon-xuanzhong1"></view>
						<view>
							<text>时间段:{{item.para}}</text>
							<text>亮度:{{item.bright}}</text>
						</view>
						<view class="icon iconfont icon-xuanzhong1"></view>
					</view>
				</uni-swipe-action-item>
			</uni-swipe-action>
		</scroll-view> -->
		
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more" :scroll-top="scrollTop" >
			<block v-for="(item,index) in brightList" :key="index">
				<view class="scene">
						<view class="delet icon iconfont icon-cha" @click="deletScene(index,item.id)"></view>
						<view class="scene-title u-f-ac">
							<view></view>
							<text>{{item.scene}}</text>
							<view class="icon iconfont icon-xuanzhong" :style="{'color':item.check?'#6390C3':'#666666'}" @tap='checkScene(index)'></view>
						</view>
						<scene-list :list="item.list" :aler="item.check" :option='option' @alterBright="alterBright($event,index)"></scene-list>
				</view>
			</block>
			<view style="text-align: center;" v-if="loaded && brightList.length>3">数据已加载完毕~~</view>	
		</scroll-view>
		
	
		
		
		<!-- 添加按钮 -->
		<add-button @add="add"></add-button>
		
		<!-- 添加弹出层 -->
		<!-- <uni-popup ref='popup' type="center">
			<add-scene @close='close' @increaseScene='increaseScene' ref="addScene"></add-scene>
		</uni-popup> -->
		
		<!-- 修改弹出层 -->
		<uni-popup ref='alterpopup' type="center">
			<add-scene @close='alterclose' @increaseScene='alterScene' :start="start" :end="end" :inputValue='inputValue' :sceneId='sceneId' v-if="hackReset" :add="false" :left='true' :right='true'></add-scene>
		</uni-popup>
	</view>
</template>

<script>
	// #ifndef APP-PLUS
	import uniNavBar from "../../components/area/uni-nav-bar.vue";
	import areaBar from "../../components/area/area-bar.vue";
	// #endif

	import addButton from "../../components/area/add-button.vue";
	import uniPopup from "@/components/uni-popup.vue";
	import addScene from "../../components/scene/add-scene.vue";
	import sceneList from "../../components/scene/scene-list.vue"
	
	export default {
		components:{
			// #ifndef APP-PLUS
			uniNavBar,
			areaBar,
			// #endif
			addButton,
			uniPopup,
			addScene,
			sceneList
		},
		data() {
			return {
				unclick:true,
				hackReset:true,
				sceneId:0,  //修改的时间段ID
				alterValue:0,  //修改brightListd的list的第几个
				alterIndex:0, //修改brightListd的第几个
				page:1,
				num:5,
				loaded:false,   //判断数据是否加载完毕
				modeId:0,
				start:[1,0],
				end:[0,4],
				inputValue:10,
				topHeight:55,
				option: [{
					text: '修改',  //滑动操作颜色
					style: {
						backgroundColor: '#6390C3'
					}
				}],
				lightList:[],
				brightList:[],
				scrollTop:0,       //滚动条位置
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
						exit = true;
						if(that.modeId == item.id){
							prepage.$vm.brightList=item.list
							prepage.$vm.scene_id=item.id
							return uni.navigateBack()
						}
						var [err,res] = await that.$http.post('/device/bindscene',{
							id:prepage.data.des_id,
							scene_id:item.id
						})
						console.log('res',res)
						if(err){
							uni.showToast({title:"保存失败",icon:"none"});
							return;
						}
						prepage.$vm.brightList=item.list
						prepage.$vm.scene_id=item.id
						uni.showToast({title:"成功保存",icon:"success"});
						uni.navigateBack();
					}
					
				})
				if(exit){ return;}
				var [errs,ress] = await that.$http.post('/device/bindscene',{
					id:prepage.data.des_id,
					scene_id:0
				})
				if(errs){
					uni.showToast({title:"保存失败",icon:"none"});
					return;
				}
				console.log('ress',ress);
				prepage.$vm.brightList=[]
				prepage.$vm.scene_id= 0
				uni.navigateBack();
			},
			
			
			// 批量管理
			async bindScene(){
				var exit = false;
				var that = this;
				this.brightList.forEach( async function(item,value){
					if(item.check == true){
						exit = true;
						if(that.modeId == item.id){
							await uni.setStorageSync('user_scene',item.list);
							await uni.showToast({title:"成功保存",icon:"success"});
							
							return uni.navigateBack()
						}
						var [err,res] = await that.$http.post('/user/bindscene',{
							scene_id:item.id
						})
						console.log('res',res)
						if(err){
							uni.showToast({title:"保存失败",icon:"none"});
							return;
						}
						await uni.setStorageSync('user_scene',item.list);
						await uni.setStorageSync('user_scene_id',item.id);
					    uni.showToast({title:"成功保存",icon:"success"});
				        return uni.navigateBack();
					}
				})
				if(exit){ return;}
				var [errs,ress] = await this.$http.post('/user/bindscene',{
					scene_id:0
				})
				if(errs){
					uni.showToast({title:"保存失败",icon:"none"});
					return;
				}
				console.log('ress',ress);
				uni.setStorageSync('user_scene',[]);
				uni.setStorageSync('user_scene_id',0);
				uni.navigateBack();
			},
			
			
			// 删除场景
			deletScene(e,id){
				var that = this;
				console.log(this.brightList[e])
				if(this.brightList[e].check){
					uni.showToast({
						icon:'none',
						title:"该情景被选中,不可删除"
					})
					return;
				}
				uni.showModal({
					title: '提示',
					content: '是否删除',
					success:async function (res){
						if (res.confirm) {
							let [err,res] =await that.$http.post('/mode/delete',{
								id:id
							});
							if(res.statusCode == 200 ){
								if (e > -1) {  
								    that.brightList.splice(e,1)
								}
								uni.showToast({title:"删除成功,请按保存后退出",icon:'none'});
								return;
							}
							uni.showToast({title:"删除失败！",icon:'none'});
							
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
						
					}
				});
				
			},

			areaContent(e){
				console.log(e)
			},
			
			check(e){
				this.brightList[e].check = !this.brightList[e].check;  
			},
			add(){
				uni.navigateTo({
					url:"../add-scene/add-scene"
				})
			},
			alterclose(){
				this.$refs.alterpopup.close();
			},
			// 添加场景
			increaseScene(){
				this.$refs.popup.close();
			},
			
			async alterScene(e){
				console.log(e.id,this.alterIndex,this.alterValue)
				var data = {light:e.light}
				var [err,res] =await this.$http.post('/adjust/alter',{
					id:e.id,
					alterlist:JSON.stringify(data)
				})
				if(err){
					uni.showToast({title:'修改失败！！',icon:'none'});
					return;
				}
				uni.showToast({title:'修改成功,请按保存后退出',icon:"none"});
				this.brightList[this.alterIndex].list[this.alterValue].light = e.light;
				this.$refs.alterpopup.close();
			},
			
			
			// 修改模式
			checkScene(e){
				// if(this.brightList[e].check) return;
				this.brightList[e].check = !this.brightList[e].check;
				this.brightList.forEach((value,index)=>{
					if(index != e) value.check = false;
				})
			},
			// 修改光照
			alterBright(value,index){
				this.hackReset = false;
				var that = this;
				console.log(value,index)
				that.start[0]=parseInt(that.brightList[index].list[value].start_time);
				that.start[1]=parseInt(that.brightList[index].list[value].start_min/10);
				that.end[0]=parseInt(that.brightList[index].list[value].end_time);
				that.end[1]=parseInt(that.brightList[index].list[value].end_min/10);
				that.inputValue = that.brightList[index].list[value].light;
				that.sceneId = that.brightList[index].list[value].id;
				this.alterIndex = index;
				this.alterValue = value;
				console.log(that.end)
				// 重新创建子标签
				this.$nextTick(() => {
				  this.hackReset = true;
				});
				this.$refs.alterpopup.open();
			},
			
			// 下滑到底部加载更多
			async more(){
				if(this.loaded){ return;}
				uni.showLoading({title:"正在加载中..."})
				this.page = this.page +1;
			    let url = `/mode/2/getlist/${this.page}/${this.num}`;
			    var [err,res] =await this.$http.get(url);
				this.brightList = this.brightList.concat(this.format(res.data.data.list,this.modeId)); 
				uni.hideLoading();
			},
			
			// 获取数据格式
			format(list,id){
				console.log(this.brightList.length);
				if(list.length<4) {
					this.loaded = true;
					uni.showToast({
						title:"数据已加载完毕~~",
						icon:"none"
					})
				}
				var brightList=[]
				
				list.forEach((item,index)=>{
					var data={}
					if(item.id == id && this.brightList.findIndex((value)=>value.id==id)){data.check = true;}
					else{data.check = false;}
					data.id = item.id;
					data.scene = `场景${this.brightList.length+index+1}`;
					data.list =  item.adjust;
					brightList.push(data);
				})
				return brightList;
			}
		},
		async onLoad(e) {
			uni.showLoading({title:"加载中"})
			this.modeId = e.id;
			var url = `/mode/2/getlist/${this.page}/${this.num}`
			var [err,res] =await this.$http.get(url);
			this.brightList = this.format(res.data.data.list,e.id)
			uni.hideLoading();
			console.log(this.modeId);
		},
		onShow() {
			// 用户添加场景后可滑动到最底部(即新添加的场景位置)
			// // #ifndef APP-PLUS
			// var scrollheight = uni.getSystemInfoSync().windowHeight -uni.getSystemInfoSync().statusBarHeight - 69;
			// // #endif
			
			// // #ifdef APP-PLUS
			// var scrollheight = uni.getSystemInfoSync().windowHeight;
			// // #endif
			// const query = uni.createSelectorQuery().in(this);
			// let   sreneH = query.selectAll('.scene');
			// this.$nextTick(()=>{
			// 	sreneH.fields({
			// 	  size: true
			// 	}, data => {
			// 		if (data) {
			// 			var itemH = 0;
			// 			for (let i = 0; i < data.length; i++) {
			// 				itemH += data[i].height;
			// 			}
			// 			this.scrollTop = itemH > scrollheight ? itemH : 0;
			// 		}
			// 	}).exec();
			// })
		},
		// 监听原生标题导航按钮点击事件
		onNavigationBarButtonTap(e){
			console.log(e);
			switch (e.index){
				case 0:
					console.log('确定')
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
	/* #ifndef APP-PLUS */
		/* .seat{
			width: 100vh;
			height: 25px;
		} */
	/* #endif */
	scroll-view{
		padding: 90rpx 30rpx 20rpx;
		box-sizing: border-box;
	}
	
	.scene{
		padding: 20rpx;
		margin-bottom:  50rpx;
		border: 1px solid #C9C9C9;
		border-radius: 20rpx;
		position: relative;
	}
	.delet{
		position: absolute;
		right: -2rpx;
		bottom: -25rpx;
		font-size: 25px;
		background-color: #FFFFFF;
		color: #666666;
	}
	.scene-title{
		
		margin:0 15px 15rpx;
	}
	.scene-title>view:nth-of-type(1){
		width: 5px;
		height: 35rpx;
		background-color: #6390C3;
		border-radius: 5rpx;
		margin-right: 10rpx;
	}
	.scene-title>.icon{
		font-size: 60rpx;
		margin-left: 20rpx;
		color: #666666;
	}

</style>
