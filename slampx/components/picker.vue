<template>
	<view>
		<picker mode="selector" :range="modeList" @change="bindModeChange" :value="index">
			<view>{{modeList[index]}}</view>
		</picker>
	</view>
</template>

<script>
	export default{
		data() {
			return {
				index:0,
				modeList:['自适应模式','节能模式','正常模式']
			}
		},
		methods:{
			bindModeChange(e) {
				this.index = parseInt( e.detail.value)
				this.$emit("picker",parseInt( e.detail.value))
			},
		},
	}
</script>

<style>
</style>
