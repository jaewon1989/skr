<!DOCTYPE html>
<html lang="kr">
    <head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.control.php' ?>  
     </head>

    <body>
    <div class="snap-drawers">
      <div class="snap-drawer snap-drawer-left" id="myDrawer">
         <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
      </div>
    </div>

    <div class="snap-content" data-extension="drawer">
        <?php include $g['dir_layout'].'/_includes/header.php' ?>
        <?php include $g['dir_layout'].'/_includes/content.control.php';?> <!-- feed 출력 관리 -->
         <?php if($mod!='search' && $mod!='view' && $mod!='admin'):?> 
         <section class="bar bar-standard bar-header-secondary">        
            <div class="dm-tabmenu">
                <ul class="dm-ul topMenu-ul">
                    <li class="dm-latest<?php echo $mod=='new'?' active':''?>">
                        <a href="./?mod=new"  data-control="push" data-transition="fade" data-mod="new"></a>
                    </li>
                    <li class="dm-favorite<?php echo $mod=='hot'?' active':''?>">
                        <a href="./?mod=hot"  data-control="push" data-transition="fade" data-mod="hot"></a>
                    </li>
                    <li class="dm-fashionking<?php echo $mod=='best'?' active':''?>" >
                        <a href="./?mod=best"  data-control="push" data-transition="fade" data-mod="best"></a>
                    </li>
                    <li class="dm-video<?php echo $mod=='video'?' active':''?>" >
                        <a href="./?mod=video"  data-control="push" data-transition="fade" data-mod="video"></a>
                    </li>

                </ul>
            </div>
        </section>     
        <?php endif?>    
        
        <!-- main content -->
        <div class="content rb-scrollable" id="<?php echo $object?>-content" >
            
            <?php if($mod!='search' && $mod!='view'):?>
                <section>
                    <div class="dm-actual-body">
                        <div class="dm-event-banner"></div>
                    </div>
                </section>              
                <section class="filter-wrap affix-top" data-role="filter-wrap">               
                      <?php echo $filter_default?>
                </section>   
                <div class="content-padded">
         
                    <?php 
                       // $profile = $feed->getProfile(5);
                         
                       // $device_token = $profile['device_token'];
                       // $title ='아이폰 테스트';
                       // $message = '아이폰 푸시 테스트 ';
                       // // echo 'result from sendIosPush : '.$feed->sendIosPush($device_token,$title,$message,$url,$attend=null,$addParams=null);
                       
                       // $noti_row = getUidData($feed->table('notification'),359); 
                       // echo 'result from setPush : '.$result= $feed->setPush(5,$noti_row,$referer);

                    ?> 
                    <?php include __KIMS_CONTENT__ ?>
                </div>
            <?php else:?>
                <?php include __KIMS_CONTENT__ ?> <!-- url 기반 페이지는 content-padded 를 자체 페이지에서 구현한다. affix 관련 이슈 -->
            <?php endif?> 
            <!-- 위로 가기 버튼 -->
            <div id="toTop" class="iconbutton ripplelink" data-scroll="top" data-speed="200"><span class="icon icon-up-nav"></span></div>
   
        </div><!-- /.content -->

    </div><!-- /.snap-content -->

    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.yun.php' ?> 
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
   <script>


// 드로어 익스텐션 초기화
snapper = new Snap({
    element: $("#myDrawer")[0],
    maxPosition: 1,
    minPosition: -1,
    transitionSpeed: 0.1
})

// Initialize drawer
RC_initDrawer();

// 디바이스 정보 업데이트 
function updateDevice(datas){
    var dts = $.parseJSON(datas);
    var regid = dts.regid;
    var uuid  = dts.uuid;
    var dev   = dts.dev;
    var memberuid='<?php echo $my['uid']?>';
    localStorage.setItem("deviceid",uuid);
    localStorage.setItem("token",regid);
    localStorage.setItem("dev",dev);
    
    //alert([regid, uuid, dev]);
    $.post(rooturl+'/?r='+raccount+'&m=sns&a=setMemberDeviceid',{
        memberuid : memberuid,
        token : regid,
        deviceid : uuid,
        dev : dev
    },function(response){
    
    }); 
    
}
// 디바이스 체크 함수  
function checkDevice(datas){
    var dts = $.parseJSON(datas);
    var now_device_id  = dts.uuid;
    //alert([dts.uuid,dts.regid]);
    var my_device_id='<?php echo $my['deviceid']?>';

    if(my_device_id=='') getUuid("updateDevice"); // deviceid 가 없는 경우 
    else {
        if(my_device_id!=now_device_id) getUuid("updateDevice"); // 기존 diviceid 값과 다른 경우 
    } 
}
function resultFail(){
   alert('fail');
}
function getUuid(_succFn){
   var param = {
      succFn : _succFn // Succ Fn name
   };
   Hybrid.exe('HybridIf.getUuid', param);
}
// 디바이스 세팅 함수 
function setUuid(_succFn){
   var param = {
      succFn : _succFn // Succ Fn name
   };
   Hybrid.exe('HybridIf.getUuid', param);
}
<?php if($my['uid']):?>
setUuid("checkDevice"); // 디바이스 체크함수를 호출
<?php endif?>

</script>

    </body>
</html>
