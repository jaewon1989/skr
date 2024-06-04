<!-- 공통 모달  -->
<div id="modal-default" class="modal">
	<div class="content" data-role="content">
	</div>
</div>

<!-- 댓글 모달 -->
<div id="modal-comment" class="modal">
	
</div>

<!-- 쪽지 모달 : 메뉴파업이 있는 상태에서 오픈되기 때문에 z-index 를 높여야 한다. -->
<div id="modal-paper" class="modal">

    <!-- write Page -->
	<div id="paper-write" class="page center" data-role="write-content">
 	</div>

	<!-- view Page -->
	<div id="paper-view" class="page right" data-role="view-content">
	</div>
	
</div>

<!-- 공통 팝업 -->
<div id="popup-default" class="popup slide-up">
	<div class="popup-content">
	    <div class="content" data-role="content">
	    </div>
	</div>
</div>

<!-- 비디오 팝업 -->
<div id="popup-video" class="popup zoom">
	<div class="popup-content">
	    <div class="content" data-role="content">
	    </div>
	</div>
</div>

<!-- 확인 팝업 -->
<div id="popup-confirm" class="popup">
	<div class="popup-content">	   
		<div class="content">
		    <div id="dm-block-action" data-role="actName" data-value="">
	            <h3 class="dm-h3 dm-ft-default" data-role="question">
	                <!-- 정말로 차단/해제하시겠습니까 ? -->
	            </h3>
	            <span class="dm-button dm-button-yes" data-confirmAct="btn-confirm" data-confirm="yes" ></span>
	            <span class="dm-button dm-button-no" data-confirmAct="btn-confirm" data-confirm="no"></span>
	        </div>
        </div>
	</div>
</div>

<!-- 공통 시트 -->
<div id="sheet-default" class="sheet p-a-0" >
	<div class="card noborder m-a-0">
        <h4 class="card-header">
         <span data-role="title"></span>
	      <a class="icon icon-close pull-right" data-history="back" role="button"></a>	
        </h4>
        <div class="card-block">
          <div class="card-deck-wrapper" data-role="content">
                       
          </div>
        </div>
    </div>

</div>	

<div id="modal-filters" class="modal">
	<div class="content">
	  <!-- 필터 페이지 마크업 Start -->
	    <div class="dm-actual-body">
	        <section class="dm-content">
	            <div class="dm-filter-area">
	                <div class="dm-filter-box">
	                    <div class="dm-takeup-space">
	                        <div>
	                            <h3 class="dm-h3 dm-ft-default">성별</h3>
	                        </div>
	                        <div class="dm-filter-buttons">
	                            <label for="dm-sex-all"><div class="dm-filter-button balanced selected" data-toggle="select_sex">모두<input type="radio" id="dm-sex-all" name="mbr_sex" value="" checked ></div></label>
	                            <label for="dm-sex-male" ><div class="dm-filter-button balanced" data-toggle="select_sex">남성<input type="radio" id="dm-sex-male" name="mbr_sex" value="1"></div></label>
	                            <label for="dm-sex-female"><div class="dm-filter-button balanced" data-toggle="select_sex">여성<input type="radio" id="dm-sex-female" name="mbr_sex" value="2"></div></label>
	                        </div>
	                    </div>
	                </div>
	                <div class="dm-filter-box">
	                    <div class="dm-takeup-space">
	                        <div>
	                            <h3 class="dm-h3 dm-ft-default">국가</h3>
	                        </div>
	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-asia" data-toggle="select-continent" data-continent="asia">
	                                    아시아
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                            	<div data-continentbox="asia">
	                            		<?php echo getNationSelector('html_mobile',array('KOR','JPN','TWN','CHN','HKG','MAC','MYS','PHL','VNM'),array('1','','','','','','','',''));?>
	                            	</div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-europe" data-toggle="select-continent" data-continent="euro">
	                                    유럽
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="euro">
	                                    <?php echo getNationSelector('html_mobile',array('GBR','NLD','SWE','CHE','DEU','ITA','FRA','TUR','RUS'),array('','','','','','','','',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-northamerica" data-toggle="select-continent" data-continent="na">
	                                    북미
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="na">
                                        <?php echo getNationSelector('html_mobile',array('USA','CAN'),array('',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-centralamerica" data-toggle="select-continent" data-continent="la">
	                                    중남미
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="la">
                                        <?php echo getNationSelector('html_mobile',array('CHL','MEX','BRA'),array('','',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-oceania" data-toggle="select-continent" data-continent="oce">
	                                    오세아니아
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="oce">
                                        <?php echo getNationSelector('html_mobile',array('NZL','AUS'),array('',''));?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="dm-filter-save">
	                    <div>
	                        <label for="dm-filter-save"><span class="dm-icon dm-icon-checkbullet"></span><span class="dm-label">이 설정을 저장하기</span><input type="checkbox" class="hidden" id="dm-filter-save" value="save" data-toggle="dm-filter-save"></label>
	                    </div>
	                    <div>
	                        <span class="dm-filter-button nation-reset" data-toggle="reset-nation">다시 선택</span>
	                        <span class="dm-filter-button nation-ok" data-role="feedSearch" data-mod="">확인</span>
	                    </div>
	                </div>
	            </div>
	        </section>
	    </div>
	    <!-- 필터 페이지 마크업 End -->
	</div>   
</div>

