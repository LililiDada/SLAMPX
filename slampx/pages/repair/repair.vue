<template>
	<view>
		<view class="top"></view>
		<view class="repair">
			<block v-for="(item,index) in status" :key="index">
				<view class="u-f-ac u-f-jsb" @click="jumpPage(index)">
					<view class="u-f-ac">
						<view class="icon iconfont"  :class="item.logo" :style="[{color:item.color,'font-size':item.size,margin:item.margin}]"></view>
						<text>{{item.title}}</text>
					</view>
					<view class="number u-f-ajc" :style="[{background:item.color}]">{{item.number}}</view>
				</view>
			</block>
		</view>
	</view>
</template>

<script>
	let _self;
	export default {
		data() {
			return {
				status:[{
					title:'紧急维修',
					logo:'icon-tishi',
					number:2,
					color:'#e11010'
				},{
					title:'日常维修',
					logo:'icon-biaoqing',
					number:2,
					color:'#ff9d1c',
					size:'104rpx',
					margin:'0 37rpx 0 0'
				},{
					title:'历史记录',
					logo:'icon-lishijilu',
					number:2,
					color:'#f4ea2a',
					size:'60rpx',
					margin:'0 62rpx 0 20rpx'
				}]
			}
		},
		methods: {
			navigete(val,color,status){
				var param = {
					data:val,
					color:color,
					status:status
				}
				var url = '/pages/repair-record/repair-record?param='+JSON.stringify(param)
				uni.navigateTo({
					url:url
				})
			},
			
			jumpPage(index){
				var that = this;
				switch(index){
					case 0:
						that.navigete('紧急维修','#e11010',0)
					break;
					case 1:
						that.navigete('日常维修','#ff9d1c',1)
					break;
					case 2:
						that.navigete('历史记录','#f4ea2a',2)
					break;
				}
			}
		},
		async onLoad() {
			_self = this;
			var [err,res] = await _self.$http.get('/repair/repairnum');
			var data = res.data.data;
			_self.$set(_self.status[0],'number',data.urgent);
			_self.$set(_self.status[1],'number',data.daily);
			_self.$set(_self.status[2],'number',data.history);
		}
	}
</script>

<style>
	.top{
		height: 80px;
		width: 100vw;
		background-color: #6390C3;
	}
	.repair{
		width: 85vw;
		margin: -50px auto 0;
	}
	.repair>view{
		width: 100%;
		height: 215rpx;
		background-color: #FFFFFF;
		box-shadow: 0 0 2px 2px rgba(153, 153, 153, 0.1);
		border-radius: 5px;
		box-sizing: border-box;
		padding: 0 60rpx 0 50rpx;
		margin-bottom: 45rpx;
	}
	.repair>view>view:nth-of-type(1){
		font-size: 35rpx;
	}
	.repair>view>view:nth-of-type(1)>.icon{
		font-size: 70rpx;
		margin-right: 50rpx;
		margin-left: 22rpx;
	}
	.repair>view>view:nth-of-type(2){
		color: #FFFFFF;
		width: 50rpx;
		font-size: 26rpx;
		border-radius: 15rpx;
	}
</style>
