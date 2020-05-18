<template>
	<view class="button icon iconfont icon-add" @tap="add"></view>
</template>

<script>
	export default{
		methods:{
			add(){
				this.$emit('add')
			}
		}
	}
</script>

<style>
	.button{
		width: 100rpx;
		height: 100rpx;
		line-height: 100rpx;
		text-align: center;
		border-radius: 50%;
		background-color: #6390C3;
		color: #FFFFFF;
		font-size: 35px;
		position: fixed;
		left: 30rpx;
		bottom: 30px;
		box-shadow: 0 0 5px 5px rgba(0, 0, 0, 0.2);
	}
</style>
