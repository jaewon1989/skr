<?php

require_once 'interface/util/InterfaceUtil.php';
require_once 'common/controller/CommonController.php';
require_once 'member/controller/model/MemberParamsModel.php';
require_once 'member/service/MemberService.php';

class MemberController extends CommonController
{
    private $_params;
    private $_memberService;
    private $_interfaceModeRequiredParams = [
        'createMember' => [
            'group',
            'name',
            'id',
            'pw1',
            'pw2',
            'email'
        ],
        'updateMember' => [
            'mbruid',
            'group',
            'name',
            'id',
            'pw1',
            'pw2',
            'email',
            'is_lock'
        ],
        'deleteMember' => [
            'mbruid'
        ],
        'resetPw' => [
            'mbruid',
            'id'
        ]
    ];

    private $_errorMsgs = [
        'mode' => '잘못된 접근입니다.',
        'mbruid' => '잘못된 접근입니다.',
        'group' => '소속 그룹을 선택해 주세요.',
        'name' => '사용자명을 입력해 주세요.',
        'id' => '아이디를 입력해 주세요.',
        'pw1' => '비밀번호를 입력해 주세요.',
        'pw2' => '비밀번호를 한번 더 입력해 주세요.',
        'email' => '이메일을 입력해 주세요.',
        'is_lock' => '상태를 선택해 주세요.'
    ];


    public function __construct()
    {
        $this->_memberService = new MemberService();
    }

    /**
     * @throws JsonException
     */
    public function interfaceProcess($params): void
    {
        $this->_params = MemberParamsModel::of($params);
        InterfaceUtil::chkInterfaceParams($this->_params, $this->_interfaceModeRequiredParams, $this->_errorMsgs);
        InterfaceUtil::returnJsonEncodeBuilder($this->{$this->_params->mode}());
    }

    public function createMember(): array
    {
        return $this->_memberService->createMember($this->_params);
    }

    public function updateMember(): array
    {
        return $this->_memberService->updateMember($this->_params);
    }

    public function resetPw(): array
    {
        return $this->_memberService->resetPw($this->_params);
    }

}