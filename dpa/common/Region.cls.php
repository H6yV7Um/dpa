<?php
/**
*  关于地区的数据，city数据列表已经有了，对它的操作有：重新排序、根据id取得city名、根据city名取得id
*  全部是数组，给县级留有余地
*/
class Region{
  // 原始数据，方便记忆和查看的city列表
  var $city_list = array(
  array
  ('北京市',
  array('东城区'),
  array('西城区'),
  array('崇文区'),
  array('宣武区'),
  array('朝阳区'),
  array('海淀区'),
  array('丰台区'),
  array('石景山区'),
  array('门头沟区'),
  array('房山区'),
  array('通州区'),
  array('顺义区'),
  array('昌平区'),
  array('大兴区'),
  array('怀柔区'),
  array('平谷区'),
  array('密云县'),
  array('延庆县'),
  ),

  array
  ('天津市',
  array('和平区'),
  array('河东区'),
  array('河西区'),
  array('南开区'),
  array('河北区'),
  array('红桥区'),
  array('塘沽区'),
  array('汉沽区'),
  array('大港区'),
  array('东丽区'),
  array('西青区'),
  array('津南区'),
  array('北辰区'),
  array('武清区'),
  array('宝坻区'),
  array('蓟  县'),
  array('宁河县'),
  array('静海县')
  ),

  array
  ('河北省',
  array('石家庄市'),
  array('唐山市'),
  array('秦皇岛市'),
  array('邯郸市'),
  array('邢台市'),
  array('保定市'),
  array('张家口市'),
  array('承德市'),
  array('沧州市'),
  array('廊坊市'),
  array('衡水市')
  ),

  array
  ('山西省',
  array('太原市'),
  array('大同市'),
  array('朔州市'),
  array('阳泉市'),
  array('长治市'),
  array('晋城市'),
  array('忻州市'),
  array('晋中市'),
  array('临汾市'),
  array('运城市'),
  array('吕梁地区')
  ),

  array
  ('内蒙古自治区',
  array('呼和浩特市'),
  array('包头市'),
  array('乌海市'),
  array('赤峰市'),
  array('通辽市'),
  array('鄂尔多斯市'),
  array('呼伦贝尔市'),
  array('乌兰察布盟'),
  array('锡林郭勒盟'),
  array('巴彦淖尔盟'),
  array('阿拉善盟')
  ),

  array
  ('辽宁省',
  array('沈阳市'),
  array('大连市'),
  array('鞍山市'),
  array('抚顺市'),
  array('本溪市'),
  array('丹东市'),
  array('锦州市'),
  array('葫芦岛市'),
  array('营口市'),
  array('盘锦市'),
  array('阜新市')
  ),

  array
  ('吉林省',
  array('长春市'),
  array('吉林市'),
  array('四平市'),
  array('辽源市'),
  array('通化市'),
  array('白山市'),
  array('松原市'),
  array('白城市'),
  array('延边朝鲜'),
  array('族自治州')
  ),

  array
  ('黑龙江省',
  array('哈尔滨市'),
  array('齐齐哈尔市'),
  array('鹤岗市'),
  array('双鸭山市'),
  array('鸡西市'),
  array('大庆市'),
  array('伊春市'),
  array('牡丹江市'),
  array('佳木斯市'),
  array('七台河市'),
  array('黑河市'),
  array('绥化市'),
  array('大兴安岭地区')
  ),

  array
  ('上海市',
  array('黄浦区'),
  array('卢湾区'),
  array('徐汇区'),
  array('长宁区'),
  array('静安区'),
  array('普陀区'),
  array('闸北区'),
  array('虹口区'),
  array('杨浦区'),
  array('宝山区'),
  array('闵行区'),
  array('嘉定区'),
  array('浦东新区'),
  array('松江区'),
  array('金山区'),
  array('青浦区'),
  array('南汇区'),
  array('奉贤区'),
  array('崇明县')
  ),

  array
  ('江苏省',
  array('南京市'),
  array('徐州市'),
  array('连云港市'),
  array('淮安市'),
  array('宿迁市'),
  array('盐城市'),
  array('扬州市'),
  array('泰州市'),
  array('南通市'),
  array('镇江市'),
  array('常州市'),
  array('无锡市'),
  array('苏州市')
  ),

  array
  ('浙江省',
  array('杭州市'),
  array('宁波市'),
  array('温州市'),
  array('嘉兴市'),
  array('湖州市'),
  array('绍兴市'),
  array('金华市'),
  array('衢州市'),
  array('舟山市'),
  array('台州市'),
  array('丽水市')
  ),

  array
  ('安徽省',
  array('合肥市'),
  array('芜湖市'),
  array('蚌埠市'),
  array('淮南市'),
  array('马鞍山市'),
  array('淮北市'),
  array('铜陵市'),
  array('安庆市'),
  array('黄山市'),
  array('滁州市'),
  array('阜阳市'),
  array('宿州市'),
  array('巢湖市'),
  array('六安市'),
  array('亳州市'),
  array('池州市'),
  array('宣城市')
  ),

  array
  ('福建省',
  array('福州市'),
  array('厦门市'),
  array('三明市'),
  array('莆田市'),
  array('泉州市'),
  array('漳州市'),
  array('南平市'),
  array('龙岩市'),
  array('宁德市')
  ),

  array
  ('江西省',
  array('南昌市'),
  array('景德镇市'),
  array('萍乡市'),
  array('新余市'),
  array('九江市'),
  array('鹰潭市'),
  array('赣州市'),
  array('吉安市'),
  array('宜春市'),
  array('抚州市'),
  array('上饶市')
  ),

  array
  ('山东省',
  array('济南市'),
  array('青岛市'),
  array('淄博市'),
  array('枣庄市'),
  array('东营市'),
  array('潍坊市'),
  array('烟台市'),
  array('威海市'),
  array('济宁市'),
  array('泰安市'),
  array('日照市'),
  array('莱芜市'),
  array('德州市'),
  array('临沂市'),
  array('聊城市'),
  array('滨州市'),
  array('菏泽市')
  ),

  array
  ('河南省',
  array('郑州市'),
  array('开封市'),
  array('洛阳市'),
  array('平顶山市'),
  array('焦作市'),
  array('鹤壁市'),
  array('新乡市'),
  array('安阳市'),
  array('濮阳市'),
  array('许昌市'),
  array('漯河市'),
  array('三门峡市'),
  array('南阳市'),
  array('商丘市'),
  array('信阳市'),
  array('周口市'),
  array('驻马店市'),
  array('济源市')
  ),

  array
  ('湖北省',
  array('武汉市'),
  array('黄石市'),
  array('襄樊市'),
  array('十堰市'),
  array('荆州市'),
  array('宜昌市'),
  array('荆门市'),
  array('鄂州市'),
  array('孝感市','云梦县','汉川县'),
  array('黄冈市'),
  array('咸宁市'),
  array('随州市'),
  array('恩施自治州')
  ),

  array
  ('湖南省',
  array('长沙市'),
  array('株洲市'),
  array('湘潭市'),
  array('衡阳市'),
  array('邵阳市'),
  array('岳阳市'),
  array('常德市'),
  array('张家界市'),
  array('益阳市'),
  array('郴州市'),
  array('永州市'),
  array('怀化市'),
  array('娄底市'),
  array('湘西自治州')
  ),

  array
  ('广东省',
  array('广州市'),
  array('深圳市'),
  array('珠海市'),
  array('汕头市'),
  array('韶关市'),
  array('河源市'),
  array('梅州市'),
  array('惠州市'),
  array('汕尾市'),
  array('东莞市'),
  array('中山市'),
  array('江门市'),
  array('佛山市'),
  array('阳江市'),
  array('湛江市'),
  array('茂名市'),
  array('肇庆市'),
  array('清远市'),
  array('潮州市'),
  array('揭阳市'),
  array('云浮市')
  ),

  array
  ('广西壮族自治区',
  array('南宁市'),
  array('柳州市'),
  array('桂林市'),
  array('梧州市'),
  array('北海市'),
  array('防城港市'),
  array('钦州市'),
  array('贵港市'),
  array('玉林市'),
  array('百色市'),
  array('贺州市'),
  array('河池市'),
  array('来宾市'),
  array('崇左市')
  ),

  array
  ('海南省',
  array('海口市'),
  array('三亚市'),
  array('五指山市'),
  array('琼海市'),
  array('儋州市'),
  array('文昌市'),
  array('万宁市'),
  array('东方市'),
  array('澄迈县'),
  array('定安县'),
  array('屯昌县'),
  array('临高县'),
  array('白沙自治县'),
  array('昌江自治县'),
  array('乐东自治县'),
  array('陵水自治县'),
  array('保亭自治县'),
  array('琼中自治县')
  ),

  array
  ('重庆市',
  array('渝中区'),
  array('大渡口区'),
  array('江北区'),
  array('沙坪坝区'),
  array('九龙坡区'),
  array('南岸区'),
  array('北碚区'),
  array('万盛区'),
  array('双桥区'),
  array('渝北区'),
  array('巴南区'),
  array('万州区'),
  array('涪陵区'),
  array('黔江区'),
  array('长寿区'),
  array('永川市'),
  array('合川市'),
  array('江津市'),
  array('南川市'),
  array('綦江县'),
  array('潼南县'),
  array('荣昌县'),
  array('璧山县'),
  array('大足县'),
  array('铜梁县'),
  array('梁平县'),
  array('城口县'),
  array('垫江县'),
  array('武隆县'),
  array('丰都县'),
  array('奉节县'),
  array('开县'),
  array('云阳县'),
  array('忠县'),
  array('巫溪县'),
  array('巫山县'),
  array('石柱自治县'),
  array('秀山自治县'),
  array('酉阳自治县'),
  array('彭水自治县')
  ),

  array
  ('四川省',
  array('成都市'),
  array('自贡市'),
  array('攀枝花市'),
  array('泸州市'),
  array('德阳市'),
  array('绵阳市'),
  array('广元市'),
  array('遂宁市'),
  array('内江市'),
  array('乐山市'),
  array('南充市'),
  array('宜宾市'),
  array('广安市'),
  array('达州市'),
  array('巴中市'),
  array('雅安市'),
  array('眉山市'),
  array('资阳市'),
  array('阿坝自治州'),
  array('甘孜自治州'),
  array('凉山自治州')
  ),

  array
  ('贵州省',
  array('贵阳市'),
  array('六盘水市'),
  array('遵义市'),
  array('安顺市'),
  array('铜仁地区'),
  array('毕节地区'),
  array('黔西南自治州'),
  array('黔东南自治州'),
  array('黔南自治州')
  ),

  array
  ('云南省',
  array('昆明市'),
  array('曲靖市'),
  array('玉溪市'),
  array('保山市'),
  array('昭通市'),
  array('思茅地区'),
  array('临沧地区'),
  array('丽江市'),
  array('文山自治州'),
  array('红河自治州'),
  array('西双版纳自治州'),
  array('楚雄自治州'),
  array('大理自治州'),
  array('德宏自治州'),
  array('怒江自治州'),
  array('迪庆自治州')
  ),

  array
  ('西藏自治区',
  array('拉萨市'),
  array('那曲地区'),
  array('昌都地区'),
  array('山南地区'),
  array('日喀则地区'),
  array('阿里地区'),
  array('林芝地区')
  ),

  array
  ('陕西省',
  array('西安市'),
  array('铜川市'),
  array('宝鸡市'),
  array('咸阳市'),
  array('渭南市'),
  array('延安市'),
  array('汉中市'),
  array('榆林市'),
  array('安康市'),
  array('商洛市')
  ),

  array
  ('甘肃省',
  array('兰州市'),
  array('金昌市'),
  array('白银市'),
  array('天水市'),
  array('嘉峪关市'),
  array('武威市'),
  array('张掖市'),
  array('平凉市'),
  array('酒泉市'),
  array('庆阳市'),
  array('定西地区'),
  array('陇南地区'),
  array('甘南自治州'),
  array('临夏自治州')
  ),

  array
  ('青海省',
  array('西宁市'),
  array('海东地区'),
  array('海北自治州'),
  array('黄南自治州'),
  array('海南自治州'),
  array('果洛自治州'),
  array('玉树自治州'),
  array('海西自治州')
  ),

  array
  ('宁夏回族自治区',
  array('银川市'),
  array('石嘴山市'),
  array('吴忠市'),
  array('固原市')
  ),

  array
  ('新疆维吾尔自治区',
  array('乌鲁木齐市'),
  array('克拉玛依市'),
  array('石河子市'),
  array('阿拉尔市'),
  array('图木舒克市'),
  array('五家渠市'),
  array('吐鲁番地区'),
  array('哈密地区'),
  array('和田地区'),
  array('阿克苏地区'),
  array('喀什地区'),
  array('克孜勒苏柯尔克孜自治州'),
  array('巴音郭楞蒙古自治州'),
  array('昌吉回族自治州'),
  array('博尔塔拉'),
  array('蒙古自治州'),
  array('伊犁哈萨克自治州')
  ),

  array
  ('香港特别行政区',
  array('中西区'),
  array('东区'),
  array('九龙城区'),
  array('观塘区'),
  array('南区'),
  array('深水埗区'),
  array('黄大仙区'),
  array('湾仔区'),
  array('油尖旺区'),
  array('离岛区'),
  array('葵青区'),
  array('北区'),
  array('西贡区'),
  array('沙田区'),
  array('屯门区'),
  array('大埔区'),
  array('荃湾区'),
  array('元朗区')
  ),

  array
  ('澳门特别行政区',
  array('澳门半岛'),
  array('凼仔岛'),
  array('路环岛')
  ),

  array
  ('台湾省',
  array('台北市'),
  array('高雄市'),
  array('台南市'),
  array('台中市'),
  array('基隆市'),
  array('新竹市'),
  array('嘉义市'),
  array('台北县'),
  array('宜兰县'),
  array('新竹县'),
  array('桃园县'),
  array('苗栗县'),
  array('台中县'),
  array('彰化县'),
  array('南投县'),
  array('嘉义县'),
  array('云林县'),
  array('台南县'),
  array('高雄县'),
  array('屏东县'),
  array('台东县'),
  array('花莲县'),
  array('澎湖县')
  )
  );
  /**
  *  将城市列表变成一个三维数组
  */
  var $new_arr3 = array();
  /**
  *  将城市列表变成一个一维数组
  */
  var $new_arr = array();

  // 构造函数，赋值给 $new_arr， 得到一维数组
  // 获得id和地名的对应关系， 数据类似： 000000->北京市 000100->东城区,为县预留数据
  function Region(){
    $province_num = count($this->city_list);       // 省级总数
    for ($i = 0;$i < $province_num;$i ++){
      $city_num = count($this->city_list[$i]);     // 省内市级数目,省名也在其中
      for ($j = 0;$j < $city_num;$j ++){        // 从0开始是因为需要将省也纳入其中
        if (is_array($this->city_list[$i][$j])){   // 县、市一级
          $xian_num = count($this->city_list[$i][$j]);
          for ($k = 0;$k < $xian_num;$k++){
            $new_arr[$this->FormatNum($i).$this->FormatNum($j).$this->FormatNum($k)] = $this->city_list[$i][$j][$k];
            $new_arr3[$this->FormatNum($i)][$this->FormatNum($j)][$this->FormatNum($k)] = $this->city_list[$i][$j][$k];
          }
        }else { // 省级 , 其中 $j = 0
          $new_arr[$this->FormatNum($i).$this->FormatNum($j)."00"] = $this->city_list[$i][$j];
          $new_arr3[$this->FormatNum($i)][$this->FormatNum($j)]["00"] = $this->city_list[$i][$j];
        }
      }
    }
    $this->new_arr = $new_arr;
    $this->new_arr3 = $new_arr3;
  }

  // 返回省市县级行政区划的名字，如"160600"返回"宜昌市"
  function getCityName($city_code){
    return $this->new_arr[$city_code];
  }
  // 返回省、市、县的行政区划的名字，如"160600"返回"湖北省宜昌市"
  function getFullCityName($city_code){
    $p_code = substr($city_code,0,2); // 省代号
    $c_code = substr($city_code,2,2); // 市代号
    $x_code = substr($city_code,4,2); // 县代号
    if ($c_code=="00" && $x_code=="00"){
      $fullCityName = $this->new_arr[$p_code."0000"];
    }else if ($c_code=="00" && $x_code<>"00"){
      return "error city code";
    }else if ($x_code == "00") {
      $fullCityName = $this->new_arr[$p_code."0000"].$this->new_arr[$p_code.$c_code."00"];
    }else {
      $fullCityName = $this->new_arr[$p_code."0000"].$this->new_arr[$p_code.$c_code."00"].$this->new_arr[$p_code.$c_code.$x_code];
    }
    return $fullCityName;
  }

  // 返回县级、市级或者省级行政区划的代码，如"湖北省"返回"160000"
  // 当县多了的时候，有重名的情况，这时候需要将省市都戴上
  // 原则上Full_city_name 表示将省、市，甚至是县一起带上
  // 但此处现在还没有这么干，或许这个用不上
  function getProvCode($province_name){
    foreach ($this->new_arr as $k=>$value){
      if ($value==$province_name){
        return $k;
      }
    }
  }

  // 当搜索地名的时候，能否返回相对应的region代号呢？可以采用模糊匹配
  // 是一项庞大的工程，需要专门的搜索类来支持
  function getCode($city_name) {
    // 可能存在的情况
    // 输入一个项，
         // 输入一项可能有 省市等一起，比较长
      // 也可能只有单独的省，或者市，县
      //
    // 输入多项
  }

  // 数字格式化, 返回字符串型数据 将0，1，2等等返回00，01，02这样的数据
  function FormatNum($city_code){
    $city_code = (int)$city_code;
    if($city_code<10){
      $city_code = "0".$city_code;
    }
    return $city_code;
  }
  // 第一次将城市列表插入到数据库中
  function InsertRegion(){
    require_once('sql/DB.cls.php');
    $DB = new DB();
    $DB -> CreateInstance();
    foreach ($this->new_arr as $k => $value){
      mysql_query("insert into region values ('$k','$value');");
    }
  }
}

?>