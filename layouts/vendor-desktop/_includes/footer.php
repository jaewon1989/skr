<footer class="footer <?php if($page=='adm/main' || $page=='adm/list'):?> main-footer<?php endif?>" id="cb-footer">
    <div class="cb-content cb-footer-wrapper">
        <div class="cb-layout">
            <div>
                <span class="cb-copyright">COPYRIGHT © SK TELECOM CO., LTD. ALL RIGHTS RESERVED.</span>
                <!-- <a href="http://www.bottalks.co.kr" target="_blank"><img src="<?php echo $g['img_layout']?>/logo_bottom.png" /> </a> -->
            </div>
            <!-- <div class="cb-right">
                <div>
                    <span class="cb-text">서울시 서초구 서초동 1642-3번지 2층</span>
                    <span class="cb-bar">|</span>
                    <span class="cb-text">사업자 번호 : 696-86-00654</span>
                    <span class="cb-bar">|</span>
                    <span class="cb-text">상호 : (주)페르소나시스템</span>
                    <span class="cb-bar">|</span>
                    <span class="cb-text">대표 : 유승재, 심병학</span>
                </div>
                <div>
                    <span class="cb-text">TEL : 02-762-8763</span>
                    <span class="cb-bar">|</span>
                    <span class="cb-text">MAIL : sungjaeq@nate.com</span>
                </div>
                <div>
                    <span class="cb-copyright">Copyright @ BOTTALKS All Rights Reserved.</span>
                </div>
            </div> -->
        </div>
    </div>
</footer>

<? 
if($my['uid']) {
?>
<!-- 관리자 정보 모달-->
<div id="modal-memberinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
</div>
<script>
    $("#member_avatar").on('click',function(){
        $("#modal-memberinfo").load('/?r=<?=$r?>&m=<?=$m?>&a=get_member', function(){
             $("#modal-memberinfo").modal();
        });
    });
</script>
<?}?>