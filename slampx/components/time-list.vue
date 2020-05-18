<template>
	<view class="axis">
		<view class="pointer"></view>
		<!-- <view class="line" v-if="index < length"></view> -->
		<view class="info">
			<text>时间段:{{item.start_time}}</text>
			<text>:{{item.start_min}}-</text>
			<text>{{item.end_time}}</text>
			<text>:{{item.end_min}}</text>
			<text style="margin-left: 10rpx;">亮度:{{item.light}}</text>
		</view>
	</view>
</template>

<script>
	export default{
		props:{
			item:Object,
			length:Number,
			index:Number
		}
	}
</script>

<style scoped>
	.axis{
		/* margin:0rpx 0 0rpx 20rpx; */
		padding-bottom: 30rpx;
		padding-left: 20rpx;
		display: flex;
		align-items: center;
		/* position: relative; */
		color: #494B68;
		font-size: 30rpx;
	}
	.pointer{
		display: inline-block;
		width: 28rpx;
		height: 28rpx;
		/* border-radius: 50%; */
		background-color: #788bf4;
		overflow: hidden;
		z-index: 10;
		transform:rotate(45deg)
	}
	.line{
		position: absolute;
		top: 10rpx;
		left: 10rpx;
		width: 4rpx;
		height: 100%;
		background-color: #789D9F;
	}
	.info{
		margin-left: 20rpx;
		flex: 1;
	}
	.info>text:nth-of-type(2){
		flex: 1;
	}
	.info>text:nth-of-type(1){
		display: inline-block;
		/* width: 300rpx; */
	}
</style>
