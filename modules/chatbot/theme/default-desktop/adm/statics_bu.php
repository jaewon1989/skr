<?php
$cmd = "netstat -n | grep 80 | grep EST | wc -l";
exec($cmd, $cmd_result); 
$nowAccessCount = sizeof($cmd_result);
?>

<!-- bootstrap css -->
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<link href="<?php echo $g['url_layout']?>/_css/statics/style.css" rel="stylesheet">
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<input type="hidden" name="mod" value="month" />
<div class="cb-statistic">
    <div class="cb-statistic-search">
        <table style="width:100%;">
            <tr>
                <td>
                    <ul class="cb-statistic-byrange">
                        <li data-smod="day" >
                            일간
                        </li>
                        <li data-smod="week">
                            주간
                        </li>
                        <li class="cb-selected" data-smod="month">
                            월간
                        </li>
                    </ul>
                </td>
                <td>
                    <div class="input-daterange">
                        <input class="cb-viewchat-search-datebox" placeholder="시작일자" type="text" name="d_start">
                    </div>
                </td>
                <td>
                    <span class="cb-statistic-search-wave">~</span>
                </td>
                <td>
                    <div class="input-daterange">
                       <input class="cb-viewchat-search-datebox" placeholder="종료일자" type="text" name="d_end">
                    </div>
                </td>
                <td>
                    <span class="cb-statistic-search-button" data-role="btn-search">조회</span>
                </td>
                <td style="width:30%;">
                    <?php $_WHERE='vendor='.$V['uid'];?>
                    <?php $RCD = getDbArray($table[$m.'bot'],$_WHERE,'*','gid','desc','',1);?>
                    <div class="cb-viewchat-search-timebox" style="width:95%;margin-left:5%;">
                        <select name="botuid" style="font-size:inherit;">
                            <?php $i=1;while($R=db_fetch_array($RCD)):?>
                            <option value="<?php echo $R['uid']?>"><?php echo $R['service']?></option>
                            <?php $i++;endwhile?> 
                        </select>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="cb-statistic-box-wrapper">
        <h1>총 채팅창 유입수</h1>
        <div class="cb-statistic-box cb-statistic-incoming">
            
        </div>
    </div>
    
    <div class="cb-statistic-box-wrapper" style="margin-top:20px;">
        <h1>통계 데이타 </h1>
        <div class="cb-statistic-box cb-statistic-target cb-layout" style="padding:50px 0;height:auto;">
            <div class="cb-left" style="width: 25%;" id="gender-wrap">
                <h4>가장 많이 한 질문</h4>
                <ul class="list-group">
                    <?php
                        $tbl = $table[$m.'chatStsLog'];
                        $_wh = 1;
                        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                        $rows=$chatbot->getAssoc($query); 
                        foreach ($rows as $row) {
                            echo '<li class="list-group-item">'.$row['sentence'].'<span class="badge">'.$row['hit'].'</span></li>';
                        }
                    ?>
                </ul>

               <!-- load gender chart -->
            </div>
            <div class="cb-left" style="width: 25%;" id="gender-wrap">
                <h4>가장 많이 사용한 단어 </h4>
                <ul class="list-group">
                    <?php
                        $tbl = $table[$m.'chatWordLog'];
                        $_wh = 1;
                        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                        $rows=$chatbot->getAssoc($query); 
                        foreach ($rows as $row) {
                            echo '<li class="list-group-item">'.$row['keyword'].'<span class="badge">'.$row['hit'].'</span></li>';
                        }
                    ?>
                </ul>

               <!-- load gender chart -->
            </div>
            <div class="cb-left" style="width: 25%;" id="gender-wrap">
                <h4>가장 많이 답변 못한 문장  </h4>
                <ul class="list-group">
                    <?php
                        $tbl = $table[$m.'unknown'];
                        $_wh = 1;
                        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                        $rows=$chatbot->getAssoc($query); 
                        foreach ($rows as $row) {
                            echo '<li class="list-group-item">'.$row['sentence'].'<span class="badge">'.$row['hit'].'</span></li>';
                        }
                    ?>
                </ul>

               <!-- load gender chart -->
            </div>
            <div class="cb-right" style="width:50%;float:right;padding-right:30px;" id="age-wrap">
               <!-- load age chart -->
            </div>
        </div>
    </div>
</div>



<div class="container-fluid">
    <!-- row -->
    <div class="row">
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                        <h5 class="text-muted vb">MYNEW CLIENTS</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-danger">23</h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">NEW PROJECTS</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-megna">169</h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">NEW INVOICES</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-primary">157</h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <!--row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box" id="total-wrap">         
               <!-- load 접속통계 차트 -->       
            </div>
        </div>
    </div>
    <!--row -->
    <div class="row">
 
             <h3 class="box-title">최근 통계 
                <div class="col-md-2 col-sm-4 col-xs-12 pull-right">
             <!--        <select class="form-control pull-right row b-none">
                        <option>March 2016</option>
                        <option>April 2016</option>
                        <option>May 2016</option>
                        <option>June 2016</option>
                        <option>July 2016</option>
                    </select> -->
                </div>
            </h3>
            <div class="col-sm-5">
                <div class="white-box">                   
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>사용한 문장</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tbl = $table[$m.'chatStsLog'];
                                    $_wh = 1;
                                    $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                                    $rows=$chatbot->getAssoc($query); 
                                    // foreach ($rows as $row) {
                                    //     echo '<li class="list-group-item">'.$row['sentence'].'<span class="badge">'.$row['hit'].'</span></li>';
                                    // }
                                ?>
                                <?php foreach ($rows as $row):?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $row['sentence']?></td>
                                </tr>
                                <?php endforeach?>                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="white-box">                   
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>답변 못한 문장</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php
                                    $tbl = $table[$m.'unknown'];
                                    $_wh = 1;
                                    $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                                    $rows=$chatbot->getAssoc($query); 
                                    // foreach ($rows as $row) {
                                    //     echo '<li class="list-group-item">'.$row['sentence'].'<span class="badge">'.$row['hit'].'</span></li>';
                                    // }
                                ?>
                                <?php foreach ($rows as $row):?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $row['sentence']?></td>
                                </tr>
                                <?php endforeach?>                          
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="white-box">                   
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>사용한 단어</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tbl = $table[$m.'chatWordLog'];
                                    $_wh = 1;
                                    $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,10", $tbl,$_wh);
                                    $rows=$chatbot->getAssoc($query); 
                                    // foreach ($rows as $row) {
                                    //     echo '<li class="list-group-item">'.$row['sentence'].'<span class="badge">'.$row['hit'].'</span></li>';
                                    // }
                                ?>
                                <?php foreach ($rows as $row):?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $row['keyword']?></td>
                                </tr>
                                <?php endforeach?>                          
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    <!-- /.row -->

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

var Load_BotChart = function(){
    var module='chatbot';
    var vendor='<?php echo $V['uid']?>';
    var botuid =$('select[name="botuid"]').val();
    var mod = $('input[name="mod"]').val();
    var d_start = $('input[name="d_start"]').val();
    var d_end = $('input[name="d_end"]').val();    
    console.log([botuid,mod,d_start,d_end]);
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
        vendor : vendor,
        botuid : botuid,
        mod : mod,
        d_start : d_start,
        d_end : d_end
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var total_chart=result.total_chart;
        var gender_chart=result.gender_chart;
        var age_chart=result.age_chart;
        
        // insert chart 
        $('#total-wrap').html(total_chart);
        // $('#gender-wrap').html(gender_chart);
        // $('#age-wrap').html(age_chart);
        //$('#total-wrap').html(response);

    }); 
}

// 일단,주간,월간 버튼 클릭 이벤트 
$('[data-smod]').on('click',function(){
    $('.cb-statistic-byrange').find('li').removeClass('cb-selected');
    $(this).addClass('cb-selected');
    var mod = $(this).attr('data-smod');
    $('input[name="d_start"]').val('');
    $('input[name="d_end"]').val('');
    $('input[name="mod"]').val(mod);
    Load_BotChart();  
});

// 기간 검색버튼 클릭 이벤트 
$('[data-role="btn-search"]').on('click',function(){
    $('input[name="mod"]').val('');
    $('.cb-statistic-byrange').find('li').removeClass('cb-selected');
    Load_BotChart();
});

// 봇 select change 이벤트 
$('select[name="botuid"]').on('change',function(){
   Load_BotChart(); 
});

// 최초 로딩시 
$(document).ready(function(){
   Load_BotChart();    
});

</script>