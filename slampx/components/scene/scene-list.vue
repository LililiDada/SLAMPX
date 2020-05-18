<template>
	<view>
		<uni-swipe-action>
			<uni-swipe-action-item v-for="(item,index) in list" :options="option" :key="index" @change="swipeChange($event)" @click="swipeClick($event,index,item.id)">
				<view class="cont u-f-ac">
					<view class="icon iconfont icon-changjing"></view>
					<view>
						<text>时间段:{{item.start_time}}</text>
						<text>:{{item.start_min}}-</text>
						<text>{{item.end_time}}</text>
						<text>:{{item.end_min}}</text>
						<text style="margin-left: 10rpx;">亮度:{{item.light}}</text>
					</view>
				</view>
			</uni-swipe-action-item>
		</uni-swipe-action>
<!-- 		<block v-for="(item,index) in list" :key="index">
			<view class="cont u-f-ajc">
					<view class="icon iconfont icon-changjing"></view>
					<view>
						<text>时间段:{{item.para}}</text>
						<text>亮度:{{item.bright}}</text>
					</view>
					<view class="icon iconfont icon-bianji" v-if="aler" @click="alterBright(index)"></view>
			</view>
		</block> -->
	</view>
	
</template>

<script>
	import uniSwipeAction from '@/components/uni-swipe-action/uni-swipe-action.vue';
	import uniSwipeActionItem from '@/components/uni-swipe-action-item/uni-swipe-action-item.vue';
	export default{
		components:{
			uniSwipeAction,
			uniSwipeActionItem,
		},
		props:{
			list:Array,
			aler:{
				type:Boolean,
				default:true,
			},
			option:Array
		},
		data(){
			return{
				
			}
		},
		created() {
			console.log(this.list)
		},
		methods:{
			alterBright(e){
				this.$emit('alterBright',e)
			},
			swipeChange(e) {
				var that = this;
				console.log('返回：', e);
				
			},
			async swipeClick(e, index,id) {
				var that= this;
				// if(!this.aler){
				// 	uni.showToast({
				// 		icon:'none',
				// 		title:"该情景未选中,不可操作"
				// 	})
				// 	return;
				// }
				let { content } = e
				if (content.text === '删除') {
					var [err,data] = await this.$http.post('/adjust/delete',{
						id:Number(id)
					})
					if(err){
						uni.showToast({title:'添加失败！'});
						return;
					}
					uni.showToast({
						title:'删除成功',
						icon:'success'
					})
					that.$emit('alterBright',index)
				} else if(content.text === '修改'){
					that.$emit('alterBright',index)
				}
				// else if (content.text === '修改') {
				// 	console.log('修改')
				// 	that.start[0]=parseInt(that.brightList[index].para.substring(0,2));
				// 	that.start[1]=parseInt(that.brightList[index].para.substring(3,5));
				// 	that.end[0]=parseInt(that.brightList[index].para.substring(6,8));
				// 	that.end[1]=parseInt(that.brightList[index].para.substring(9,11));
				// 	that.inputValue = that.brightList[index].bright;
				// 	that.sceneId = that.brightList[index].id;
				// 	console.log(typeof(that.sceneId),'sceneId')
				// 	// 重新创建子标签
				// 	this.$nextTick(() => {
				// 	  this.hackReset = true;
				// 	});
				// 	this.$refs.alterpopup.open();
				// } 
			
			},
		}
	}
</script>

<style>
	.cont {
		flex: 1;
		height: 40px;
		line-height: 40px;
		padding: 0 15px;
		position: relative;
		background-color: #fff;
		font-size: 15px;
		/* border-bottom-color: #C9C9C9;
		border-bottom-width: 1px;
		border-bottom-style: solid; */
	}
	.cont>.icon{
		width: 45rpx;
		font-size: 40rpx;
		color: #494B68;
	}
	.cont>view:nth-of-type(1){
		font-size: 38rpx;
		color: #494B68;
	}
	.cont>view:nth-of-type(2){
		flex: 1;
		padding-left: 15upx;
		position: relative;
	}
	.cont>view:nth-of-type(2)>text:nth-of-type(2){
		flex: 1;
	}
	.cont>view:nth-of-type(2)>text:nth-of-type(1){
		display: inline-block;
		/* width: 300rpx; */
	}
</style>
