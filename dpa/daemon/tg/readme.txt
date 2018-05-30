一、行情
   1) 个股的行情均来自数据提供方，他们提供的flash行情，我们在页面以类似iframe的方式嵌入即可
   2) 大盘指数行情我们通过抓取其接口，获得分时行情数据。并且在前端能进行调用：
      http://hq.sinajs.cn/list=twi1,twi1_i  
      其中 _i是公司资料部分，例如中文名称等。

二、图片
1) 个股全部使用数据提供方的flash，没有图片
2) 大盘有三张小图是数据提供方，并下载到我们自己的服务器上来的。
   上市指数、柜台指数、台指期货走势图。
   见首页：http://vip.stock.finance.sina.com.cn/q/view/tw.php

3) 程序部署在： 61.135.152.74  finance帐号下
   # tw stock : grap and send index minline image
   50 8 * * 1-5 /usr/home/finance/projects/cron/tw_zq/run_grap.sh > /dev/null
   
三、flash地址:

http://210.5.28.134/cqs/Cat9IndexChart.swf
http://210.5.28.134/cqs/Category.swf
http://210.5.28.134/cqs/FTA.swf
http://210.5.28.134/cqs/IndexChart.swf
http://210.5.28.134/cqs/MulitSEIFChart.swf
http://210.5.28.134/cqs/StockChart.swf
http://210.5.28.134/cqs/StockVolumeAtPrice.swf

我们均下载下来了。如果该服务器能支撑，则可先外链他们的flash，如果他们的服务器压力大，也可以换成我们的flash


四、另外：

这里的抓取程序都是在本地跑的，没有采用libstart，不过都有日志记录。

由于考虑到对表和字段不清楚，因此采用的是自动创建表和自动增加字段的方式。

目前程序运行比较稳定，为了效率，暂时注释掉了字段自动创建和自动创建表功能

ue、其他说明和文档均在 docs目录下

抓取程序部署在: 

202.108.6.137   /data1/SINA/projects/deve/tw_grab 下， 并且在finance帐号下有crontab里面也有

五、联系人

台北联系方式：副手 鄭明坤
+886 2 77201888 Ext 鄭明坤, 分機7319
matt_jam@hotmail.com
mattc@systex.com.tw

陳孝強 Alex Chen
資深處長　富聯網研發處
Senior Director, MONEY LINK Products R&D Department
 精誠資訊股份有限公司
 114台北市內湖區瑞光路318號
 T + 886-2-7720-1888　ext 1281
手机: +886 931090202
 F + 886-2-8798-7986
MSN; alexchen74@hotmail.com
 www.systex.com.tw

还有一个msn：jimchien@ooommm.com 不知道是干啥的。


六、定时任务
###########   taigu   ###########
# sector_mem , dapan hangqing data
* 9-14 * * 1-5 cd projects/deve/tw_grab; php sector_mem.php > log/sector_mem.log
*/5 9-18 * * 1-5 cd projects/deve/tw_grab; php paihang.php > log/paihang.log
2 9-18 * * 1-5 cd projects/deve/tw_grab; php sandafaren.php > log/sandafaren.log
20 */2 * * 1-5 cd projects/deve/tw_grab; php dapan_info.php > log/dapan_info.log
7 8 * * 1-5  cd projects/deve/tw_grab; php info.php > log/info.log
