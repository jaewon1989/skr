
<form id="nodeSetting">
    <input type="hidden" name="graph_id" /> <!-- 그래프 쪽에서 랜덤 생성 -->
    <input type="hidden" name="node_id" />
    <div class="form-group ele-hideFilterBox" id="node_name">
        <input type="text" name="node_name" class="form-control" placeholder="노드 이름">
    </div>
    <div id="nodeScroll" data-role="respond-scroll">
        <div class="form-group">
            <label for="recognize">
                인풋 
                <i class="fa fa-question-circle guide-question guide-input" aria-hidden="true" data-toggle="tooltip" id="guide-input"></i>
            </label>
            <div class="inputFilter-wrapper" data-role="inputFilter-wrapper">
                <div class="input-item" data-role="inputFilter-Box" data-order="1">
                   <input type="hidden" name="recognize[]" />
                   <span class="inputFilter-group query-span and-or">
                        <select name="recognize[]">
                            <option value="and">AND</option>
                            <option value="or">OR</option>
                        </select>
                   </span>
                  <span class="inputFilter-group">
                        <input type="text" class="panel-input" placeholder="인텐트 or 엔터티" data-role="input-filterData">
                   </span>
                   <span class="inputFilter-group query-span del-inputFilter" data-role="delete-inputFilter" data-order="1">
                       <i class="fa fa-minus-circle" aria-hidden="true"></i>
                   </span>
                   <span class="inputFilter-group query-span add-inputFilter" data-role="add-inputFilter" data-order="1">
                       <i class="fa fa-plus-circle" aria-hidden="true"></i>
                   </span>  
                </div>
            </div>
            <div id="filterBox" data-role="selectFilterBox">
                <div id="filter-wrapper">
                    <button type="button" class="close" data-role="hide-filterBox" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 id="filter-guide" data-role="filterBox-title">
                        아래에서 검색대상을 선택해주세요
                    </h5>
                    <div class="list-group" id="filter-list" data-role="filterListBox">
                        <a href="#" class="list-group-item list-group-item-action" data-role="filter-item" data-filter="#">#인텐트</a>
                        <a href="#" class="list-group-item list-group-item-action" data-role="filter-item" data-filter="@">@엔터티</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group ele-hideFilterBox">
            <label for="recognize">아웃풋 </label>
            <div class="dd respondGroup">
                <div id="btn-addRespond">
                    <div class="dropdown">
                        <span class="dropdown-toggle" id="add-respond" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </span> 
                        <ul class="dropdown-menu" aria-labelledby="add-respond">
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>텍스트 타입
                            </li>    
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-square-o" aria-hidden="true"></i>카드 타입
                            </li>    
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-picture-o" aria-hidden="true"></i>이미지 타입
                            </li>    
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-th-large" aria-hidden="true"></i>메뉴 타입
                            </li>    
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-qrcode" aria-hidden="true"></i>QR코드 타입
                            </li>    
                            <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>지도 타입
                            </li>    
                        </ul>
                    </div>
                </div>
                <ul class="dd-list">
                    <li class="dd-item">
                        <input type="hidden" name="respond_type[]" value="text">
                        <div class="panel panel-default">
                            <div class="panel-heading dd-handle">
                                <span class="move-handle">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                </span> 
                                <span class="respondGroup-title"> 텍스스 타입 </span>
                                <span class="delete-handle" >
                                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                </span>   
                            </div>
                            <div class="panel-body">
                                <textarea class="form-control" rows="2" placeholder="여기에 메세지를 입력해주세요."></textarea>
                            </div>
                        </div>
                    </li>     
                    <li class="dd-item">
                        <input type="hidden" name="respond_type[]" value="card">
                        <div class="panel panel-default">
                            <div class="panel-heading dd-handle">
                                <span class="move-handle">
                                    <i class="fa fa-square-o" aria-hidden="true"></i>
                                </span> 
                                <span class="respondGroup-title"> 카드 타입 </span>
                                <span class="delete-handle" >
                                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                </span>   
                            </div>
                            <div class="panel-body">
                                 <div class="card" data-role="card-item">
                                    <div class="card-body">
                                        <p class="card-text">
                                            <input type="text" name="card_title[]" placeholder="제목을 입력해주세요." /> 
                                        </p>
                                        <p class="card-text">
                                            <input type="text" name="card_subTitle[]" placeholder="부제목을 입력해주세요." /> 
                                        </p>
                                        <p class="card-text">
                                            <input type="text" name="card_link[]" placeholder="링크를 연결해주세요." /> 
                                        </p>
                                    </div>
                                </div>
                                <div class="card" data-role="add-card">
                                    <div class="card-img-more"></div>
                                </div>
                            </div>
                        </div>
                    </li> 
                    <!-- <li class="dd-item">
                        <input type="hidden" name="respond_type[]" value="image">
                        <div class="input-group">
                           <span class="dd-handle input-group-addon">
                               <i class="fa fa-arrows fa-fw"></i> 
                           </span> 
                           <input type="text" class="form-control no-rb" placeholder="응답내용을 입력해주세요...">
                           <span class="input-group-addon fff-bg" id="basic-addon1">
                               <i class="fa fa-minus-circle" aria-hidden="true"></i>
                           </span> 
                        </div>
                    </li> -->
                </ul>                
            </div>
        </div>
        <div class="form-group ele-hideFilterBox">
            <label for="recognize" data-role="test-click">액션 </label>
            <input type="text" class="form-control" id="recognize" placeholder="인텐트 or 엔터티를 입력해주세요">
        </div>

    </div>
</form>
<?php getImport('nestable','jquery.nestable',false,'js') ?>
<script>
// nestable 초기화 
var init_nestable =function(){
    $('.respondGroup').nestable();
}
$(document).ready(function(){
    
    // 초기함수 실행 
    init_nestable();
});
</script>

