<?php 
/*
   등록 스크립트는 layouts/rc-instar/_includes/build_script.php 에 있다. (모달 페이지에서도 사용하기 위해서 )
*/

// 단계 출력 부분  
include $g['dir_module_skin'].'build/step_top.php';

?>

<section id="cb-chatbot-form">
    <div>
        <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return regisBotCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <?php if($R['uid']):?>
            <input type="hidden" name="a" value="update_bot" />
            <input type="hidden" name="mod" value="edit" />
            <?php else:?>
            <input type="hidden" name="a" value="regis_bot" />
            <?php endif?>
            <input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
            <input type="hidden" name="step" value="1" />
            <input type="hidden" name="next_page" value="build/step2" /> 
            <input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
            <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />
            <input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$m?>/" /> <!-- 포토 업로드 폴더 -->
            <input type="hidden" name="avatar" value="<?php echo $R['avatar']?>" /> 

            <h1>회사로고를 넣어주세요.</h1>
            <div class="cb-chatbot-form-profileholder">
                <span style="display:none;"><input type="file" name="file" id="logo-inputfile"/></span>
                <div id="preview-logo" <?php echo $avatar_src?>>
                    <span class="cb-icon cb-icon-camera" id="getLogoPhoto"></span>
                </div>
            </div>
            <div class="cb-inputnaked-label" style="margin-top:50px;">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <span>업종</span>
                    </div>
                    <div class="cb-cell cb-cell-center">
                        <div class="cb-selectholder">
                            <select name="induCat">
                                <option value="">업종을 선택해주세요.</option>
                                <?php foreach($upjongArray as $upjong):?>
                                 <option value="<?php echo $upjong?>"<?php if($upjong==$R['induCat']):?> selected<?php endif?>><?php echo $upjong?></option> 
                                <?php endforeach?>
                            </select>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <span>업체명</span>
                    </div>
                    <div class="cb-cell cb-cell-center">
                        <input name="name" value="<?php echo $V['name']?>"type="text">
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <span>서비스명</span>
                    </div>
                    <div class="cb-cell cb-cell-center">
                        <input name="service" value="<?php echo $R['service']?>" type="text">
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label cb-wide">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <span>서비스명 소개</span>
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-center">
                        <textarea name="intro" rows="3" style="width:100%;border:none;"><?php echo $R['intro']?></textarea>
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label">
                <div class="cb-cell-layout">
                    <div class="cb-cell cb-cell-left">
                        <span>웹사이트</span>
                    </div>
                    <div class="cb-cell cb-cell-center">
                        <input name="website" value="<?php echo $R['website']?>" type="text">
                    </div>
                </div>
            </div>
            <div class="cb-inputnaked-label">
                <div class="cb-cell-layout">
                    <span style="display:none;"><input type="file" name="intro_attach" id="intro-inputfile"/></span>
                    <div class="cb-cell cb-cell-left" style="width: 170px;" id="attach-result">
                        <span id="preview-intro">
                            <?php if($R['upload']):?>
                               <?php echo $chatbot->getBotUpload($R,'name');?>
                            <?php else:?>
                            회사소개용 이미지를 첨부해주세요.                            
                            <?php endif?>
                        </span>
                    </div>
                    <div class="cb-cell cb-cell-right">
                        <span id="cb-attachfile" data-role="attach-introPhoto" style="cursor: pointer;">첨부하기</span>
                    </div>
                </div>
            </div>
            <?php if($R['uid']):?>
            <input type="submit" value="다음으로">
            <?php else:?>
            <input type="submit" value="만들기">
            <?php endif?>                
        </form>
    </div>
</section>
