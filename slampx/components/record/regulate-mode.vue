<template>
	<uni-popup ref="showtip" type="center" :mask-click="false">
		<view class="uni-tip">
			<text class="uni-tip-title">是否开启{{modeTitle}}</text>
			<view class="uni-tip-content">
				<view v-if="pickerIndex==0" class="tip u-f u-f-ajc">
					<view class="pointer"></view>
					<text style="word-wrap: break-word;">自适应模式下路灯会自动调至适宜亮度</text>
				</view>
				<view class="record u-f-ajc u-f-column" v-if="pickerIndex!=0">
					<block v-for="(item,index) in list" :key="index">
						<time-list :item="item" :index="index" :length="list.length-1"></time-list>
					</block>
				</view>
			</view>
			<view class="uni-tip-group-button">
				<text class="uni-tip-button" @click="cancel(false)">取消</text>
				<text class="uni-tip-button" @click="cancel(true)">确定</text>
			</view>
		</view>
	</uni-popup>
</template>

<script>
	import uniPopup from "../uni-popup.vue";
	import timeList from '../time-list.vue';
	export default{
		components:{
			uniPopup,
			timeList
		},
		props:{
			modeTitle:String,
			pickerIndex:Number,
			list:Array,
		},
		data(){
			return{
				
			}
		},
		methods:{
			cancel(e){
				console.log(e)
				if(e){
					this.$emit('queryMode')
				}
				this.$refs.showtip.close();
			}
		}
	}
</script>

<style scoped>
	.uni-tip {
		/* #ifndef APP-NVUE */
		display: flex;
		flex-direction: column;
		/* #endif */
		padding: 15px;
		width: 300px;
		background-color: #fff;
		border-radius: 10px;
	}

	.uni-tip-title {
		margin-bottom: 20px;
		text-align: center;
		font-weight: bold;
		font-size: 16px;
		color: #333;
	}

	.uni-tip-content {
		/* padding: 15px;
*/
		font-size: 14px;
		color: #666;
	}

	.uni-tip-group-button {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		flex-direction: row;
		margin-top: 20px;
	}

	.uni-tip-button {
		flex: 1;
		text-align: center;
		font-size: 14px;
		color: #3b4144;
	}
	
	
	/* 自定义 */
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
	.tip{
		margin-bottom: 10px;
	}
</style>
