<?php
/*******************************************************************
 * 创建图片验证码, TODO 以后放到web中，不适合在common里面
 */
require_once(dirname(dirname(dirname(__FILE__))) . "/configs/system.conf.php");
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/lib/Session.php");

// TODO to delete, pls use GetImgCode();
function CreateAICode() {
  session_start();
  $code = RandomString('alpha');

  $_SESSION['AI-code'] = $code;

  $im = @imageCreate(60, 20) or die("Cannot Initialize new GD image stream");

  $background_color = imageColorAllocate($im, 220, 60, 250 ); //0,0,0

  for ($i=0; $i < strlen($code); $i++) {
    $x   = 5 + 15 * $i ;
    $text_color = imageColorAllocate($im, 0, 0, 0); // 255, 255, 255
    ImageChar($im, 4, $x, 5, $code[$i], $text_color);
  }
  // Date in the past
  header("Expires: Thu, 28 Aug 1997 05:00:00 GMT");

  // always modified
  $timestamp = gmdate("D, d M Y H:i:s");
  header("Last-Modified: " . $timestamp . " GMT");

  // HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);

  // HTTP/1.0
  header("Pragma: no-cache");

  // dump out the image
  header("Content-type: image/jpeg");
  ImageJPEG($im);
  ImageDestroy($im);
}

/*********************************************************************
 *   验证输入和产生的验证码是否一致
 */
function CheckAICode($code) {
  session_start();
  if (!isset($_SESSION['AI-code'])) {
    $_SESSION['AI-code'] = RandomString('alpha');
    return 0;
  }
  // Comment out following two lines to be case sensitive
  $code = strtolower($code);
  $entered_code = strtolower($_SESSION['AI-code']);

  $return = 0;
  if($code == $entered_code) $return = 1;

  // set new random code.
  $_SESSION['AI-code'] = RandomString('alpha');

  return $return;
}

/**********************************************************************
 *  this is the function to generate random string in the auth graphic
 */
function RandomString($type = 'num', $length = 4) {
  $randstr = '';
  mt_srand(microtime(true) * 1000000);

  $chars = '23456789';
  if ('alpha' == $type) {
    $chars .= 'abcdehkmnpqrstwxABCDEFGHKLMNPRSTUVWXYZ';
  }

  $char_len = strlen($chars) - 1;
  for ($rand = 0; $rand < $length; $rand++) {
    $randstr .= $chars[mt_rand(0, $char_len)];
  }

  return $randstr;
}

// 生成验证码
function GetImgCode($imgkey = 'img_code', $imagetype = 'gif', $width = 120, $height = 35, $length = 4) {
  session_start();
  $code = RandomString('alpha', $length);

  $_SESSION[$imgkey] = strtolower($code);

  $len = strlen($code);
  // 每个字的宽度如果超过了高度，需要减少字的宽度，
  $size = $width / $len;
  if ($size > $height)
    $size = $height - 30;
  else
    $size = $height - 10;

  $left = 5;
  $image = @imageCreate($width, $height) or die("Cannot Initialize new GD image stream");
  $back = imagecolorallocate ($image, '255', '255', '255');
  imageFilledRectangle($image, 0, 0, $width, $height, $back);
  // 随机数
  for ($i = 0; $i < $len; $i++) {
    $randtext = $code[$i];
    $textColor = imageColorAllocate($image , mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
    $randsize = mt_rand($size - $size / 12, $size + $size / 12);
    $location = $left + $i * $size + $size / 10;
    imagettftext($image, $randsize, mt_rand(-10, 10), $location, mt_rand($size-$size/10, $size+$size/10), $textColor, $GLOBALS['cfg']['IMG_TTF_FILE'], $randtext);
  }
  // 加干扰点
  $noise = true;
  if ($noise) {
    $noisenum = mt_rand(300,500);
    for ($i = 0; $i < $noisenum; $i++) {
      $randColor = imageColorAllocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
      imageSetPixel($image, mt_rand(0, $width), mt_rand(0, $height), $randColor);
    }
  }
  //加干扰线
  $noiseline=true;
  if ($noiseline) {
    $baseheight = $height / 2;
    $startRadiu = mt_rand(0, 1) + M_PI/3;
    $startRadiu2= mt_rand(0, 1) + M_PI/3;
    for($i = 0; $i < $width; $i++) {
      $color = imageColorAllocate($image, 0, 0, 0);
      imageSetPixel($image, $i, cos($startRadiu - $i/$width*M_PI)*$baseheight, $color);
      imageSetPixel($image, $i, tan($startRadiu2- $i/$width*M_PI)*$baseheight, $color);
    }
  }
  header('Content-Type: image/' . $imagetype);
  switch (strtolower($imagetype)) {
    case "jpg":
      imageJpeg($image);
      break;
    case "png":
      imagePng($image);
      break;
    case "gif":
      imageGif($image);
      break;
    default:
      imageJpeg($image);
      break;
  }

  imagedestroy($image);
  // exit();
}

// 调用图像函数
if (isset($_GET['create']) && 'yes' == $_GET['create']) {
  //GetImgCode('AI-code', 'gif', 90, 30); // gif比jpg图片小，破解难道大
  GetImgCode('AI-code'); // 生成比CreateAICode()更好的图片
}
