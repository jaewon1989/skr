<?php
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='form';
$getAdBot = $chatbot->getAdmBot($_data);
$use_chatting = $getAdBot['use_chatting'];
$getAdBot['bot_client_url'] = "http".($g['https_on'] ? 's' : '')."://".$getAdBot['id'].".".$g['chatbot_host'];
?>
<link href="<?php echo $g['url_module_skin']?>/css/jquery.tag-editor.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/monitering.css" rel="stylesheet">
<!-- 클립보드 복사용 타겟 -->
<span data-role="clipboard-target" data-clipboard-text="" style="display:none"></span>
<div class="container-fluid" id="page-monitering">
    <div class="row bg-title">
        <div class="col-md-12">
            <h4 class="page-title"><?php echo $pageTitle?> : <span class="tit-url"><?php echo $getAdBot['bot_client_url']?></span></h4>
        </div>
    </div>
    <!-- row -->

    <div class="row">
        <div class="col-md-12 col-xs-12">
             <?php if($use_chatting =='on'):?>
            <div class="col-md-3">
                <div class="white-box" id="liveBot-list">
                    <table class="table table-striped table-full" id="tbl-intentSet" data-role="tbl-intentSet">
                        <thead>
                            <tr class="table-header">
                                <th class="roomToken-title">User Token</th>
                                <th class="roomUN-title" style="text-align:center;">U/R</th>
                                <th class="roomState-title">상태</th>
                            </tr>
                        </thead> 
                    </table>
                    <div data-role="table-scroll" class="table-scroll">    
                        <div class="list-group" data-role="botList-wrapper">
                            <!-- botList 실시간 출력 -->
                            
                        </div> 
                        <div class="moniteringPage-guide botList-guide" data-role="botListWrapper-guide">
                              <i class="fa fa-info-circle fa-lg"></i> 사용자가 챗봇 접속시 리스트가 출력됩니다.
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-5">
                <div class="white-box auto-box" >
                    <div class="moniteringChatbot-wrapper" data-role="moniteringChatbot-wrapper">
                        <div class="navTabs-wrapper">
                        <ul id="respondGroup-tab" class="nav nav-tabs" data-role="chatHeaderContainer">

                        </ul>
                        </div>
                        <div class="tab-content" data-role="chatBodyContainer">
                   
                        </div>                      
                    </div>
                    <div class="moniteringPage-guide chatBox-guide" data-role="chatbotWrapper-guide">
                          <i class="fa fa-info-circle fa-lg"></i> 좌측 챗봇 리스트를 클릭해주세요.
                    </div>                   
                </div>
            </div>
            <div class="col-md-4">
                <div class="white-box auto-box" id="mn-rightPanel">
                    
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#mn-search" data-toggle="tab">응답 힌트</a> 
                        </li>
                        <li>
                            <a href="#mn-fa" data-toggle="tab">자주 사용하는 문장</a> 
                        </li> 
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="mn-search">
                            <div class="moniteringPage-guide" data-role="resHintWrapper-guide">
                                  <i class="fa fa-info-circle fa-lg"></i> 사용자가 문장 입력시  응답 힌트가 출력됩니다.
                            </div>
                            <div class="resHint-wrapper moni-right" data-role="resHint-wrapper" id="resHintWrapper-" >
                                 <!-- 응답 힌트 출력 -->
                            </div>                                                       
                        </div> 
                        <div class="tab-pane" id="mn-fa">
                            <div class="form-group row">
                                
                                <div class="col-md-12">
                                    <textarea class="codehim-input-tags ta-addFA" rows="2" name="tags" placeholder="문장을 입력한 엔터키를 눌러주세요" data-role="ta-addFA"></textarea>          
                                </div>
                                <!-- <div class="col-md-2">
                                    <div class="btn btn-primary btn-block btn-outline waves-effect waves-light btn-addFA" data-role="btn-addFA">등록</div>
                                </div> -->
                             
                                
                            </div>
                            <div class="form-group tagFA-wrapper" data-role="tagFA-wrapper"> 
                                <section class="show-input-tags">
                                    <ul class="tags cloud-tags" data-role="tagFA-UL">
                        
                                    </ul>
                                </section> 
                                  
                            </div>                   
                        </div>
                    </div>      
                   
                </div>
            </div>
            <?php else:?>
            <div class="col-md-12">
                <div class="white-box auto-box">
                    <h3 class="bot-title"> 챗봇설정 > 고급설정 > 모니터링 부분을 '사용' 으로 설정해주세요</h3>
                </div>
            </div>
            <?php endif?>

        </div>
    </div><!-- row -->   
</div>
<?php include($g['dir_module_skin'].'adm/_monitering_script.php');?>