<?php
$BL=getDbArray($table[$m.'bot'],"hidden=0",'uid,name,id','gid','asc','',1);
?>
<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <ul class="nav" id="side-menu" data-role="sideMenu-ul">
            <li>
                <div class="lcform-label">       
                    <span>학습대상 챗봇</span>
                </div>
                <ul class="slimScrollDiv submenu collapse in">
                    <li>
                        <select class="form-control lc-select" data-role="lc-target">
                            <option value="">학습대상 챗봇 선택</option>
                            <?php while($B = db_fetch_array($BL)):?>
                            <option value="<?php echo $B['id']?>"><?php echo $B['name']?></option>
                            <?php endwhile?>
                        </select>

                    </li>
                </ul>
            </li>
            <li>
                <div class="lcform-label">       
                    <span>단어/문장 입력 </span>
                </div>
                <ul class="slimScrollDiv submenu collapse in">
                    <li>
                       <textarea class="form-control lc-ta" rows="10" placeholder="여러문장 입력시 엔터" data-role="lc-source"></textarea>
                    </li>
                </ul>

            </li>
            <li>
                <div class="lcform-label">       
                    <span>예문 갯수 </span>
                </div>
                <ul class="slimScrollDiv submenu collapse in">
                    <li>
                       <div class="input-group">
                            <input type="text" class="form-control lc-input" data-role="lc-exNum">
                            <span class="input-group-addon lc-input">
                                개
                            </span>
                        </div>
                    </li>
                </ul>

            </li>
            <li>
                <div class="center p-20">
                    <div class="btn btn-primary btn-block btn-outline waves-effect waves-light" data-role="btn-startLC">학습시작</div> 
                </div>  
            </li>
            <li>
                <div class="lcform-label">       
                    <span>답변 실패 문장 </span>
                    <span class="btn btn-primary btn-outline waves-effect waves-light btn-failDown" data-role="btn-downLoadFail">다운로드</span>
                </div>
                <ul class="slimScrollDiv submenu collapse in failList" data-role="LCbot-failList">
                   
                </ul>
            </li>
        </ul>
       
    </div>
</div>

<form name="downFailForm" id="downFailForm" action="/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data"> 
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="a" value="do_chatbotLC">
    <input type="hidden" name="linkType" value="downLoadFail" /> 
    <input type="hidden" name="failData" value="">
</form>
<!-- Left navbar-header end -->
