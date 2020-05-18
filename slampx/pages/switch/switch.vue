<template>
	<view>
		<!-- 开关按钮 -->
		<view class="switch">
			<view class="icon iconfont icon-kai" @tap="switchTap"></view>
		</view>
		<!-- 设置 -->
		<view class="set-up">
			<!-- 亮度 -->
				
				<bright :brightList='brightList'  @navigaTo="navigaTo" @picker='picker' :spectacleList="spectacleList"></bright>  
			<!-- 区域 -->
				<region :regionList='regionList' :allCheck="allCheck" @checkAll='alterCheck' @selectLoc='selectLoc'></region>
		</view>
		
		<!-- 添加弹出层 -->
		<!-- <uni-popup ref='popup' type="center">
			<add-scene @close='close' @increaseScene='increaseScene'  ref="addScene" v-if='hackReset'></add-scene>
		</uni-popup> -->
	</view>
</template>

<script>
	let _self;
	import bright from "../../components/switch/bright.vue"
	import region from "../../components/switch/region.vue"
	import uniPopup from "@/components/uni-popup.vue";
	export default {
		components:{
			bright,
			region,
			uniPopup
		},
		data() {
			return {
				allCheck:false,
				pickerIndex:0,
				brightList:[{
					para:'10:00-12:00',
					bright:100
				},{
					para:'12:00-01:00',
					bright:80
				},{
					para:'01:00-05:00',
					bright:40
				}],
				spectacleList:[],
				regionList:[]
			}
		},
		methods: {
			switchTap(){
				var modeId,mode,title,regionlist=[];
				switch(_self.pickerIndex){
					case 0:
						modeId = 0;
						mode   = 2;
						title  = '自适应模式'
					break;
					case 1:
						modeId = uni.getStorageSync('user_scene_id');
						mode   = 3;
						title  = '节能模式'
					break;
					case 2:
						modeId = uni.getStorageSync('user_plain_id');
						mode   = 1;
						title  = '正常模式'
					break;
				}
				_self.regionList.forEach(index=>{
					if(index.check){
						regionlist.push(index.id)
					}
				})
				if(regionlist.length<1){
					uni.showToast({
						title:"请选择区域再下发命令",
						icon:'none'
					})
					return;
				}
				uni.showModal({
					title:'温馨提示',
					content:`是否开启${title}`,
					success: async function(res){
						if (res.confirm) {
							var [err,res] = await _self.$http.post('/user/bindmode',{
								'mode_id':modeId
							})
							var [serr,sres] = await _self.$http.post('/device/sendcommand',{
								scene_id:modeId,
								regionlist:regionlist.join(),
								mode:mode
							})
							if(res.statusCode == 200) {
								uni.showToast({
									title:"命令下发成功！！"
								})
								_self.regionList.forEach(index=>{
									index.check = false;
								})
								_self.allCheck = false;
							}
							
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				})
			},
			// 全选
			alterCheck(val){
				console.log(val)
				_self.allCheck = val;
				_self.regionList.forEach(index=>{
					index.check = val;
				})
			},
			// 选中某一个区域
			selectLoc(val){
				_self.regionList[val].check = !_self.regionList[val].check;
			},
			// close(){
			// 	_self.$refs.popup.close();
			// },
			// increaseScene(e){
			// 	_self.spectacleList.push(e);
			// 	_self.$refs.popup.close();
			// },
			navigaTo(){
				if(_self.pickerIndex ==1) uni.navigateTo({url:"../scene/scene?id="+ uni.getStorageSync('user_scene_id')})
				if(_self.pickerIndex ==2) uni.navigateTo({url:"../region-list/region-list?id="+ uni.getStorageSync('user_plain_id')})
			},
			// 下拉操作
			picker(e){
				_self.pickerIndex = e;
				console.log('picker',e)
			}
		},
		async onLoad() {
			_self = this;
			uni.showLoading({title:"数据加载中"});
			let [err,res] = await _self.$http.get('/region/allregion');
			res.data.data.list.forEach((item,index)=>{
				item.check = false;
				_self.regionList.push(item);
			})
			
			uni.hideLoading();
		},
		onShow() {
			
			// _self.$set(_self.brightList[_self.alterIndex].list,0,data,true);
			_self.spectacleList = uni.getStorageSync('user_plain');
			_self.brightList = uni.getStorageSync('user_scene');
		}
	}
</script>

<style>
	/* #ifdef APP-PLUS */
		view{
			font-size: 33rpx;
		}
	/* #endif */
	/* 开关按钮 */
	.switch{
		height: 335rpx;
		background: #6390C3;
		text-align: center;
	}
	.switch>view:first-child{
		padding-top: 25rpx;
		font-size: 150rpx;
		color: #FFFFFF;
	}
	/* 设置 */
	.set-up{
		width: 85vw;
		height: calc(100vh - 225rpx);
		/* border: 1rpx solid #C8C7CC; */
		box-shadow: 0 0 2px 2px rgba(153, 153, 153, 0.1);
		position: fixed;
		top:225rpx;
		left: 0;
		right: 0;
		margin: 0 auto;
		border-top-left-radius: 10rpx;
		border-top-right-radius: 10rpx;
		background-color: #FFFFFF;
		box-sizing: border-box;
		padding: 40rpx 45rpx 25rpx;
	}
	/* .set-up>view{
		background-color: #FFFFFF;
		box-shadow: 0 0 2px 2px rgba(153, 153, 153, 0.1);
		border-radius: 5px;
		box-sizing: border-box;
		padding: 30rpx 60rpx 5rpx 50rpx;
		margin-bottom: 45rpx;
	} */
</style>
