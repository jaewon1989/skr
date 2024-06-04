<!-- <div class="text-center pt" >
     <ul class="pagination pagination-sm">
        <script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
      </ul>
</div> -->
<!-- bootstrap css -->
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<!-- End of  bootstrap-timepicker,  https://github.com/jdewit/bootstrap-timepicker/ , http://jdewit.github.io/bootstrap-timepicker/ : 메뉴얼 -->
<?php getImport('bootstrap-timepicker','js/bootstrap-timepicker.min',false,'js')?>
<?php getImport('bootstrap-timepicker','css/bootstrap-timepicker.min',false,'css')?>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>

<script>

    $('.tpicker').timepicker({
      defaultTime : '',
      //showSeconds : true, // 초 노출
      showMeridian:true, // 24시 모드 
      maxHours: 24,
      minuteStep : 15
   });

  // 날짜 선택 
  $('.input-daterange').datepicker({
      format: "yyyy-mm-dd",
      todayBtn: "linked",
      language: "kr",
      calendarWeeks: true,
      todayHighlight: true,
      autoclose: true
  });



$(document).on('click','[data-role="btn-search"]',function(){
   var f = document.storySearchForm;
   f.submit(); 
});

</script>