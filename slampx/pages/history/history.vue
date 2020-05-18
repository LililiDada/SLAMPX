<template>
	<view>
		<view class="title u-f-ajc">
			<text class="all" :style="[{'color':check? 'rgba(255,255,255,.8)':'white'}]" @click="allTap">全部</text>
			<view class="region u-f-ac"  :style="[{'color':check? 'white':'rgba(255,255,255,.8)'}]" @click="regionTap">
				<text>区域</text>
				<view class="icon iconfont icon-xiangxia"></view>
			</view>
		</view>
			<!-- <view class="back-bottom"></view> -->
		<view class="statis">
			<image src="../../static/back.png" mode="widthFix" style="width: 100vw;" class="back-top"></image>
			<!-- <view class="back-top"></view>
			<canvas style ="width: 100vw; height: 180rpx;" canvas-id="firstCanvas" class="cire"></canvas>-->
			<view class="form  u-f-ac u-f-jsb">
				<block v-for="(item,index) in statisList" :key="index">
					<view class="statis-one u-f-ajc u-f-column" @tap="showMore(index)">
						<text>{{item.title}}</text>
						<view class="icon iconfont statis-one-icon" :class="item.logo"  :style="[{color:item.color,'font-size':item.size +'px','margin':item.margin}]"></view>
						<text class="statis-one-value">{{item.value}}{{item.unit}}</text>
					</view>
				</block>
			</view>
		</view>
		
		
		
		<view class="charts">
			<calendar @calendar="calendar" :chartsReset.sync='chartsReset' @sevenSelect='sevenSelect' ref='calendar'></calendar>
			<charts-area ref='pageChart' v-if="chartsReset"></charts-area>
		</view>
	
	
		<!-- 区域选择 -->
		<uni-drawer :visible="showLeft" mode="left" @close="closeDrawer">
			<uni-list :scrollY="true" @scrolltolower='scrolltolower'>
				<block v-for="(item,index) in regionList" :key='index'>
					<uni-list-item :title="item.regionname" @click='regionSelect(item.id)'></uni-list-item>
				</block>
			</uni-list>
		</uni-drawer>
	
	
		<!-- 开关时间 -->
		<uni-popup ref='Pageswitc' type='top'>
			<switch-time :mode='mode' :modeId='modeId' :switchList='switchList' v-if="switchReset"></switch-time>
		</uni-popup>
	
		<!-- 亮灯率 -->
		<uni-popup ref="Pagefault" type="top">
			<view class="light">
				<view class="light-text-top">当路灯运作时，亮灯率为正常运行的路灯率！</view>
				<view class="light-text-bottom">当路灯关闭运作时，亮灯率为发生故障的路灯率</view>
			</view>
			
		</uni-popup>
			
	</view>
</template>

<script>
	let _self;
	import chartsArea from "../../components/history/charts-area.vue";
	import uniPopup from "@/components/uni-popup.vue";
	import switchTime from "../../components/history/switch-time.vue";
	import charts from '../../components/charts.vue';
	import uniDrawer from '../../components/uni-drawer/uni-drawer.vue';
	import uniList from '../../components/uni-list/uni-list.vue';
	import uniListItem from '../../components/uni-list-item/uni-list-item.vue';
	import calendar from '../../components/history/calendar.vue';
	export default {
		components:{
			chartsArea,
			uniPopup,
			switchTime,
			charts,
			uniDrawer,
			uniList,
			uniListItem,
			calendar
		},
		data() {
			return {
				statisList:[{
					id:'3301_0_5700',
					title:"亮灯率",
					value:'0',
					unit:'%',
					color:"#36b598",
					logo:'icon-banliangdeng'
				},{
					id:'3331_0_5805',
					title:"月用电量",
					value:'0',
					unit:'Wh',
					color:"#788bf4",
					size:uni.upx2px(100),
					margin:'5rpx 0rpx 5rpx 0',
					logo:'icon-yueyongdianliangchaxun1-copy'
				},{
					id:'3300_0_5700',
					title:"故障统计",
					value:'0',
					unit:'台/月',
					color:"#f7ee41",
					size:uni.upx2px(90),
					margin:'5rpx 0rpx 15rpx 0',
					logo:'icon-guzhang'
				},{
					id:'3316_0_5700',
					title:"电压",
					value:'0',
					unit:'V',
					color:"#d81e06",
					size:uni.upx2px(75),
					logo:'icon-dianya'
				},{
					id:'3317_0_5700',
					title:"电流",
					value:'0',
					unit:'mA',
					color:"#000000",
					size:uni.upx2px(75),
					logo:'icon-dianliu'
				},{
					id:'',
					title:"开关时间",
					value:'',
					unit:'',
					color:"#ba75e2",
					size:uni.upx2px(100),
					logo:'icon-kaiguan1'
				}],
				switchList:[],  //开关时间弹出层数据
				modeId:0,        //当前用户模式id判断用户是否节能模式
				mode:'节能模式',
				switchReset:true,
				check:false,
				showLeft: false,
				regionList :[],
				chartsReset:true,
				regionPage:1,
				clickRegion:false,
				streams:['3316_0_5700','3317_0_5700','3331_0_5805','3203_0_5603']  ,//查询的数据
				AlldeviceId:'',    //当前用户全部设备ID
				SomedeviceId:'',
			}
		},
		methods: {
			// 提示封装
			tip(title,icon){
				return uni.showToast({
					title:title,
					icon:icon
				})
			},
			// 点击全部
			async allTap(){
				this.check = false;
				_self.chartsReset = true;
				// 具体数据亮灯率等等
				await _self.history('/user/gethistory');
				// 将日期选择提示设为请选择状态
				_self.$refs.calendar.time = false;
				if(!uni.getStorageSync('admin')){
					await _self.sevenData();
					// 月用电量数据
					var monthDate = _self.getData();
					await _self.monthDate(_self.AlldeviceId,monthDate);
				}else{
				  await _self.areaRquest(700);
				}
				
			},
			// 点击区域
			regionTap(){
				// check为true时表示区域，false表示全部
				this.check = true;
				// 显示左边区域选择弹出层
				this.showLeft = true;
				// 隐藏层级过高的图表
				_self.chartsReset = false;
				// 默认为未点击区域状态
				_self.clickRegion = false;
				// 将时间选择提示设置为空
				_self.calendar.time = false
			},
			// 滑到底部再加载15个数据
			async scrolltolower(){
				_self.regionPage = _self.regionPage++;
				let url = `/region/getregion/${_self.regionPage}/15`;
				let [regionerr,regionres] = await _self.$http.get(url);
				_self.regionList.push(regionres.data.data.list);
				console.log('到底了')
			},
			// 区域选择
			async regionSelect(id){
				var data = new Array(1).fill(id);
				console.log(data);
				// 将日期选择提示设为请选择状态
				_self.$refs.calendar.time = false;
				// 显示层级过高的图表
				_self.chartsReset = true;
				_self.clickRegion = true;
				await _self.history(`/user/regionhistory/${id}`);
				if(!uni.getStorageSync('admin')){
					var deviceid = await _self.getDeviceId(data);
					_self.someSevenData(deviceid);
					// 月用电量数据
					var monthDate = _self.getData();
					await _self.monthDate(deviceid,monthDate);
				}else{
					await _self.areaRquest(500);
				}
				// 关闭区域选择弹出层
				this.showLeft = false;
			},
			
			// 区域最近七天的数据
			async someSevenData(deviceId){
				// 获取今日日期和七天前的日期
				var date = [_self.getBeforeDate(7),_self.getBeforeDate(0)]
				var list = await _self.getOneNet(deviceId,date);
				var xData = ['今日',_self.getBeforeMonthDay(1),_self.getBeforeMonthDay(2),_self.getBeforeMonthDay(3),_self.getBeforeMonthDay(4),_self.getBeforeMonthDay(5),_self.getBeforeMonthDay(6)]
				await _self.showData(list,xData);
			},
			
			// 关闭抽屉
			async closeDrawer(){
				this.showLeft = false;
				
				// 未选区域，防止图标为空，默认为全部设备
				if(!_self.clickRegion){
					_self.tip('未选择区域，此时为全部区域！！','none')
					_self.chartsReset = true;
					// 将日期选择提示设为请选择状态
					_self.$refs.calendar.time = false;
					this.check = false;
					// 权限之分
					if(!uni.getStorageSync('admin')){
						await _self.sevenData();
						// 月用电量数据
						var monthDate = _self.getData();
						await _self.monthDate(_self.AlldeviceId,monthDate);
					}else{
						await _self.areaRquest(700);
					}	
				}
			},
			// 日期选择
			async calendar(e){
				
				// 权限之分
				if(!uni.getStorageSync('admin')){
					var date = [];
					var des_id = '';
					e.data.forEach(item=>{ date.push(item.substring(5)) })
					console.log(e,date)
					if(!_self.check){
						// 全部
						des_id = _self.AlldeviceId
						
					}else{
						// 某区域
						des_id = _self.SomedeviceId
					}
					var list = await _self.getOneNet(des_id,[e.startDate,e.endDate])
					await _self.showData(list,date);
					// 月用电量数据
					await _self.monthDate(des_id,[e.startDate,e.endDate]);
					
				}else{
					await _self.areaRquest(500);
				}
				
			},
			// 点击七天最新数据
			async sevenSelect(){
				if(!uni.getStorageSync('admin')){
					if(!_self.check){
						// 全部
						await _self.sevenData();
						// 月用电量数据
						var monthDate = _self.getData();
						await _self.monthDate(_self.AlldeviceId,monthDate);
					}else{
						_self.someSevenData(_self.SomedeviceId);
						// 月用电量数据
						var monthDate = _self.getData();
						await _self.monthDate(_self.SomedeviceId,monthDate);
					}
					
				}else{
				  await _self.areaRquest(700);
				}
				
			},
			async showMore(index){
					switch(index){
						case 0:
							console.log('亮灯率')
							_self.$refs.Pagefault.open();
						break;
						case 1:
							console.log('月用电量')
						break;
						case 2:
							console.log('故障统计')
							uni.navigateTo({
								url:'../data-display/data-display?type='+'fault'
							})
							
						break;
						case 3:
							console.log('电流')
						break;
						case 4:
							console.log('电压')
						break;
						case 5:
							this.switchReset = false;
							var [tperr,tpres] = await this.$http.get('/user/gethistory');
							let data = tpres.data.data;
							_self.switchList = data.switch;
							_self.modeId = data.mode;
							_self.mode = (data.mode==0?'节能模式':data.switch.length==3?'场景模式':'普通模式');
							this.$nextTick(() => {
							  this.switchReset = true;
							});
							_self.$refs.Pageswitc.open();
						
						break;
					}
			},
			// 获取本月的最后一天和第一天
			getData(){
				var nowDate = new Date();
				var cloneNowDate = new Date();
				var fullYear = nowDate.getFullYear();
				var month = nowDate.getMonth() + 1; // getMonth 方法返回 0-11，代表1-12月
				var endOfMonth = new Date(fullYear, month, 0).getDate(); // 获取本月最后一天
				function getFullDate(targetDate) {
				        var D, y, m, d;
				        if (targetDate) {
				            D = new Date(targetDate);
				            y = D.getFullYear();
				            m = D.getMonth() + 1;
				            d = D.getDate();
				        } else {
				            y = fullYear;
				            m = month;
				            d = date;
				        }
				        m = m > 9 ? m : '0' + m;
				        d = d > 9 ? d : '0' + d;
				        return y + '-' + m + '-' + d;
				    };
				var endDate = getFullDate(cloneNowDate.setDate(endOfMonth));//当月最后一天
				var starDate = getFullDate(cloneNowDate.setDate(1));//当月第一天
				return [starDate,endDate]
			},
			// 亮灯率的百分比
			percentage(num, total) {
			    if (num == 0 || total == 0){
			        return 0;
			    }
			    return (Math.round(num / total * 1000) / 10.00);// 小数点后一位百分比
			},
			
			// 获取日期数
			getBeforeDay(n){
				var date = new Date() ;
				var day ;
				date.setDate(date.getDate()-n);
				day = date.getDate() ;
				return day;
			},
			
			// 权限之分，仅指定用户可用
			async areaRquest(num){
				var url = `https://api.heclouds.com/devices/591488735/datapoints?limit=${num}`
				uni.request({
					url: url,
					header:{
						'content-type':'application/json;charset=UTF-8',
						'api-key':'lODwUzE3zIsoIXUd4dSRav=UZkM=',
						'Content-Type':'application/x-www-from-urlencoded',
					},
					data: {},
					method:'GET',
					success: function(res) {
						// let [err,res] = await this.$onenet.get(url);
						console.log(res);
						// if(err){ 
						// 	uni.hideLoading();
						// 	_self.tip("数据请求失败！",'none');
						// 	 return;
						// }
						var xData = ['今日',_self.getBeforeDay(1)+'日',_self.getBeforeDay(2)+'日',_self.getBeforeDay(3)+'日',_self.getBeforeDay(4)+'日',_self.getBeforeDay(5)+'日',_self.getBeforeDay(6)+'日'];
						var maxValue = 0;
						var yData1 = [0,0,0,0,0,0,0];
						var yData2 = [0,0,0,0,0,0,0];
						res.data.data.datastreams.forEach((item,index)=>{
							switch(item.id){
								case '3316_0_5700':  //电压
									console.log(Math.round(item.datapoints.length/7))
									var value = 0;
									var length = item.datapoints.length
									item.datapoints.forEach((item,index)=>{
										value = value + item.value;
									})
									_self.$set(_self.statisList[3],'value',Number((value/length).toFixed(3)));
								break;
								case '3317_0_5700':  //电流
									var value = 0;
									var length = item.datapoints.length
									item.datapoints.forEach((item,index)=>{
										value = value + item.value;
									})
									
									_self.$set(_self.statisList[4],'value',Number((value/length).toFixed(3))*1000);
								break;
								case '3331_0_5805':  //电能量
									var a = Math.round(item.datapoints.length/7)
									for(var i=0;i<7;i++){
										var datapoints = item.datapoints.slice(i*a,(i+1)*a)
										datapoints.forEach((item,index)=>{
											yData1[i] = Math.round(yData1[i]+ item.value*1000);
											if(yData1[i]>maxValue) maxValue = yData1[i];
										})
									}
									
								break;
								case '3203_0_5603':  //C02排放量
									console.log(Math.round(item.datapoints.length/7));
									var a = Math.round(item.datapoints.length/7)
									for(var i=0;i<7;i++){
										var datapoints = item.datapoints.slice(i*a,(i+1)*a)
										datapoints.forEach((item,index)=>{
											yData2[i] = Math.round(yData2[i]+ item.value*100);
											if(yData2[i]>maxValue) maxValue = yData2[i];
										})
									}
								break;
							}
						})
						 var  series=[{
						       color: "#2572f4",
						       data:yData1,
						       name: "C02排放量/g",
								legendShape: "circle",
								shape:'circle'
						   },{
							   color: "#4b46d3",
							   data:yData2,
							   name: "日用电量",
							   	legendShape: "circle",
						   }]
						   let Mix={categories:[],series:[]};
							Mix.categories=xData;
							Mix.series=series; 
							Mix.max = maxValue+10
							console.log(series)
						    _self.$refs.pageChart.showArea('canvasArea',Mix);
						  
					},
					fail: () => {
						_self.tip = ("网络错误，小程序端请检查合法域名",'none');
					}
				});
				
			},
			
			// 获取亮灯率等数据
			async history(url){
				var [tperr,tpres] = await this.$http.get(url);
				let data = tpres.data.data;
				var online = data.online==0? data.fault:data.online       //亮灯率
				_self.$set(_self.statisList[0],'value',_self.percentage(online,data.count));
				_self.$set(_self.statisList[2],'value', data.fault);
				_self.switchList = data.switch;
				_self.modeId = data.mode;
				_self.mode = (data.mode==0?'节能模式':data.switch.length==3?'场景模式':'普通模式');
				// data.switch.length==3&&modeId!=0?_self.mode='场景模式':_self.mode='普通模式'
				console.log(tpres);
			},
			
			// 权限模式，获取总电量
			async getTotal(){
				var date = _self.getData();
				console.log(date);
				var value = 0;
				var dianurl = `https://api.heclouds.com/devices/591488735/datapoints?datastream_id=3331_0_5805&start=${date[0]}T00:00:00&end=${date[1]}T00:00:00`;
				uni.request({
					url: dianurl,
					header:{
						'content-type':'application/json;charset=UTF-8',
						'api-key':'lODwUzE3zIsoIXUd4dSRav=UZkM=',
						'Content-Type':'application/x-www-from-urlencoded',
					},
					data: {},
					method:'GET',
					success: function(res) {
						try{
							res.data.data.datastreams[0].datapoints.forEach((item,index)=>{
								 value = value + item.value;
							})
							_self.$set(_self.statisList[1],'value',value.toFixed(3)*1000);
						}catch(e){
							_self.$set(_self.statisList[1],'value',0);
						}
					},
					fail: () => {
						_self.tip = ("网络错误，小程序端请检查合法域名",'none');
					}
				});
			},
			// 第一次获取十五个区域
			async getRegion(){
				let url = `/region/getregion/${_self.regionPage}/15`;
				let [regionerr,regionres] = await _self.$http.get(url);
				_self.regionList = regionres.data.data.list;
			},
			
			// 获取该用户指定区域下的所有设备ID
			async getDeviceId(data){
				let regionId = JSON.stringify(data);
				let url = `/device/getdeviceid?regionid=${regionId}&type=lamp_id`;
				let [err,res] = await _self.$http.get(url);
				let deviceId = res.data.data.join();
				_self.SomedeviceId = deviceId;
				return deviceId;
			},
			
			// 获取前几日的日期
			getBeforeDate(n) {
				var n = n;
				var d = new Date();
				var year = d.getFullYear();
				var mon = d.getMonth() + 1;
				var day = d.getDate();
				if(day <= n) {
				    if(mon > 1) {
				        mon = mon - 1;
				    } else {
				        year = year - 1;
				        mon = 12;
				    }
				}
				d.setDate(d.getDate() - n);
				year = d.getFullYear();
				mon = d.getMonth() + 1;
				day = d.getDate();
				var s = year + "-" + (mon < 10 ? ('0' + mon) : mon) + "-" + (day < 10 ? ('0' + day) : day);
				return s;
			},
			
			// 获取前几日的月份
			getBeforeMonthDay(n) {
				var n = n;
				var d = new Date();
				var year = d.getFullYear();
				var mon = d.getMonth() + 1;
				var day = d.getDate();
				if(day <= n) {
				    if(mon > 1) {
				        mon = mon - 1;
				    } else {
				        year = year - 1;
				        mon = 12;
				    }
				}
				d.setDate(d.getDate() - n);
				year = d.getFullYear();
				mon = d.getMonth() + 1;
				day = d.getDate();
				var s = (mon < 10 ? ('0' + mon) : mon) + "-" + (day < 10 ? ('0' + day) : day);
				return s;
			},
			
			// 获取oneNet平台存入数据库的数据
			async getOneNet(deviceId,date){
				var streams = _self.streams.join();
				var date = date.join();
				// var url = `/devices/${deviceId}/datapoints?datastream_id=3203_0_5603,3331_0_5805,3317_0_5700,3316_0_5700&start=${date[0]}T00:00:00&end=${date[1]}T00:00:00`
				// let [err,res] = await this.$onenet.get(url);
				var url = `/message/datastreams?dev_id=${deviceId}&ds_id=${streams}&time=${date}`;
				let [err,res] =await _self.$http.get(url);
				console.log(res)
				if(err){ 
					uni.hideLoading();
					_self.tip("数据请求失败！",'none');
					 return;
				}
				return res.data.data
			},
			
			// 数据展示
			showData(datastreams,xData){
				var num = xData.length;
				if(!datastreams.length){
					_self.tip('无数据，请重新选择!','none');
				}
				console.log(xData,datastreams)
				var maxValue = 0;
				var yData1 = new Array(num).fill(0);
				var yData2 = new Array(num).fill(0);
				
				datastreams.forEach((item,index)=>{
					switch(item.ds_id){
						case '3316_0_5700':  //电压
							// console.log(Math.round(item.datapoints.length/7))
							var value = 0;
							var length = item.list.length
							item.list.forEach((item,index)=>{
								value = value + Number(item.avg);
							})
							_self.$set(_self.statisList[3],'value',Number((value/length).toFixed(3)));
						break;
						case '3317_0_5700':  //电流
							var value = 0;
							var length = item.list.length
							item.list.forEach((item,index)=>{
								value = value + Number(item.avg);
							})
							_self.$set(_self.statisList[4],'value',Number((value/length).toFixed(3)));
						break;
						case '3331_0_5805':  //电能量
							item.list.forEach(item=>{
								if(Number(item.sum)>maxValue) maxValue = Number(item.sum);
								var i = xData.indexOf(item.create_time.substring(5))
								if(i){ yData1[i] = Number(item.sum).toFixed(3);}
							})	
						break;
						case '3203_0_5603':  //C02排放量
							item.list.forEach(item=>{
								if(Number(item.sum)>maxValue) maxValue = Number(item.sum);
								var i = xData.indexOf(item.create_time.substring(5))
								if(i){ yData2[i] = Number(item.sum).toFixed(3);}
							})
						break;
					}
				})
				 var  series=[{
				       color: "#2572f4",
				       data:yData1,
				       name: "C02排放量/kg",
						legendShape: "circle",
						shape:'circle'
				   },{
					   color: "#4b46d3",
					   data:yData2,
					   name: "日用电量/Wh",
					   	legendShape: "circle",
				   }]
				   let Mix={categories:[],series:[]};
					Mix.categories=xData;
					Mix.series=series; 
					Mix.max = maxValue+10
					console.log(series)
				    _self.$refs.pageChart.showArea('canvasArea',Mix);
				  
			},
			
			// 全部最近七天的数据
			async sevenData(){
				
				// 获取今日日期和七天前的日期
				var date = [_self.getBeforeDate(7),_self.getBeforeDate(0)]
				var list = await _self.getOneNet(_self.AlldeviceId,date);
				var xData = ['今日',_self.getBeforeMonthDay(1),_self.getBeforeMonthDay(2),_self.getBeforeMonthDay(3),_self.getBeforeMonthDay(4),_self.getBeforeMonthDay(5),_self.getBeforeMonthDay(6)]
				await _self.showData(list,xData);
			},
			// 获取本月/指定日期用电量
			async monthDate(deviceId,date){
				var date = date.join();
				var url = `/message/monthstatis?dev_id=${deviceId}&time=${date}`
				var [err,res] = await _self.$http.get(url)
				var value = res.data.data
				if(value.length){
					_self.$set(_self.statisList[1],'value',Number(value[0].sum).toFixed(3));
				}
			}
		},
		async onShow() {
			_self = this;
			uni.showLoading({title:"数据加载中..."})
			// 具体数据亮灯率等等
			await _self.history('/user/gethistory');
			// 获取前十五个区域
			await _self.getRegion();
			
			
			// 权限之分
			if(!uni.getStorageSync('admin')){
				// 获取全部设备Id
				_self.AlldeviceId = await _self.getDeviceId([0])
				await _self.sevenData()
				// 获取本月/指定日期用电量
				var monthDate = _self.getData();
				await _self.monthDate(_self.AlldeviceId,monthDate);
			}else{
				// 图表数据
				await _self.areaRquest(700);
				// 获取总电量
				await _self.getTotal();
			}
			
			uni.hideLoading();
		},
		 // onReady: function (e) {
			// var screenWidth
			// uni.getSystemInfo({
			// 	success: function (res) {
			// 		console.log(res)
			// 		screenWidth = res.windowWidth/2
			// 		// that.windowHeight = res.windowHeight;
			// 	}
			// });
		 //    var ctx = uni.createCanvasContext('firstCanvas')
			// ctx.beginPath()
			// ctx.moveTo(0, 0)
			// ctx.quadraticCurveTo(screenWidth, 180, screenWidth*2, 0)
			// ctx.setFillStyle('#6390C3')
			// ctx.fill()
			// ctx.draw()
		 //    },
	}
</script>

<style scoped>
	/* #ifdef APP-PLUS */
		view{
			font-size: 33rpx;
		}
	/* #endif */
	.title{
		background-color: #6390C3;
		color: #FFFFFF;
		font-size: 35rpx;
		padding-bottom: 20rpx;
	}
	.region{
		color: rgba(255,255,255,.8);
		margin-left: 150rpx;
	}
	.back-top{
		position: absolute;
		top: 0;
		left: 0;
		z-index: -11;
	}
	
	.back-bottom{
		width:100vw;
		height: 145rpx;
		border-radius: 50vw;
		border-top-left-radius: 0;
		border-top-right-radius: 0;
		background-color: #6390C3;
	}
	.statis{
		position: relative;
	}
	.form{
		flex-wrap: wrap;
		width: 80vw;
		margin: 0rpx auto 0;
		padding-top:20rpx ;
	}
	.statis-one{
		width: 29%;
		height: 220rpx;
		box-sizing: border-box;
		background-color:#FFFFFF;
		border-radius: 5px;
		border: 3rpx solid #eeeeee;
		/* box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.1); */
		font-size: 28rpx;
		margin-bottom: 40rpx;
	}
	.statis-one-icon{
		font-size: 90rpx;
		margin: 10rpx 0;
	}
	.statis-one-value{
		font-size: 25rpx;
	}
	.light{
		padding: 40rpx 50rpx;
		background-color: #FFFFFF;
	}
	.light-text-top{
		margin-bottom: 10rpx;
		color: #494B68;
	}
	.light-text-bottom{
		color: #494B68;
	}
</style>
