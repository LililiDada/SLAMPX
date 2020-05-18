<template>
	<view>
		<scroll-view scroll-y="true" :style="getHeight" @scrolltolower="more">
			<view class="tips" v-if="repeirList.length<1">
				~~暂无维修数据~~
			</view>
			<view v-else>
				<uni-swipe-action>
					<uni-swipe-action-item v-for="(item,index) in repeirList" :options="options" :key="index" @change="swipeChange" @click="swipeClick($event,index,item.id)">
						<view class="cont u-f-ac">
							<repair-list :item="item" :color="color"></repair-list>
						</view>
					</uni-swipe-action-item>
				</uni-swipe-action>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	let _self;
	import uniSwipeAction from '@/components/uni-swipe-action/uni-swipe-action.vue';
	import uniSwipeActionItem from '@/components/uni-swipe-action-item/uni-swipe-action-item.vue';
	import repairList from "@/components/repair-record/repair-list.vue";
	export default {
		components:{
			uniSwipeAction,
			uniSwipeActionItem,
			repairList
		},
		data() {
			return {
				data:'',
				color:'',
				page:1,
				num:10,
				loaded:false,
				repeirList:[],

			}
		},
		computed: {
			getHeight() {
				let height = uni.getSystemInfoSync().windowHeight
				return `height: ${height}px;`;
			}
		},
		methods: {
			tips(title,icon){
				uni.showToast({
					title:title,
					icon:icon
				})
			},
			async more(){
				if(_self.loaded){ _self.tips("数据已加载完毕~~","none"); return;}
				uni.showLoading({title:"正在加载中..."})
				let page = _self.page ++;
				let url = `/repair/${_self.status}/getrepair/${_self.page}/${_self.num}`;
				var [err,res] =await _self.$http.get(url);
				_self.repeirList = _self.repeirList.concat(_self.formet(res.data.data.list)); 
				
				uni.hideLoading();
			},
			formet(list){
				console.log(list)
				if(list.length<10) {
					_self.loaded = true;
					_self.tips("数据已加载完毕~~","none")
				}
				var repeirList=[]
				list.forEach((item,index)=>{
					if(item.device){
						var data 
						// 权限之分
						console.log(uni.getStorageSync('admin'))
						if(!uni.getStorageSync('admin')){
							data ={
								fault:item.fault,
								address:item.device.region?`${item.device.address}(${item.device.region.regionname})`:item.device.address,
								number:item.device.lamp_id,
								time:item.create_time.slice(0,10)
							}
						}else{
							data ={
								fault:item.fault,
								address:item.device.region?`${item.device.address}(${item.device.region.regionname})`:item.device.address,
								number:item.device_id<10?`5121260000${item.device_id}`:`5121260000${item.device_id}`,
								time:item.create_time.slice(0,10)
							}
						}
						
						
						repeirList.push(data);
					}
				})
				return repeirList;
			},
			
		},
		async onLoad(e) {
			var param = JSON.parse(e.param)
			_self = this;
			uni.showLoading({title:"正在加载中"});
			uni.setNavigationBarTitle({title: param.data});
			this.data = param.data;
			this.color = param.color;
			this.status = param.status;
			var url = `/repair/${param.status}/getrepair/${this.page}/${this.num}`;
			var [errs,ress] = await this.$http.get(url)
			this.repeirList = this.formet(ress.data.data.list)
			console.log(errs,ress,ress.data.data.list)
			console.log(this.repeirList)
			uni.hideLoading();
			
		}
	}
</script>

<style>
	.tips{
		padding-top: 60rpx;
		text-align: center;
		color: #494B68;
	}
</style>
