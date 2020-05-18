<template>
	<view class="bright">
		<view class="mode u-f-ac">
			<view class="icon iconfont icon-moshi1"></view>
			<text>模式~~~</text>
			<mode-picker @picker="picker"></mode-picker>
			<view class="icon iconfont icon-down-trangle"></view>
		</view>
		<view v-if="list.length<1 && pickerIndex!=0" class="tip u-f u-f-ac">
			<view class="pointer"></view>
			<text>请点击右上角添加数据！</text>
		</view>
		<view v-if="pickerIndex==0" class="tip u-f u-f-ac">
			<view class="pointer"></view>
			<text style="word-wrap: break-word;">自适应模式下路灯会自动调至适宜亮度</text>
		</view>
		<view class="record u-f u-f-column" v-if="pickerIndex!=0">
			<block v-for="(item,index) in list" :key="index">
				<time-list :item="item" :index="index" :length="list.length-1"></time-list>
			</block>
		</view>
		<view class="icon iconfont icon-tianjia1" @click="navigaTo"  v-if="pickerIndex!=0"></view>
	</view>
</template>

<script>
	import timeList from "../../components/time-list.vue";
	import ModePicker from '../../components/picker.vue';
	
	export default{
		components:{
			timeList,
			ModePicker,
			
		},
		props:{
			brightList:Array,
			spectacleList:Array,
		},
		watch:{
			
			
			brightList:{
				immediate:true,
				handler(val){
					console.log(val)
					// #ifndef APP-PLUS
						this.list = val
					// #endif
					// #ifdef APP-PLUS
					   if(this.pickerIndex==1) {
					   	this.$nextTick(()=>{
					   		this.list = val;
					   	 })
					   }
					// #endif
					
					
				}
			},
			spectacleList:{
				immediate:true,
				handler(val){
					// #ifndef APP-PLUS
						this.list = val
					// #endif
					// #ifdef APP-PLUS
					   if(this.pickerIndex==2) {
					   	this.$nextTick(()=>{
					   		this.list = val;
					   	 })
					   }
					// #endif
				}
			},
		},
		data(){
			return{
				pickerIndex:0,
				list:this.brightList,
			}
		},
		methods:{
			// 跳转至模式页面
			navigaTo(){
				this.$emit('navigaTo')
			},
			picker(e){
				this.pickerIndex = parseInt(e)
				if(this.pickerIndex==1) {
					this.$nextTick(()=>{
						this.list = this.brightList;
					 })
				}
				if(this.pickerIndex==2){
					this.$nextTick(()=>{
						this.list = this.spectacleList;
					 })
				}
				this.$emit('picker',parseInt(e))
			}
		}
	}
</script>

<style>
	.bright{
		width: 77vw;
		position: relative;
		margin-bottom: 30rpx;
	}
	.bright>view.record{
		/* height: 20vh; */
		width: 70vw;
		margin-top: 10rpx;
		margin-bottom: 10rpx;
		margin-left: 10rpx;
		display: flex;
		justify-content: center;
	}
	.bright>.icon{
		position: absolute;
		right: 50rpx;
		top: 0rpx;
		font-size: 65rpx;
		font-weight: bold;
		color: #8b67ff;
	}
	.mode{
		width: 85vw;
		height: 70rpx;
		margin-bottom: 30rpx;
	}
	.tip{
		color: #494B68;
		width: 88%;
		margin-bottom: 40rpx;
		margin-left: 10rpx;
		align-items: baseline;
	}
	.pointer{
		display: inline-block;
		width: 28rpx;
		height: 28rpx;
		margin-left: 20rpx;
		margin-right: 15rpx;
		/* border-radius: 50%; */
		background-color: #788bf4;
		overflow: hidden;
		z-index: 10;
		transform:rotate(45deg)
	}
	.mode>view:nth-of-type(1){
		font-size: 60rpx;
		color: #8b67ff;
		margin-right: 15rpx;
		/* margin-left: 35rpx; */
		/* border-radius: 5rpx; */
	}
	.mode>view:nth-of-type(2){
		font-size: 40rpx;
		display: inline-block;
		color: #789D9F;
	}
</style>
