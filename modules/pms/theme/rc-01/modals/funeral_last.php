<link href="/modules/catalog/theme/rc-01/style.css" rel="stylesheet">
<div class="page center" id="page-catalog-view">
        <div class="modal active rb-catalog-view">
            <header class="bar bar-nav rb-nav">
                <button class="btn btn-link btn-nav pull-left" data-history="back"><span class="icon icon-left-nav"></span></button>
                <button class="btn btn-link btn-nav pull-right"><span class="icon icon-more-vertical"></span></button>
                <h1 class="title" data-role="title">
                    <!-- title 영역 -->
                </h1>
            </header>

            <div class="bar bar-standard bar-footer rb-buttons">
                <div class="row">
                    <div class="col-xs-6">
                           <a href="#" data-toggle="page" data-start="#page-catalog-view" data-target="#page-catalog-contact" data-title="'" data-url="" data-id="" data-role="event-handler" class="btn btn-primary btn-block">가입신청</a>
                    </div>
                    <div class="col-xs-6">
                        <button class="btn btn-positive btn-block">전화상담</button>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="row">

                    <div class="col-sm-6">
                        <section class="rb-cover">
                            <div class="card card-full card-inverse">
                                <div class="card-img-backdrop rb-cover-bg"></div>
                                <img class="card-img img-fluid rb-cover-image" data-role="cover-img" src="" alt="">
                                <div class="card-img-overlay text-xs-center" id="cover-name">
                                    <p class="card-text" data-role="review"><!-- 리뷰 영역 --></p>
                                    <h3 class="card-title" data-role="title"><!-- title 영역 --></h3>
                                </div>
                            </div>
                        </section>

                        <section class="rb-price content-padded">
                            <div class="input-group">
                              <div class="input-row">
                                <label>총 납입금액</label>
                                <span class="badge badge-primary badge-inverted rb-total" data-role="price"></span>
                              </div>
                              <div class="input-row" data-role="addinfo">
                                   <!-- 부가정보 : 월납입... -->
                              </div>
                            </div>
                        </section>

                        <section class="rb-buttons content-padded">

                        </section>

                        <section class="rb-related content-padded">
                            <h3>사용가능 상품</h3>
                            <div class="card-group" data-role="label">
                               
                            </div>
                        </section>
                    </div>

                    <div class="col-sm-6">

                        <section class="rb-detail">
                            <div class="segmented-control content-padded">
                              <a class="control-item active" href="#tab-detail-01">
                                구성품목
                              </a>
                              <a class="control-item" href="#tab-detail-02">
                                유의사항
                              </a>
                              <a class="control-item" href="#tab-detail-03">
                                사진보기
                              </a>
                            </div>
                            <div class="card">
                                <span id="tab-detail-01" class="control-content active content-padded rb-article" data-role="content01"></span>
                                <span id="tab-detail-02" class="control-content content-padded rb-article" data-role="content02"> </span>

                                <span id="tab-detail-03" class="control-content content-padded rb-article" data-role="content03"></span>

                            </div>
                        </section>

                    </div>

                </div>
                <div style="height: 50px"></div>
            </div>
        </div>
  </div>      
 <div class="page right" id="page-catalog-contact" >
        <div class="rb-catalog-form">
            <header class="bar bar-nav rb-nav">
                <a class="btn btn-link btn-nav pull-left" href="#" data-history="back">취소</a>
                <h1 class="title" data-role="title"><!-- 상품명 --> </h1>
            </header>

            <div class="bar bar-standard bar-footer rb-buttons">
                <a href="#" class="btn btn-primary btn-block" data-role="event-handler" data-toggle="submit" data-module="catalog" data-actionfile="get_Sendquestion" data-target="#question-form" data-id="" >전송하기</a>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-sm-6">

                        <section class="rb-form content-padded">

                            <h3>가입문의</h3>
                            <p>가입신청서를 작성해 주시면 담당자가 연락드리겠습니다.</p>

                            <form id="question-form">
                                <input type="text" placeholder="이름" name="name">
                                <input type="text" placeholder="휴대폰" name="hp">
                                <input type="email" placeholder="이메일" name="email">
                                <textarea rows="5" name="content"></textarea>
                            </form>

                        </section>

                    </div>
                    <div class="col-sm-6">
                    </div>
                </div>
                <div style="height: 50px"></div>
            </div>
        </div>
    </div>
<script>
 $(document).on('tap click','[data-toggle="submit"]',function(e){
      e.preventDefault();
      var form=$(this).data('target');
      var module=$(this).data('module');
      var actionfile=$(this).data('actionfile');
      var uid=$(this).attr('data-id');
      var formData=$(form).serializeArray();
      var chkEmail=chkEmailAddr(formData[2].value);
      if(formData[2].value==''){
            alert('이메일 주소를 입력해주세요 ');
            $(form).find('input[name="email"]').focus();
            return false;
      }else if(!chkEmail){
            alert('올바른 이메일 주소가 아닙니다.');
            $(form).find('input[name="email"]').focus();
            return false;
      } 
      $.post('/?m='+module+'&a='+actionfile,{
            from_name : formData[0].value,
            from_hp : formData[1].value,
            from_email : formData[2].value,
            from_content : formData[3].value,
            module : module,
            uid : uid   
      },function(response){
            var result=$.parseJSON(response);
            alert(result.msg);
      });        
  });
</script>

