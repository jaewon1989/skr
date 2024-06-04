<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<?php if($front=='search_product'):?>
      <input type="hidden" name="iframe" value="Y" />
      <?php endif?>
      <div class="rb-heading well well-sm"> 
		<div class="form-group">
			 <label class="col-sm-1 control-label">필터</label>
			 <div class="col-sm-10">
			 	<div class="row">
					<div class="col-sm-4">
						 <select class="form-control" name="cat" onchange="this.form.submit();">
							 <option value="">&nbsp;+ 카테고리(전체)</option>
							 <option value="">------------------</option>
							 <?php getCategoryShowSelect($table[$module.'category'],0,0,0,0,0)?>
						 </select>
					 </div>
					 <div class="col-sm-3">
						<select class="form-control" name="maker" onchange="this.form.submit();">
							<option value="">&nbsp;+ 제조사</option>
							<option value="">--------</option>
							<?php $_makerset=explode(',',implode('',file($g['path_module'].$module.'/var/set.maker.txt')))?>
							<?php foreach($_makerset as $_maker):if(!trim($_maker))continue?>
							<option value="<?php echo $_maker?>"<?php if($maker==$_maker):?> selected="selected"<?php endif?>>ㆍ<?php echo $_maker?></option>
							<?php endforeach?>
						</select>
					 </div>	
					 <div class="col-sm-3">	 
						 <select class="form-control" name="brand" onchange="this.form.submit();">
							 <option value="">&nbsp;+ 브랜드</option>
							 <option value="">--------</option>
							 <?php $_brandset=explode(',',implode('',file($g['path_module'].$module.'/var/set.brand.txt')))?>
							 <?php foreach($_brandset as $_brand):if(!trim($_brand))continue?>
							 <option value="<?php echo $_brand?>"<?php if($brand==$_brand):?> selected="selected"<?php endif?>>ㆍ<?php echo $_brand?></option>
							 <?php endforeach?>
					  	 </select>
					 </div>				
				</div>
			</div> <!-- .col-sm-10 -->
		</div>	<!-- .form-group -->		
	      <!-- 고급검색 시작 -->
		<div id="search-more" class="collapse<?php if($_SESSION['sh_productlist']):?> in<?php endif?>">
			<div class="form-group">
				 <label class="col-sm-1 control-label">필터2</label>
				 <div class="col-sm-10">
				 	<div class="row">
						<div class="col-sm-3">
							<select class="form-control" name="display" onchange="this.form.submit();">
								<option value=""<?php if($display==''):?> selected="selected"<?php endif?>>판매상태</option>
								<option value="">--------</option>
								<option value="0"<?php if($display=='0'):?> selected="selected"<?php endif?>>정상판매</option>
								<option value="1"<?php if($sort=='1'):?> selected="selected"<?php endif?>>임시품절</option>
								<option value="2"<?php if($sort=='2'):?> selected="selected"<?php endif?>>노출중단</option>
							</select>
						 </div>
						  <div class="col-sm-2">
						    	 <label class="checkbox" style="margin-top:0">
							        <input  type="checkbox"  name="is_free" id="is_free" value="Y"<?php if($is_free=='Y'):?> checked="checked"<?php endif?> onclick="this.form.submit();"  class="form-control"> <i></i>무료배송
							 </label>
						 </div>   
					       <div class="col-sm-2">
					 	       <label class="checkbox" style="margin-top:0">
					                 <input  type="checkbox" name="is_cash" id="is_cash" value="Y"<?php if($is_cash=='Y'):?> checked="checked"<?php endif?> onclick="this.form.submit();"  class="form-control"><i></i>현금결제		
					            </label>
					      </div> 	
					     <div class="col-sm-2">
					 	       <label class="checkbox" style="margin-top:0">
					                 <input  type="checkbox" name="stock" id="stock" value="Y"<?php if($stock=='Y'):?> checked="checked"<?php endif?> onclick="this.form.submit();"  class="form-control"><i></i>재고관리		
					            </label>
					      </div>
					</div>
				</div> <!-- .col-sm-10 -->
			</div>	<!-- .form-group -->
			<div class="form-group hidden-xs">
				 <label class="col-sm-1 control-label">정렬</label>
				 <div class="col-sm-10">
					<div class="btn-toolbar">
						<div class="btn-group btn-group-sm" data-toggle="buttons">
							<label class="btn btn-default<?php if($sort=='gid'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="gid" name="sort"<?php if($sort=='gid'):?> checked<?php endif?>> 등록일
							</label>
							 <label class="btn btn-default<?php if($sort=='price'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="price" name="sort"<?php if($sort=='price'):?> checked<?php endif?>> 판매가격
							</label>
							<label class="btn btn-default<?php if($sort=='point'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="point" name="sort"<?php if($sort=='point'):?> checked<?php endif?>> 적립금
							</label>
							<label class="btn btn-default<?php if($sort=='hit'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="hit" name="sort"<?php if($sort=='hit'):?> checked<?php endif?>> 조회
							</label>
							<label class="btn btn-default<?php if($sort=='wish'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="wish" name="sort"<?php if($sort=='wish'):?> checked<?php endif?>> 위시
							</label>
							<label class="btn btn-default<?php if($sort=='qna'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="qna" name="sort"<?php if($sort=='qna'):?> checked<?php endif?>> 문의
							</label>
							<label class="btn btn-default<?php if($sort=='comment'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="comment" name="sort"<?php if($sort=='comment'):?> checked<?php endif?>> 평가
							</label>
							<label class="btn btn-default<?php if($sort=='vote'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="vote" name="sort"<?php if($sort=='vote'):?> checked<?php endif?>> 평가점수
							</label>
							<label class="btn btn-default<?php if($sort=='buy'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							       <input type="radio" value="buy" name="sort"<?php if($sort=='buy'):?> checked<?php endif?>> 판매
							</label>
						</div>	
						<div class="btn-group btn-group-sm" data-toggle="buttons">
							<label class="btn btn-default<?php if($orderby=='desc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="desc" name="orderby"<?php if($orderby=='desc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-desc"></i>역순
							</label>
							<label class="btn btn-default<?php if($orderby=='asc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
								<input type="radio" value="asc" name="orderby"<?php if($orderby=='asc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-asc"></i>정순
							</label>							
						</div>
					</div>
				</div>	<!-- .col-sm-10 -->
			</div> <!-- .form-group -->
			<div class="form-group">
				<label class="col-sm-1 control-label">검색</label>
				<div class="col-sm-10">
					<div class="input-group input-group-sm">
						<span class="input-group-btn hidden-xs" style="width:165px">
							<select name="where" class="form-control btn btn-default">
								<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>상품명</option>
								<option value="tags"<?php if($where=='tags'):?> selected="selected"<?php endif?>>태그</option>
							</select>
						</span>
						<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">검색</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-1 control-label">출력</label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-2">
							<select name="recnum" onchange="this.form.submit();" class="form-control input-sm">
								<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20</option>
								<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35</option>
								<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50</option>
								<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75</option>
								<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90</option>
							</select>
						</div>
						<div class="col-sm-2">
						</div>
					</div>
				</div>
			</div>
		</div><!-- 고급검색 끝 -->	
		<div class="form-group">
			<div class="col-sm-offset-1 col-sm-10">
				<button type="button" class="btn btn-link rb-advance<?php if(!$_SESSION['sh_productlist']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#search-more" onclick="sessionSetting('sh_productlist','1','','1');">고급검색<small></small></button>
					<a href="<?php echo $g['adm_href']?>" class="btn btn-link">초기화</a>
			</div>
		</div>
	</div>	<!-- .well -->	
</form>