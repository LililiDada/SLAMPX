<template>
	<view class="add-scene">
		<view class="icon-top u-f-ac u-f-jsb">
			<view class="icon iconfont icon-cha" @click="close"></view>
			<text style="font-size: 20px;">{{add?'新增':'修改'}}</text>
			<view class="icon iconfont icon-gou" @click="query"></view>
		</view>
		<view class="time u-f u-f-column">
			<view>时间段</view>
			<view class="u-f-ac">
				<view class="picker u-f-ajc">
					<picker mode="multiSelector" :range="startTime" :disabled="left" :value="startIndex" @change="startTimeChange">
						<view>{{startTime[0][startIndex[0]]}}:{{startTime[1][startIndex[1]]}}</view>
					</picker>
					<view class="icon iconfont icon-down-trangle" v-if="!left"></view>
				</view>
				<text>~~~</text>
				<view class="picker u-f-ac">
					<picker mode="multiSelector" :range="endTime" :disabled="right" :value="endIndex" @change="endTimeChange">
						<view>{{endTime[0][endIndex[0]]}}:{{endTime[1][endIndex[1]]}}</view>
					</picker>
					<view class="icon iconfont icon-down-trangle" v-if="!right"></view>
				</view>
			</view>
		</view>
		<view class="bright">
			<view>灯泡亮度</view>
			<input type="number" :value="bright" @input="getBright"/>
		</view>
	</view>
</template>

<script>
	export default{
		data(){
			return {
				startTime: [['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],['00','10','20','30','40','50']],
				startIndex: this.start,
				endTime: [['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],['00','10','20','30','40','50']],
				endIndex: this.end,
				bright:this.inputValue,
			}
		},
		props:{
			add:{
				type: Boolean,
				default: true
			},
			start:{
				type:Array,
				default(){
					return [0,0]
				}
			},
			end:{
				type:Array,
				default(){
					return [0,0]
				}
			},
			inputValue:{
				type:[Number,String]
			},
			sceneId:{
				type:Number,
				default:0
			},
			left:{
				type:Boolean,
				default:false
			},
			right:{
				type:Boolean,
				default:false
			},
			plain:{
				type:Boolean,
				default:false
			}
		},
		methods:{
			
			selectTime(e){
				if( e[0] < 10){
					return `0${e[0]}:${e[1]}0`
				}
				return `${e[0]}:${e[1]}0`
			},
			// 选择开始时间
			startTimeChange(e){
				console.log(this.begin)
				this.startIndex = e.target.value
				
			},
			// 选择结束时间
			endTimeChange(e){
				this.endIndex = e.target.value
			},
			// 提示
			tip(e){
				uni.showToast({
					icon:'none',
					title:e
				})
			},
			sameTime(a,b){
				
				if(a[0]-b[0] == 0&&a[1]-b[1]==0) return true;
			},
			// 添加或修改
			async query(){
				var that = this;
				// 判断两个时间选择是否一样
				if(this.bright>100 || this.bright<0){
					that.tip("路灯亮度仅可设0-100！")
					return;
				}
				if(!this.bright){
					that.tip("请填写灯泡亮度！")
					return;
				}
				if(this.add){
					if(that.sameTime(that.startIndex,that.endIndex)){
						that.tip("时间选择不能一致！")
						return;
					}
					var reqdata = {
						light:this.bright,
						start_time:that.startIndex[0]<10?`0${that.startIndex[0]}`:that.startIndex[0],
						start_min:that.startIndex[1]==0?`0${that.startIndex[1]}`:that.startIndex[1]*10,
						end_time:that.endIndex[0]<10?`0${that.endIndex[0]}`:that.endIndex[0],
						end_min:that.endIndex[1]==0?`0${that.endIndex[1]}`:that.endIndex[1]*10,
					}
					var [err,add] = await this.$http.post('/adjust/create',reqdata)
					if(err){
						that.tip("添加失败！")
						return;
					}
					var id = add.data.data.id;
					// 添加普通模式
					if(this.plain){
						var [err,res] = await this.$http.post('/mode/add',{
							pattern_id:3,
							modelist:JSON.stringify([add.data.data.id])
						});
						console.log(res.data.data.id)
						id = res.data.data.id;
					}
					// var begin = that.selectTime(that.startIndex)
					// var finish = that.selectTime(that.endIndex)
					// var time = `${begin}-${finish}`;
					
					reqdata.id = id;
					that.$emit('increaseScene',reqdata)
					return;
				}
				this.$emit('increaseScene',{
					id:this.sceneId,
					light:this.bright,
					start_time:that.startIndex[0],
					start_min:that.startIndex[1]*10,
					end_time:that.endIndex[0],
					end_min:that.endIndex[1]*10,
				})
				console.log('修改');
			},
			// 关闭
			close(){
				this.$emit('close')
			},
			getBright(e){
				this.bright = e.detail.value;
			}
		},
		created() {
			console.log(this.left,this.right)
		}
	}
</script>

<style scoped>
	.add-scene{
		border-radius: 20rpx;
		padding:50rpx  60rpx 60rpx;
		background-color: #FFFFFF;
		color: #494b68;
		width: 500rpx;
	}
	.add-scene>.icon-top{
		color: #6390C3;
		margin-bottom: 20rpx;
	}
	.icon-top>view{
		font-size: 30px;
	}
	.time>view:nth-of-type(1),.bright>view:nth-of-type(1){
		border-left: 10rpx solid #6390C3;
		padding-left: 10rpx;
		margin-bottom: 20rpx;
		margin-top: 20rpx;
	}
	.bright>view:nth-of-type(1){
		margin-bottom: 0rpx;
	}
	.time,.bright{
		margin-left: 10rpx;
	}
	.picker{
		padding:10rpx 20rpx;
		border: 1px solid #C9C9C9;
		border-radius: 10rpx;
	}
	.bright>input{
		border: 1px solid #C9C9C9;
		padding: 15rpx 10rpx;
		height: 60rpx;
		width: 400rpx;
		border-radius: 20rpx;
		margin-top: 20rpx;
	}
</style>
