<template>
	<view class="qiun-charts">
		<!--#ifdef MP-ALIPAY -->
		<canvas canvas-id="canvasArea" id="canvasArea" class="charts" :width="cWidth*pixelRatio" :height="cHeight*pixelRatio" :style="{'width':cWidth+'px','height':cHeight+'px'}"   @touchstart="touchArea" @touchmove="moveArea" @touchend="touchEndArea" disable-scroll=true></canvas>
		<!--#endif-->
		<!--#ifndef MP-ALIPAY -->
		<canvas canvas-id="canvasArea" id="canvasArea" class="charts" :width="cWidth*pixelRatio" :height="cHeight*pixelRatio" :style="{'width':cWidth+'px','height':cHeight+'px'}" @touchstart="touchArea" @touchmove="moveArea" @touchend="touchEndArea" disable-scroll=true></canvas>
		<!--#endif-->
	</view>
</template>

<script>
	// 引入ucharts方法组件
	import uCharts from "../../common/u-charts.js";
	// 定义全局变量
	var _self;
	var canvasArea = null;
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
			showArea(canvasId, chartData) {
				canvasArea = new uCharts({
					$this:_self,
					canvasId: canvasId,
					type: 'area',
					fontSize:11,
					padding:[0,20,10,20],
					legend:{
						show:true,
						position:'top',
						float:'right',
						itemGap:30,
						padding:5,
						lineHeight:18,
						margin:0
					},
					dataLabel:false,
					dataPointShape:true,
					background:'#FFFFFF',
					pixelRatio:_self.pixelRatio,
					categories: chartData.categories,
					series: chartData.series,
					enableMarkLine:true,
					animation: true,
					enableScroll: true,//开启图表拖拽功能
					xAxis: {
						 type:'grid',
						    gridType:'dash',
						    itemCount:5,//x轴单屏显示数据的数量，默认为5个
						    scrollShow:true,//新增是否显示滚动条，默认false
						    scrollAlign:'left',//滚动条初始位置
						   // scrollBackgroundColor:'#F7F7FF', // 默认为 #EFEBEF
						   scrollColor:'#9998a4',//默认为 #A6A6A6
						   fontColor:'#494b68',
						   boundaryGap:'justify',
						   rotateLabel:'true'
					},
					yAxis: {
						gridType:'dash',
						gridColor:'#CCCCCC',
						dashLength:8,
						splitNumber:5,
						fontColor:'#494b68',
						max: chartData.max,
						min:0,
						format: (val) => {
							return val.toFixed(0)
						} 
					},
					width: _self.cWidth*_self.pixelRatio,
					height: _self.cHeight*_self.pixelRatio,
					extra: {
						area:{
							type: 'curve',
							opacity:0.2,
							addLine:true,
							width:2
						}
					}
				});
			},
			touchArea(e) {
				canvasArea.scrollStart(e);
			},
			moveArea(e) {
				canvasArea.scroll(e);
			},
			touchEndArea(e) {
				canvasArea.scrollEnd(e);
				//下面是toolTip事件，如果滚动后不需要显示，可不填写
				canvasArea.showToolTip(e, {
					format: function(item, category) {
						return category + ' ' + item.name + ':' + item.data
					}
				});
			},
			 //生成图片
			async canvasToTempImage () {
				await uni.canvasToTempFilePath({
					canvasId: 'canvasArea',
					success: (res) => {
						let tempFilePath = res.tempFilePath;
						console.log(res)
				    },fail(err) {
				    	console.log(err)
				    }
			    }, this);
				
		   }
			
		},
		async created() {
			_self = this;
			// await this.canvasToTempImage()
			this.cWidth = uni.upx2px(750);
			this.cHeight = uni.upx2px(500);
			
		}
		
	}
</script>

<style scoped>
    /*样式的width和height一定要与定义的cWidth和cHeight相对应*/
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
</style>

