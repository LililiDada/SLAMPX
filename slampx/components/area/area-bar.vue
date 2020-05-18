<template>
	<view>
		<view class="custom-nav u-f-ac u-f-jsb" :style="{'top':topHeight + 'px'}">
			<view class="icon iconfont icon-fanhui" @tap="back"></view>
			<view>{{title}}</view>
			<view @tap="query">保存</view>
		</view>
	</view>
</template>

<script>
	export default{
		props:{
			title:String
		},
		data(){
			return{
				topHeight:60
			}
		},
		methods:{
			back(){
				uni.navigateBack();
			},
			query(){
				this.$emit('query');
			}
			
		},
		created() {
			this.topHeight = uni.getSystemInfoSync().statusBarHeight +44
		}
	}
</script>

<style scoped>
	.custom-nav{
		padding: 0 30rpx 20rpx;
		font-size: 32rpx;
		background-color: #6390C3;
		height: 30px;
		color: #FFFFFF;
		box-sizing: border-box;
		width: 100vw;
		position: fixed;
		left: 0;
	}
	.custom-nav>view:nth-of-type(1){
		font-size: 34rpx;
		width: 130rpx;
	}
	.custom-nav>view:nth-of-type(3){
		width: 130rpx;
		text-align: right;
		font-size: 15px;
	}
	.custom-nav>view:nth-of-type(2){
		flex: 1;
		text-align: center;
	}
</style>
