<?php

require_once 'member/repository/dto/MbridDto.php';
require_once 'member/repository/dto/MbrdataDto.php';
require_once 'member/repository/dto/ManagerDto.php';

require_once 'member/repository/mapper/MbridMapper.php';
require_once 'member/repository/mapper/MbrdataMapper.php';
require_once 'member/repository/mapper/ManagerMapper.php';
require_once 'member/repository/mapper/VendorMapper.php';


class MemberService
{
    private $_dbErrorVal = 0;
    private $_defaultSiteVal = 1;
    private $_defaultLoginFailCnt = 0;
    private $_isError = true;
    private $_strTrue = 'true';
    private $_strN = 'N';
    private $_matchVal = 1;
    private $_minIdLength = 4;
    private $_maxIdLength = 20;
    private $_minPwdLength = 9;
    private $_maxPwdLength = 16;
    private $_mbridMapper;
    private $_mbrdataMapper;
    private $_managerMapper;
    private $_vendorMapper;

    private $_chkBeforeCreateMemberProcesses = [
        '_chkIdProcess',
        '_chkPwProcess',
        '_chkEmailProcess',
    ];


    public function __construct()
    {
        global $DB_CONNECT;
        $this->_mbridMapper = new MbridMapper($DB_CONNECT);
        $this->_mbrdataMapper = new MbrdataMapper($DB_CONNECT);
        $this->_managerMapper = new ManagerMapper($DB_CONNECT);
        $this->_vendorMapper = new VendorMapper($DB_CONNECT);
    }

    public function createMember(MemberParamsModel $memberParams): array
    {
        $errorMsg = $this->_chkBeforeCreateMember($memberParams);
        if (empty($errorMsg)) {
            $insertMbridUid = $this->_setMbrid($this->_makeMbridSetData($memberParams));
            if ($this->_dbErrorVal !== $insertMbridUid) {
                $this->_isError = false;
                $this->_setMbrdata($this->_makeMbrdataSetData($memberParams, $insertMbridUid));
                $this->_setManager($this->_makeManagerSetData($memberParams, $insertMbridUid));
            }
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $errorMsg : '사용자 정보 등록이 완료되었습니다.'
        ];
    }

    public function updateMember(MemberParamsModel $memberParams): array
    {
        $errorMsg = '잘못된 접근입니다.';
        $memberData = $this->_getMemberDataByMbruid($memberParams->mbruid);
        if ($memberParams->id === $memberData['id']) {
            $errorMsg = $this->_chkPwChange($memberParams, $memberData);
            if (empty($errorMsg)) {
                $errorMsg = $this->_chkEmailProcess($memberParams);
                if (empty($errorMsg)) {
                    $this->_isError = false;
                    if ($this->_strTrue === $memberParams->pw_change) {
                        $this->_updateIsPwChangeInSession();
                        $this->_updateMbrid($this->_makeMbridUpdateData($memberParams, $memberData));
                    }
                    $this->_updateMbrdata($this->_makeMbrdataUpdateData($memberParams, $memberData));
                }
            }
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $errorMsg : '사용자 정보 수정이 완료되었습니다.'
        ];
    }

    public function resetPw(MemberParamsModel $memberParams): array
    {
        $defaultPwPlainText = 'Ccaas0101@';
        $memberData = $this->_getMemberDataByMbruid($memberParams->mbruid);

        if ($memberParams->id === $memberData['id']) {
            $this->_isError = false;
            $this->_updateIsPwChangeInSession();
            $this->_mbridMapper->updateMbrid(MbridDto::of([
                'pw' => getCryptByCCaaS($defaultPwPlainText, $memberData['d_regis']),
                'uid' => $memberParams->mbruid
            ]));

            $this->_mbrdataMapper->updateMbrdata(MbrdataDto::of([
                'last_pw' => date('Ymd'),
                'memberuid' => $memberParams->mbruid
            ]));
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? '비밀번호 초기화를 실패하였습니다.' : '비밀번호 초기화가 완료되었습니다.'
        ];
    }

    private function _chkBeforeCreateMember(MemberParamsModel $memberParams): string
    {
        $errorMsg = '';
        foreach ($this->_chkBeforeCreateMemberProcesses as $process) {
            $errorMsg = $this->{$process}($memberParams);
            if ($errorMsg) {
                break;
            }
        }

        return $errorMsg;
    }

    private function _chkIdProcess(MemberParamsModel $memberParams): string
    {
        $idErrorMsg = '';
        if ($this->_chkSpaces($memberParams->id)) {
            return "아이디는 공백 없이 입력해 주세요.";
        }

        if ($this->_chkIdPolicy($memberParams->id)) {
            return "아이디는 " . $this->_minIdLength . "~" . $this->_maxIdLength . "자의 영문, 숫자로 입력해 주세요.";
        }

        if ($this->_chkDupId($memberParams->id)) {
            return "이미 등록된 아이디입니다.";
        }

        return $idErrorMsg;
    }

    private function _chkPwProcess(MemberParamsModel $memberParams, $memberData): string
    {
        if (empty($memberParams->prev_pw)) {
            return '기존 비밀번호를 입력해 주세요.';
        }

        if ($memberParams->prev_pw === $memberParams->pw1) {
            return '기존 비밀번호와 신규 비밀번호를 다르게 입력해 주세요.';
        }

        if ((getCryptByCCaaS($memberParams->prev_pw, $memberData['d_regis']) !== $memberData['pw']) &&
            (getCrypt($memberParams->prev_pw, $memberData['d_regis']) !== $memberData['pw'])
        ) {
            return '기존 비밀번호가 맞지 않습니다.';
        }

        if ($memberParams->pw1 !== $memberParams->pw2) {
            return '비밀번호가 일치하지 않습니다.';
        }

        if ($this->_chkSpaces($memberParams->pw1)) {
            return '비밀번호는 공백 없이 입력해 주세요.';
        }

        if ($this->_chkBeforePw(getCryptByCCaaS($memberParams->pw1, $memberData['d_regis']))) {
            return '직전 2개 비밀번호를 재사용할 수 없습니다.';
        }

        return $this->_chkPwPolicy($memberParams->id, $memberParams->pw1);
    }

    private function _chkEmailProcess(MemberParamsModel $memberParams): string
    {
        $emailErrorMsg = '';
        if ($this->_chkEmailPolicy($memberParams->email)) {
            return '정확한 이메일을 입력해 주세요.';
        }


        if (empty($memberParams->mbruid) ?
            $this->_chkDupEmail($memberParams->email) :
            $this->_chkDupEmailOfMbruid($memberParams->email, $memberParams->mbruid)) {
            return '이미 등록된 이메일입니다.';
        }

        return $emailErrorMsg;
    }

    private function _chkSpaces($str): bool
    {
        return $this->_matchVal === preg_match("/\s+/", $str);
    }

    private function _chkBeforePw($cryptPw): bool
    {
        return $this->_strTrue === $this->_mbrdataMapper->chkBeforePw($cryptPw)->fetch_assoc()['result'];
    }

    private function _chkIdPolicy($id): bool
    {
        return !preg_match("/^[a-z0-9]{" . $this->_minIdLength . "," . $this->_maxIdLength . "}$/i", trim($id));
    }

    private function _chkDupId($id): bool
    {
        return $this->_strTrue === $this->_mbridMapper->chkDupId($id)->fetch_assoc()['result'];
    }

    private function _chkPwPolicy($id, $pw): string
    {
        if (strlen($pw) < $this->_minPwdLength || strlen($pw) > $this->_maxPwdLength) {
            return '비밀번호는 9-16글자로 구성해야 합니다.';
        }

        if (!preg_match('/[A-Z]/', $pw)) {
            return '영어 대문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) A-Z';
        }

        if (!preg_match('/[a-z]/', $pw)) {
            return '영어 소문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) a-z';
        }

        if (!preg_match('/[0-9]/', $pw)) {
            return '숫자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) 0-9';
        }

        if (!preg_match('/[!@#$%^&.*()]/', $pw)) {
            return '특수문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) ! @ # $ % ^ & . * ( )';
        }

        if (preg_match('/(.)\1{2,}/', $pw)) {
            return '동일한 문자를 연속으로 3개 이상 사용할 수 없습니다. ex) aaa,111';
        }

        if ($this->_isConsecutiveSequence($pw)) {
            return '연속된 문자를 3개 이상 사용할 수 없습니다. ex) abc, 123';
        }

        if (strpos($pw, $id) !== false) {
            return '비밀번호에 아이디를 포함할 수 없습니다.';
        }

        return '';
    }

    private function _isConsecutiveSequence($pw): bool
    {
        for ($i = 0; $i < strlen($pw) - 2; $i++) {
            if (ord($pw[$i + 1]) === ord($pw[$i]) + 1 &&
                ord($pw[$i + 2]) === ord($pw[$i]) + 2) {
                return true;
            }

            if (is_numeric($pw[$i]) && is_numeric($pw[$i + 1]) && is_numeric($pw[$i + 2])) {
                if ($pw[$i + 1] === $pw[$i] + 1 && $pw[$i + 2] === $pw[$i] + 2) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _chkEmailPolicy($email): string
    {
        return !preg_match("/[\da-z\-_\.]+@([a-z\d]([a-z\d\-]*)([a-z\d]*)\.)+[a-z]{2,6}/i", trim($email));
    }

    private function _chkDupEmail($email): bool
    {
        return $this->_strTrue === $this->_mbrdataMapper->chkDupEmail(str_replace(' ', '', $email))->fetch_assoc()['result'];
    }

    private function _chkDupEmailOfMbruid($email, $mbruid): bool
    {
        return $this->_strTrue === $this->_mbrdataMapper->chkDupEmailOfMbruid(str_replace(' ', '', $email), $mbruid)->fetch_assoc()['result'];
    }

    private function _makeMbridSetData(MemberParamsModel $memberParams): MbridDto
    {
        return MbridDto::of([
            'site' => $this->_defaultSiteVal,
            'id' => $memberParams->id,
            'pw' => getCryptByCCaaS($memberParams->pw1, date('YmdHis')),
        ]);
    }

    private function _setMbrid(MbridDto $mbridDto)
    {
        return $this->_mbridMapper->setMbrid($mbridDto);
    }

    private function _makeMbrdataSetData(MemberParamsModel $memberParams, $insertMbridUid): MbrdataDto
    {
        return MbrdataDto::of([
            'memberuid' => $insertMbridUid,
            'site' => $this->_defaultSiteVal,
            'auth' => 1,
            'mygroup' => $memberParams->group,
            'level' => $memberParams->level,
            'comp' => 0,
            'admin' => 0,
            'email' => $memberParams->email,
            'name' => $memberParams->name,
            'nic' => $memberParams->name,
            'd_regis' => date('YmdHis'),
            'manager' => 1
        ]);
    }

    private function _setMbrdata(MbrdataDto $mbrdataDto)
    {
        $this->_mbrdataMapper->setMbrdata($mbrdataDto);
    }

    private function _makeManagerSetData(MemberParamsModel $memberParams, $insertMbridUid): ManagerDto
    {
        $vendorData = $this->_vendorMapper->getVendor()->fetch_assoc();

        return ManagerDto::of([
            'auth' => 1,
            'mbruid' => $insertMbridUid,
            'vendor' => $vendorData['uid'],
            'bot' => '0',
            'parentmbr' => $vendorData['mbruid'],
            'role' => '',
            'role_intro' => '',
            'd_regis' => date('YmdHis')
        ]);
    }

    private function _setManager(ManagerDto $managerDto)
    {
        $this->_managerMapper->setManager($managerDto);
    }

    private function _chkPwChange(MemberParamsModel $memberParams, $memberData): string
    {
        $pwChangeErrorMsg = '';
        if (isset($memberParams->pw_change) && $this->_strTrue === $memberParams->pw_change) {
            $pwChangeErrorMsg = $this->_chkPwProcess($memberParams, $memberData);
        }

        return $pwChangeErrorMsg;
    }

    private function _makeMbridUpdateData(MemberParamsModel $memberParams, $memberData): MbridDto
    {
        return MbridDto::of([
            'pw' => getCryptByCCaaS($memberParams->pw1, $memberData['d_regis']),
            'uid' => $memberParams->mbruid
        ]);
    }

    private function _updateMbrid(MbridDto $mbridDto)
    {
        $this->_mbridMapper->updateMbrid($mbridDto);
    }

    private function _makeMbrdataUpdateData(MemberParamsModel $memberParams, $memberData): MbrdataDto
    {
        $mbrdata = [
            'mygroup' => $memberParams->group,
            'name' => $memberParams->name,
            'email' => $memberParams->email,
            'memberuid' => $memberParams->mbruid,
            'last_pw' => $this->_strTrue === $memberParams->pw_change ? date('Ymd') : null,
            'log_fail_cnt' => $this->_strN === $memberParams->is_lock ? $this->_defaultLoginFailCnt : null,
            'is_lock' => $memberParams->is_lock,
            'before_pw1' => $memberData['pw'],
            'before_pw2' => $memberData['before_pw1']
        ];

        $mbrdata = array_filter($mbrdata, function ($value) {
            return !is_null($value);
        });

        return MbrdataDto::of($mbrdata);
    }

    private function _updateMbrdata(MbrdataDto $mbrdataDto)
    {
        $this->_mbrdataMapper->updateMbrdata($mbrdataDto);
    }

    private function _getMemberDataByMbruid($mbruid)
    {
        return $this->_mbridMapper->getMemberDataByMbruid($mbruid)->fetch_assoc();
    }

    private function _updateIsPwChangeInSession(): void
    {
        $_SESSION['is_pw_change'] = 'N';
    }

}