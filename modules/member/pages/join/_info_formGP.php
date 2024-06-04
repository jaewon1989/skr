<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>이름</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="name" type="text" placeholder="이름을 입력해주세요" value="<?php echo $my['name']?>">
        </div>
        <div class="cb-cell cb-cell-right">
            <ul class="cb-inputnaked-gender">
                <li<?php if($my['sex']==1):?> class="cb-selected"<?php endif?> data-sex="1">남</li>
                <li<?php if($my['sex']==2):?> class="cb-selected"<?php endif?> data-sex="2">여</li>
            </ul>
        </div>
    </div>
</div>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>연락처</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="tel2" type="text" value="<?php echo $my['tel2']?>" placeholder="연락처를 입력해주세요.">
        </div>
        <div class="cb-cell cb-cell-right">

        </div>
    </div>
</div>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>주소</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="addr1" id="addr1" value="<?php echo $my['addr1']?>" type="text" placeholder="우편번호를 선택해주세요." readonly >
        </div>
        <div class="cb-cell cb-cell-right">
            <input type="hidden" name="zip" id="zipcode"/>
            <span class="cb-address-findbutton" data-role="btn-zipcode">우편번호</span>
        </div>
    </div>
</div>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span></span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="addr2" id="addr2" value="<?php echo $my['addr2']?>" type="text" placeholder="상세주소 입력">
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>이메일</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="email" type="text" value="<?php echo $my['email']?>" placeholder="이메일을 입력해주세요." onblur="sameCheck(this,'hLayerid');" <?php echo $my['uid']?'disabled':''?>>
            <span id="hLayerid" class="join-message"></span>
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
<?php if(!$my['uid']):?>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>비밀번호</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="pw1" type="password" placeholder="비밀번호를 입력해주세요." value="something">
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>비번확인</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="pw2" type="password" placeholder="비밀번호를 다시 한 번 입력해주세요." value="something">
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
<?php endif?>
<div class="cb-inputnaked-label cb-wide">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>연령대</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <ul class="cb-inputnaked-age">
             <?php $age_arr=array("10","20","30","40","50","60")?>
             <?php foreach ($age_arr as $age):?>
                <li data-age="<?php echo $age?>"<?php if($age==$my['age']):?> class="cb-selected"<?php endif?>>
                    <?php echo $age?>대<?php if($age=='60'):?> 이상<?php endif?>
                </li>
             <?php endforeach?>   
            </ul>
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
// 우편번호 버튼 클릭 
$('[data-role="btn-zipcode"]').on('click',function(){
    getAddrApi();
});

function getAddrApi() {
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = ''; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수

            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;

            } else { // 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }

            // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
            if(data.userSelectedType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('zipcode').value = data.zonecode; //5자리 새우편번호 사용
            document.getElementById('addr1').value = fullAddr;

            // 커서를 상세주소 필드로 이동한다.
            document.getElementById('addr2').focus();
        }
    }).open();
}

// 성별 체크 
$('.cb-inputnaked-gender li').each(function(){
    $(this).on('click',function(){
        $('.cb-inputnaked-gender').find('li').removeClass('cb-selected');
        $(this).addClass('cb-selected');
        var sex=$(this).data('sex');
        $('input[name="sex"]').val(sex);
    })
});

// 나이 체크 cb-inputnaked-age
$('.cb-inputnaked-age li').each(function(){
    $(this).on('click',function(){
        $('.cb-inputnaked-age').find('li').removeClass('cb-selected');
        $(this).addClass('cb-selected');
        var age=$(this).data('age');
        $('input[name="age"]').val(age);
    })
});

</script>
