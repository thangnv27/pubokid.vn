<?php
/**
*  Defautl action
*  @author		: Ong Thế Thành	
*  @date		: 2012/01/23	
*  @version		: 0.0.1
*/
function default_default(){
	global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
	#
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable; $assign_list["clsClassTable"] = $clsClassTable;
    $clsUser = new User(); $assign_list['clsUser'] = $clsUser; $me = $clsUser->getMe();
    $pkeyTable = $clsClassTable->pkey; $assign_list["pkeyTable"] = $pkeyTable;
    #
    if($_GET['is_trash']) $cons.=" is_trash=".$_GET['is_trash'];
    else $cons.=" is_trash=0";
    if($_POST) {
        if($_POST['order_footer']) foreach($_POST['order_footer'] as $key=>$val) $clsClassTable->updateOne($key, array('order_footer'=>$val));
        if($_POST['order_no']) foreach($_POST['order_no'] as $key=>$val) $clsClassTable->updateOne($key, array('order_no'=>$val));
        $clsClassTable->deleteArrKey();
        header('Location: '.$core->getAddress());
    }
    if($_POST['keyword']) {
        $slug = $core->toSlug($_POST['keyword']);
        $cons .= " and slug like '%".$slug."%'";
        $assign_list["keyword"] = $_POST['keyword'];
    }
    #
    $listItem = $clsClassTable->getListPage($cons."  order by order_no");
    $paging = $clsClassTable->getNavPage($cons);
    $assign_list["listItem"] = $listItem;
    $assign_list["paging"] = $paging;
    $assign_list["cursorPage"] = isset($_GET["page"])? $_GET["page"] : 1;
    #
	/*=============Title & Description Page==================*/
	$title_page = "List - ".$classTable;
	$description_page = '';
	$keyword_page = '';
	/*=============Content Page==================*/
}
function default_new(){
	global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
	#
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable; $assign_list["clsClassTable"] = $clsClassTable;
    #
	$tableName = $clsClassTable->tbl;
	$pkeyTable = $clsClassTable->pkey ;
    #
    if($_POST) {
        $_POST['reg_date'] = time();
        if($_POST['title']) $_POST['slug']=$core->toSlug($_POST['title']);
        
        if($_POST['is_push']==1) {
            $_POST['push_date'] = time();
        }
        
        
        if($_POST['image'] && $_POST['image']!='') {
            $image = $core->ftpUrlUpload($_POST['image'], 'upload', $slug, time());
        } else if($_FILES['image']['name']) {
            $image = $core->ftpUpload('image', 'upload', $slug, time());
        } unset($_POST['image']);
        if($image) $_POST['image'] = '/'.$image;
        
        if($clsClassTable->insertOne($_POST)) {
            $maxId = $clsClassTable->getMaxID();
            header('location: ?mod='.$mod.'&act=edit&id='.$maxId.'&mes=insertSuccess');
        }
        else {
            foreach ($_POST as $key => $val) {
                $assign_list[$key] = $val;
            }
            $msg = "Thêm mới thất bại!";
        }
        unset($_POST);
    }
    #
	/*=============Title & Description Page==================*/
	$title_page = "New - ".$classTable;
	$description_page = '';
	$keyword_page = '';
	/*=============Content Page==================*/
}
function default_edit(){
	global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
	#
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable; $assign_list["clsClassTable"] = $clsClassTable;
    $oneItem = $clsClassTable->getOne($_GET['id']);
    if($oneItem) foreach($oneItem as $key => $val) {
        $assign_list[$key] = $val;
    }
	$tableName = $clsClassTable->tbl;
	$pkeyTable = $clsClassTable->pkey ;
    #
    if($_POST) {
        
        
        if($_POST['is_push'] != $oneItem['is_push']) {
            $_POST['push_date'] = time();
        }
        
        if($_POST['image'] && $_POST['image']!='') {
            $image= $core->ftpUrlUpload($_POST['image'], 'upload', $oneItem['slug'], strtotime($oneItem['reg_date']));
        } else if($_FILES['image']['name']) {
            $image = $core->ftpUpload('image', 'upload', $oneItem['slug'], strtotime($oneItem['reg_date']));
        } unset($_POST['image']);
        if($image) $_POST['image'] = '/'.$image;
        $clsClassTable->deleteArrKey();
        if($clsClassTable->updateOne($_GET['id'],$_POST))
            header('location: ?mod='.$mod.'&act='.$act.'&id='.$_GET['id'].'&mes=updateSuccess');
        else {
            foreach ($_POST as $key => $val) {
                $assign_list[$key] = $val;
            }
            $msg = "Chỉnh sửa thất bại!";
        }
        unset($_POST);
    }
	#
	/*=============Title & Description Page==================*/
	$title_page = "Edit - ".$classTable;
	$description_page = '';
	$keyword_page = '';
	/*=============Content Page==================*/
}
function default_in_trash(){
	global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable; $assign_list["clsClassTable"] = $clsClassTable;
    $pkeyTable = $clsClassTable->pkey; $assign_list["pkeyTable"] = $pkeyTable;
    #
    $listItem = $clsClassTable->getListPage("is_trash=1 order by ".$pkeyTable." desc");
    $paging = $clsClassTable->getNavPage("is_trash=1");
    $assign_list["listItem"] = $listItem;
    $assign_list["paging"] = $paging;
    $assign_list["cursorPage"] = isset($_GET["page"])? $_GET["page"] : 1;
    #
	/*=============Title & Description Page==================*/
	$title_page = "In trash - ".$classTable;
	$description_page = '';
	$keyword_page = '';
	/*=============Content Page==================*/
}
function default_trash(){
    global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
	$id = $_GET['id'];
    if(!$id) die('Not ID!');
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable;
    $clsClassTable->deleteArrKey();
    if($clsClassTable->updateOne($id,array('is_trash'=>'1'))) die('1');
    else die('Update!');
}
function default_restore(){
    global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
	$id = $_GET['id'];
    if(!$id) die('0');
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable;
    $clsClassTable->deleteArrKey();
    if($clsClassTable->updateOne($id,array('is_trash'=>'0'))) die('1');
    else die('0');
}
function default_delete(){
    global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
	$id = $_GET['id'];
    if(!$id) die('0');
    $classTable = ucfirst(strtolower($mod));
    $clsClassTable = new $classTable;
    $clsClassTable->deleteArrKey();
    if($clsClassTable->deleteOne($id)) die('1');
    else die('0');
}
function default_add_ajax(){
    global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
	$title = $_GET['title'];
    if(!$title) die('0');
    $classTable = ucfirst(strtolower($mod));
    if(!class_exists($classTable)) die('System not found mod \''.$classTable.'\'');
    $clsClassTable = new $classTable;
    $title = addslashes($_GET['title']);
    $slug = $core->toSlug($title);
    $channel_id = $clsClassTable->getMax('channel_id', "slug='".$slug."'"); if($channel_id) die(''.$channel_id);
    $data['title'] = $title;
    $data['slug'] = $slug;
    $data['reg_date'] = time();
    if($clsClassTable->insertOne($data)) {
        $channel_id = $clsClassTable->getMax('channel_id', "slug='".$slug."'");
        if($channel_id) die(''.$channel_id); else die('0');
    }
}
function default_load_ajax(){
    global $assign_list, $mod, $act, $_LANG_ID, $title_page, $description_page, $keyword_page, $core, $msg;
    #
	$title = $_GET['title'];
    if(!$title) die('0');
    $classTable = ucfirst(strtolower($mod));
    if(!class_exists($classTable)) die('System not found mod \''.$classTable.'\'');
    $clsClassTable = new $classTable;
    $slug = $core->toSlug($title);
    $all_channel = $clsClassTable->getAll("is_trash = 0 and (slug like '%".$slug."%' or title like '%".$title."%') order by reg_date limit 10");
    if($all_channel) {
        $html = '<ul>';
        foreach($all_channel as $oneChannel) { $oneChannel=$clsClassTable->getOne($oneChannel);
            $html .= "<li><a href='#' rel='".$oneChannel['channel_id']."'>".$oneChannel['title']."</a></li>";
        }
        die($html.'</ul>');
    } else die('0');
}
?>