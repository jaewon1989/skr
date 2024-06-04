<!-- 레거시 추가 모달-->
<div id="modal-settingsLegacy" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" data-role="settingLegacyModal">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addModal-title"><?php echo $itemName?> 추가하기</h4>
            </div>
            <div class="modal-body">  
                <form id="botForm" data-role="settingsLegacyForm" autocomplete="off">
                    <input type="hidden" name="uid" />
                    <div class="form-group row">
                        <label class="col-md-3 control-label"><?php echo $itemName?>명</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label"><?php echo $itemName?> 설명</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label"> 기본 URI</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="url"></textarea>
                        </div>
                    </div>
                </form>                   
            </div>                    
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="save-legacySettings">추가하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
           
        </div>
    </div>
</div>

<!-- Auth 추가 모달-->
<div id="modal-settingsAuth" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" data-role="settingAuthModal">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Authorization 추가하기</h4>
            </div>
            <div class="modal-body">  
                <form data-role="settingsAuthForm" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">Type</label>
                        <div class="col-md-9">
                            <div class="checkbox checkbox-info checkbox-circle">
                                <input id="checkbox8" type="checkbox" checked="" disabled>
                                <label for="checkbox8"> Basic </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">Username</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="user_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="pw">
                            <p class="help-block">
                                <div class="checkbox checkbox-info">
                                    <input id="checkBox-showPW" type="checkbox" data-role="chkBox-showPW">
                                    <label class="task-done" for="checkBox-showPW">
                                    Password 보기 
                                    </label> 
                                </div>
                            </p>
                        </div>
                    </div>
                </form>                   
            </div>                    
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="save-AuthSettings">추가하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
           
        </div>
    </div>
</div>