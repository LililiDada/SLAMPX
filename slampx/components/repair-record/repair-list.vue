<template>
	<view class="repair u-f">
		<view class="line" :style="[{background:color}]"></view>
		<view class="des u-f u-f-column">
			<text>{{item.fault}}</text>
			<text>{{item.address}}</text>
		</view>
		<view class="number" :style="[{background:color}]">{{item.number}}</view>
		<text class="time">{{item.time}}</text>
	</view>
</template>

<script>
	export default{
		props:{
			item:Object,
			color:String
		}
	}
</script>

<style>
	.repair{
		width: 100vw;
		padding: 40rpx 0rpx 40rpx 50rpx;
		border-top: 3rpx solid #eeeeee;
		border-bottom: 3rpx solid #eeeeee;
		position: relative;
		margin-top: 40rpx;
	}
	.line{
		width: 6rpx;
		height: 70rpx;
		/* background-color: #e11010; */
		margin-top: 30rpx;
	}
	.des{
		width: 55%;
		padding-left: 30rpx;
	}
	.des>text:nth-of-type(2){
		margin-top: 20rpx;
	}
	.number,.time{
		position: absolute;
		right: 110rpx;
	}
	.time{
		bottom:30rpx
	}
	.number{
		top:-15rpx;
		/* background-color: #E11010; */
		padding: 15rpx 15rpx;
		color: #FFFFFF;
		border-radius: 8rpx;
	}
	
</style>
