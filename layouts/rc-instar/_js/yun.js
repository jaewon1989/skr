/* modal 관련 스크립트 */
var continent_btn=$('[data-toggle="filterbox"] [data-toggle="select-continent"]'); // 대륙 버튼
var nation_btn=$('[data-toggle="filterbox"] [data-continentbox] > .dm-filter-button'); // 국가 버튼
var nation_reset_btn=$('[data-toggle="filterbox"] [data-toggle="reset-nation"]'); // 선택 국가 초기화
var sexbox = $('[data-toggle="filterbox"] [data-toggle="select_sex"]'); // 모달 내 성별 선택 input 상자 
var saver = $('[data-toggle="filterbox"] [data-toggle="dm-filter-save"]');
var social_login_btn = $('[data-role="social-login"][data-connect]');
var toggle_btn = $('[data-toggling]'); // 토글링 할 형제 레이어를 갖고있는 버튼
var translate_btn = $('[data-translang]'); // 번역할 국가 버튼

// 전체 선택 클릭시 동작(체크박스도 체크상태로 변경)
$(continent_btn).on('click',function(){
    var continent=$(this).data('continent'); // 대륙
    var continent_items = $('[data-continentbox="'+continent+'"] > .dm-filter-button');
    // 해당 자식요소들 모두 체크상태인지 확인
    if(continent_items.children('input:not(:checked)').length == 0)
      {
        continent_items.children('input.dm-nation').prop('checked',false);
        $(this).removeClass('selected');
        continent_items.removeClass('selected');
      }
      else {
        continent_items.children('input.dm-nation').prop('checked',true);
        $(this).addClass('selected');
        continent_items.addClass('selected');
      }

})

// 다시 선택 버튼 클릭시
$(nation_reset_btn).on('click',function(){
   $('.dm-filter-button > .dm-nation').prop('checked',false);
   $('.dm-filter-button').removeClass('selected');
})

// nav-nation 관련 checkbox 클릭 선택
 $(nation_btn).on('click',function(){
    var active_obj = $(this).parents().data('continentbox');

 	// 셀렉트 박스 시각효과 및 체크박스 처리
    $(this).children('input.dm-nation').click();
      if($(this).children('input.dm-nation').prop('checked')==true){
         $(this).addClass('selected');
      } else {
         $(this).removeClass('selected');
      }

      // 대륙 활성화 처리
      if($('[data-continentbox="'+active_obj+'"] > .dm-filter-button.selected').length>0){
      	$('[data-continent="'+active_obj+'"]').addClass("selected");
      }
      else {
      	$('[data-continent="'+active_obj+'"]').removeClass("selected");	
      }
  });

  $('.dm-filter-button.selected').parents().parents().siblings('.dm-left').children('[data-continent]').addClass("selected");

$(sexbox).on('click', function(){
    $(sexbox).removeClass("selected")
    $(this).addClass("selected")
})

$(saver).on('click', function(){
      if($(this).prop('checked')==true){
         $(this).siblings('.dm-icon-checkbullet').addClass('selected');
      } else {
         $(this).siblings('.dm-icon-checkbullet').removeClass('selected');
      }
})


// 소셜 로그인 버튼 클릭시
$(social_login_btn).on('click',function(){
  var url = $(this).data('connect');
  location.href=url;
})

// href 링크 처리
$('[data-href]').on('click',function(){
    var url = $(this).data('href');
    location.href = url;
})
// 알람창 처리
$('[data-alert]').on('click',function(){
    var alert = $(this).data('alert');
    alert(alert);
})
// 비로그인시 로그인 유도
$('[data-status="nologin"]').on('click',function(){
  var select = $(this).data('msg');
  if(select != 'no') alert('로그인이 필요합니다.');
    $("#modal-login").modal({
      title: '로그인'
    });
})

// 토글링
toggle_btn.on('click', function(){
  var obj = $(this).data('toggling');
  $('#'+obj).toggle();
})


// 번역
translate_btn.on('click', function(){
  var tolang = $(this).data('translang');
    var form = document.createElement('form');
    var objs;
    objs = document.createElement('input');
    objs.setAttribute('type', 'hidden');
    objs.setAttribute('name', 'la');
    objs.setAttribute('value', tolang);
    form.appendChild(objs);
    form.setAttribute('method', 'get');
    form.setAttribute('action', location.href);
    document.body.appendChild(form);
    form.submit();
});


// 번역시 국가 국기 변경
var lang_check_interval = '';

$(window).load(function(){
  var lang_checking_system = $("iframe.goog-te-banner-frame").attr("src");
  if(lang_checking_system){
    lang_check_interval = setInterval(function () {lang_check_action()}, 2000); 
  }
  // URL 해쉬 태그에 구글 번역기 #googtrans 문자열이 있으면 해쉬 초기화
  if(document.location.href.indexOf('#googtrans') > 0){
    var stateObj = { foo: "play" };
    history.pushState(stateObj, "SHOTPING PLAY", "/play/");
  }

});
function lang_check_action(){
  var lang_text = $(".quick_mmenu_window .wrap2 .box1 .lang_ck font font").text();
  lang_text = lang_text.replace(/\s/g, '');
  if(lang_text == 'All'){
    $("#top_lang_img").attr("src","/data/skin/mobile_ver2_default/images/custom/lang2.png");
    clearTimeout(lang_check_interval);
  }else if(lang_text=='该'){
    $("#top_lang_img").attr("src","/data/skin/mobile_ver2_default/images/custom/lang3.png");
    clearTimeout(lang_check_interval);
  }else if(lang_text=='すべて'){
    $("#top_lang_img").attr("src","/data/skin/mobile_ver2_default/images/custom/lang4.png");
    clearTimeout(lang_check_interval);
  }
}

