<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

include_once(BG_PATH_CLASS . "tpl.class.php"); //载入模板类
include_once(BG_PATH_MODEL . "app.class.php"); //载入管理帐号模型

/*-------------管理员控制器-------------*/
class CONTROL_APP {

	private $adminLogged;
	private $obj_base;
	private $config; //配置
	private $obj_tpl;
	private $mdl_app;
	private $tplData;

	function __construct() { //构造函数
		$this->obj_base       = $GLOBALS["obj_base"]; //获取界面类型
		$this->config         = $this->obj_base->config;
		$this->adminLogged    = $GLOBALS["adminLogged"]; //获取已登录信息
		$this->mdl_app        = new MODEL_APP(); //设置管理员模型
		$this->obj_tpl        = new CLASS_TPL(BG_PATH_TPL_ADMIN . $this->config["ui"]); //初始化视图对象
		$this->tplData = array(
			"adminLogged" => $this->adminLogged
		);
	}

	/*============编辑管理员界面============
	返回提示
	*/
	function ctl_show() {
		$_num_appId = fn_getSafe($_GET["app_id"], "int", 0);

		if ($_num_appId == 0) {
			return array(
				"str_alert" => "x050203",
			);
		}

		if ($this->adminLogged["admin_allow"]["app"]["browse"] != 1) {
			return array(
				"str_alert" => "x050301",
			);
			exit;
		}
		$_arr_appRow = $this->mdl_app->mdl_read($_num_appId);
		if ($_arr_appRow["str_alert"] != "y050102") {
			return $_arr_appRow;
			exit;
		}

		$this->tplData["appRow"] = $_arr_appRow; //管理员信息

		$this->obj_tpl->tplDisplay("app_show.tpl", $this->tplData);

		return array(
			"str_alert" => "y050102",
		);
	}

	/*============编辑管理员界面============
	返回提示
	*/
	function ctl_form() {
		$_num_appId = fn_getSafe($_GET["app_id"], "int", 0);

		if ($_num_appId > 0) {
			if ($this->adminLogged["admin_allow"]["app"]["edit"] != 1) {
				return array(
					"str_alert" => "x050303",
				);
				exit;
			}
			$_arr_appRow = $this->mdl_app->mdl_read($_num_appId);
			if ($_arr_appRow["str_alert"] != "y050102") {
				return $_arr_appRow;
				exit;
			}
		} else {
			if ($this->adminLogged["admin_allow"]["app"]["add"] != 1) {
				return array(
					"str_alert" => "x050302",
				);
				exit;
			}
			$_arr_appRow = array(
				"app_status"    => "enable",
				"app_sync"      => "off",
			);
		}

		$this->tplData["appRow"] = $_arr_appRow; //管理员信息

		$this->obj_tpl->tplDisplay("app_form.tpl", $this->tplData);

		return array(
			"str_alert" => "y050102",
		);
	}

	/*============列出管理员界面============
	无返回
	*/
	function ctl_list() {
		if ($this->adminLogged["admin_allow"]["app"]["browse"] != 1) {
			return array(
				"str_alert" => "x050301",
			);
			exit;
		}

		$_str_key     = fn_getSafe($_GET["key"], "txt", "");
		$_str_status  = fn_getSafe($_GET["status"], "txt", "");

		$_arr_search = array(
			"key"    => $_str_key,
			"status" => $_str_status,
		);

		$_num_appCount    = $this->mdl_app->mdl_count($_str_key, $_str_status);
		$_arr_page        = fn_page($_num_appCount); //取得分页数据
		$_str_query       = http_build_query($_arr_search);
		$_arr_appRows     = $this->mdl_app->mdl_list(BG_SITE_PERPAGE, $_arr_page["except"], $_str_key, $_str_status);

		$_arr_tpl = array(
			"query"      => $_str_query,
			"pageRow"    => $_arr_page,
			"search"     => $_arr_search,
			"appRows"    => $_arr_appRows,
		);

		$_arr_tplData = array_merge($this->tplData, $_arr_tpl);

		$this->obj_tpl->tplDisplay("app_list.tpl", $_arr_tplData);
		return array(
			"str_alert" => "y050302",
		);
	}
}
?>