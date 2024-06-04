<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/foot.engine.php';?>
 <!-- jQuery -->

<!-- Menu Plugin JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/sidebar-nav/dist/sidebar-nav.js"></script>
<!--slimscroll JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/waves.js"></script>

<!-- Custom Theme JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/custom.js"></script>
<script src="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/toast-master/js/jquery.toast.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/layout.js?240425"></script>

<script type="text/javascript">
var adminObj;
$(document).ready(function() {
   	<?php if($page!='adm/graph' && $B['id']):?>
    $('#page-wrapper').KRE_Admin({
       vendor: '<?php echo $V['uid']?>',
       bot: '<?php echo $bot?>',
       botId: '<?php echo $B['id']?>',
       module: '<?php echo $m?>',
       callIntent: '<?php echo $callIntent?>',
       callEntity: '<?php echo $callEntity?>',
       sescode: '<?php echo $sescode?>',
       page: '<?php echo $page?>',
       bottype: '<?php echo $bottype?>'
    });
    adminObj = $('#page-wrapper')[0].getAdminObj();
    <?php endif?>
    <?php $panelPage = array(
        'adm/intentSet',
        'adm/entitySet',
        'adm/legacy',
        'adm/monitering',
        'adm/response',
        'adm/learning'
        );
    ?>
    <?php if($page=='adm/main' || $page=='adm/list'){?>
    $('#page-wrapper').css('min-height', ($(window).innerHeight()-60)+'px');
    <?php }?>
    $('.h_400').css("min-height", "400px");
});
<?php if(in_array($page,$panelPage)):?>
// 동적 높이 조절
$(window).on("load resize", function () {
    var topOffset = 100;
    var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
    height = height - topOffset;
    if (height < 1) height = 1;
    if (height > topOffset) {
        var rp_height = height-220;
        var rpS_height = rp_height-200; // panel scroll
        var tS_height = rp_height-96; // table scroll
        var autoBox_height = height-126; // white box
        var iframe_in_autoBox_height = height-134;
        var ta_inWB_h = tS_height-70;
        var resHint_height = autoBox_height-150; // 모니터링 > 응답힌트
        var tagFA_height = resHint_height-50; // 모니터링 > 자주 사용하는 문장
        $('[data-role="table-wrapper"]').css("height", rp_height + "px");
        $('[data-role="rightPanel"]').css("height", rp_height + "px");
        $('[data-role="panel-scroll"]').css("height", rpS_height + "px");
        $('[data-role="table-scroll"]').css("height", tS_height + "px");
        $('[data-role="ta-moniteringFA"]').css("height", ta_inWB_h + "px");
        $('[data-role="resHint-wrapper"]').css("height", resHint_height + "px");
        $('[data-role="tagFA-wrapper"]').css("height", tagFA_height + "px");
        $('.auto-box').css("height", autoBox_height + "px");
        $('#cb-chatting-body').css("height", (height) + "px");
        $('#modal-chatbot').css("height", (height+50) + "px");
        <?php if($page!='adm/response'):?>
        $('.white-box').css("height", (rp_height) + "px");
        <?php endif?>
        $('.default-box').css("height","auto");
    }
});
<?php endif?>
</script>
<?php
// 그래프 스크립트
if($page=='adm/graph'){
   include $CONF['module'].'/theme/'.$d['chatbot']['skin_desktop'].'/adm/_graph_script.php';
}
?>

<?php if($d['layout']['header_fixed']=='true'):?>
<style>
body {padding-top:70px}
</style>
<?php endif?>

<div id="logext" class="logext">
    <div class="msg">로그아웃 시간이 2분 남았습니다.<br>로그아웃 시간을 연장하시겠습니까?</div>
    <div class="col-lg-12 col-md-12 hidden-sm" style="text-align:center;">
        <button class="btn_logext btn btn-primary btn-rounded waves-effect waves-light" data-mod="ext">
            연장하기
        </button>
        <button class="btn_logext btn btn-gray btn-rounded waves-effect waves-light" data-mod="out">
            로그아웃
        </button>
    </div>
</div>