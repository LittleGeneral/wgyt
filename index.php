<?php


// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

	// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false

	//1确定应用名称
	define('APP_NAME', 'App');
	//2确定应用路径
	define('APP_PATH', './App/');
	//3开启调试模式
	define('APP_DEBUG', true);
	//4引入thinkphp核心文件
	require './ThinkPHP/ThinkPHP.php';

//  define("SITE_URL","http://127.0.0.1/");
// define("CSS_URL",SITE_URL."wgyt/public/css/");
// define("JS_URL",SITE_URL."wgyt/public/js/");
// define("IMG_URL",SITE_URL."wgyt/public/image/");

?>