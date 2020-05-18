<template>
	<view  style="padding-top: 20rpx;">
		<charts ref="charts"></charts>
	</view>
</template>

<script>
	var _self;
	import charts from '../../components/charts.vue';
	export default {
		components:{
			charts
		},
		data() {
			return {
				
			}
		},
		methods: {
			
		},
		async onLoad(e) {
			_self = this;
			switch(e.type){
				case 'fault':
					uni.setNavigationBarTitle({
					    title: '故障统计'
					});
					var [err,res] = await _self.$http.get('/repair/statisnum');
					let LineA = {categories:[],series:[{
							name:'故障统计',
							data:[]
					}],max:50};
					LineA.categories = res.data.data.xData;
					LineA.series[0].data = res.data.data.yData;
					LineA.max = res.data.data.max+5;
					LineA.unit = '个';
					console.log(LineA)
					_self.$refs.charts.popupTitle = '故障统计(每个节点为五天的故障数量)';
					_self.$refs.charts.showLineA('canvasLineA',LineA);
					console.log(res.data)
				break;
			}
			
		}
	}
</script>

<style>

</style>
