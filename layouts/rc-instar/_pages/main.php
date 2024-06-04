<?php
$bot_id = 'bj7d6LuKIk9VvfE';//$_GET['botid']?$_GET['botid']:($B['id']?$B['id']:$B_id);
$themeName ='default-mobile'; 
$emoticon_path = $g['path_module'].'chatbot/lib/emoticon/';
$cmod = 'cs';
?>
<input type="hidden" name="huid" />
<div id="chatBox-container">
</div>

<script>
var mbruid = localStorage.getItem("mbruid");//'<?php echo $my['uid']?$my['uid']:$_SESSION['mbruid']?>';
$(function() {
    $('#chatBox-container').PS_chatbot({
        moduleName : 'chatbot',
        themeName : '<?php echo $themeName?>',
        emoticon_path : '<?php echo $emoticon_path?>',
        botId : '<?php echo $bot_id?>',
        cmod : '<?php echo $cmod?>',
        mbruid : mbruid,
    });
});

$(document).on('show.rc.page','#page-movieView',function(e){
     var target = e.relatedTarget;
     var title = $(target).data('title');
     var rated = $(target).data('rated');
     var grade = $(target).data('grade');
     var genre = $(target).data('genre');
     var actors = $(target).data('actors');
     var director = $(target).data('director');
     var country = $(target).data('country');
     var pyear = $(target).data('pyear');
     var poster = $(target).data('poster');
     var token = $(target).data('token');
     var aid = $(target).data('aid');
     var rmv = $(target).data('rmv');

     // 값 입력
     $(this).find('[data-role="dt-title"]').text(title);
     $(this).find('[data-role="dt-rated"]').text(rated);
     $(this).find('[data-role="dt-genre"]').text(genre);
     $(this).find('[data-role="dt-actors"]').text(actors);
     $(this).find('[data-role="dt-director"]').text(director);
     $(this).find('[data-role="dt-country"]').text(country);
     $(this).find('[data-role="dt-pyear"]').text(pyear);
     $(this).find('[data-role="dt-title"]').text(title);
     $(this).find('[data-role="dt-grade"]').text(grade);
     $(this).find('[data-role="dt-poster"]').css({
        "background":"url("+poster+") no-repeat",
        "background-size":"cover",
        "background-position":"top"
    });

    // 인공지능 추천영화  
    if(rmv){
        $(this).find('[data-role="show-rmv"]').css("display","block");
        var slot_rows =
         "<div class='swiper-container keyword-swiper' data-extension='swiper' data-pagination='false' data-spaceBetween='20' data-freemode='true' data-slidesperview='auto' style='margin-left:0;'>\n"
              + "<div class='swiper-wrapper'>\n";
        var rmv_arr = rmv.split('(^^)');
        for(var i=0;i<rmv_arr.length;i++){
            var rm_data = rmv_arr[i];
            var rm_data_arr = rm_data.split('/');
            var rm_title = rm_data_arr[0];
            var rm_aid = rm_data_arr[1];
            var rm_rated = rm_data_arr[2];
            var rm_genre = rm_data_arr[3];
            var rm_actors = rm_data_arr[4];
            var rm_director = rm_data_arr[5];
            var rm_country = rm_data_arr[6];
            var rm_pyear = rm_data_arr[7];
            var rm_grade = rm_data_arr[8];
            var rm_poster = rooturl+'/mv_poster/'+rm_aid+'.jpg';
            var rm_thumb = rooturl+'/_core/opensrc/timthumb/thumb.php?src='+rooturl+'/mv_poster/'+rm_aid+'.jpg&h=230&q=1000&a=t';
            if(rm_aid){
                slot_rows +='<div class="mv-slot-item swiper-slide" data-toggle="page" data-start="#page-movieView" data-target="#page-movieView2" data-title="'+rm_title+'" data-url="/view/'+rm_aid+'" data-rated = "'+rm_rated+'" data-genre="'+rm_genre+'" data-actors="'+rm_actors+'" data-director="'+rm_director+'" data-country="'+rm_country+'" data-pyear="'+rm_pyear+'" data-grade="'+rm_grade+'" data-poster="'+rm_poster+'" style="width:161.5px;">';
                    slot_rows +='<div class="poster-wrapper">';
                        slot_rows +='<img src="'+rm_thumb+'" />';
                    slot_rows +='</div>';
                    slot_rows +='<p class="slot-title">'+rm_title+'</p>';
                slot_rows +='</div>'; 
            }
        }
        slot_rows +='</div></div>';
        $(this).find('[data-role="rmv-swiper"]').html(slot_rows);
        setTimeout(function(){
           RC_initSwiper();   
        },200);

    } 
    
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=save_viewLog',{
        mbruid : mbruid,
        aid : aid,
        title : title,
        rated : rated,
        grade : grade,
        genre : genre,
        actors : actors,
        director : director,
        country : country,
        token : token
    },function(response) {
        var result = $.parseJSON(response);
        var content = result.content;    
        console.log(content);

    });    


});

// 1단계 상세보기 페이지 닫히면서 
$(document).on('hide.rc.page','#page-movieView',function(e){
    setTimeout(function(){
       $('#page-movieView').find('[data-role="rmv-swiper"]').html('');
       $('#page-movieView').find('[data-role="show-rmv"]').css("display","none");
    },200);
})

// 1단계 상세보기 
$(document).on('hidden.rc.page','#page-movieView',function(e){
    setTimeout(function(){
        $('#page-movieView').find('.content').animate({
             scrollTop: 0
        }, 10);
        $('#page-movieView').find('.cb-chatting-scroll').animate({
             scrollTop: 0
        }, 10);
    },200);

})

// 연관영화 상세보기 
$(document).on('show.rc.page','#page-movieView2',function(e){
     var target = e.relatedTarget;
     var title = $(target).data('title');
     var rated = $(target).data('rated');
     var grade = $(target).data('grade');
     var genre = $(target).data('genre');
     var actors = $(target).data('actors');
     var director = $(target).data('director');
     var country = $(target).data('country');
     var pyear = $(target).data('pyear');
     var poster = $(target).data('poster');
     var aid = $(target).data('aid');

     // 값 입력
     $(this).find('[data-role="dt-title"]').text(title);
     $(this).find('[data-role="dt-rated"]').text(rated);
     $(this).find('[data-role="dt-genre"]').text(genre);
     $(this).find('[data-role="dt-actors"]').text(actors);
     $(this).find('[data-role="dt-director"]').text(director);
     $(this).find('[data-role="dt-country"]').text(country);
     $(this).find('[data-role="dt-pyear"]').text(pyear);
     $(this).find('[data-role="dt-title"]').text(title);
     $(this).find('[data-role="dt-grade"]').text(grade);
     $(this).find('[data-role="dt-poster"]').css({
        "background":"url("+poster+") no-repeat",
        "background-size":"cover",
        "background-position":"top"
    });

});

// 2단계 상세보기 닥고 나서 
$(document).on('hidden.rc.page','#page-movieView2',function(e){
    setTimeout(function(){
        $('#page-movieView2').find('.content').animate({
             scrollTop: 0
        }, 10);
        $('#page-movieView2').find('.cb-chatting-scroll').animate({
             scrollTop: 0
        }, 10);
   
    },200);

})

</script>