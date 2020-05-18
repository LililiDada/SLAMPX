<template>
	<view>
		<view class="bright">
			<scene-list :list="brighList"   @alterBright="alterBright($event)" :option='option'></scene-list>
		</view>
		<view class="add icon iconfont icon-add" @click="add" v-if="brighList.length<3"></view>
		<button class="query" @click="query" v-else>确定</button>
		
		<!-- 添加弹出层 -->
		<uni-popup ref='popup' type="center">
			<add-scene @close='close' @increaseScene='increaseScene' :left="brighList.length>0?true:false" :start="start" ref="addScene" v-if='hackReset'></add-scene>
		</uni-popup>
	</view>
</template>

<script>
	import sceneList from "../../components/scene/scene-list.vue";
	import uniPopup from "@/components/uni-popup.vue";
	import addScene from "../../components/scene/add-scene.vue";
	export default {
		components:{
			sceneList,
			uniPopup,
			addScene
		},
		data() {
			return {
				hackReset:true,
				basedata:false,
				brighList:[],
				modelist:[],
				option:[{
					text: '删除',  //滑动操作颜色
					style: {
						backgroundColor: 'rgb(255,58,49)',
					}
				}],
				start:[0,0]
			}
		},
		methods: {
			add(){
				this.hackReset = false;
				this.$nextTick(() => {
				  this.hackReset = true;
				});
				this.$refs.popup.open();
			},
			close(){
				this.$refs.popup.close();
			},
			async alterBright(e){
				console.log(e)
				this.brighList.splice(e,1)
				this.modelist.splice(e,1)
				console.log(this.brighList)
				this.start[0]= this.brighList.length==0?0:parseInt(this.brighList[this.brighList.length-1].end_time);
				this.start[1]= this.brighList.length==0?0:parseInt(this.brighList[this.brighList.length-1].end_min/10);
			},
			increaseScene(e){
				this.brighList.push(e);
				this.modelist.push(e.id);
				console.log(e)
				this.start[0]=parseInt(e.end_time);
				this.start[1]=parseInt(e.end_min/10);
				this.$refs.popup.close();
			},
			async query(){
				uni.showLoading({
					title:'添加中'
				});
				var [err,res] = await this.$http.post('/mode/add',{
					pattern_id:2,
					modelist:JSON.stringify(this.modelist)
				});
				if(err){
					uni.showToast({title:'添加失败！'});
					return;
				}
				console.log(res);
				 this.basedata =  true;
				 var pages = getCurrentPages();
				 var prepage = pages[pages.length - 2]; //上一个页面
				 var length = prepage.$vm.brightList.length + 1
				 prepage.$vm.brightList.forEach(value=>{
					 value.check = false
				 })
				 var data = {
					 id:res.data.data.id,
					 scene:`场景${length}`,
					 list:this.brighList,
					 check:true
				 }
				 prepage.$vm.brightList.push(data);
				 uni.navigateBack();
				 uni.hideLoading();
				console.log("添加成功")
			}
		},
		onUnload(){
			if(this.brighList.length!=0 && !this.basedata){
				this.$http.post('/adjust/batchdelete',{
					adjustlist:JSON.stringify(this.modelist)
				})
			}
		}
	}
</script>

<style>
	.add{
		width: 85vw;
		height: 50px;
		line-height: 50px;
		text-align: center;
		color: #CCCCCC;
		border: 2px dashed #C9C9C9;
		margin: 30px auto 20px;
	}
	button[plain],button[type=default][plain]{
		border:1upx solid #C9C9C9 ;
		/* border: none; */
	}
	.query{
		width: 85vw;
		background-color: #6390C3;
		color: #FFFFFF;
		height: 55px;
		line-height: 55px;
		margin-top: 35px;
	}
	.bright{
		width: 84vw;
		margin: 20px auto 20px;
	}
	.cont{
		height: 60px !important;
		line-height: 60px !important;
		padding: 0  !important;
		font-size: 17px !important;
		border-bottom: 1px solid #C9C9C9;
	}
</style>
