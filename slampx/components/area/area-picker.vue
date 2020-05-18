<template>
	<view>
		<picker mode="selector" :range="modeList"  range-key="regionname"  @change="bindModeChange" :value="index">
			<view>{{modeList[index].regionname}}</view>
		</picker>
	</view>
</template>

<script>
	export default{
		data() {
			return {
				index:0,
				modeList:{}
			}
		},
		methods:{
			bindModeChange(e) {
				let index = parseInt( e.detail.value);
				this.index = index
				this.$emit("picker",this.modeList[index].id)
			},
			async getRegion(){
				console.log('重来')
				let [err,res] = await this.$http.get('/region/allregion');
				let list = res.data.data.list;
				list.unshift({
					id:0,
					regionname:'请选择'
				});
				this.modeList = list;
				console.log(this.modeList);
			},
		},
	}
</script>

<style>
</style>
