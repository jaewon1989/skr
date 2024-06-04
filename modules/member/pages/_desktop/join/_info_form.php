<div class="cb-inputnaked-label">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>이름</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <input name="name" type="text" placeholder="이름을 입력해주세요">
        </div>
        <div class="cb-cell cb-cell-right">
            <ul class="cb-inputnaked-gender">
                <li class="gender-man" data-sex="1">남</li>
                <li data-sex="2">여</li>
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
            <input name="tel2" type="text" placeholder="연락처를 입력해주세요." value="010-0000-0000">
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
            <input name="addr1" id="addr1" type="text" placeholder="우편번호를 선택해주세요." readonly >
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
            <input name="addr2" id="addr2" type="text" placeholder="상세주소 입력">
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
            <input name="email" type="text" placeholder="botalks@botalks.com" onblur="sameCheck(this,'hLayerid');">
            <span id="hLayerid" class="join-message"></span>
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>
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
<div class="cb-inputnaked-label cb-wide">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left">
            <span>연령대</span>
        </div>
        <div class="cb-cell cb-cell-center">
            <ul class="cb-inputnaked-age">
                <li data-age="10">10대</li>
                <li data-age="20">20대</li>
                <li data-age="30">30대</li>
                <li data-age="40">40대</li>
                <li data-age="50">50대</li>
                <li data-age="60">60대 이상</li>
            </ul>
        </div>
        <div class="cb-cell cb-cell-right">
        </div>
    </div>
</div>