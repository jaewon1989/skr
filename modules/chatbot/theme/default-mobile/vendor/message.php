<?php
$_WHERE2='vendor='.$V['uid'].' and type=1';
$BCD = getDbArray($table[$m.'bot'],$_WHERE2,'*','gid','asc','',1);
?>
<section id="cb-chatbot-admsg">
	<form name="messageForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
	    <input type="hidden" name="r" value="<?php echo $r?>" />
	    <input type="hidden" name="c" value="<?php echo $c?>" />
	    <input type="hidden" name="m" value="<?php echo $m?>" />
	    <input type="hidden" name="a" value="regis_notification" />
	    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
	    <div class="cb-chatbot-admsg-wrapper" id="msgTa-wrapper" style="position:relative;">
            <div class="cb-viewchat-search-timebox" style="width:100%;border:solid 1px #d9d9d9;margin-bottom: 20px;">
                <select name="botuid" style="font-size:inherit;">
                	<option value=""> + 챗봇을 선택해주세요 </option>
                    <?php $i=1;while($B=db_fetch_array($BCD)):?>
                    <option value="<?php echo $B['uid']?>" <?php if($botuid==$B['uid']):?>selected<?php endif?>>
                        <?php echo $B['service']?>
                    </option>
                    <?php $i++;endwhile?> 
                </select>
            </div>

	        <div class="cb-chatbot-admsg-box">
	            <textarea name="message" data-role="ta-message"></textarea>
	        </div>
	        <div class="cb-chatbot-admsg-send" data-role="btn-sendMessage" data-vendor="<?php echo $V['uid']?>">보내기</div>
	    </div>
    </form>
</section>


