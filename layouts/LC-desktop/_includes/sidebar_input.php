<?php
$BL=getDbArray($table[$m.'bot'],"hidden=0",'uid,name','gid','asc','',1);
?>
<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <ul class="nav" id="side-menu">
            <li>
                <div class="lcform-label">       
                    <span>학습대상 챗봇</span>
                </div>
                <ul class="slimScrollDiv submenu collapse in">
                    <li>
                        <select class="form-control lc-select">
                            <option>선택</option>
                            <?php while($B = db_fetch_array($BL)):?>
                            <option value="<?php echo $B['uid']?>"><?php echo $B['name']?></option>
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
                       <div class="input-group">
                            <input type="text" class="form-control  lc-input">
                            <span class="input-group-addon lc-input">
                                <i class="fa fa-trash-o"></i>
                            </span>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- Left navbar-header end -->
