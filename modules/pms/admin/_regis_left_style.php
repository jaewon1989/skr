<style>
.bootstrap-tagsinput {width:100%;}
</style>
<div class="rb-system-sidebar rb-system-site rb-default" role="application" data-role="catalogRegiMenu">
	<div class="rb-content-padded">
		<ul class="nav nav-tabs" role="tablist">
			<li<?php if($_COOKIE['catalogRegiMenu']=='product'||!$_COOKIE['catalogRegiMenu']):?> class="active"<?php endif?>><a href="#product-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('catalogRegiMenu','product');">상품정보</a></li>
			<li<?php if($_COOKIE['catalogRegiMenu']=='media'):?> class="active"<?php endif?>><a href="#media-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('catalogRegiMenu','media');">미디어</a></li>
		</ul>
		<div class="tab-content" style="padding-top:15px;">
			<div class="tab-pane<?php if($_COOKIE['catalogRegiMenu']=='product'||!$_COOKIE['catalogRegiMenu']):?> active<?php endif?>" id="product-settings">
				<div class="panel-group rb-scrollbar" id="default-settings-panels">
					<div class="panel panel-default" id="default-settings-01">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#default-settings-01" data-role="panel-title" href="#default-settings-01-body">
									<i></i>기본정보 
								</a>
							</h4>
						</div>
						<div id="default-settings-01-body" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group">
									<label>상품명</label>
									<input type="text" class="form-control" name="name" value="<?php echo $R['name']?>">
								</div>
								<div class="form-group">
									<label>카테고리</label>
									<select name="category" class="form-control">
										<option value="">&nbsp;+ 선택하세요.</option>
										<option value="">------------------</option>
										<?php $cat=$R['category']?>
										<?php getCategoryShowSelect($table[$module.'category'],0,0,0,0,0)?>
									</select>
								</div>	
								<div class="form-group">
									<label>원산지</label>
									<input type="text" class="form-control" name="country" value="<?php echo $R['country']?>">
								</div>
								<div class="form-group">
									<label>제조사</label>
									<select name="maker" class="form-control">
										<option value="">&nbsp;+ 선택하세요.</option>
										<option value="">------------</option>
										<?php $_makerset=explode(',',implode('',file($g['path_module'].$module.'/var/set.maker.txt')))?>
										<?php foreach($_makerset as $_maker):if(!trim($_maker))continue?>
										<option value="<?php echo $_maker?>"<?php if($R['maker']==$_maker):?> selected="selected"<?php endif?>>ㆍ<?php echo $_maker?></option>
										<?php endforeach?>
									</select>
								</div>	
								<div class="form-group">
									<label>모델명</label>
									<input type="text" class="form-control" name="model" value="<?php echo $R['model']?>">
								</div>
								<div class="form-group">
									<label>브랜드</label>
									<select name="brand" class="form-control">
										<option value="">&nbsp;+ 선택하세요.</option>
											<option value="">-------------</option>
											<?php $_brandset=explode(',',implode('',file($g['path_module'].$module.'/var/set.brand.txt')))?>
											<?php foreach($_brandset as $_brand):if(!trim($_brand))continue?>
											<option value="<?php echo $_brand?>"<?php if($R['brand']==$_brand):?> selected="selected"<?php endif?>>ㆍ<?php echo $_brand?></option>
											<?php endforeach?>
									</select>
								</div>	

							</div>
						</div> <!-- #default-settings-01-body-->						
					</div><!-- #default-settings-01 : 개별 탭 content 분기 -->
			
					<div class="panel panel-default" id="default-settings-03">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#default-settings-03" data-role="panel-title" href="#default-settings-03-body">
									<i></i>리뷰/부가정보/태그   
								</a>
							</h4>
						</div>
						<div id="default-settings-03-body" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group">
									<label>리뷰</label>
									<textarea class="form-control" name="review" rows="5" id="meta-description-content"  maxlength="155"><?php echo $R['review']?></textarea>
								</div>	
								<div class="form-group">
									<label>부가정보</label>
									<textarea class="form-control" name="addinfo" rows="5" ><?php echo $R['addinfo']?></textarea>
									<span class="help-block">
						                 	 <small>
				정보제목=정보값 을 콤마(,)로 구분해서 등록해 주세요.<br />
				보기)칼라=검정,원산지=중국<br /></small>   
						                 </span>
								</div>	
								<div class="form-group">
									<label>태그</label>
									<div>
									<input type="text" class="form-control" id="meta-tag-content" data-role="tagsinput"  name="tags" value="<?php echo $R['tags']?>">
								      </div>
								</div>

							</div>
						</div> <!-- #default-settings-03-body-->						
					</div><!-- #default-settings-03 : 개별 탭 content 분기 -->

					<div class="panel panel-default" id="product-settings-02" >
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#product-settings-02" data-role="panel-title" href="#product-settings-02-body">
									<i></i>가격정보 
								</a>
							</h4>
						</div>
						<div id="product-settings-02-body" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group">
									<label>판매가격</label>
									<div class="input-group">
									      <input type="text" class="form-control" name="price" value="<?php echo $R['price']?>" onkeydown="numFormat(this);" onkeypress="numFormat(this);" >
									      <span class="input-group-addon">원</span>
									 </div>     
								</div>
								<div class="form-group">
									<label>시중가</label>
									<div class="input-group">
									      <input type="text" class="form-control" name="price1" value="<?php echo $R['price1']?>" onkeydown="numFormat(this);" onkeypress="numFormat(this);" >
									      <span class="input-group-addon">원</span>
									 </div>     
								</div>								
								<div class="form-group">
									<label>적립포인트</label>
									<div class="input-group">
									      <input type="text" class="form-control" name="point" value="<?php echo $R['point']?>">
									      <span class="input-group-addon">Point</span>
								      </div> 
								</div>	
							</div>
						</div> <!-- #product-settings-02-body-->						
					</div><!-- #product-settings-02 : 개별 탭 content 분기 -->

					<div class="panel panel-default" id="product-settings-03" >
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#product-settings-03" data-role="panel-title" href="#product-settings-03-body">
									<i></i>상태/노출/라벨 정보 
								</a>
							</h4>
						</div>
						<div id="product-settings-03-body" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group">
									<label>판매상태</label>
									<p>
	  	  	   	                                 <label>
				        		                      <input type="radio" name="dispaly" value="0" <?php if(!$R['display']):?> checked="checked"<?php endif?> />  정상판매
				        		                </label>
                                                   </p>
                                                   <p>
	  	  	   	                                 <label>
				        		                      <input type="radio" name="dispaly" value="1" <?php if($R['display']==1):?> checked="checked"<?php endif?> />  임시품절
				        		                </label>
                                                   </p>
                                                   <p>
	  	  	   	                                 <label>
				        		                      <input type="radio" name="dispaly" value="2" <?php if($R['display']==2):?> checked="checked"<?php endif?> />  노출중단
				        		                </label>
                                                   </p>
              						</div>
								<div class="form-group">
									<label>재고량</label>
									<div class="input-group">
									      <input type="text" name="stock_num" value="<?php echo $R['stock_num']?$R['stock_num']:0?>" size="4" class="form-control" maxlength="5" onkeydown="numFormat(this);" onkeypress="numFormat(this);" />   
									      <span class="input-group-addon">Unit</span>
									</div>      
								</div>								
								<div class="form-group">
									<label>재고관리 여부</label>
									<div class="checkbox">
								           <label>
									          <input  type="checkbox" name="stock" value="1" <?php if($R['stock']):?> checked="checked"<?php endif?>  class="form-control"><i></i>재고관리 함    	
								            </label>
						                 </div>
						                 <span class="help-block">
						                 	 <small>체크하면 재고관리가 진행되며 재고량에 따라 품절여부를 처리합니다.</small>   
						                 </span>
								</div>	
								<div class="form-group">
									<label>상품 라벨 설정</label>
									
										<?php $idir = $g['path_module'].$module.'/var/icons/'?>
										<?php $dirs = opendir($idir)?>
										<?php while(false !== ($icon = readdir($dirs))):?>
										<?php if(!is_file($idir.$icon)||$icon=='good_icon_soldout.gif')continue?>
									      <div class="checkbox">
											<label>
											<input type="checkbox" name="iconmembers[]" value="<?php echo $icon?>"<?php if(strstr($R['icons'],$icon)):?> checked="checked"<?php endif?> class="form-control" /><i></i><img src="<?php echo $g['s'].'/modules/'.$module?>/var/icons/<?php echo $icon?>" alt="<?php echo $icon?>" style="width:25px;" />
											</label>
										 </div>	
										<?php endwhile?>
										<?php closedir($dirs)?>		
								</div>	
							</div>
						</div> <!-- #product-settings-03-body-->						
					</div><!-- #product-settings-03 : 개별 탭 content 분기 -->


				</div><!-- #default-settings-panels-->
			</div> <!-- #default-settings : 멀티탭 분기 -->

			<div class="tab-pane<?php if($_COOKIE['catalogRegiMenu']=='media'):?> active<?php endif?>" id="media-settings">
				<div class="panel-group rb-scrollbar" id="media-settings-panels">
					<div class="panel panel-default" id="media-settings-01">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#media-settings-panels" href="#media-settings-01-body">
									<i></i>사진추가 
								</a>
							</h4>
						</div>
						<div id="media-settings-01-body" class="panel-collapse collapse">
							<div class="panel-body">
				                            <?php getWidget('default/attach',array('parent_module'=>$module,'theme'=>'bs-markdownPlus','parent_data'=>$R,'attach_object_type'=>'photo'));?>
							</div>
						</div> <!-- #media-settings-01-body-->
					</div><!-- #media-settings-01 : 개별 탭 content 분기 -->

					<div class="panel panel-default" id="media-settings-02">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#media-settings-panels" href="#media-settings-02-body">
									<i></i>링크 
								</a>
							</h4>
						</div>
						<div id="media-settings-02-body" class="panel-collapse collapse">
							<div class="panel-body">
				                           
							</div>
						</div> <!-- #media-settings-02-body-->
					</div><!-- #media-settings-02 : 개별 탭 content 분기 -->

					<div class="panel panel-default" id="media-settings-03">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#media-settings-panels" href="#media-settings-03-body">
									<i></i>동영상
								</a>
							</h4>
						</div>
						<div id="media-settings-03-body" class="panel-collapse collapse">
							<div class="panel-body">
				                           
							</div>
						</div> <!-- #media-settings-03-body-->
					</div><!-- #media-settings-03 : 개별 탭 content 분기 -->
					
					<div class="panel panel-default" id="media-settings-04">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#media-settings-panels" href="#media-settings-04-body">
									<i></i>위치 
								</a>
							</h4>
						</div>
						<div id="media-settings-04-body" class="panel-collapse collapse">
							<div class="panel-body">
				                           
							</div>
						</div> <!-- #media-settings-04-body-->
					</div><!-- #media-settings-04 : 개별 탭 content 분기 -->

				</div><!-- #media-settings-panels-->
			</div> <!-- #media-settings : 멀티탭 분기 -->
		</div> <!-- .tab-content-->					
	</div> <!-- .rb-content-padded-->
</div> <!-- .rb-system-sidebar-->
