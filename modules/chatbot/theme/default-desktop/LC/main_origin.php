<div class="container-fluid LC-container">
    <!-- row -->
    <div class="row" data-role="LC-square">
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box LC-bot">
                <h3 class="box-title LCbot-title">BOT {$order} : <span data-role="LCbot-title-{$order}"></span></h3>
                <div class="message-center LCbot-content">
                    <?php for($i=1;$i<8;$i++):?>
                    <div class="LC-block">
                        <div class="utt-block">
                            <div class="offense-utt">
                                <div class="user-input">
                                 안녕하세요 안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요
                                </div>
                                <div class="intent-block">
                                    <div class="show-intent">
                                        #인텐트 
                                    </div>
                                </div>                            
                                <ul class="entity-list">
                                    <li>@과자:새우깡</li>
                                    <li>@가족:누나</li>
                                </ul>
                            </div>
                        </div>
                        <div class="utt-block">
                            <div class="depense-utt">
                                <div class="response-utt">
                                    <span class="res-text">무슨 말인지 모르겠어요. </span>
                                    <span class="succ-fail">성공</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor?>
                </div>
                <div class="LCbot-bottom">
                    <div class="col-md-4 col-sm-4 text-center">
                        <h5 class="text-info">전체 : <span data-role="LCbot-total-{$order}">5</span></h5> 
                    </div>
                    <div class="col-md-4 col-sm-4 text-center">
                        <h5 class="text-success">성공 : <span data-role="LCbot-success-{$order}">3</span></h5> 
                    </div>
                    <div class="col-md-4 col-sm-4 text-center">
                        <h5 class="text-danger">실패 : <span data-role="LCbot-fail-{$order}">2</span></h5> 
                    </div>
                </div>
            </div>
        </div> 
        <!-- /.col -->
        
    </div>
    <!-- /.row -->

</div>




