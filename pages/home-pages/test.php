<?php 
// include $g['path_module'].'chatbot/includes/base.class.php';
// include $g['path_module'].'chatbot/includes/module.class.php';
// $chatbot = new Chatbot();
// $QCD = getDbSelect($table['chatbotquestion'],'hidden=0','*');
// while($Q=db_fetch_array($QCD)){
//         $RP = getDbData($table['chatbotreply'],'uid='.$Q['r_uid'],'*');
//         // 답변관련 세팅 
//         $r_uid = $RP['uid'];
//         $r_type = $RP['type'];
//         $reply = $RP['content'];
//         $vendor =$RP['vendor'];

//         // 질문관련 세팅 
//         $q_uid = $Q['uid'];
//         $language = $Q['lang'];
//         $quesCat = $Q['quesCat'];
//         $pattern = $chatbot->getReplyRule($Q['morpheme']);

//         $is_row = getDbData($table['chatbotrule'],'r_uid='.$r_uid.' and q_uid='.$q_uid,'uid');
//         // 기존에 있는 경우 pattern 과 reply 만 업데이트 한다 .
//         if($is_row['uid']){
//            $QVAL = "pattern='".$pattern."',reply='".$reply."'";
//            getDbUpdate($table['chatbotrule'],$QVAL,'uid='.$is_row['uid']);
//         }else{
//            $QKEY ="vendor,quesCat,lang,r_uid,r_type,q_uid,pattern,reply";
//            $QVAL ="'$vendor','$quesCat','$language','$r_uid','$r_type','$q_uid','$pattern','$reply'";
//            getDbInsert($table['chatbotrule'],$QKEY,$QVAL);

//         } 
// }
include $g['path_module'].'chatbot/includes/google.trans.class.php';
//use \Statickidz\GoogleTranslate;

$source = 'en';
$target = 'ko'; // en, zh-CN, zh-TW, ja
$text = 'hello';

$trans = new GoogleTranslate();
$result = $trans->translate($source, $target, $text);

echo $result;

?>
<div role="form">
    <div class="form-group">
        <textarea name="message" placeholder="무엇을 도와드릴까요? ..." class="c-form-message form-control" id="c-form-message"></textarea>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary pull-right" id="send-message">Send message</button>
    </div>
</div>
<div id="result" style="padding-top:20px;" ><?php echo $percent;?>   </div>
        
<script>
$('#send-message').on('click',function(e){
    
    e.preventDefault();
    var message = $('textarea[name="message"]').val();
    if(message==''){
        alert('질문내용을 입력해주세요');
        return false;
    }
    $.post(rooturl+'/?r='+raccount+'&m=site&a=get_reply2',{
        message : message
    },function(response) {
        var result = $.parseJSON(response); 
        $("#result").html(result.content);
    });
});

</script>
