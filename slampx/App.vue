<script>
	export default {
		onLaunch: function() {
			// 网络监听（用户目前断网，切换wifi)
			this.lib.NetWork.On();
			
			console.log('hello')
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
			
		}
	}
</script>

<style>
	
		
	/* #ifndef APP-PLUS-NVUE */
		/* 引入动画库 */
		@import './common/animate.css';
		/*每个页面公共css */
		@import './common/iconfont.css';
		/* 引入公共css */
		@import './common/common.css';
	/* #endif */
	
</style>
