
<form id="nodeSetting">
    <input type="hidden" name="graph_id" /> <!-- 그래프 쪽에서 랜덤 생성 -->
    <input type="hidden" name="node_id" />
    <div class="form-group ele-hideFilterBox" id="node_name">
        <input type="text" name="node_name" class="form-control" placeholder="노드 이름">
    </div>
    <div id="nodeScroll">
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
            <label for="recognize" data-role="test-click">아웃풋 </label>
            <div class="respondGrop">
                <ul id="respondGroup-tab" class="nav nav-tabs">
                    <li class="active item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-file-text-o" aria-hidden="true"></i> 텍스트 타입
                       </a>
                    </li>
                    <li class="item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-square-o" aria-hidden="true"></i> 카드 타입
                       </a>
                    </li>
                    <li class="item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-picture-o" aria-hidden="true"></i> 이미지 타입
                       </a>
                    </li>
                    <li class="item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-th-large" aria-hidden="true"></i> 메뉴 타입
                       </a>
                    </li>
                    <li class="item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-qrcode" aria-hidden="true"></i> QR코드 타입
                       </a>
                    </li>
                    <li class="item">
                       <input type="hidden" name="respondGroup_index[]" value="0"> 
                       <a href="#1" data-toggle="tab">
                           <i class="fa fa-map-marker" aria-hidden="true"></i> 지도 타입
                       </a>
                    </li>
                </ul>
                <div class="tab-content ">
                    <div class="tab-pane active" id="1">
                        <h3> 첫번째 내용 </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group ele-hideFilterBox">
            <label for="recognize" data-role="test-click">액션 </label>
            <input type="text" class="form-control" id="recognize" placeholder="인텐트 or 엔터티를 입력해주세요">
        </div>

    </div>
</form>
<script type="text/javascript" src="<?php echo $g['url_layout'].'/_js/respondGroup_sortable.js'?>"></script>
<script>
$('#respondGroup-tab .item').sortable({
    flow: 'horizontal',
    wrapPadding: [10, 10, 0, 0],
    elMargin: [0, 0, 10, 10],
    elWidth: 50,
    elHeight: 40,
    timeout: 50
});
</script>

