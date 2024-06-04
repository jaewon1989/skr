<?php
$sort   = 'uid';
$orderby= $orderby ? $orderby : 'asc';
$recnum = 20;
if($t_start || $t_end){
    if($t_start){
       $s_val = explode(' ',$t_start);
       $s_hm = $s_val[0];
       $s_ap = $s_val[1];
       $s_hm_arr = explode(':',$s_hm);
       $s_h = $s_hm_arr[0];// 시간 
       $s_m = $s_hm_arr[1];// 분 
       if($s_ap=='PM') $last_s_h = $s_h+12;
       else $last_s_h = $s_h;
       $last_s_hm = $last_s_h.$s_m;  
    } 
    if($t_end){
       $e_val = explode(' ',$t_end);
       $e_hm = $e_val[0];
       $e_ap = $e_val[1];
       $e_hm_arr = explode(':',$e_hm);
       $e_h = $e_hm_arr[0];// 시간 
       $e_m = $e_hm_arr[1];// 분 
       if($e_ap=='PM') $last_e_h = $e_h+12;
       else $last_e_h = $e_h;
       $last_e_hm = $last_e_h.$e_m;  
    } 

}
$_WHERE='vendor='.$V['uid'];

if($d_start) $_WHERE .= ' and d_regis > '.str_replace('-','',$d_start).($last_s_hm?$last_s_hm.'00':'000000');
if($d_end) $_WHERE .= ' and d_regis < '.str_replace('-','',$d_end).($last_e_hm?$last_e_hm.'00':'000000');
if($botuid) $_WHERE .=' and bot='.$botuid;


$RCD = getDbArray($table[$m.'chatLog'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'chatLog'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>

<!-- bootstrap css -->
<link href="<?php echo $g['url_layout']?>/_css/cb.css" rel="stylesheet">    
<input type="hidden" name="mod" value="month" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
<!--         <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> <a href="http://wrappixel.com/templates/pixeladmin/" target="_blank" class="btn btn-danger pull-right m-l-20 btn-rounded btn-outline hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro</a>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Fontawesome Icons</li>
            </ol>
        </div> -->
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title">대화내역</h3>
                <div class="table-responsive cb-management-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>아이디</th>
                                <th>일자</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($R = db_fetch_array($RCD)):?>
                            <?php 
                               $userPic = $chatbot->getUserAvatar($R['userUid'],'src');
                               $chatUrl = $chatbot->getChatUrl($R); 
                               $userName = $chatbot->getUserName($R['userUid']);
                            ?>
                         
                            <tr>
                                <td>
                                    <img src="<?php echo $userPic?>" alt="viewchat search result" />
                                    <span class="cb-name"><?php echo $userName.'('.$R['userUid'].')'?></span>
                                </td>
                                <td>
                                    <span class="cb-date"><?php echo getDateFormat($R['d_regis'],'Y-m-d')?></span>
                                </td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#modal-chatbox" data-role="getComponent" data-id="<?php echo $R['bot'].'-'.$R['userUid']?>" data-markup="userChatBox" class="cb-button">채팅내역</a>
                                </td>
                            </tr>
                            <?php endwhile?>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>   
      <div class="text-center pt" >
         <ul class="pagination pagination-sm">
            <script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
          </ul>
    </div> 

</div>

<!-- Modal -->
<div id="modal-chatbox" class="modal fade">
    <div class="modal-dialog" style="width:505px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body" data-role="content" style="padding:0;">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- End of  bootstrap-timepicker,  https://github.com/jdewit/bootstrap-timepicker/ , http://jdewit.github.io/bootstrap-timepicker/ : 메뉴얼 -->
<?php getImport('bootstrap-timepicker','js/bootstrap-timepicker.min',false,'js')?>
<?php getImport('bootstrap-timepicker','css/bootstrap-timepicker.min',false,'css')?>
<script>
 $('.tpicker').timepicker({
    defaultTime : '',
    //showSeconds : true, // 초 노출
    showMeridian:true, // 24시 모드 
    maxHours: 24,
    minuteStep : 15
 });

</script>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
// 날짜 선택 
$('.input-daterange').datepicker({
    format: "yyyy-mm-dd",
    todayBtn: "linked",
    language: "kr",
    calendarWeeks: true,
    todayHighlight: true,
    autoclose: true
});

$('[data-role="btn-search"]').on('click',function(){
   var f = document.procForm;
   f.submit(); 
});

</script>