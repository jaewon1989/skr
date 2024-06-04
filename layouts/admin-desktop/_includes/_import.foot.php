<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/foot.engine.php';?>
 <!-- jQuery -->

<!-- Menu Plugin JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
<!--slimscroll JavaScript -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/waves.js"></script>

<!-- Custom Theme JavaScript -->
<script src="<?php echo $g['url_layout']?>/_js/jquery.treetable.js"></script>
<script src="<?php echo $g['url_layout']?>/pixeladmin/js/custom.min.js"></script>
<script src="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/toast-master/js/jquery.toast.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/layout.js"></script>
<script type="text/javascript">
$(document).ready(function() {
   	<?php if($page!='suAdm/tempGraph'):?>
    $('#page-wrapper').KRE_Admin({
       vendor: '<?php echo $V['uid']?>',
       bot: '<?php echo $bot?>',
       module: '<?php echo $m?>',
       callIntent: '<?php echo $callIntent?>',
       callEntity: '<?php echo $callEntity?>',
       sescode: '<?php echo $sescode?>',

    });
    <?php endif?> 
    <?php $panelPage = array('suAdm/sysEntity','suAdm/sysLegacy','suAdm/tempList','suAdm/nodeList');?>
    <?php if(in_array($page,$panelPage)):?>
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
                $('.right-panel').css("height", (rp_height) + "px");
                $('[data-role="graphTable-tbodyWrapper"]').css("height", (rp_height-100) + "px");
                $('[data-role="resultBody-wrapper"]').css("height", (rp_height-100) + "px");
                $('[data-role="panel-scroll"]').css("height", (rpS_height) + "px");
                $('#cb-chatting-body').css("height", (height) + "px");
                $('#modal-chatbot').css("height", (height+50) + "px");

            }
        });
    });
    <?php endif?>
  
});
</script>
<?php 
// 그래프 스크립트 
if($page=='suAdm/tempGraph'){
   include $CONF['module'].'/theme/'.$d['chatbot']['skin_desktop'].'/adm/_graph_script.php';
}
?>


<?php if($d['layout']['header_fixed']=='true'):?>
<style>
body {padding-top:70px}
</style>
<?php endif?>

