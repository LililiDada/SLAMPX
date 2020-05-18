<template>
	<view>
		<view class="calendar u-f-ajc u-f-column">
			<view class="picker_date u-f u-f-ac u-f-jsb" :style="[{width:windowWidth + 'px'}]" @click="openCalendar">
				<text class="picker_date_text" :style="[{color:time?'#000000':'#909090'}]">{{time?timePeriod:'请选择时间段'}}</text>
				<!-- #ifndef APP-PLUS-NVUE -->
				<view class="icon iconfont icon-rili"></view>
				<!-- #endif -->
				<!-- #ifdef APP-PLUS-NVUE -->
				<text :style="[{color:time?'#000000':'#909090'}]">&#xe66f;</text>
				<!-- #endif -->
			</view>
			<view class="picker_findnew u-f u-f-ac u-f-jsb" :style="[{width:windowWidth + 'px'}]" @click="queryNew">
				<!-- <view class="picker_find u-f-ajc" :style="[{width:buttonWidth+'px'}]">
					<view class="icon iconfont icon-chazhao"></view>
					<text class="picker_text">查询数据</text>
				</view> -->
				<!-- <view class="" @click="queryNew"> -->
					<text class="picker_text">最近七天数据</text>
					<!-- #ifndef APP-PLUS-NVUE -->
					<view class="icon iconfont icon-tian"></view>
					<!-- #endif -->
					<!-- #ifdef APP-PLUS-NVUE -->
					<text class="picker_text">&#xe66f;</text>
					<!-- #endif -->
				<!-- </view> -->
			</view>
		</view>
		<uni-calendar ref="calendar" :date="info.date" :insert="info.insert" :lunar="info.lunar" :startDate="info.startDate" :endDate="info.endDate" :range="info.range"  :showMonth="info.showMonth" @confirm="confirm" @close="close" />
	</view>
</template>

<script>
	import uniCalendar from '../uni-calendar/uni-calendar.vue';
	export default{
		props:{
			chartsReset:Boolean,
		},
		components: {
			uniCalendar,
		},
		data(){
			return{
				info: {
					date: '2019-08-15',
					startDate: '',
					endDate: '',
					lunar: false,
					range: true,
					insert: false,
					showMonth:false,
					selected: []
				},
				windowWidth:0,
				buttonWidth:0,
				time:false,
				timePeriod:''
			}
		},
		created() {
			var _self = this;
			uni.getSystemInfo({
				success: function (res) {
					_self.windowWidth = res.windowWidth *0.8
					_self.buttonWidth = res.windowWidth *0.4
					console.log(res)
				}
			});
			_self.info.date = new Date().toLocaleDateString();
			
		},
		methods:{
			// 显示日期组件
			openCalendar(){
				this.$emit('update:chartsReset', false);
				this.$refs.calendar.open()
			},
			close(){
				this.$emit('update:chartsReset', true);
			},
			confirm(e) {
				this.$emit('update:chartsReset', true);
				console.log(e)
				var today = this.info.date;
				var data = e.range.data
				// var reg = new RegExp('/',"g");
				// today = today.replace(reg,'-');
				// today = `${today} 00:00:00`;
				var oneDay = 24*60*60*1000;
				console.log(new Date(today).getTime() , new Date(data[data.length-1]).getTime(),today,data[data.length-1])
				if(data.length == 0){
					return uni.showToast({
						title:"请选择日期范围！！",
						icon:"none"
					});
				}
				// if(new Date(data[data.length-1]).getTime() -  new Date(today).getTime() > oneDay){
				// 	return uni.showToast({
				// 		title:"仅可选择小于今天的日期！！",
				// 		icon:"none"
				// 	});
				// }
				this.timePeriod=`${data[0]}/${data[data.length-1]}`;
				this.time = true;
				this.$emit('calendar',{
					startDate:data[0],
					endDate:data[data.length-1],
					data:data
				})
			},
			// 最近七天数据
			queryNew(){
				this.time = false;
				this.$emit('sevenSelect')
			}
		}
	}
</script>

<style scoped>
	/* #ifdef APP-NVUE */
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
	.calendar{
		color: #6390C3;
		margin-bottom: 20rpx;
	}
	.picker_date{
		height: 65rpx;
		color: #909090;
		border-width:1px;
		border-style:solid;
		border-color:#CCCCCC;
		padding:0 20rpx;
		margin-bottom: 20rpx;
		border-radius: 10rpx;
		box-sizing: border-box;
	},
	.picker_find,.picker_findnew{
		width: 150rpx;
		height: 65rpx;
		color: #6390C3;
		border-radius: 10rpx;
		padding:0 20rpx;
		border-width:1px;
		border-style:solid;
		border-color:#CCCCCC;
		box-sizing: border-box;
	}
	.picker_find{
		margin-right:20rpx;
	}
	.picker_date_text{
		color: #909090;
		font-size: 30rpx;
	}
	.picker_text{
		color: #6390C3;
		font-size: 30rpx;
	}
</style>
