<?php 
$upjongArray =explode(',',$d['chatbot']['upjong']);// 업종 배열 추출 
if($uid) $R = getDbData($table[$m.'bot'],'uid='.$uid,'*');

if($R['avatar']){
    $avatar_src = 'style="background: url('.$R['avatar'].') center center no-repeat;background-size:150px 150px;"'; 
}
?>

<section id="cb-chatbot-form">
    <div>
        <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <?php if($R['uid']):?>
            <input type="hidden" name="a" value="update_bot" />
            <input type="hidden" name="mod" value="edit" />
            <?php else:?>
            <input type="hidden" name="a" value="regis_bot" />
            <?php endif?>
            <input type="hidden" name="name" value="<?php echo $V['name']?>" />
            <input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
            <input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
            <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />
            <input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$m?>/" /> <!-- 포토 업로드 폴더 -->
            <input type="hidden" name="avatar" value="<?php echo $R['avatar']?>" /> 

            <h1>봇 아바타를 넣어주세요.</h1>
            <div class="cb-chatbot-form-profileholder">
                <span style="display:none;"><input type="file" name="file" id="logo-inputfile"/></span>
                <div id="preview-logo" <?php echo $avatar_src?>>
                    <span class="cb-icon <?php echo !$avatar_src?'cb-icon-camera':''?>" id="getLogoPhoto"></span>
                </div>
            </div>
            <div class="cb-inputnaked-label">
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

            <?php if($R['uid']):?>
            <input type="submit" value="수정하기">
            <?php else:?>
            <input type="submit" value="추가하기">
            <?php endif?>                
        </form>
    </div>
</section>
<?php include $g['dir_module_skin'].'build/build_script.php';?>
