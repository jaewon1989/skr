<style>
.bootstrap-tagsinput {width:100%;}
</style>
<div class="rb-system-sidebar rb-system-site rb-default" role="application" data-role="catalogRegiMenu">
	<div class="rb-content-padded">
		<ul class="nav nav-tabs" role="tablist">
			<li<?php if($_COOKIE['catalogRegiMenu']=='media'|| !$_COOKIE['catalogRegiMenu']):?> class="active"<?php endif?>><a href="#media-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('catalogRegiMenu','media');">미디어</a></li>
			<li<?php if($_COOKIE['catalogRegiMenu']=='product'):?> class="active"<?php endif?>><a href="#product-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('catalogRegiMenu','product');">.</a></li>
			
		</ul>
		<div class="tab-content" style="padding-top:15px;">
			<div class="tab-pane<?php if($_COOKIE['catalogRegiMenu']=='media' || !$_COOKIE['catalogRegiMenu']):?> active<?php endif?>" id="media-settings">
				<div class="panel-group rb-scrollbar" id="media-settings-panels">
					<div class="panel panel-default" id="media-settings-01">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#media-settings-panels" href="#media-settings-01-body">
									<i></i>사진추가 
								</a>
							</h4>
						</div>
						<div id="media-settings-01-body" class="panel-collapse collapse in">
							<div class="panel-body">
				                            <?php getWidget('default/attach',array('parent_module'=>$module,'theme'=>'bs-markdownPlus','parent_data'=>$R,'attach_object_type'=>'photo'));?>
							</div>
						</div> <!-- #media-settings-01-body-->
					</div><!-- #media-settings-01 : 개별 탭 content 분기 -->		

				</div><!-- #media-settings-panels-->
			</div> <!-- #media-settings : 멀티탭 분기 -->
			<div class="tab-pane<?php if($_COOKIE['catalogRegiMenu']=='product'):?> active<?php endif?>" id="product-settings">
			
			</div> <!-- #default-settings : 멀티탭 분기 -->
		</div> <!-- .tab-content-->					
	</div> <!-- .rb-content-padded-->
</div> <!-- .rb-system-sidebar-->
