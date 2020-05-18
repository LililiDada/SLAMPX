 <template>
	<view>
		<view class="detail-control">
			<view class="control-right u-f-ac">
				<image src="/static/iconfont/bright.png" lazy-load></image>
				<text>灯泡亮度</text>
				<input type="text" class="input light-input"  @confirm='sendlight'  @input='newInput'  placeholder="多个亮度请用-隔开" placeholder-style="color:#494b68" :value="brightValue"/>
				<button type="default" @click.stop="queryValue">确定</button>
			</view>
			<view class="control-left u-f-ac">
				<image src="/static/iconfont/mode.png" lazy-load></image>
				<text>灯泡模式</text>
				<view class="u-f-ac">
					<mode-picker @picker="picker"></mode-picker>
				</view>
				<button type="default" @click="sendMode">确定</button>
				<view class="icon iconfont icon-tianjia" @tap="timeSetting" v-if="pickerIndex!=0"></view>
			</view>
		</view>
		<scroll-view class="detail" scroll-y="true">
			<view class="deltai-left" v-for="(items,index) in markerDetail" :key="index">
				<image :src="items.img" lazy-load></image>
				<view class="u-f-ac">
					<text>{{items.stream}}__</text>
					<text>{{items.value}}{{items.unit}}</text>  
				</view>
				<image src="/static/iconfont/record.png" lazy-load @click="history(items)" v-if="items.chart"></image>
			</view>
		</scroll-view>
		
		<uni-popup ref="popup" type="center">
			<charts ref="charts"></charts>
		</uni-popup>
		
		
		<regulate-mode ref='regulate' :pickerIndex='pickerIndex' :modeTitle ='modeTitle'  :list='list' @queryMode='queryMode'></regulate-mode>
	</view>
</template>

<script>
	import ModePicker from '../../components/picker.vue';
	import charts from '../../components/charts.vue';
	import uniPopup from "@/components/uni-popup.vue";
	import regulateMode from "../../components/record/regulate-mode.vue";
	// 定义全局变量
	var _self, ct_id;
	var canvasLineA = null;
	export default {
		components: {
			uniPopup,
			ModePicker,
			charts,
			regulateMode,
		},
		data() {
			return {
				pickerIndex:0,
				modeTitle:'',
				list:[],
				des_id:0,  //数据库设备id
				lamp_id:0,
				scene_id:0,
				plain_id:0,
				brightList:[],
				spectacleList:[],
				scrollHeight:"",
				popupTitle:'',
				outputArr: [],
				dateArr: [],
				brightValue: '',
				mode: true,
				markerDetail: [{
					id:'status',
					stream:'运行状态',
					value:'正常',
					unit:'',
					img:'/static/iconfont/status.png',
					chart:false
				},{
					id:'3311_0_5851',
					stream:'最新亮度',
					value:'78',
					unit:'',
					img:'/static/iconfont/dimming.png',
					chart:false
				},{
					id:'3331_0_5805',
					stream: '有功总电量',
					value: '0.005',
					unit:'Wh',
					img: '/static/iconfont/quantity.png',
					chart:true
				},
				{
					id:'3301_0_5700',
					stream: '光照强度',
					value: '20',
					unit:'lx',
					img: '/static/iconfont/beam.png',
					chart:true
				},
				// {
				// 	id:'3303_0_5700',
				// 	stream: '环境温度',
				// 	value: '20',
				// 	unit:'℃',
				// 	img: '/static/iconfont/celsius.png',
				// 	chart:true
				// }, 
				// {
				// 	id:'3304_0_5700',
				// 	stream: '环境湿度', 
				// 	value: '20',
				// 	unit:'RH',
				// 	img: '/static/iconfont/temperature.png',
				// 	chart:true
				// }, 
				{
					id:'3316_0_5700',
					stream: '电压',
					value: '20',
					unit:'V',
					img: '/static/iconfont/voltage.png',
					chart:true
				}, 
				{
					id:'3317_0_5700',
					stream: '电流',
					value: '20',
					unit:'mA',
					img: '/static/iconfont/electric.png',
					chart:true
				}, {
					id:'3328_0_5700',
					stream: '有用功率',
					value: '20',
					unit:'W',
					img: '/static/iconfont/useful.png',
					chart:true
				}, 
				// {
				// 	id:'3328_1_5700',
				// 	stream: '功率因数',
				// 	value: '20',
				// 	unit:'',
				// 	img: '/static/iconfont/power.png',
				// 	chart:true
				// },
				{
					id:'3318_0_5700',
					stream: '频率',
					value: '20',
					unit:'Hz',
					img: '/static/iconfont/frequency.png',
					chart:true
				},
				// {
				// 	id:'3303_1_5700',
				// 	stream: '芯片温度',
				// 	value: '20℃',
				// 	unit:'℃',
				// 	img: '/static/iconfont/gentle.png',
				// 	chart:true
				// },
				{
					id:'3203_0_5603',
					stream: '二氧化碳排量',
					value: '0.005',
					unit:'g',
					img: '/static/iconfont/carbon.png',
					chart:true
				},{
					id:'region',
					stream:'所属区域',
					value:'南区',
					unit:'',
					img:'/static/iconfont/region.png',
					chart:false
				},{
					id:'address',
					stream:'地址',
					value:'广东省韶关市浈江区大学路韶关学院科技楼',
					unit:'',
					img:'/static/iconfont/address.png',
					chart:false
				}]
			}
		},
		methods: {
		
			async history(e) {
				console.log(e);
				_self.popupTitle = e.stream;
				this.$refs.popup.open();
				let url = `/devices/${this.lamp_id}/datapoints`
				let [err,res] = await this.$onenet.get(url,{
					datastream_id:e.id,
					limit:10
				})
				console.log(res);
				if(err){
					uni.showToast({title:"获取失败！！",icon:'none'})
					return;
				}
				var datapoints = res.data.data.datastreams[0].datapoints;
				var maxValue = 0;
				let LineA = {categories:[],series:[{
						name:e.stream,
						data:[]
				}],max:50};
				console.log(datapoints)
				datapoints.forEach((item,value)=>{
					if(e.id == '3317_0_5700' || e.id == '3331_0_5805'|| e.id == '3300_1_5700'){
						
						if(item.value * 1000>maxValue) maxValue = item.value* 1000;
						LineA.categories.push(item.at.slice(11,16));
						LineA.series[0].data.push((item.value*1000).toFixed(1));
					}else{
						if(item.value>maxValue) maxValue = item.value;
						LineA.categories.push(item.at.slice(11,16));
						LineA.series[0].data.push(item.value.toFixed(3));
					}
					
				})
				LineA.max = Math.ceil(maxValue + 5);
				LineA.unit = e.unit;
				console.log(LineA)
				_self.$refs.charts.popupTitle = e.stream;
				_self.$refs.charts.showLineA('canvasLineA',LineA);
			},
			// 修改亮度
			async sendlight(e) {
				this.pot(e.detail.value)
			},
			// 修改亮度
			async queryValue(){
				this.pot(this.brightValue)
				
			},
			
			// 封装修改亮度
			async pot(value){
				let that = this;
					if(!value){
						uni.showToast({
							title:"请正确填写路灯亮度",
							icon:"none",
							duration:2000
						})
						return;
					}
					else if(this.pickerIndex == 0){
						return uni.showModal({
						    title: '温馨提示',
						    content: '此时路灯模式为自适应模式，请选择节能模式或普通模式再修改路灯亮度',
						    success: function (res) {
						
						    },
						});
					}
					else if(this.pickerIndex == 1){
						console.log(_self.brightList)
						if(_self.brightList.length < 0){
							return  _self.reminder();
						}
						var light = (value.split("-"));
						console.log(light)
						if(light.length<3){
							return uni.showModal({
								title: '温馨提示',
								content: '请用正确的英文字符横杠-把3个亮度隔开填写后按确定',
								success: function (res) {
								},
							});
						}
						var breaks = false,lightvalue='';
						light.forEach((item,index)=>{
							item = Number(item);
							console.log(item);
							if( item>100){
								breaks = true;
								uni.showModal({
										title: '温馨提示',
										content: '亮度在0-99之间',
										success: function (res) {
										},
									});
							}
							if(item%1 != 0){
								breaks = true;
								uni.showModal({
									title: '温馨提示',
									content: '请填写亮度为整数',
									success: function (res) {
									},
								});
							}
							if(breaks) return;
							if(item<10){
								item = `0${item}`;
							}
							lightvalue = index==2?lightvalue + `${item}`:lightvalue +`${item}-`
						})
						if(breaks) return;
						console.log(lightvalue)
						var timevalue = _self.slice(_self.brightList);
						// var lightvalue = _self.sliceLight(_self.brightList)
						var setvalue = `${timevalue}${lightvalue}`;
						console.log(setvalue);
						uni.showLoading({
							title:"设置中..."
						})
											
						var time = {
							obj_id:3300,
							obj_inst_id:0,
							data:[{
								res_id:5750,
								val:setvalue
							}]
						}
						var moderes = await that.onenet(time);
						uni.hideLoading();
						if(moderes.data.errno ==0) {
							uni.showToast({title:"设置成功",duration:2000})
						}
						else{
							uni.showToast({title:"设置失败,缓存命令最多下发十次",icon:'none',duration:2000})
						}
						_self.brightValue = '';
					}
					else if(this.pickerIndex == 2){
						if(_self.spectacleList.length < 0){
							return  _self.reminder();
						}
						if(!Number(value)){
							return uni.showModal({
								title: '温馨提示',
								content: '灯泡亮度为整数,且仅填写一个即可',
								success: function (res) {
								},
							});
						}
						if(Number(value)<10){
							value = '0'+value;
						}
						var timevalue = _self.slice(_self.spectacleList);
						// var lightvalue = _self.sliceLight(_self.spectacleList)
						var setvalue = `${timevalue}${value}`;
						console.log(setvalue);
						uni.showLoading({
							title:"设置中..."
						})
						var time = {
							obj_id:3300,
							obj_inst_id:1,
							data:[{
								res_id:5750,
								val:setvalue
							}]
						}
						var moderes = await that.onenet(time);
						console.log(moderes);
						uni.hideLoading();
						if(moderes.data.errno ==0) {
							uni.showToast({title:"设置成功",duration:2000})
						}
						else{
							uni.showToast({title:"设置失败,缓存命令最多下发十次",icon:'none',duration:2000})
						}
						_self.brightValue = '';
					}
			},
			getDate(){
				var date=new Date();
				date.setDate(date.getDate()+1);
				var year=date.getFullYear();  //年
				var month = date.getMonth() + 1<10? "0"+(date.getMonth() + 1):date.getMonth() + 1;  //月
				var day = date.getDate()<10? "0"+(date.getDate()):date.getDate();  //日
				var hh = date.getHours()<10? "0"+(date.getHours()):date.getHours(); //时
				var mm = date.getMinutes()<10? "0"+(date.getMinutes()):date.getMinutes();  //分
				var ss = date.getSeconds()<10? "0"+(date.getSeconds()):date.getSeconds(); //秒
				var rq=year+"-"+month+"-"+day+"T"+hh+":"+mm+":"+ss;
				return rq;
			},
			async onenet(e){
				let netUrl = `/nbiot?imei=${_self.imei}&obj_id=${e.obj_id}&obj_inst_id=${e.obj_inst_id}&mode=2`;
				let [neterr,netres] = await this.$onenet.post(netUrl,{
					data:e.data
				})
				uni.hideLoading()
				if(neterr){
					uni.showToast({title:"请求失败！！",icon:"none",duration:2000});
					return;
				}
				if(netres.data.errno == 2001){
					var expired_time = _self.getDate();
					var url = `/nbiot/offline?imei=${_self.imei}&obj_id=${e.obj_id}&obj_inst_id=${e.obj_inst_id}&mode=2&expired_time=${expired_time}`;
					var [err,res] = await this.$onenet.post(url,{
						data:e.data
					})
					console.log(res)
					return res;
					// uni.showToast({title:"设备不在线，不可操作！！",icon:"none",duration:2000});
				}
				console.log(netres.data.errno);
				// if(netres.data.errno !=0 || netres.data.errno !=2001){
				// 	uni.showToast({title:netres.data.error,icon:"none",duration:2000});
				// 	return false;
				// }
				return netres;
			},
			// 拼接
			slice(event){
				var value ='';
				// index==event.length-1?value + `${item.start_time}:${item.start_min}-${item.end_time}:${item.end_min}`:
				event.forEach((item,index)=>{
					value = index==event.length-2?value:value + `${item.start_time}:${item.start_min}-${item.end_time}:${item.end_min}-`
				})
				return value;
			},
			// 拼接亮度
			sliceLight(event){
				var value ='';
				event.forEach((item,index)=>{
					value = index==event.length-1?value + `${item.light}`:value +`${item.light}-`
				})
				return value;
			},
			// 确定发送模式
			async queryMode(){
				var that = this;
				switch (this.pickerIndex){
					case 0:
						console.log('确定0')
						uni.showLoading({
							title:"设置中..."
						})
						var mode = {
							obj_id:3341,
							obj_inst_id:0,
							data:[{
								res_id:5528,
								val:2
							}]
						}
						var moderes = await that.onenet(mode);
						uni.hideLoading();
						if(moderes.data.errno ==0) {
							uni.showToast({title:"设置成功",duration:2000})
						}
						else{
							uni.showToast({title:"设置失败,缓存命令最多下发十次",icon:'none',duration:2000})
						}
					break;
					case 1:
						var timevalue = _self.slice(_self.brightList);
						var lightvalue = _self.sliceLight(_self.brightList)
						var setvalue = `${timevalue}${lightvalue}`;
						console.log(setvalue);
						console.log(timevalue);
						uni.showLoading({
							title:"设置中..."
						})
						// var mode = {
						// 	obj_id:3341,
						// 	obj_inst_id:0,
						// 	data:[{
						// 		res_id:5528,
						// 		val:3
						// 	}]
						// }
						var mode = {
							obj_id:3300,
							obj_inst_id:0,
							data:[{
								res_id:5750,
								val:setvalue
							}]
						}
						// var light1 = {
						// 	obj_id:3311,
						// 	obj_inst_id:1,
						// 	data:[{
						// 		res_id:5851,
						// 		val:_self.brightList[0].light
						// 	}]
						// }
						// var light2 = {
						// 	obj_id:3311,
						// 	obj_inst_id:2,
						// 	data:[{
						// 		res_id:5851,
						// 		val:_self.brightList[1].light
						// 	}]
						// }
						// var light3 = {
						// 	obj_id:3311,
						// 	obj_inst_id:3,
						// 	data:[{
						// 		res_id:5851,
						// 		val:_self.brightList[2].light
						// 	}]
						// }
						var moderes = await that.onenet(mode);
						uni.hideLoading();
						if(moderes.data.errno ==0) {
							uni.showToast({title:"设置成功",duration:2000})
						}
						else{
							uni.showToast({title:"设置失败,缓存命令最多下发十次",icon:'none',duration:2000})
						}
						// if(moderes && moderes.data.errno ==0) var timeres = await that.onenet(time);
						// if(timeres && timeres.data.errno ==0)  var light1res = await _self.onenet(light1);
						// if(light1res && light1res.data.errno ==0)  var light2res = await _self.onenet(light1);
						// if(light2res && light2res.data.errno ==0)  var light3res = await _self.onenet(light1);
						// 
						// if(light3res && light3res.data.errno ==0 ) uni.showToast({title:"设置成功",duration:2000})
					break;
					case 2:
						var timevalue = _self.slice(_self.spectacleList);
						var lightvalue = _self.sliceLight(_self.spectacleList)
						var setvalue = `${timevalue}${lightvalue}`;
						console.log(setvalue);
						uni.showLoading({
							title:"设置中..."
						})
						// var mode = {
						// 	obj_id:3341,
						// 	obj_inst_id:0,
						// 	data:[{
						// 		res_id:5528,
						// 		val:1
						// 	}]
						// }
						var time = {
							obj_id:3300,
							obj_inst_id:1,
							data:[{
								res_id:5750,
								val:setvalue
							}]
						}
						var moderes = await _self.onenet(time);
						uni.hideLoading();
						if(moderes.data.errno ==0) {
							uni.showToast({title:"设置成功",duration:2000})
						}
						else{
							uni.showToast({title:"设置失败",icon:'none',duration:2000})
						}
						// var light = {
						// 	obj_id:3311,
						// 	obj_inst_id:0,
						// 	data:[{
						// 		res_id:5851,
						// 		val:_self.spectacleList[0].light
						// 	}]
						// }
						// if(moderes && moderes.data.errno ==0) var timeres = await that.onenet(time);
						// if(timeres && timeres.data.errno ==0)  var lightres = await _self.onenet(light);
						// 
						// if(lightres && lightres.data.errno ==0 ) uni.showToast({title:"设置成功",duration:2000})
					break;
				}
				
			},
			reminder(){
				uni.showModal({
				    title: '温馨提示',
				    content: '当前设备未选择的默认时间段和亮度，请按右上角按钮选择',
				    success: function (res) {
				        if (res.confirm) {
				            console.log('用户点击确定');
				        } else if (res.cancel) {
				            console.log('用户点击取消');
				        }
				    },
				});
			},
			// 发送模式
			async sendMode() {
				var that = this;
				console.log(this.pickerIndex)
				switch(this.pickerIndex){
					case 0:
						_self.modeTitle = '自适应模式',
						_self.list = [];
						_self.$refs.regulate.$refs.showtip.open();
					break;
					case 1:
						if(this.brightList.length==0){
							_self.reminder();
							break;
						}
						_self.modeTitle = '节能模式',
						_self.list = _self.brightList;
						_self.$refs.regulate.$refs.showtip.open();
					break;
					case 2:
						if(this.spectacleList.length==0){
							_self.reminder();
							break;
						}
						_self.modeTitle = '正常模式',
						_self.list = _self.spectacleList;
						_self.$refs.regulate.$refs.showtip.open();
					break;
						
				}
				
				
			},
			// 显示模式时间表
			timeSetting(){
				if(this.pickerIndex ==1) uni.navigateTo({url:"../scene/scene?id="+this.scene_id})
				if(this.pickerIndex ==2) uni.navigateTo({url:"../region-list/region-list?id="+this.plain_id})
			},
			// 下拉框
			picker(e){
				this.pickerIndex = parseInt(e)
			},
			// 获取亮度
			newInput(event){
				this.brightValue = event.target.value;
			},
			forment(list){
				var brightList=[];
				list.forEach((item,value)=>{
					var data = {
						id:item.id,
						start_time:item.start_time,
						start_min:item.start_min,
						end_time:item.end_time,
						end_min:item.end_min,
						light:item.light,
					}
					brightList.push(data);
				});
				return brightList;
			},
			listDtat(id,value){
				switch(id){
					case "3000_0_0000" :
						console.log(id,value)
						// 亮度
						_self.$set(_self.markerDetail[1],'value', value);
					break;
					case "3331_0_5805" :
						// 有功总电量
						console.log(id,value)
						_self.$set(_self.markerDetail[2],'value', value*1000);
					break;
					// case "3303_0_5700" :
					// 	console.log(id,value)
					// 	_self.$set(_self.markerDetail[3],'value', value);
					// break;
					// case "3304_0_5700" :
					// 	console.log(id,value)
					// 	_self.$set(_self.markerDetail[4],'value', value);
					// break;
					case "3301_0_5700" :
						console.log(id,value)
						// 光照强度
						_self.$set(_self.markerDetail[3],'value', value);
					break;
					case "3316_0_5700" :
						console.log(id,value)
						// 电压
						_self.$set(_self.markerDetail[4],'value', value);
					break;
					case "3317_0_5700" :
						console.log(id,value)
						// 电流
						_self.$set(_self.markerDetail[5],'value', (value*1000).toFixed(3));
					break;
					case "3328_0_5700" :
						console.log(id,value)
						// 有用功率
						_self.$set(_self.markerDetail[6],'value', value);
					break;
					// case "3328_1_5700" :
					// 	console.log(id,value)
					// 	_self.$set(_self.markerDetail[8],'value', value);
					// break;
					case "3318_0_5700" :
						console.log(id,value)
						// 频率
						_self.$set(_self.markerDetail[7],'value', value);
					break;
					case "3203_0_5603" :
						// 二氧化碳
						console.log(id,value)
						_self.$set(_self.markerDetail[8],'value', value);
					break;
					// case "3300_1_5700" :
					// 	console.log(id,value)
						
					// 	_self.$set(_self.markerDetail[11],'value', value*1000);
					// break;
					case "3341_1_5528":
						console.log(id,value)
						// 故障设备
						_self.$set(_self.markerDetail[0],'value', value);
					break;
				}
			}
		},
		async onLoad(e) {
			console.log(e);
			_self = this;
			this.des_id = e.des_id;
			uni.showLoading({title: '加载中'});
			let url = `/device/devicedetail/${e.des_id}`
			var [err,res] = await this.$http.get(url)
			console.log(err,res)
			var list = res.data.data.list
			_self.brightList = _self.forment(list.scene);
			_self.spectacleList = _self.forment(list.plain);
			_self.scene_id = list.scene_id;
			_self.plain_id = list.plain_id;
			_self.imei = list.imei;
			console.log(_self.brightList,_self.spectacleList)
			
			// 权限之分
			if(!uni.getStorageSync('admin')){
				uni.setNavigationBarTitle({
					title: list.lamp_id
				});
			}else{
				uni.setNavigationBarTitle({
					title: e.des_id<10?`5121260000${e.des_id}`:`5121260000${e.des_id}`
				});
			}
			
			
			_self.lamp_id = list.lamp_id;
			if(list.status) {this.markerDetail[0].value  = '正常';}
			else { this.markerDetail[0].value = '故障';}
			this.markerDetail[10].value = list.address;
			
			this.markerDetail[9].value = list.region?list.region.regionname:'未选择区域';
			let netUrl = `/devices/datapoints`;
			let [neterr,netres] = await this.$onenet.get(netUrl,{
				devIds:list.lamp_id
			})
			console.log(neterr,netres);
			// 设备不在线，亮度直接为0 
			// if(!list.online){
			// 	console.log('不在线');
			// 	_self.listDtat('3000_0_0000',0);
			// }
			let datastreams = netres.data.data.devices[0].datastreams
			datastreams.forEach((item,value)=>{
				if(item.id == '3341_1_5528'){  //设备故障
					return;
				}
				// 当前模式为正常模式
				if(item.id == '3341_0_5528'  && item.value == 1){
					var index = datastreams.findIndex((val)=>val.id== '3300_1_5750');
					var value = datastreams[index].value;
					console.log('最新亮度' + value.substring(value.length-2));
					_self.$set(_self.markerDetail[1],'value',value.substring(value.length-2));
				}
				// 当前模式为自适应模式
				if(item.id == '3341_0_5528'  && item.value == 2){
					var index = datastreams.findIndex((val)=>val.id== '3300_1_5750');
					var value = datastreams[index].value;
					console.log('最新亮度' + value.substring(value.length-2));
					_self.$set(_self.markerDetail[1],'value',value.substring(value.length-2));
				}
				// 当前模式为节能模式&& 
				if(item.id == '3341_0_5528'  && item.value == 3){
					var index = datastreams.findIndex((val)=>val.id== '3300_0_5750');
					var value = datastreams[index].value;
					var length = value.length;
					var timeArr = [];
					timeArr[0] = datastreams[index].value.substr(0,5)
					timeArr[1] = datastreams[index].value.substr(6,5)
					timeArr[2] = datastreams[index].value.substr(12,5)
					timeArr[3] = datastreams[index].value.substr(18,5)
					var currentHours = new Date().getHours();
					var currentmin = new Date().getMinutes();
					var date  = new Date().toLocaleDateString();
					var curDate = `${date} ${currentHours}:${currentmin}`;
					for(var i=0;i<timeArr.length;i++){
						var next,flag = 0,maxDate,minDate,temp;
						next = !timeArr[i+1]?0:i+1;
						maxDate = `${date} ${timeArr[next]}`;
						minDate = `${date} ${timeArr[i]}`;
						if(new Date(maxDate) < new Date(minDate)){
							tmp = maxDate;
							maxDate = minDate;
							minDate = tmp;
						}
						// if(new Date(maxDate) > new Date(minDate)){
							if (new Date(curDate) >= new Date(minDate) && new Date(curDate) <= new Date(maxDate)) {
								flag = 1;
							    if(i==0 || i==3){
									// var index = datastreams.findIndex((val)=>val.id== '3311_1_5851'); 
									// this.markerDetail[1].id = '3311_1_5851';
									// return _self.listDtat('3000_0_0000',datastreams[index].value)
									_self.$set(_self.markerDetail[1],'value',value.substring(length-8,length-6));
								}else if(i ==1){
									// var index = datastreams.findIndex((val)=>val.id== '3311_2_5851');
									// this.markerDetail[1].id = '3311_2_5851';
									// return _self.listDtat('3000_0_0000',datastreams[index].value)
									_self.$set(_self.markerDetail[1],'value',value.substring(length-5,length-3));
								}else{
								   // var index = datastreams.findIndex((val)=>val.id== '3311_3_5851');
								   // this.markerDetail[1].id = '3311_3_5851';
								   // return _self.listDtat('3000_0_0000',datastreams[index].value)
								   _self.$set(_self.markerDetail[1],'value',value.substring(length-2,length));
								}
							}
							// if(flag == 1) return;
						// }else{
						// 	if (new Date(curDate) >= new Date(maxDate) && new Date(curDate) <= new Date(minDate)) {
						// 	   flag = 1;
						// 	   if(i==0 || i==3){
						// 	   	var index = datastreams.findIndex((val)=>val.id== '3311_1_5851');
						// 	   	this.markerDetail[1].id = '3311_1_5851';
						// 	   	return _self.listDtat('3000_0_0000',datastreams[index].value)
						// 	   }else if(i ==1){
						// 	   	var index = datastreams.findIndex((val)=>val.id== '3311_2_5851');
						// 	   	this.markerDetail[1].id = '3311_2_5851';
						// 	   	return _self.listDtat('3000_0_0000',datastreams[index].value)
						// 	   }else{
						// 		   var index = datastreams.findIndex((val)=>val.id== '3311_3_5851');
						// 		   this.markerDetail[1].id = '3311_3_5851';
						// 		   return _self.listDtat('3000_0_0000',datastreams[index].value)
						// 	   }
						// 	}
						// 	if(flag == 1) return;
						// }
						if(flag == 1) return;
						
					}
				}
				_self.listDtat(item.id,item.value);
			});
			
			_self.$chat.Open();
			
			// 实时监听数据变化
			uni.$on('message',function(data){
				console.log(data);
				if(_self.lamp_id == data.dev_id){
					if(data.ds_id == '3341_1_5528'){  //设备故障
						var value;
						switch(data.value){
							case 0:
							    value = '正常';
							case 1:
								value = '测量芯片温度过高'
							break;
							case 2:
								value = '开发板断电故障'
							break;
						}
						_self.listDtat(data.ds_id,value);
					}else{
						_self.listDtat(data.ds_id,data.value);
					}
				}
			})
			
			
			uni.hideLoading();
		},
		onUnload(){
			console.log('onUnload')
			_self.$chat.Close();
		},
		onHide() {
			console.log('onHide');
		},
		onShow: function() {
			
		}
		
	}
</script>

<style scoped>
	.qiun-bg-white {
		background: #FFFFFF;
	}

	.qiun-title-bar {
		/* width: 96%; */
		padding: 10upx 3%;
		flex-wrap: nowrap;
	}

	.qiun-common-mt {
		margin-top: 10upx;
	}

	.qiun-title-dot-light {
		border-left: 10upx solid #0ea391;
		padding-left: 10upx;
		font-size: 32upx;
		color: #666666;
	}
	.mode-title{
		background-color: #E5E5E5;
		padding: 10upx 2%;
	}
	.mode-title>view:nth-of-type(1){
		color: #666666;
		margin-right: 10upx;
	}
	.mode-title>view:nth-of-type(2){
		border-left: none;
	}

	/* 通用样式 */
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

	.detail{
		/* height: 920upx; */
		height: calc(100vh - 285upx);
	}
	.detail-title {
		text-align: center;
		line-height: 30upx;
	}

	.deltai-left {
		border: 2upx solid #edeff8;
		padding: 3upx 20upx;
		margin: 15upx auto;
		width: 85%;
		height: 80upx;
		display: flex;
		/* justify-content: space-around; */
		align-items: center;
		font-size: 30upx;
		color: #494b68;
		background-color: #f3f4f9;
	}

	.deltai-left>image:first-child {
		width: 50upx;
		height: 50upx;
		margin-right: 20upx;
	}

	.deltai-left>image:last-child {
		width: 40upx;
		height: 40upx;
	}

	.deltai-left>view {
		flex: 1;
	}

	.detail-control {
		height: 265upx;
		background-color: #6390C3;
		padding-top: 20upx;
		font-size: 30upx;
		box-shadow: 2upx 2upx 5upx #C8C7CC;
		/* border-bottom:1upx solid #6390C3; */
		/* display: flex; */
		border-radius: 0 0 10upx 10upx;
		justify-content: space-around;
		position: relative;
	}

	.detail-control image {
		width: 50upx;
		height: 50upx;
		margin: 0 10upx 0 20upx;
	}

	.switch {
		width: 70%;
		height: 60upx;
		line-height: 60upx;
		border-radius: 25upx;
		text-align: center;
	}

	.control-left,
	.control-right {
		color: #FFFFFF;
		background-color: #216ABD;
		width: 95vw;
	}

	.control-left {
		width: 90vw;
		height: 115upx;
		position: absolute;
		bottom: 30upx;
		right: 0;
		border-radius: 10upx 0  0 10upx;
	}

	.control-right {
		height: 100upx;
		margin-bottom: 20upx;
		border-radius: 0 10upx 10upx 0;
	}
	.control-left>text,.control-right>text{
		margin: 0 20upx 0 15upx;
	}
	.input {
		border-radius: 25upx;
		background-color: #FFFFFF;
		position: relative;
		padding-left: 30upx;
		width: 300upx;
		color: #3F536E;
		height: 68upx;
		line-height: 68upx;
		border: 1upx solid #3F536E;
	}
	.control-left>view:nth-of-type(1){
		background-color: #FFFFFF;
		width: 325upx;
		height: 68upx;
		color: #3F536E;
		border-radius: 25upx;
		padding-left: 30upx;
		border: 1upx solid #3F536E;
	}
	.control-right>button::after,.control-left>button::after{ 
		border: none;
		border-radius: 0;
	}

	.control-right>button,.control-left>button{
		width: 140upx;
		height: 63upx;
		line-height: 63upx;
		margin-left: -144upx;
		border-radius:23upx;
		font-size: 30upx;
		color: #3F536E;
		border-left: 1upx solid #3F536E;
	}
	.control-right>button{
		margin-left: -38upx;
		height: 68upx;
		line-height: 68upx;
		/* border: 1upx solid #3F536E; */
	}
	.light-input{
		width: 320upx;
	}
	.control-left>.icon{
		position: absolute;
		top:-15upx;
		right: 10upx;
		color: #FFFFFF;
		font-size: 50upx;
	}
	.mode-list{
		margin:35upx 0 0 20upx;
		color: #666666;
	}
	.set{
		background-color: #FFFFFF;
		position: relative;
	}
	.set>view.icon{
		position: absolute;
		top:0upx;
		right: 30upx;
		color: #6390C3;
		font-size: 55upx;
	}
</style>
