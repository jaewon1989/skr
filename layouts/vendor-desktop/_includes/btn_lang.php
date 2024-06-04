<style>
#langsel {
    display: none;
    position: absolute;
    display: none;
    width: 80px;
    height: auto;
    padding: 0px 10px 10px 10px;
    right: -40px;
    top: 35px;
    background-color: #fff;
    border: 1px solid #999;
    border-radius: 8px;
    box-shadow: 0px 0px 3px #666;
    font-size: 12px;
    text-align: left;
    z-index: 5;
}
#langsel li {
    margin-top: 10px;
}
.langbox {position: relative;}
.langbox .flag-wrap {display:inline-block;cursor: pointer;width: 17px;height: 17px;}
#langsel .langimg {
    display: inline-block;
    width: 20px;
    height: auto;
    vertical-align: middle;
    margin-right: 10px;
}

#langsel a {color: #333 !important;font-size: 10pt;}
#langbox-wrap {position: absolute; right: 20px;}
</style>
<?php
$data_la = $_GET['la']?$_GET['la']:$_POST['la'];
$language = $data_la ? $data_la : 'ko';
?>
<div id="langbox-wrap" style="top:<?php echo $my['uid']?'10':'0'?>px">
    <div class="langbox">
        <span class="flag-wrap" >
            <img id="top_lang_img" src="<?php echo $g['s']?>/_core/images/flag/<?php echo $language?>1.png" data-role="toggle-langsel">
        </span>

        <div class="langsel" id="langsel">
            <ul>
                <li>
                    <a href="#googtrans(pl|ko)" data-role="trans-act" data-lang="ko">
                       <img class="langimg" src="<?php echo $g['s']?>/_core/images/flag/ko2.png" alt=""> 한국
                    </a>
                </li>
                <li>
                    <a href="#googtrans(pl|en)" data-role="trans-act" data-lang="en">
                       <img class="langimg" src="<?php echo $g['s']?>/_core/images/flag/en2.png" alt=""> 미국
                    </a>
                </li>
                <li>
                    <a href="#googtrans(pl|zh-CN)" data-role="trans-act" data-lang="zh">
                        <img class="langimg" src="<?php echo $g['s']?>/_core/images/flag/zh2.png" alt=""> 중국
                    </a>
                </li>
                <li>
                    <a href="#googtrans(pl|ja)" data-role="trans-act" data-lang="ja">
                        <img class="langimg" src="<?php echo $g['s']?>/_core/images/flag/ja2.png" alt=""> 일본
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>
// var toggle_btn = $('[data-toggling]'); // 토글링 할 형제 레이어를 갖고있는 버튼
// var translate_btn = $('[data-translang]'); // 번역할 국가 버튼

// 번역 버튼 클릭 
$('[data-role="trans-act"]').on('click',function(){
   var location = $(this).attr('href'); 
   var lang = $(this).data('lang');
   sessionStorage.setItem("now_lang",lang);
   
   window.location=location;
   window.location.reload();
 
});

// 토글링
$('[data-role="toggle-langsel"]').on('click', function(){
   $('#langsel').toggle();
})


// 번역시 국가 국기 변경
var lang_check_interval = '';

function lang_check_action(){
   var now_lang = sessionStorage.getItem("now_lang")?sessionStorage.getItem("now_lang"):'ko';
   var src = rooturl+'/_core/images/flag/'+now_lang+'1.png';
   $('#top_lang_img').attr("src",src);
   //console.log(sessionStorage);
}

function lang_check(){
    setTimeout(lang_check_action,500); 
}

// $(document).ready(function(){
//    lang_check();
// });
function googleTranslateElementInit() {
 new google.translate.TranslateElement({
 pageLanguage: 'ko',autoDisplay: false 
 }, 'google_translate_element');
 lang_check();
}

</script>
<script src="http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>