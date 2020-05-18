<template>
	<view class="device">
		<view class="u-f-ac">
			<view class="icon iconfont icon-shebei"></view>
			<text>{{item.deviceId}}</text>
		</view>
		<view class="u-f-ac" style="align-items: baseline;">
			<view class="icon iconfont icon-quyu"></view>
			<text>{{item.location}}</text>
		</view>
		<view class="u-f-ajc">
			<view class="icon iconfont icon-quyu1"></view>
			<text>{{item.area.regionname}}</text>
			<view class="icon iconfont icon-bianji alter" @click="alterArea"></view>
		</view>
	</view>
</template>

<script>
	export default{
		props:{
			item:Object
		},
		methods:{
			alterArea(){
				this.$emit('alterArea')
			}
		}
	}
</script>

<style scoped>
	.device{
		width: 80vw;
		margin: 14px 0 10px;
	}
	.device>view>view:nth-of-type(1){
		color: #6390C3;
		font-size: 30rpx;
		width: 40rpx;
		margin-right: 10rpx;
	}
	.device>view{
		margin-bottom: 10px;
	}
	.device text{
		color: #494B68;
		flex:1
	}
	.alter{
		color: #999999;
		font-size: 40rpx;
		width: 40rpx;
	}
</style>
