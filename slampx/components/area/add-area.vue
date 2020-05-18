<template>
	<!-- 添加区域 -->
	<view class="add-region">
		<view class="u-f-jsb u-f-ac">
			<view class="region-title">添加区域</view>
			<view class="icon iconfont icon-gou" @click="increaseArea"></view>
		</view>
		<input type="text" :value="region" @input='regionValue'/>
	</view>
</template>

<script>
	export default{
		data(){
			return{
				region:''
			}
		},
		methods:{
			// 输入框内容
			regionValue(e){
				this.region = e.detail.value;
			},
			// 添加区域
			async increaseArea(){
				let that = this;
				console.log(this.region)
				if(!this.region){
					uni.showToast({
						title: '区域不能为空',
						icon:'none'
					});
					return;
				};
				// 数据库操作
				let [err,res] = await this.$http.post('/region/addregion',{
					regionname:this.region
				})
				console.log(err,res);
				if(res.data.errorCode || res.data.errorCode ==40001){
					that.region = '';
					uni.showToast({
						title:"该区域已存在，请勿重复添加！",
						icon:'none'
					})
					return;
				}
				that.region = '';
				uni.showToast({
					title:'添加成功'
				})
				this.$emit('increaseArea',{
					id:res.data.data.id,
					regionname:res.data.data.regionname
				})
			},
		}
	}
</script>

<style scoped>
	.add-region{
		border-top-left-radius: 20rpx;
		border-top-right-radius: 20rpx;
		padding:50rpx  50rpx 60rpx;
		background-color: #FFFFFF;
		color: #494b68;
	}
	.region-title{
		padding-left: 20rpx;
		border-left:10rpx solid #6390C3;
	}
	.add-region>input{
		border: 1rpx solid #6390C3;
		width: 500rpx;
		height: 70rpx;
		margin-top: 30rpx;
		border-radius: 15rpx;
		padding-left: 20rpx;
		color: #494B68;
	}
	.add-region>view:nth-of-type(1)>.icon{
		font-size: 60rpx;
		color: #6390C3;
	}
</style>
