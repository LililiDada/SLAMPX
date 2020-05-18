<template>
	<view class="switch">
		<!-- :style="[{'padding-top': modeId? padding+'px':''}]" -->
		<view class="u-f-ac">
			<view class="line"></view>
			<text>{{mode}}</text>
		</view>
		<view v-if="modeId==0" class="tip u-f u-f-ac">
			<view class="pointer"></view>
			<text style="word-wrap: break-word;">自适应模式下路灯会自动调至适宜亮度</text>
		</view>
		<view class="record u-f u-f-column" v-if="modeId!=0">
			<block v-for="(item,index) in switchList" :key="index">
				<time-list :item="item" :index="index" :length="switchList.length-1"></time-list>
			</block>
		</view>
	</view>
</template>

<script>
	import timeList from '../time-list.vue';
	export default{
		components:{
			timeList,
		},
		props:{
			modeId:Number,
			mode:String,
			switchList:Array,
		},
		data(){
			return{
				
				padding:uni.upx2px(50),
			}
		}
	}
</script>

<style scoped>
	.switch{
		background-color: #FFFFFF;
		padding: 45rpx 50rpx 30rpx;
		border-radius: 10rpx;
	}
	.switch>view:nth-of-type(1){
		margin-bottom: 30rpx;
	}
	.line{
		width: 10rpx;
		height: 35rpx;
		background-color: #ba75e2;
		margin-right: 10rpx;
	}
	.tip{
		color: #494B68;
		margin-bottom: 20rpx;
		margin-top: 20rpx;
		align-items: baseline;
	}
	.pointer{
		display: inline-block;
		width: 28rpx;
		height: 28rpx;
		/* border-radius: 50%; */
		margin-right: 20rpx;
		margin-left: 20rpx;
		background-color: #788bf4;
		overflow: hidden;
		z-index: 10;
		transform:rotate(45deg)
	}
	.record{
		margin-top: 20rpx;
	}
</style>
