<div class="right-panel" data-role="rightPanel">
    <div class="container-fluid">

        <div class="overview">
            <div class="page-title">미인식 설정</div>
            <div class="sub-frame">
                <div class="sub-title"><b style="color:red;">챗봇</b>의 미인식 설정은 <b style="color:red;">Fallback</b> 대화상자에서 일괄 설정 됩니다.</div>
            </div>
        </div>
        <div class="table-responsive table-wrapper table-container table-aicc-skin" data-role="config-table-wrapper">
            <table class="table table-striped table-full">
                <colgroup>
                    <col width="20%">
                    <col width="*">
                </colgroup>
                <thead>
                    <tr class="table-header">
                        <th>항목</th>
                        <th>값</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>미인식 최대 횟수</td>
                        <td><input type="text" class="form-control" name="unrecognizedCount" style="width:100px;float:left;"> 회</td>
                    </tr>
                    <tr>
                        <td>미인식 메세지</td>
                        <td><input type="text" class="form-control" name="unrecognizedMsg"></td>
                    </tr>
                    <tr>
                        <td>횟수 초과 메세지</td>
                        <td><input type="text" class="form-control" name="exceededMsg"></td>
                    </tr>
                    <tr>
                        <td>실패 메세지</td>
                        <td><input type="text" class="form-control" name="failMsg"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h1></h1>
        <div class="overview">
            <div class="page-title">세션 종료 설정</div>
            <div class="sub-frame">
                <div class="sub-title">대화 상자별 타임아웃을 설정합니다. <b style="color:red;">기본설정 > 세션 종료 설정 보다 우선 순위가 높습니다.</b></div>
            </div>
        </div>
        <div class="table-responsive table-wrapper table-container table-aicc-skin" data-role="table-wrapper">
            <table class="table table-striped table-full">
                <colgroup>
                    <col width="20%">
                    <col width="*">
                </colgroup>
                <thead>
                    <tr class="table-header">
                        <th>항목</th>
                        <th>값</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>세션 종료 시간</td>
                        <td><input type="text" class="form-control" name="timeout" style="width:100px;float:left;"> 초</td>
                    </tr>
                    <tr>
                        <td>세션 종료 메세지</td>
                        <td><input type="text" class="form-control" name="timeoutMsg"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group btnSave-wrapper" id="config-submitForm">
            <div class="row">
                <div class="col-md-8 col-md-offset-5">
                    <button data-role="btn-config" data-pact="save" class="btn btn-secondary">저장</button>
                    <button data-role="btn-config" data-pact="cancel" class="btn btn-default">취소</button>
                    <input type="hidden" name="dialogName">
                </div>
            </div>
        </div>
    </div>
</div>