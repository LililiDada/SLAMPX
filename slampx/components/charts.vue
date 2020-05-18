<template>
	<view>
		<view class="qiun-bg-white qiun-title-bar qiun-common-mt">
			<view class="qiun-title-dot-light">{{popupTitle}}</view>
			<!-- 使用图表拖拽功能时，建议给canvas增加disable-scroll=true属性，在拖拽时禁止屏幕滚动 -->
		</view>
		<view class="qiun-charts">
			<!--#ifdef MP-ALIPAY -->
			<canvas canvas-id="canvasLineA" id="canvasLineA" class="charts" disable-scroll=true @touchstart="touchLineA" @touchmove="moveLineA" @touchend="touchEndLineA"></canvas>
			<!-- 使用图表拖拽功能时，建议给canvas增加disable-scroll=true属性，在拖拽时禁止屏幕滚动 -->
			<!--#endif-->
			<!--#ifndef MP-ALIPAY -->
			<canvas canvas-id="canvasLineA" id="canvasLineA" class="charts" disable-scroll=true @touchstart="touchLineA"
			 @touchmove="moveLineA" @touchend="touchEndLineA"></canvas>
			<!-- 使用图表拖拽功能时，建议给canvas增加disable-scroll=true属性，在拖拽时禁止屏幕滚动 -->
			<!--#endif-->
		</view>
	</view>
</template>

<script>
	// 引入ucharts方法组件
	import uCharts from '../common/u-charts.js';
	// 定义全局变量
	var _self;
	var canvasLineA = null;
	export default{
		props:{
			
		},
		data(){
			return{
				cWidth: '',
				cHeight: '',
				pixelRatio: 1,
				popupTitle:'',
			}
		},
		methods:{
			showLineA(canvasId, chartData) {
				canvasLineA = new uCharts({
					$this: _self,
					canvasId: canvasId,
					type: 'line',
					fontSize: 11,
					colors:['#6390C3'],
					legend: true,
					dataLabel: false,
					dataPointShape: false,
					background: '#FFFFFF',
					pixelRatio: _self.pixelRatio,
					categories: chartData.categories,
					series: chartData.series,
					animation: true,
					enableScroll: true, //开启图表拖拽功能
					xAxis: {
						disableGrid: false,
						type: 'grid',
						gridType: 'dash',
						itemCount: 8, //可不填写，配合enableScroll图表拖拽功能使用，x轴单屏显示数据的数量，默认为5个
						scrollShow: true, //新增是否显示滚动条，默认false
						scrollAlign: 'left',
						//scrollBackgroundColor:'#F7F7FF',//可不填写，配合enableScroll图表拖拽功能使用，X轴滚动条背景颜色,默认为 #EFEBEF
						//scrollColor:'#DEE7F7',//可不填写，配合enableScroll图表拖拽功能使用，X轴滚动条颜色,默认为 #A6A6A6
					},
					yAxis: {
						//disabled:true
						gridType: 'dash',
						splitNumber: 5,
						min:0,
						max: chartData.max,
						format: (val) => {
							return val.toFixed(0) + chartData.unit
						} //如不写此方法，Y轴刻度默认保留两位小数
					},
					width: _self.cWidth * _self.pixelRatio,
					height: _self.cHeight * _self.pixelRatio,
					dataLabel: true, //是否在图表中显示数据标签内容值
					dataPointShape: true , // 是否在图表中显示转折点
					extra: {
						lineStyle: 'straight'
					},
					legend:{
						show:false
					}
				});
			
			},
			touchLineA(e) {
				canvasLineA.scrollStart(e);
			},
			moveLineA(e) {
				canvasLineA.scroll(e);
			},
			touchEndLineA(e) {
				canvasLineA.scrollEnd(e);
				//下面是toolTip事件，如果滚动后不需要显示，可不填写
				canvasLineA.showToolTip(e, {
					format: function(item, category) {
						return category + ' ' + item.name + ':' + item.data
					}
				});
			},
			
		},
		created() {
			_self = this;
			this.cWidth = uni.upx2px(750);
			this.cHeight = uni.upx2px(500);
		}
		
	}
</script>

<style>
	.qiun-charts {
		width: 750upx;
		height: 500upx;
		background-color: #FFFFFF;
	}
	
	.charts {
		width: 750upx;
		height: 500upx;
		background-color: #FFFFFF;
	}
	.qiun-title-dot-light {
		border-left-width: 10upx;
		border-left-style: solid;
		border-left-color:#0ea391 ;
		padding-left: 10upx;
		font-size: 32upx;
		color: #000000
	}
	.qiun-bg-white{
		background-color: #FFFFFF;
		padding-left: 10rpx;
		padding-top: 10rpx;
	}
</style>
