<div class="panel panel-default testLogInner">
    <div class="panel-heading testLogInner-heading">
        <div class="row">
            <div class="col-md-3">입력값 분석</div>
            <div class="col-md-3"><?php echo $callIntent?> 분석</div>
            <div class="col-md-3"><?php echo $callEntity?> 분석</div>
            <div class="col-md-3">대화상자 분석</div>
        </div>
        <a href="#" data-role="btn-closeTestLogPanel" id="btn-closeTestLogPanel">
            <span class="cb-icon cb-icon-close"></span>
        </a>
    </div>
    <div class="panel-body testLogInner-body">
        <div class="row">
            <div class="col-md-3" id="show-userInputAnal">
                <div class="form-group">
                    <label for="usr">사용자 입력값</label>
                    <div data-role="testLogPanel-userInput" class="testLog-value"></div>
                </div>
                <div class="form-group">
                    <label for="usr">형태소 분석</label>
                    <div>
                        <table class="table mop-table" data-role="mopAnalBox">
                  
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3" id="show-intentAnal">
                <table class="table">
                    <tbody>
                      <tr>
                        <th><?php echo $callIntent?>명 : </th>
                        <td data-role="intentName" class="testLog-value"></td>
                      </tr>
                      <tr>
                        <th>Score : </th>
                        <td data-role="intentScore" class="testLog-value"></td>
                      </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <label for="usr">Score 리스트</label>
                    <div>
                        <table class="table mop-table" data-role="intentScoreList">
                  
                        </table>
                    </div>
                </div>

            </div>                    
            <div class="col-md-3" id="show-entityAnal">
                <table class="table" data-role="entityListBox">

                </table> 
            </div>
            <div class="col-md-3" id="show-nodeAnal">
                <table class="table">
                    <tbody>
                      <tr>
                        <th>대화상자 : </th>
                        <td data-role="nodeName" class="testLog-value"></td>
                      </tr>
                    </tbody>
                </table>
 <!--                <div class="form-group">
                    <label for="usr">응답내용 분석</label>
                    <div>
                        <ul class="list-group" data-role="nodeResAnalBox">
                        </ul> 
                    </div>
                </div> -->

            </div>
        </div>
    </div>
</div> 