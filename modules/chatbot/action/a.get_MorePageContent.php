<?php
if(!defined('__KIMS__')) exit;

// class 패스 지정 및 인클루드   
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅

$feed = new feed();

$result=array();
$result['error']=false;

if($mod=='otherStyle'){
    // 다른 스타일 출력 
    $other_style_rows = $this->getUserOtherStyle($post,$register,$page,$position); // array(list, NUM) 리턴 
    $other_style_rows_list = $other_style_rows[0];
    $other_style_rows_total = $other_style_rows[1];
    
    // 이전 버튼 세팅 
    if($page==1) $prev_disabled = 'disabled';
    else $prev_page=$page-1;
    
    // 다음 버튼 세팅 
    if($other_style_rows_total>5) $next_page=$page+1;
    else{
       $next_page ='';
       $next_disabled ='disabled';  
    } 

    $btn_otherStyle_nav='
    <div class="btn-group">
         <button type="button" class="btn btn-sm btn-default" data-act="paging" data-mod="otherStyle" data-post="'.$post.'" data-register="'.$register.'" data-page="'.$prev_page.'" data-position="'.$position.'" '.$prev_disabled.'>
            <i class="fa fa-angle-left"></i>
         </button>
         <button type="button" class="btn btn-sm btn-default" data-act="paging" data-mod="otherStyle" data-post="'.$post.'" data-register="'.$register.'" data-page="'.$next_page.'" data-position="'.$position.'" '.$next_disabled.'><i class="fa fa-angle-right"></i>
         </button>
    </div>';

    $result['btn'] = $btn_otherStyle_nav;
    $result['list'] = $other_style_rows_list;

}


echo json_encode($result);
exit;

?>
