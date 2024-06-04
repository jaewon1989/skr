<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
    <head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.control.php' ?>  
     </head>

    <body class="cb-body">
    <div class="snap-drawers">
        <div class="snap-drawer snap-drawer-left" id="myDrawer">
             <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
        </div>
    </div>

    <div class="snap-content" data-extension="drawer">
        <?php include $g['dir_layout'].'/_includes/header.php' ?>
        <section id="cb-mainbanner">
            <div class="cb-mainbanner-wrapper">
                <div class="cb-mainbanner-slidezone">
                    <ul>
                        <li>
                            <img src="http://economychosun.com/query/upload/156/156_86.jpg" alt="Main banner Image" />
                        </li>
                        <li>
                            <img src="http://economychosun.com/query/upload/156/156_86.jpg" alt="Main banner Image" />
                        </li>
                        <li>
                            <img src="http://economychosun.com/query/upload/156/156_86.jpg" alt="Main banner Image" />
                        </li>
                    </ul>
                </div>
                <div class="cb-mainbanner-overlay">
                    <div class="cb-mainbanner-slider">
                        <span class="cb-icon cb-circle cb-slider cb-selected"></span>
                        <span class="cb-icon cb-circle cb-slider"></span>
                        <span class="cb-icon cb-circle cb-slider"></span>
                    </div>

                    <div class="cb-mainbanner-label">
                        <h3>WENESDAY, MARCH 25</h3>
                    </div>

                    <div class="cb-circle cb-mbot-wrapper">
                        <span class="cb-icon cb-icon-mbot"></span>
                    </div>
                </div>
            </div>
        </section>
        <section class="cb-list-slidelane">
            <div class="cb-list-slidelane-wrapper">
                <div class="cb-list-slidelane-header cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <h3 class="cb-list-slidelane-category">전자</h3>
                    </div>
                    <div class="cb-cell cb-cell-right">
                        <a href="">더보기</a>
                    </div>
                </div>
                <div class="cb-list-slidelane-limiter">
                    <div class="cb-list-slidelane-limitless">
                        <ul>
                            <li>
                                <img src="<?php echo $g['img_layout']?>/dice.png" alt="List Item Image" />
                                <div>
                                    <a href="">업체명</a>
                                </div>
                            </li>
                            <li>
                                <img src="<?php echo $g['img_layout']?>/dice.png" alt="List Item Image" />
                                <div>
                                    <a href="">업체명</a>
                                </div>
                            </li>                      
                        </ul>
                    </div>
                </div>            
            </div>
        </section>
   
    </div><!-- /.snap-content -->

    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>
    
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
