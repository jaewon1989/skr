<?php 
$sort   = $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum = $recnum && $recnum < 200 ? $recnum : 10;

$_WHERE='vendor='.$V['uid'];

$BCD = getDbArray($table[$m.'bot'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'bot'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);  
?>
<section id="cb-chatbot-adder">
    <div class="cb-chatbot-adder-wrapper">
        <div class="cb-chatbot-adder-top">
            <?php if($NUM):?>
            <a href="<?php echo RW('c=build')?>" data-control="push" data-transition="fade" style="width:100%;">
                <div class="cb-button cb-button-adder">+ 추가하기</div>
            </a>
            <?php else:?>
            <a href="<?php echo RW('c=build')?>" style="width:100%;" data-control="push" data-transition="fade">
                <div class="cb-button cb-button-adder cb-pink">+ 추가하기</div>
            </a>
            <?php endif?>
        </div>
        <div class="cb-chatbot-adder-body">
            <div class="cb-chatbot-adder-list cb-row">
                <?php if($NUM):?>
                    <?php $i=1;while($B=db_fetch_array($BCD)):?>
                    <?php $chatUrl = $chatbot->getChatUrl($B);?>
                    <div class="cb-row-2d" id="bot-box-<?php echo $B['uid']?>">
                        <div class="cb-chatbot-adder-item" style="position:relative;" id="botUrl-wrapper-<?php echo $i?>">
                            <div class="cb-chatbot-adder-box">
                                <div class="cb-rightward">
                                    <span class="cb-icon cb-icon-close" data-act="delete-bot" data-uid="<?php echo $B['uid']?>"></span>
                                </div>
                                <div class="cb-centerward">
                                    <span class="cb-chatbotname"><?php echo $B['service']?></span>
                                    <div class="cb-circle-image">
                                        <a href="#" data-toggle="modal" data-target="#modal-chatBox" data-role="getComponent" data-markup="chatbox" data-url="<?php echo $chatUrl?>" data-open="chatbox" data-id="<?php echo $B['id']?>" >  
                                        <img src="<?php echo $chatbot->getBotAvatarSrc($B)?>" alt="Circle Image">
                                        </a>
                                    </div>
                                    <div class="cb-button cb-button-copyurl" data-role="clipboard-copy" data-clipboard-text="<?php echo $chatUrl?>" data-container="#botUrl-wrapper-<?php echo $i?>" data-feedback="챗봇 URL 이 복사되었습니다.">URL 복사</div>
                                </div>
                            </div>
                            <div class="cb-chatbot-adder-enableoption">
                                <div class="cb-cell-layout">
                                    <div class="cb-cell cb-cell-left">
                                        <span class="cb-label" data-role="botActive-label-<?php echo $B['uid']?>">챗봇 활성화</span>
                                    </div>
                                    <div class="cb-cell cb-cell-right<?php echo !$B['display']?' botSwitch-off':''?>">
                                        <div class="cb-switch" data-role="bot-active" data-uid="<?php echo $B['uid']?>" data-container="#botUrl-wrapper-<?php echo $i?>">
                                            <span class="cb-switch-button"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cb-chatbot-adder-enableoption">
                                <div class="cb-cell-layout">
                                    <div class="cb-cell cb-cell-left">
                                        <span class="cb-label">설정 변경</span>
                                    </div>
                                    <div class="cb-cell cb-cell-right">
                                        <a href="<?php echo RW('c=build')?>&amp;uid=<?php echo $B['uid']?>">
                                            <div class="cb-switch icon-go-wrap">
                                               <span class="cb-icon cb-icon-go"></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $i++;endwhile?>
                <?php else:?>
                     <div class="cb-row-2d">
                        <div class="cb-chatbot-adder-item">
                            <div class="cb-chatbot-adder-box">
                                <div class="cb-chatbot-centering">
                                    <a href="<?php echo RW('c=build')?>" style="margin-top:30%" data-control="push" data-transition="fade"> 
                                    <span class="cb-icon cb-icon-plus2"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif?>
               

            </div>
        </div>
    </div>
</section>