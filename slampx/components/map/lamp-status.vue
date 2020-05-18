<template>
	<view class="u-f u-f-jsa">
		<!-- <view class="status_left"> -->
		<!-- #ifdef APP-PLUS-NVUE -->
			<view class="status_left u-f-ajc">
				<text :style="[{color:item.activeColor,fontSize:'45rpx'}]" style="font-family:iconfont9">&#xe606;</text>
			</view>
		<!-- #endif -->
		<!-- #ifndef APP-PLUS-NVUE -->
			<view class="icon iconfont icon-zhinengludeng status_left" :style="[{color:item.activeColor}]"></view>
		<!-- #endif -->
		<!-- </view> -->
		<view class="status_right u-f u-f-column" :style="[{ width:rWidth? rWidth + 'px':'40vw'}]">
			<view class="u-f u-f-ac u-f-jsb">
				<text class="status_right_status">{{item.status}}</text>
				<text class="status_right_num">{{item.number}}</text>
			</view>
			<view class="u-f  u-f-ac u-f-jsb">
				<view class="progress" :style="[{width: item.width+'px','background-color':item.activeColor}]"></view>
				<text class="percent">{{item.percent}}%</text>
			</view>
		</view>
	</view>
	
</template>

<script>
	export default{
		props:{
			item:Object,
			// #ifdef APP-NVUE
			rWidth:Number,
			// #endif
		},
		data(){
			return{
				// #ifdef APP-PLUS-NVUE
					fontName:'\ue606',
				// #endif
				activeColor:'#f4ea2a',
				noActiveColor:'#FFFFFF',
				// width:'150',
				// borderRadius:20,
				// handleColor:"transparent",
				// status:"关闭路灯",
				// percent:'15%',
				// number:15
			}
		},
		beforeCreate() {
			// #ifdef APP-PLUS-NVUE
				const domModule = weex.requireModule('dom')
				domModule.addRule('fontFace', {
				    'fontFamily': "iconfont9",
				    'src': "url('data:font/truetype;charset=utf-8;base64,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')"
				});
			// #endif
             
        }
	}
</script>
<style>
	/* #ifdef APP-PLUS */
		.u-f{
			flex-direction: row;
		}
		.u-f-column{
			flex-direction: column;
		}
		.u-f-ac{
			align-items: center;
		}
		.u-f-ajc{
			align-items: center;
			justify-content: center;
		}
		.u-f-jsa{
			justify-content: space-around;
		}
		.u-f-jsb{
			justify-content: space-between;
		}
	/* #endif */
	.status_left,.status_right{
		height: 70rpx;
		background-color: #FFFFFF;
		box-shadow: 0 0 2px 2px rgba(238, 238, 238, 0.9);
		border-radius: 10rpx;
	}
	.status_left{
		width:80rpx;
		text-align: center;
		line-height: 70rpx;
		font-size: 45rpx;
		margin-right: 30rpx;
	}
	.progress{
		height: 13rpx;
		border-radius: 20px;
	}
	.status_right{
		
		/* #ifdef APP-NVUE */
			padding: 10rpx 18rpx 10rpx 30rpx;
		/* #endif */
		/* #ifndef APP-NVUE */
			width: 40vw;
			box-sizing: border-box;
			padding: 0 20rpx 0 35rpx;
		/* #endif */
		justify-content: center;
		
	}
	.status_right_status{
		margin-bottom: 7rpx;
		font-size: 26rpx;
	}
	.status_right_num{
		margin-bottom: 7rpx;
		/* #ifdef APP-NVUE */
			font-size: 28rpx;
		/* #endif */
		/* #ifndef APP-NVUE */
			font-size: 26rpx;
		/* #endif */
	}
	.percent{
		color: #bfbfbf;
		font-size: 15rpx;
	}
</style>