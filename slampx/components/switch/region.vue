<template>
	<view>
		<view class="allcheck u-f-ac">
			<view class="icon iconfont icon-pintu"></view>
			<text>区域~~~全选</text>
			<view class="icon iconfont icon-gou  u-f-ajc" @tap="bindAllCheck" :style="{'background-color':allCheck?'#788bf4':'#BDBDBD'}"></view>
		</view>
		<scroll-view class="region" scroll-y="true">
			<view class="region-center u-f-jsa u-f">
				<block v-for="(item,index) in regionList" :key="index">
					<view class="region-one" @click="selectLoc(index)">
						<view class="u-f-ajc region-loc">{{item.regionname}}</view>
						<view class="icon iconfont icon-gou  u-f-ajc " v-if="item.check"></view>
					</view>
				</block>
				<view v-if="regionList.length % 3 == 1" class="region-trans"></view>
				<view v-if="regionList.length % 3 != 0" class="region-trans"></view>				
			</view>
		</scroll-view>
	</view>
</template>

<script>

	export default{
		props:{
			regionList:Array,
			allCheck:Boolean
		},
		methods:{
			// 全选
			bindAllCheck(){
				this.$emit('checkAll', !this.allCheck)
			},
			// 选中某一个区域
			selectLoc(val){
				this.$emit('selectLoc', val)
			}
		}
	}
</script>

<style>
	/* 全选 */
	.allcheck{
		
	}
	.allcheck>view:nth-of-type(1){
		font-size: 60rpx;
		color: #8b67ff;
		margin-right: 15rpx;
		/* width: 10rpx;
		height: 40rpx;
		background-color: #789D9F;
		border-radius: 5rpx; */
	}
	.allcheck>view:nth-of-type(2){
		color: #FFFFFF;
		width: 50rpx;
		height: 50rpx;
		border-radius: 25%;
		margin-left: 10rpx;
		font-size: 35rpx;
	}
	/* 区域 */
	.region{
		height: 46vh;
		/* background: #FFFFFF; */
		
	}
	
	.region-center{
		width: 100%;
		box-sizing: border-box;
		padding-top: 30rpx;
		flex-wrap: wrap;
	}
	.region-loc{
		width: 150rpx;
		height: 130rpx;
		font-size: 30rpx;
		/* box-shadow: 0px 0px 10rpx 2rpx #C8C7CC; */
		border: 1rpx solid #C8C7CC;
		border-radius: 28rpx;
		/* background-color: #6390C3; */
		/* color: #FFFFFF; */
		margin-bottom: 40rpx;
		box-sizing: border-box;
		padding: 0 12rpx;
	}
	.region-trans{
		width: 150rpx;
		height: 130rpx;
		margin-bottom: 40rpx;
		background-color: transparent;
	}
	.region-one{
		position: relative;
	}
	.region-one>.icon{
		position: absolute;
		bottom: 31rpx;
		right: -13rpx;
		font-size: 35rpx;
		background-color: #788bf4;
		color: #FFFFFF;
		width: 50rpx;
		height: 50rpx;
		border-radius: 50%;
	}
</style>
