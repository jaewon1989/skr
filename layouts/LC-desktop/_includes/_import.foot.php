<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/foot.engine.php';?>
 <!-- jQuery -->
<?php
$themeName ='default-desktop'; 
$cmod = $_GET['cmod']?$_GET['cmod']:"LC";
?>

<!--slimscroll JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/waves.js"></script>

<!-- Custom Theme JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/custom.js"></script>
<script src="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/toast-master/js/jquery.toast.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/layout.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.bottalksLC.1.0.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.timer.js"></script>
<script type="text/javascript">
$(function() {
    $('#wrapper').PS_chatbotLC({
        moduleName : '<?php echo $m?>',
        themeName : '<?php echo $themeName?>',
        cmod : '<?php echo $cmod?>',
        showTimer: '<?php echo $showTimer?>'
    });

});
$(document).ready(function() {
    // 동적 높이 조절 
    $(function () {
        $(window).bind("load resize", function () {
            var topOffset = 100;
            var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            if (height < 1) height = 1;
            if (height > topOffset) {
                var rp_height = height-140;
                var rpS_height = rp_height-200; // panel scroll
                $('[data-role="table-wrapper"]').css("height", (rp_height) + "px");
                $('[data-role="rightPanel"]').css("height", (rp_height) + "px");
                $('[data-role="panel-scroll"]').css("height", (rpS_height) + "px");
                $('[data-role="recData-scroll"]').css("height", (rp_height) + "px");
                $('#cb-chatting-body').css("height", (height) + "px");
                $('#modal-chatbot').css("height", (height+50) + "px");

            }
        });
    });
  
});
</script>

<?php if($d['layout']['header_fixed']=='true'):?>
<style>
body {padding-top:70px}
</style>
<?php endif?>

