<template>
	<view class="qiun-charts">
		<!--#ifdef MP-ALIPAY -->
		<canvas canvas-id="canvasMix" id="canvasMix" class="charts" :width="cWidth*pixelRatio" :height="cHeight*pixelRatio" :style="[{'width':cWidth+'px','height':cHeight+'px'}]" disable-scroll=true @touchstart="touchMix" @touchmove="moveMix" @touchend="touchEndMix"></canvas>
		<!--#endif-->
		<!--#ifndef MP-ALIPAY -->
		<canvas canvas-id="canvasMix" id="canvasMix" class="charts" disable-scroll=true @touchstart="touchMix" @touchmove="moveMix" @touchend="touchEndMix"></canvas>
		<!--#endif-->
	</view>
</template>

<script>
	import uCharts from "../../common/u-charts.js";
	var _self;
	var canvasMix = {};
	export default{
		data(){
			return{
				cWidth: '',
				cHeight: '',
				pixelRatio: 1,
				popupTitle:'',
			}
		},
		methods:{
			touchMix(e) {
				canvasMix.scrollStart(e);
			},
			moveMix(e) {
				canvasMix.scroll(e);
			},
			touchEndMix(e) {
				canvasMix.scrollEnd(e);
				canvasMix.touchLegend(e);
					//下面是toolTip事件，如果滚动后不需要显示，可不填写
					canvasMix.showToolTip(e, {
						format: function(item, category) {
							return category + ' ' + item.name + ':' + item.data
						}
				});
			},
			showMix(canvasId, chartData) {
				canvasMix = new uCharts({
					$this:_self,
					canvasId: canvasId,
					type: 'mix',
					fontSize:11,
					padding:[15,15,0,15],
					legend:{
						show:true,
						padding:5,
						lineHeight:11,
						margin:6,
					},
					background:'#FFFFFF',
					pixelRatio:_self.pixelRatio,
					categories: chartData.categories,
					series: chartData.series,
					animation: false,
					enableScroll: true,//开启图表拖拽功能
					xAxis: {
						disableGrid:false,
						type:'grid',
						gridType:'dash',
						itemCount:4,
						scrollShow:true,
						scrollAlign:'left',
					},
					yAxis: {
						gridType:'dash',
						dashLength:4,
						splitNumber:5,
						min:10,
						max:180,
						format:(val)=>{return val.toFixed(0)}
					},
					width: _self.cWidth*_self.pixelRatio,
					height: _self.cHeight*_self.pixelRatio,
					dataLabel: true,
					dataPointShape: true,
					extra: {
						column:{
						  width:20*_self.pixelRatio
						},
						tooltip:{
							bgColor:'#000000',
							bgOpacity:0.7,
							gridType:'dash',
							dashLength:8,
							gridColor:'#1890ff',
							fontColor:'#FFFFFF',
							horizentalLine:true,
							xAxisLabel:true,
							yAxisLabel:true,
							labelBgColor:'#DFE8FF',
							labelBgOpacity:0.95,
							labelAlign:'left',
							labelFontColor:'#666666'
						}
					},
				});
			},
		},
		created() {
			_self = this;
			//#ifdef MP-ALIPAY
			uni.getSystemInfo({
				success: function(res) {
					if (res.pixelRatio > 1) {
						//正常这里给2就行，如果pixelRatio=3性能会降低一点
						//_self.pixelRatio =res.pixelRatio;
						_self.pixelRatio = 2;
					}
				}
			});
			//#endif
			this.cWidth = uni.upx2px(750);
			this.cHeight = uni.upx2px(500);
			// uni.request({
			// 	url: 'https://unidemo.dcloud.net.cn/hello-uniapp-ucharts-data.json',
			// 	data: {},
			// 	success: function(res) {
			// 		console.log(res.data.Mix.categories);
			// 		console.log(res.data.Mix.series[0]);
			// 		let Mix={categories:[],series:[]};
			// 		res.data.Mix.series[0].enableMarkLine=true;
			// 		Mix.categories=res.data.Mix.categories;
			// 		Mix.series=res.data.Mix.series[0];
			// 		_self.showMix("canvasMix", res.data.Mix);
			// 	},
			// 	fail: () => {
			// 		// _self.tips = "网络错误，小程序端请检查合法域名";
			// 	},
				
				 
			// });
			var categories= ['2012', '2013', '2014', '2015', '2016', '2017'];
			 var  series=[{
			       name: '成交量A',
			       data: [100, 80, 95, 150, 112, 132],
			       color: '#facc14',
					enableMarkLine: true,
					legendShape: "triangle",
					pointShape: "circle",
					show: true,
					style: "curve",
					type: "area",
			   }]
			   let Mix={categories:[],series:[]};
				Mix.categories=categories;
				Mix.series=series;
				_self.showMix("canvasMix", Mix);
				console.log(Mix)
			
		}
	}
</script>

<style>
</style>
