<?php
require_once dirname(__file__).'/reserve.class.php';

class Reserve extends ReserveBasic {
    public function __construct() {
        parent::__construct();
    }

    public function getReserveRequest($r_data) {
        global $m, $table, $chatbot, $callbot;

        $result = $response = array();

        // hangup 들어왔을 경우 처리
        if($r_data['msg_type'] == "hangup") {
            getDbUpdate($table[$m.'token'], "r_data=''", "bot='".$this->bot."' and access_mod='callInput' and access_token='".$callbot->accessToken."'");
            exit;

        } else if($r_data['msg_type'] == "noinput" || $r_data['msg_type'] == "sttfail") {
            $response['next_status'] = $r_data['next_status'];

            $r_data['content'] = str_replace("죄송합니다.", "", $r_data['content']);
            $r_data['content'] = str_replace("네. 알겠습니다.", "", $r_data['content']);
            $r_data['content'] = str_replace("확인 감사합니다.", "", $r_data['content']);
            $r_data['content'] = str_replace("예약을 확인해주세요.", "", $r_data['content']);
            $r_data['content'] = str_replace("감사합니다.", "", $r_data['content']);
            $r_data['content'] = str_replace("네. ", "", $r_data['content']);
            $response['content'] = "죄송합니다. 다시 내용을 듣고 말씀해주세요. ".$r_data['content'];
            $response['r_data'] = $r_data;

        } else {
            switch($r_data['step']) {
                case('start') :
                    // 사용자 입력 번호
                    $this->msg = str_replace(array("일본", "일반"), "일번", $this->msg);
                    $this->msg = str_replace(array("음상", "이상", "이성"), "음성", $this->msg);

                    // 번호 파싱
                    /*
                    $oNumber = new NumberParse();
                    $_data['clean_input'] = $this->msg;
                    $DT = $oNumber->getNumberParse($_data);
                    $_num = $DT['sum'] ? $DT['sum'] : ''; // 검출된 번호
                    */
                    $is_stt = preg_match("/음성/u", $this->msg);
                    $is_ars = preg_match("/보이는|ars/iu", $this->msg);

                    //if($_num != 1 && $_num != 2 && !$is_stt && !$is_ars) {
                    if(!$is_stt && !$is_ars) {
                        // 잘못된 값이 들어올 경우 직전 응답을 다시 출력
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = $r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        //if($_num == 2 || $is_ars) {
                        if($is_ars) {
                            // ARS 링크
                            $response = $this->getReserveARSLink($r_data);
                        }
                        //if($_num == 1 || $is_stt) {
                        if($is_stt) {
                            // 휴대폰번호로 과거 예약을 포함한 예약 리스트 API 조회
                            $this->api_data['postParam']['bot_id'] = $this->botid;
                            $this->api_data['postParam']['mode'] = 'get_reserve_list';
                            $this->api_data['postParam']['phone'] = $r_data['uphone'];
                            $apiResult = $chatbot->getReserveAPI($this->api_data);
                            /*--------------------------------------*/

                            if($apiResult['result'] && count($apiResult['reserve']) > 0) {
                                // 예약 정보 있을 경우 종료된 예약과 미종료 예약 정보 설정 후 예약자 이름 확인
                                $dNow = date('Y-m-d H:i');
                                $_name = "";
                                $_lastReserve = $_futureReserve = array();
                                foreach($apiResult['reserve'] as $_reserve) {
                                    $_reserveDate = $_reserve['date'].' '.$_reserve['time'];
                                    if($_reserveDate < $dNow && $_reserveDate > $_lastReserve['date'].' '.$_lastReserve['time']) $_lastReserve = $_reserve;
                                    if($_reserveDate >= $dNow && $_reserveDate > $_futureReserve['date'].' '.$_futureReserve['time']) $_futureReserve = $_reserve;
                                }

                                $r_data['uname_temp'] = $_futureReserve['name'] ? $_futureReserve['name'] : $_lastReserve['name'];
                                $r_data['last_reserve'] = $_lastReserve;
                                $r_data['future_reserve'] = $_futureReserve;

                                $next_status = array('action'=>'recognize');
                                $response['next_status'] = $next_status;

                                $aName_Temp = preg_split('//u', $r_data['uname_temp'], null, PREG_SPLIT_NO_EMPTY);
                                $_name_temp = implode(" ", $aName_Temp);
                                $response['content'] = "고객님의 이름이 ".$_name_temp."님 맞으십니까?";

                                $r_data['step'] = 'auth_name';
                                $r_data['content'] = $response['content'];
                                $r_data['next_status'] = $next_status;
                                $response['r_data'] = $r_data;

                            } else {

                                // 휴대폰 번호로 예약 조회 건이 없을 경우
                                if($r_data['action'] == "request") {
                                    if($this->name_stt) {
                                        // 예약자 이름을 자연어로 처리할 경우
                                        $next_status = array('action'=>'recognize');
                                        $response['next_status'] = $next_status;
                                        $response['content'] = "예약자의 이름을 천천히 말씀해주세요.";

                                        $r_data['step'] = 'ask_name';
                                        $r_data['content'] = $response['content'];
                                        $r_data['next_status'] = $next_status;
                                        $response['r_data'] = $r_data;
                                    } else {
                                        // 예약자 이름을 받지 않을 경우 챗봇 링크 전송
                                        $response = $this->getReserveARSLink($r_data);
                                    }

                                } else {
                                    $next_status = array('action'=>'recognize');
                                    $response['next_status'] = $next_status;
                                    $response['content'] = "죄송합니다. 고객님의 휴대폰 번호로 조회된 예약이 없습니다. 예약을 처음부터 진행하시겠습니까?";
                                    $r_data['step'] = 'ask_new';
                                    $r_data['content'] = $response['content'];
                                    $r_data['next_status'] = $next_status;
                                    $response['r_data'] = $r_data;
                                }
                            }
                        }
                    }
                break;

                case('ask_name') :
                    // 예약자 이름 맞는지 확인 질문
                    // 형태소 분석
                    $name_morph = getMecabMorph(getMorphStrReplace($this->msg), '|');
                    // 불용 품사 제거(조사, 어미, 명사+조사 제거)
                    $name_morph = preg_replace("/(\w+\|NNG)+(\s)+(\w+\|JX|JKS)+(\s)|(\w+\|(([A-Z\w]+\+[A-Z\w]+)|EC|JC|JX|IC|JKS|JKC|JKG|JKO|JKB|JKV|JKQ|VCP))+(\s)?/ui", "", $name_morph);
                    // 인명 품사 검색
                    preg_match('/\w+\|NNP/ui', $name_morph, $_match);
                    if($_match[0]) {
                        $name = trim(str_replace("|NNP", "", $_match[0]));
                    } else {
                        $_names = explode(" ", $name_morph);
                        $name = "";
                        foreach($_names as $_temp) {
                            $_temps = explode("|", $_temp);
                            $name .=$_temps[0];
                        }
                        $name = trim(preg_replace("/(\+)?[A-Z]+/ui", "", $name));
                    }

                    if($name == "") {
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = $r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {

                        //---------------------------------------------
                        $next_status = array('action'=>'recognize');
                        $response['next_status'] = $next_status;

                        $aName_Temp = preg_split('//u', $name, null, PREG_SPLIT_NO_EMPTY);
                        $_name_temp = implode(" ", $aName_Temp);
                        $response['content'] = "예약자 이름이 ".$_name_temp."님 맞으십니까?";

                        $r_data['uname_temp'] = $name;
                        $r_data['step'] = 'ask_name_check';
                        $r_data['next_status'] = $next_status;
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $r_data;
                    }
                break;

                case('ask_name_check') :
                    // 예약자 이름 맞는지 확인
                    $is_yes = $this->getYesorNo($this->msg, $r_data);
                    if(!$is_yes) {
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = "예약자 이름을 다시 천천히 말씀해주세요.";
                        $r_data['step'] = 'ask_name';
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        // 예약 시작
                        $r_data['uname'] = $r_data['uname_temp'];
                        $response = $this->getReserveBranch($r_data);
                    }
                break;

                case('auth_name') :
                    // 예약자 이름 확인
                    $is_yes = $this->getYesorNo($this->msg, $r_data);
                    if(!$is_yes) {
                        if(!isset($r_data['not_name'])) {
                            $response['next_status'] = $r_data['next_status'];
                            $response['content'] = "죄송합니다. ".$r_data['content'];
                            $r_data['step'] = 'auth_name';
                            $r_data['content'] = $response['content'];
                            $r_data['not_name'] = true;
                            $response['r_data'] = $r_data;
                        } else {
                            // 등록된 이름에 대해 2번 아니라고 할 경우 고객센터로
                            $next_status = array('action'=>'hangup');
                            $response['next_status'] = $next_status;
                            $response['content'] = "죄송합니다. 고객님의 이름을 확인할 수 없어 예약 진행이 어렵습니다. 고객센터로 연락 부탁드립니다.";

                            $r_data['step'] = 'finish';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $this->getFinishReset($r_data);
                        }
                    } else {
                        // 이름이 맞다면
                        $r_data['uname'] = $r_data['uname_temp'];
                        $next_status = array('action'=>'recognize');
                        $response['next_status'] = $next_status;

                        if($r_data['future_reserve']['reserve_idx']) {
                            // 미종료 예약 정보가 있다면 정보 알려주고 예약 변경 질문
                            $_date = $this->getDateKorean($r_data['future_reserve']['date'], "no_year");
                            $_week = $this->getWeekKorean($r_data['future_reserve']['date'])."요일";
                            $_time = $this->getTimeKorean($r_data['future_reserve']['time']);

                            // 예약 내용
                            $response['content'] = "확인 감사합니다. ";
                            $response['content'] .=$_date." ".$_week." ".$_time.", ".$r_data['future_reserve']['department_name']." ".$r_data['future_reserve']['doctor_name']." ";
                            // 조선호텔
                            if($this->cgroup == "josunhotel") {
                                $response['content'] .="예약이 되어있습니다. ";
                            } else {
                                $response['content'] .="선생님으로 예약이 되어있습니다. ";
                            }
                            if($r_data['action'] == "request" || $r_data['action'] == "search") {
                                $r_data['step'] = 'ask_modify';
                                $response['content'] .="예약을 변경하고자 할 경우 예약 변경 또는 예약 취소라고 말씀해 주세요.";

                            } else if($r_data['action'] == "modify") {
                                $r_data['step'] = 'ask_modify';
                                $response['content'] .="예약을 변경하시겠습니까?";

                            } else if($r_data['action'] == "cancel") {
                                $r_data['step'] = 'ask_cancel';
                                $r_data['reserve_idx'] = $r_data['future_reserve']['reserve_idx'];
                                $response['content'] .="예약을 취소하시겠습니까?";
                            }

                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        } else {
                            // 과거 예약 정보가 있다면 과거 예약 의사로 진행할지 질문
                            $response['content'] = "확인 감사합니다. ";

                            if($r_data['action'] == "request" || $r_data['action'] == "search" || $r_data['action'] == "modify") {
                                if(!$r_data['action'] == "request") {
                                    $response['content'] .="현재 예약된 정보가 없습니다. ";
                                }
                                // 조선호텔
                                if($this->cgroup == "josunhotel") {
                                    $response['content'] .="지난번과 동일하게 ".$r_data['last_reserve']['doctor_name']." 예약해드릴까요?";
                                } else {
                                    $response['content'] .="지난번과 동일하게 ".$r_data['last_reserve']['doctor_name']." 선생님으로 예약해드릴까요?";
                                }

                                $r_data['step'] = 'ask_reserve';
                            } else if($r_data['action'] == "cancel") {
                                $response['content'] .="현재 예약된 정보가 없습니다. 다른 문의 사항이 있으면 말씀해주세요.";
                                $r_data['step'] = 'finish';
                            }
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        }
                    }
                break;

                case('ask_new') :
                    // 신규로 예약 진행할지 여부
                    $is_yes = $this->getYesorNo($this->msg, $r_data);
                    if(!$is_yes) {
                        $next_status = array('action'=>'recognize');
                        $response['next_status'] = $next_status;
                        $response['content'] = "네. 다른 문의 사항이 있으면 말씀해주세요.";

                        $r_data['step'] = 'finish';
                        $r_data['next_status'] = $next_status;
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $this->getFinishReset($r_data);
                    } else {
                        //처음부터 신규 예약
                        $r_data['sys_date'] = "";
                        $r_data['sys_week'] = "";
                        $r_data['sys_time'] = "";
                        $r_data['action'] = "request";

                        if($this->name_stt) {
                            // 예약자 이름을 자연어로 처리할 경우
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "예약자의 이름을 천천히 말씀해주세요.";

                            $r_data['step'] = 'ask_name';
                            $r_data['content'] = $response['content'];
                            $r_data['next_status'] = $next_status;
                            $response['r_data'] = $r_data;
                        } else {
                            // 예약자 이름을 받지 않을 경우 챗봇 링크 전송
                            $response = $this->getReserveARSLink($r_data);
                        }
                    }
                break;

                case('ask_modify') :
                    $_action = "";
                    if($r_data['action'] == "modify" || $r_data['action'] == "cancel") {
                        $is_yes = $this->getYesorNo($this->msg, $r_data);
                        if($is_yes) {
                            $_action = $r_data['action'];
                        } else {
                            // 변경, 취소하지 않을 경우
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "네. 다른 문의 사항이 있으면 말씀해주세요.";

                            $r_data['step'] = 'finish';
                            $r_data['content'] = $response['content'];
                            $r_data['next_status'] = $next_status;
                            $response['r_data'] = $this->getFinishReset($r_data);
                        }
                    } else {
                        $is_modify = preg_match("/변경|바꾸|바꿀/u", $this->msg);
                        $is_cancel = preg_match("/취소/u", $this->msg);

                        if(!$is_modify && !$is_cancel) {
                            // 변경, 취소하지 않겠다면 그대로 진행 여부 질문
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "네. 다른 문의 사항이 있으면 말씀해주세요.";

                            $r_data['step'] = 'finish';
                            $r_data['content'] = $response['content'];
                            $r_data['next_status'] = $next_status;
                            $response['r_data'] = $this->getFinishReset($r_data);

                        } else {
                            // 변경하겠다면 변경 시나리오 진행
                            $_action = $is_modify ? "modify" : "cancel";
                        }
                    }

                    if($_action) {
                        $r_data['action'] = $_action;
                        $r_data['uname'] = $r_data['uname_temp'];
                        $r_data['reserve_idx'] = $r_data['future_reserve']['reserve_idx'];

                        if($r_data['action'] == "modify") {
                            // 예약 변경
                            $response = $this->getReserveBranch($r_data);
                        } else {
                            // 예약 취소 여부 질문
                            $_date = $this->getDateKorean($r_data['future_reserve']['date'], "no_year");
                            $_week = $this->getWeekKorean($r_data['future_reserve']['date'])."요일";
                            $_time = $this->getTimeKorean($r_data['future_reserve']['time']);

                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = $_date." ".$_week." ".$_time.", ".$r_data['future_reserve']['doctor_name']." ";
                            // 조선호텔
                            if($this->cgroup == "josunhotel") {
                                $response['content'] .="예약을 취소하시겠습니까?";
                            } else {
                                $response['content'] .="선생님의 예약을 취소하시겠습니까?";
                            }
                            $r_data['step'] = 'ask_cancel';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        }
                    }
                break;

                case('ask_cancel') :
                    // 취소 여부 체크
                    $is_yes = $this->getYesorNo($this->msg, $r_data);

                    if($is_yes) {
                        // 취소하겠다면 예약 취소
                        $_result = $this->getReserveSend($r_data);
                        if($_result['result'] == 1) {
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "예약이 취소되었습니다. 감사합니다. 다른 문의 사항이 있으면 말씀해주세요.";

                            $r_data['step'] = 'finish';
                            $r_data['content'] = $response['content'];
                            $r_data['next_status'] = $next_status;
                            $response['r_data'] = $this->getFinishReset($r_data);
                        } else {
                            $response = $this->getReserveErrorForword($r_data);
                        }
                    } else {
                        // 취소하지 않겠다면 종료
                        $next_status = array('action'=>'recognize');
                        $response['next_status'] = $next_status;
                        $response['content'] = "감사합니다. 다른 문의 사항이 있으면 말씀해주세요.";

                        $r_data['step'] = 'finish';
                        $r_data['content'] = $response['content'];
                        $r_data['next_status'] = $next_status;
                        $response['r_data'] = $this->getFinishReset($r_data);
                    }
                break;

                case('ask_reserve') :
                    // 동일 선생님에게 예약 여부 체크
                    $is_yes = $this->getYesorNo($this->msg, $r_data);
                    if(!$is_yes) {
                        // 다른 선생님 요구라면 의사 선택하기
                        $r_data['branch_idx'] = $r_data['last_reserve']['branch_idx'];
                        $r_data['branch_name'] = $r_data['last_reserve']['branch_name'];
                        $r_data['department_idx'] = $r_data['last_reserve']['department_idx'];
                        $r_data['department_name'] = $r_data['last_reserve']['department_name'];
                        $response = $this->getReserveDoctor($r_data);
                    } else {
                        $r_data['branch_idx'] = $r_data['last_reserve']['branch_idx'];
                        $r_data['branch_name'] = $r_data['last_reserve']['branch_name'];
                        $r_data['department_idx'] = $r_data['last_reserve']['department_idx'];
                        $r_data['department_name'] = $r_data['last_reserve']['department_name'];
                        $r_data['doctor_idx'] = $r_data['last_reserve']['doctor_idx'];
                        $r_data['doctor_name'] = $r_data['last_reserve']['doctor_name'];

                        // 날짜 정보가 있다면
                        if($r_data['sys_date']) {
                            $r_data['step'] = 'date';
                            $response = $this->getReserveScheduleDate($r_data);
                        } else {
                            //날짜 정보가 없다면 날짜 묻기
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "예약 날짜와 시간을 말씀해주세요.";

                            $r_data['step'] = 'date';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        }
                    }
                break;

                case('ask_finish_modify') :
                    // 예약 완료 후 변경 요청
                    $r_data['action'] = "modify";
                    $r_data['finish_modify'] = true;
                    $response = $this->getRenewRequest($r_data);
                break;

                case('ask_finish_search') :
                    // 예약 후 확인 요청
                    $r_data['action'] = "search";
                    $_result = $this->getReserveSend($r_data);
                    if($_result['result'] == 1) {
                        $_reserve = $_result['reserve'];

                        $_date = $this->getDateKorean($_reserve['date'], "no_year");
                        $_week = $this->getWeekKorean($_reserve['date'])."요일";
                        $_time = $this->getTimeKorean($_reserve['time']);

                        $next_status = array('action'=>'recognize');
                        $response['next_status'] = $next_status;

                        $response['content'] = $_reserve['name']."님의 예약은 ";
                        $response['content'] .=$_date." ".$_week." ".$_time.", ".$_reserve['department_name']." ".$_reserve['doctor_name']." ";
                        // 조선호텔
                        if($this->cgroup == "josunhotel") {
                            $response['content'] .="예약이 되어있습니다. 다른 문의 사항이 있으면 말씀해주세요.";
                        } else {
                            $response['content'] .="선생님으로 예약이 되어있습니다. 다른 문의 사항이 있으면 말씀해주세요.";
                        }

                        $r_data['step'] = 'finish';
                        $r_data['next_status'] = $next_status;
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $this->getFinishReset($r_data);
                    } else {
                        $response = $this->getReserveErrorForword($r_data);
                    }
                break;

                case('ask_finish_new') :
                    // 예약 취소 후 다시 예약
                    $r_data['sys_date'] = "";
                    $r_data['sys_week'] = "";
                    $r_data['sys_time'] = "";
                    $r_data['action'] = "request";
                    $response = $this->getReserveBranch($r_data);
                break;

                case('branch') :
                    // 지점 확인
                    $aBranch = $r_data['branches'];
                    $_branch = array_column($aBranch, "name");
                    preg_match("/".implode("|", $_branch)."/iu", $this->msg, $_match);
                    if(count($_match) == 0 || !$_match[0]) {
                        // 지점명이 없을 경우 재질문
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = "죄송합니다. ".$r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        $_key = array_search($_match[0], array_column($aBranch, "name"));
                        $_branch = $aBranch[$_key];
                        $r_data['branch_idx'] = $_branch['idx'];
                        $r_data['branch_name'] = $_branch['name'];
                        $r_data['branches'] = $aBranch;
                        $response = $this->getReserveDepartment($r_data);
                    }
                break;

                case('department') :
                    // 진료과목 확인
                    $aDepartment = $r_data['departments'];

                    // 사용자 입력 번호
                    $this->msg = str_replace(array("일본", "일반"), "일번", $this->msg);
                    $_data['clean_input'] = $this->msg;

                    // 번호 파싱
                    $oNumber = new NumberParse();
                    $aResultNumber = $oNumber->getNumberParse($_data);
                    if(isset($aResultNumber['result']) && count($aResultNumber['result']) > 0) {
                        $DT = $aResultNumber['result'][0];
                        $_num = $DT['sum'] ? $DT['sum'] : ''; // 검출된 번호
                    } else {
                        $_num = '';
                    }

                    $_department = array_column($aDepartment, "name");
                    preg_match("/".implode("|", $_department)."/iu", $this->msg, $aMatch);
                    $_name = $aMatch[0] ? $aMatch[0] : ""; // 검출된 진료과목명

                    if(!$_num && !$_name) {
                        // 진료과목명이 없을 경우 재질문
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = "죄송합니다. ".$r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        // 진료과목 설정
                        foreach($aDepartment as $department) {
                            if($department['no'] == $_num) {
                                $r_data['department_idx'] = $department['idx'];
                                $r_data['department_name'] = $department['name'];
                                break;
                            } else if($department['name'] == $_name) {
                                $r_data['department_idx'] = $department['idx'];
                                $r_data['department_name'] = $department['name'];
                            }
                        }

                        if(!$r_data['department_idx']) {
                            // 해당 번호가 없다면
                            $response['next_status'] = $r_data['next_status'];
                            $response['content'] = $r_data['content'];
                            $response['r_data'] = $r_data;
                        } else {
                            $response = $this->getReserveDoctor($r_data);
                        }
                    }
                break;

                case('doctor') :
                    // 의사명 확인
                    $aDoctor = $r_data['doctors'];
                    $_rand_doctor = false;

                    $_doctor = array_column($aDoctor, "name");
                    preg_match("/".implode("|", $_doctor)."/iu", $this->msg, $aMatch);
                    $_name = $aMatch[0] ? $aMatch[0] : ""; // 검출된 의사명
                    if($_name) {
                        foreach($aDoctor as $doctor) {
                            if($doctor['name'] == $_name) {
                                $r_data['doctor_idx'] = $doctor['idx'];
                                $r_data['doctor_name'] = $doctor['name'];
                                break;
                            }
                        }
                    } else {
                        // 사용자 입력 번호
                        $this->msg = str_replace(array("일본", "일반"), "일번", $this->msg);
                        $_data['clean_input'] = $this->msg;

                        // 번호 파싱
                        $oNumber = new NumberParse();
                        $aResultNumber = $oNumber->getNumberParse($_data);
                        if(isset($aResultNumber['result']) && count($aResultNumber['result']) > 0) {
                            $DT = $aResultNumber['result'][0];
                            $_num = $DT['sum'] ? $DT['sum'] : ''; // 검출된 번호
                        } else {
                            $_num = '';
                        }
                        if($_num) {
                            foreach($aDoctor as $doctor) {
                                if($doctor['no'] == $_num) {
                                    $r_data['doctor_idx'] = $doctor['idx'];
                                    $r_data['doctor_name'] = $doctor['name'];
                                    break;
                                }
                            }
                        }
                    }

                    if(!$_num && !$_name) {
                        if(preg_match("/아무/iu", $this->msg)) {
                            $_key = array_rand($aDoctor);
                            $_rand_doctor = true;
                            $r_data['doctor_idx'] = $aDoctor[$_key]['idx'];
                            $r_data['doctor_name'] = $aDoctor[$_key]['name'];
                        } else {
                            // 잘못된 값이 들어올 경우 직전 응답을 다시 출력
                            $response['next_status'] = $r_data['next_status'];
                            $response['content'] = $r_data['content'];
                            $response['r_data'] = $r_data;
                        }
                    }

                    if(!$r_data['doctor_idx']) {
                        // 해당 번호가 없다면
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = $r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        // 날짜 정보가 있다면
                        if($r_data['sys_date'] || $r_data['sys_fast']) {
                            $r_data['step'] = 'date';
                            $response = $this->getReserveScheduleDate($r_data);
                        } else {
                            // 예약 날짜 물어보기
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            if($_rand_doctor) {
                                $response['content'] = "예약 날짜와 시간을 말씀해주세요.";
                            } else {
                                // 조선호텔
                                if($this->cgroup == "josunhotel") {
                                    $response['content'] = $r_data['doctor_name']." 진료받을 예약 날짜와 시간을 말씀해주세요.";
                                } else {
                                    $response['content'] = $r_data['doctor_name']." 선생님께 진료받을 예약 날짜와 시간을 말씀해주세요.";
                                }
                            }

                            $r_data['step'] = 'date';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        }
                    }
                break;

                case('date') :
                    if(preg_match("/빠른|빨리|아무|언제/ui", $this->msg)) $r_data['sys_fast'] = true;
                    // 날짜 파싱
                    if(!$r_data['temp_sys_date'] && $r_data['sys_fast'] != true) {
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = $r_data['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        if(!$r_data['sys_date']) {
                            $r_data['sys_date'] = $r_data['temp_sys_date'] ? $r_data['temp_sys_date'] : "";
                            $r_data['sys_week'] = $r_data['temp_sys_week'] ? $r_data['temp_sys_week'] : "";
                            $r_data['sys_time'] = $r_data['temp_sys_time'] ? $r_data['temp_sys_time'] : "";
                            $response = $this->getReserveScheduleDate($r_data);
                        } else {
                            $response = $this->getRenewDateTime($r_data);
                        }
                    }
                break;

                case('time') :
                    // 날짜 파싱
                    $aDT = explode(":", $r_data['temp_sys_time']);
                    if ($aDT[0] == "" || (int)$aDT[0] == 0) {
                        // 시간 확인 중 다른 날짜 요청일 경우
                        $response = $this->getRenewDateTime($r_data);
                    } else {
                        $r_data['sys_time'] = $r_data['temp_sys_time'];
                        $response = $this->getReserveScheduleDate($r_data);
                    }
                break;

                case('confirm') :
                    // 사용자 입력 날짜
                    $is_yes = $this->getYesorNo($this->msg, $r_data);
                    if($is_yes) {
                        $_result = $this->getReserveSend($r_data);
                        if($_result['result'] == 1) {
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;

                            if($r_data['action'] == "request") {
                                $r_data['reserve_idx'] = $_result['reserve_idx'];
                                $response['content'] = "감사합니다. 예약이 완료되었습니다. 예약이 변경될 경우, 연락드리겠습니다.";
                            } else if($r_data['action'] == "modify") {
                                $response['content'] = "감사합니다. 예약이 변경되었습니다.";
                            } if($r_data['action'] == "cancel") {
                                $response['content'] = "감사합니다. 예약이 취소되었습니다.";
                            }

                            $response['content'] .=" 다른 문의 사항이 있으면 말씀해주세요.";

                            if(isset($r_data['is_no'])) unset($r_data['is_no']);
                            $r_data['step'] = 'finish';
                            $r_data['content'] = $response['content'];
                            $r_data['next_status'] = $next_status;
                            $response['r_data'] = $this->getFinishReset($r_data);
                        } else {
                            $r_data['is_no'] = isset($r_data['is_no']) ? ($r_data['is_no']+1) : 1;
                        }
                    } else {
                        if($r_data['action'] == "request" || $r_data['action'] == "modify") {
                            // 예약 확인 중 변경 요청 체크
        		            if($r_data['intentName'] == "시스템-날짜시간변경" || $r_data['intentName'] == "시스템-시간문의") {
        		                $r_data['finish_modify'] = true;
        		                $response = $this->getRenewRequest($r_data);
        		            } else {
        		                $r_data['is_no'] = isset($r_data['is_no']) ? ($r_data['is_no']+1) : 1;
        		            }
        		        } else {
        		            $r_data['is_no'] = isset($r_data['is_no']) ? ($r_data['is_no']+1) : 1;
        		        }
        		    }

                    // 부정 응답/실패 2회 이상일 경우 상담원 연결
                    if(isset($r_data['is_no'])) {
                        if($r_data['is_no'] >= 2) {
                            $response = $this->getReserveErrorForword($r_data);
                        } else {
                            $next_status = $r_data['next_status'];
                            $response['next_status'] = $next_status;
                            $response['content'] = $r_data['content'];
                            $response['r_data'] = $r_data;
                        }
                    }

                break;
            }
        }

        // 봇응답 로그 기록
        $callbot->getCallbotBotChatLog($response['content']);

        $response['type'] = "text";
        $response['bargein'] = false;

        $response['r_data']['intentName'] = "";
        $response['r_data']['temp_sys_date'] = "";
        $response['r_data']['temp_sys_week'] = "";
        $response['r_data']['temp_sys_time'] = "";

        $result[] = $response;
        return $result;
    }

    private function getReserveBranch($r_data) {
        global $m, $table, $chatbot;

        // 지점 확인 API
        $this->api_data['postParam']['bot_id'] = $this->botid;
        $this->api_data['postParam']['mode'] = 'get_branch';
        $this->api_data['postParam']['date'] = $r_data['sys_date'];
        $apiResult = $chatbot->getReserveAPI($this->api_data);
        if($apiResult['result']) {
            // 지점 1개일 경우 진료과목 조회
            if(count($apiResult['branch']) == 1) {
                $r_data['branch_idx'] = $apiResult['branch'][0]['idx'];
                $r_data['branch_name'] = $apiResult['branch'][0]['name'];
                $r_data['branches'] = $apiResult['branch'];
                $response = $this->getReserveDepartment($r_data);
            } else {
                // 지점 목록 응답
                $aBranch = "";
                foreach($apiResult['branch'] as $branch) {
                    $aBranch .=$branch['name'].", ";
                }
                $aBranch = rtrim($aBranch, ", ");

                $next_status = array('action'=>'recognize');
                $response['next_status'] = $next_status;
                $response['content'] = "예약할 지점을 말씀해주세요. 저희 병원의 지점은 다음과 같습니다. ".$aBranch;

                $r_data['step'] = 'branch';
                $r_data['next_status'] = $next_status;
                $r_data['content'] = $response['content'];
                $r_data['branches'] = $apiResult['branch'];
                $response['r_data'] = $r_data;
            }
        } else {
            // API 확인 실패
            $msg = "죄송합니다. 예약 지점 정보를 확인할 수가 없습니다. 고객센터로 연락 부탁드립니다.";
            $response = $this->getReserveErrorForword($r_data, $msg);
        }
        return $response;
    }

    private function getReserveDepartment($r_data) {
        global $m, $table, $chatbot;

        $this->api_data['postParam']['bot_id'] = $this->botid;
        $this->api_data['postParam']['mode'] = 'get_department';
        $this->api_data['postParam']['branch_idx'] = $r_data['branch_idx'];
        $this->api_data['postParam']['branch_name'] = $r_data['branch_name'];
        $this->api_data['postParam']['date'] = $r_data['sys_date'];
        $apiResult = $chatbot->getReserveAPI($this->api_data);
        if($apiResult['result']) {
            // 진료과목 1개일 경우 의사 조회
            if(count($apiResult['department']) == 1) {
                $r_data['department_idx'] = $apiResult['department'][0]['idx'];
                $r_data['department_name'] = $apiResult['department'][0]['name'];
                $r_data['departments'] = $apiResult['department'];
                $response = $this->getReserveDoctor($r_data);
            } else {
                // 진료과목 목록 응답
                $aDepartment = "";
                for($i=0, $nCnt=count($apiResult['department']); $i<$nCnt; $i++) {
                    $apiResult['department'][$i]['no'] = ($i+1);
                    $aDepartment .=($i+1)."번 ".$apiResult['department'][$i]['name'].", ";
                }
                $aDepartment = rtrim($aDepartment, ", ");

                $next_status = array('action'=>'recognize');
                $response['next_status'] = $next_status;
                $response['content'] = "진료과목의 번호 또는 진료과목명을 말씀해주세요. ".$aDepartment;

                $r_data['step'] = 'department';
                $r_data['next_status'] = $next_status;
                $r_data['content'] = $response['content'];
                $r_data['departments'] = $apiResult['department'];
                $response['r_data'] = $r_data;
            }
        } else {
            // API 확인 실패
            $msg = "죄송합니다. 진료과목 정보를 확인할 수가 없습니다. 고객센터로 연락 부탁드립니다.";
            $response = $this->getReserveErrorForword($r_data, $msg);
        }
        return $response;
    }

    private function getReserveDoctor($r_data) {
        global $m, $table, $chatbot;

        $this->api_data['postParam']['bot_id'] = $this->botid;
        $this->api_data['postParam']['mode'] = 'get_doctor';
        $this->api_data['postParam']['branch_idx'] = $r_data['branch_idx'];
        $this->api_data['postParam']['branch_name'] = $r_data['branch_name'];
        $this->api_data['postParam']['department_idx'] = $r_data['department_idx'];
        $this->api_data['postParam']['department_name'] = $r_data['department_name'];
        $this->api_data['postParam']['date'] = $r_data['sys_date'];
        $apiResult = $chatbot->getReserveAPI($this->api_data);
        if($apiResult['result']) {
            // 의사 1명일 경우 예약 가능 날짜 묻기
            if(count($apiResult['doctor']) == 1) {
                $r_data['doctor_idx'] = $apiResult['doctor'][0]['idx'];
                $r_data['doctor_name'] = $apiResult['doctor'][0]['name'];
                $r_data['doctors'] = $apiResult['doctor'];

                // 날짜 정보가 있다면
                if($r_data['sys_date']) {
                    $r_data['step'] = 'date';
                    $response = $this->getReserveScheduleDate($r_data);
                } else {
                    //날짜 정보가 없다면 날짜 묻기
                    $next_status = array('action'=>'recognize');
                    $response['next_status'] = $next_status;
                    $response['content'] = "예약 날짜와 시간을 말씀해주세요.";

                    $r_data['step'] = 'date';
                    $r_data['next_status'] = $next_status;
                    $r_data['content'] = $response['content'];
                    $response['r_data'] = $r_data;
                }
            } else {
                // 의사 목록 응답
                $aDoctor = "";
                for($i=0, $nCnt=count($apiResult['doctor']); $i<$nCnt; $i++) {
                    $apiResult['doctor'][$i]['no'] = ($i+1);
                    // 조선호텔
                    if($this->cgroup == "josunhotel") {
                        $aDoctor .=($i+1)."번 ".$apiResult['doctor'][$i]['name'].", ";
                    } else {
                        $aDoctor .=($i+1)."번 ".$apiResult['doctor'][$i]['name']." 선생님, ";
                    }
                }
                $aDoctor = rtrim($aDoctor, ", ");

                $next_status = array('action'=>'recognize');
                $response['next_status'] = $next_status;
                // 조선호텔
                if($this->cgroup == "josunhotel") {
                    $response['content'] = "예약하려는 번호 또는 이름을 말씀해주세요. ".$aDoctor;
                } else {
                    $response['content'] = "예약하려는 선생님의 번호 또는 이름을 말씀해주세요. ".$aDoctor;
                }
                $r_data['step'] = 'doctor';
                $r_data['next_status'] = $next_status;
                $r_data['content'] = $response['content'];
                $r_data['doctors'] = $apiResult['doctor'];
                $response['r_data'] = $r_data;
            }
        } else {
            // API 확인 실패
            $msg = "죄송합니다. 예약 의사 정보를 확인할 수가 없습니다. 고객센터로 연락 부탁드립니다.";
            $response = $this->getReserveErrorForword($r_data, $msg);
        }
        return $response;
    }

    private function getReserveScheduleDate($r_data) {
        global $m, $table, $chatbot;

        $response = array();

        // 예약 가능 날짜 API
        $this->api_data['postParam']['bot_id'] = $this->botid;
        $this->api_data['postParam']['mode'] = $r_data['step'] == 'time' ? 'get_times' : 'get_dates';
        $this->api_data['postParam']['phone'] = $r_data['uphone'];
        $this->api_data['postParam']['name'] = $r_data['uname'];
        $this->api_data['postParam']['branch_idx'] = $r_data['branch_idx'];
        $this->api_data['postParam']['branch_name'] = $r_data['branch_name'];
        $this->api_data['postParam']['department_idx'] = $r_data['department_idx'];
        $this->api_data['postParam']['department_name'] = $r_data['department_name'];
        $this->api_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
        $this->api_data['postParam']['doctor_name'] = $r_data['doctor_name'];
        $this->api_data['postParam']['date'] = $r_data['sys_date'];
        if($r_data['reserve_idx']) {
            $this->api_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
        }

        $apiResult = $chatbot->getReserveAPI($this->api_data);
        if($r_data['step'] == 'date' && count($apiResult['dates']) == 0) {
            $next_status = array('action'=>'recognize');;
            $response['next_status'] = $next_status;
            if($r_data['sys_date']) {
                $_datesKorean =$this->getDateKorean($r_data['sys_date'], 'no_year', 'with_week');
                $response['content'] = "죄송합니다. 말씀하신 ".$_datesKorean."에는 예약이 불가능합니다. 다른 날짜를 말씀해주세요.";
            } else {
                $response['content'] = "죄송합니다. 예약 가능한 날짜가 없습니다. 다른 날짜를 말씀해주세요.";
            }
            $r_data['content'] = $response['content'];
            $response['r_data'] = $r_data;

        } else if($r_data['step'] == 'time' && count($apiResult['times']) == 0) {
            $next_status = array('action'=>'recognize');
            $response['next_status'] = $next_status;
            if($r_data['sys_time']) {
                $_timesKorean .=$this->getTimeKorean($r_data['sys_time']);
                $response['content'] = "말씀하신 ".$_timesKorean."에는 예약이 불가능합니다. 다른 시간을 말씀해주세요.";
            } else {
                $response['content'] = "죄송합니다. 예약 가능한 시간이 없습니다. 다른 시간을 말씀해주세요.";
            }
            $r_data['content'] = $response['content'];
            $response['r_data'] = $r_data;

        } else {
            if(count($apiResult['dates']) > 0) {
                $r_data['dates'] = $apiResult['dates'];
            }
            if(count($apiResult['times']) > 0) {
                $r_data['times'] = array();
                foreach($apiResult['times'] as $time) {
                    $r_data['times'][] = substr($time, 0, 5);
                }
            }

            // 발화문에서 희망 예약 날짜가 없다면
            if(!$r_data['sys_date'] || !in_array($r_data['sys_date'], $r_data['dates'])) {
                $next_status = array('action'=>'recognize');
                $response['next_status'] = $next_status;
                $response['content'] = "";
                if($r_data['sys_date'] && !$r_data['sys_fast']) {
                    $_datesKorean =$this->getDateKorean($r_data['sys_date'], 'no_year', 'with_week');
                    $response['content'] .="말씀하신 ".$_datesKorean."에는 예약이 불가능합니다.";
                    $_initDate = $r_data['sys_date'];
                } else {
                    $_initDate = date("Y-m-d");
                }

                if(!$r_data['sys_date'] && !$r_data['temp_sys_date'] && $r_data['intentName'] == "시스템-날짜시간변경") {
                    $response['content'] = "원하시는 예약 날짜를 말씀해주세요. ";
                } else {
                    $ableDates = $this->getNearestTimes($_initDate, $r_data['dates'], 'date');
                    if(count($ableDates) > 0) {
                        $r_data['able_weeks'] = array();
                        $_datesKorean = "";
                        foreach($ableDates as $date) {
                            $_week = $this->getWeekKorean($date)."요일";
                            $r_data['able_weeks'][$_week] = $date;
                            $_datesKorean .=$this->getDateKorean($date, 'no_year', 'with_week').", ";
                        }
                        $_datesKorean = rtrim($_datesKorean, ", ");
                        $response['content'] .=" 예약 가능한 날짜는 ".$_datesKorean."입니다. 원하시는 예약 날짜를 말씀해주세요.";
                    }
                }

                $r_data['step'] = 'date';
                $r_data['content'] = $response['content'];
                $response['r_data'] = $r_data;

            // 발화문에서 예약 시간 정보가 없다면
            } else if(!$r_data['sys_time'] || (int)substr($r_data['sys_time'], 0, 2) == 0) {
                // 가능 시간대 문의 체크
                $checkTimeRange = $this->getCheckTimeRange($r_data);
                if($checkTimeRange['result'] == true) {
                    $response = $checkTimeRange['response'];
                } else {
                    $next_status = array('action'=>'recognize');
                    $response['next_status'] = $next_status;
                    $response['content'] = "원하시는 예약 시간을 말씀해주세요. ";

                    $ableTimes = array_slice($r_data['times'], 0, 2);
                    // 예약 가능 시간 리스트 안내
                    if(count($ableTimes) > 0) {
                        $_timesKorean = "";
                        foreach($ableTimes as $time) {
                            $_timesKorean .=$this->getTimeKorean($time).", ";
                        }
                        $_timesKorean = rtrim($_timesKorean, ", ");
                        if($r_data['sys_date']) {
                            $_datesKorean =$this->getDateKorean($r_data['sys_date'], 'no_year', 'with_week');
                            $response['content'] = $_datesKorean."에 ";
                            $response['content'] .="예약 가능한 시간은 ".$_timesKorean."입니다. ";
                            $response['content'] .="원하시는 예약 시간을 말씀해주세요.";
                        } else {
                            $response['content'] .="예약 가능한 시간은 ".$_timesKorean."입니다.";
                        }
                    }

                    $r_data['step'] = 'time';
                    $r_data['next_status'] = $next_status;
                    $r_data['content'] = $response['content'];
                    $response['r_data'] = $r_data;
                }

            // 희망 시간이 해당 날짜의 예약 가능 시간대에 없다면
            } else if(!in_array($r_data['sys_time'], $r_data['times'])) {
                // 가능 시간대 문의 체크
                $checkTimeRange = $this->getCheckTimeRange($r_data);
                if($checkTimeRange['result'] == true) {
                    $response = $checkTimeRange['response'];
                } else {
                    // 다른 날짜의 해당 시간 검색
                    $ableDates = $this->getReserveScheduleDateByTime($r_data);
                    if(count($ableDates) > 0) {
                        $response['next_status'] = $next_status;
                        $response['content'] = "";

                        $r_data['able_weeks'] = array();
                        $_datesKorean = "";
                        foreach($ableDates as $date) {
                            $_week = $this->getWeekKorean($date)."요일";
                            $r_data['able_weeks'][$_week] = $date;
                            $_datesKorean .=$this->getDateKorean($date, 'no_year', 'with_week').", ";
                        }
                        $_datesKorean = rtrim($_datesKorean, ", ");
                        $response['content'] .=" 예약 가능한 날짜는 ".$_datesKorean."입니다. 원하시는 예약 날짜를 말씀해주세요.";
                        $r_data['step'] = 'date';
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $r_data;
                    } else {
                        $next_status = $r_data['next_status'];
                        $response['next_status'] = $next_status;

                        $_timesKorean = $this->getTimeKorean($r_data['sys_time']);
                        if($r_data['step'] == "date" && $r_data['sys_date']) {
                            $_datesKorean =$this->getDateKorean($r_data['sys_date'], 'no_year', 'with_week');
                            $_timesKorean = $_datesKorean." ".$_timesKorean;
                        }
                        $response['content'] = "말씀하신 ".$_timesKorean."에는 예약이 불가능합니다.";

                        $ableTimes = $this->getNearestTimes($r_data['sys_time'], $r_data['times'], 'time');
                        // 예약 가능 시간 리스트 안내
                        if(count($ableTimes) > 0) {
                            $_timesKorean = "";
                            foreach($ableTimes as $time) {
                                $_timesKorean .=$this->getTimeKorean($time).", ";
                            }
                            $_timesKorean = rtrim($_timesKorean, ", ");
                            $response['content'] .=" 예약 가능한 시간은 ".$_timesKorean."입니다.";
                        }

                        $r_data['step'] = 'time';
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $r_data;
                    }
                }
            } else {

                // 최종 확인 내용 묻기
                $response = $this->getAskConfirm($r_data);
            }
        }

        return $response;
    }

    private function getReserveScheduleDateByTime($r_data) {
        global $m, $table, $chatbot;

        // 예약 가능 날짜 API
        $this->api_data['postParam']['bot_id'] = $this->botid;
        $this->api_data['postParam']['mode'] = 'get_times';
        $this->api_data['postParam']['phone'] = $r_data['uphone'];
        $this->api_data['postParam']['name'] = $r_data['uname'];
        $this->api_data['postParam']['branch_idx'] = $r_data['branch_idx'];
        $this->api_data['postParam']['branch_name'] = $r_data['branch_name'];
        $this->api_data['postParam']['department_idx'] = $r_data['department_idx'];
        $this->api_data['postParam']['department_name'] = $r_data['department_name'];
        $this->api_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
        $this->api_data['postParam']['doctor_name'] = $r_data['doctor_name'];
        if($r_data['reserve_idx']) {
            $this->api_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
        }

        // 주어진 날짜 이외의 다른 날짜의 시간 검색
        $aDates = array();
        if(is_array($r_data['dates']) && count($r_data['dates']) > 0) {
            $_dates = $r_data['dates'];
            unset($_dates[$r_data['sys_date']]);
            foreach($_dates as $_date) {
                $this->api_data['postParam']['date'] = $_date;
                $apiResult = $chatbot->getReserveAPI($this->api_data);
                if(count($apiResult['times']) > 0) {
                    foreach($apiResult['times'] as $time) {
                        if(substr($time, 0, 5) == $r_data['sys_time']) {
                            $aDates[] = $_date;
                        }
                    }
                }
            }
        }
        return $aDates;
    }

    private function getAskConfirm($r_data) {
        $r_data['date'] = $r_data['sys_date'];
        $r_data['time'] = $r_data['sys_time'];
        $r_data['week'] = $r_data['sys_week'];

        $_date = $this->getDateKorean($r_data['date'], "no_year");
        $_week = $r_data['week']."요일";
        $_time = $this->getTimeKorean($r_data['time']);

        // 확인 내용 묻기
        $next_status = array('action'=>'recognize');
        $response['next_status'] = $next_status;

        $response['content'] = "";
        if($r_data['finish_modify']) {
            $response['content'] .="네. 알겠습니다. ";
            // 조선호텔
            if($this->cgroup == "josunhotel") {
                $response['content'] .=$_date." ".$_week." ".$_time.", ".$r_data['department_name']." ".$r_data['doctor_name']." 변경해드릴까요?";
            } else {
                $response['content'] .=$_date." ".$_week." ".$_time.", ".$r_data['department_name']." ".$r_data['doctor_name']." 선생님으로 변경해드릴까요?";
            }
        } else {
            $response['content'] .="예약을 확인해주세요. ";
            // 조선호텔
            if($this->cgroup == "josunhotel") {
                $response['content'] .=$_date." ".$_week." ".$_time.", ".$r_data['department_name']." ".$r_data['doctor_name']." 맞으신가요?";
            } else {
                $response['content'] .=$_date." ".$_week." ".$_time.", ".$r_data['department_name']." ".$r_data['doctor_name']." 선생님 맞으신가요?";
            }
        }

        $r_data['step'] = 'confirm';
        $r_data['next_status'] = $next_status;
        $r_data['content'] = $response['content'];
        if(isset($r_data['time_range'])) unset($r_data['time_range']);

        $response['r_data'] = $r_data;
        return $response;
    }

    private function getCheckIsPrevDateTime($r_data) {
        $today = date("Y-m-d");
        $time = date("H:i");

        $result = $response = array();
        $result['result'] = false;

        if(isset($r_data['temp_sys_date']) && $r_data['temp_sys_date'] < $today) {
            $response['next_status'] = $r_data['next_status'];
            $response['content'] = "죄송합니다. 오늘 이전의 날짜로는 예약이 불가능합니다. 다른 날짜를 말씀해주세요.";
            $r_data['content'] = $response['content'];
            $response['r_data'] = $r_data;
            $result['result'] = true;
            $result['response'] = $response;
        } else if($r_data['temp_sys_date'] == $today && (isset($r_data['temp_sys_time']) && $r_data['temp_sys_time'] < $time)) {
            $response['next_status'] = $r_data['next_status'];
            $response['content'] = "죄송합니다. 지금보다 이전의 시간으로는 예약이 불가능합니다. 다른 시간을 말씀해주세요.";
            $r_data['content'] = $response['content'];
            $response['r_data'] = $r_data;
            $result['result'] = true;
            $result['response'] = $response;
        }
        return $result;
    }

    private function getCheckTimeRange($r_data) {
        $result = $response = array();
        $result['result'] = false;
        if(isset($r_data['time_range']) && is_array($r_data['time_range'])) {
            $_times = array();
            $_nTime = strtotime($r_data['time_range']['time']);
            foreach($r_data['times'] as $time) {
                if($r_data['time_range']['order'] == "before" && strtotime($time) > $_nTime) continue;
                else if($r_data['time_range']['order'] == "after" && strtotime($time) < $_nTime) continue;
                $_times[] = $time;
            }
            $ableTimes = $this->getNearestTimes($r_data['time_range']['time'], $_times, 'time');

            if(count($ableTimes) > 0) {
                $_timesKorean = "";
                foreach($ableTimes as $time) {
                    $_timesKorean .=$this->getTimeKorean($time).", ";
                }
                $_timesKorean = rtrim($_timesKorean, ", ");
                $response['content'] .=" 예약 가능한 시간은 ".$_timesKorean."입니다.";
            } else {
                $response['content'] = "죄송합니다. 예약 가능한 시간이 없습니다. 다른 시간을 말씀해주세요.";
            }

            $result['result'] = true;
        }

        $next_status = array('action'=>'recognize');
        $response['next_status'] = $next_status;
        $r_data['step'] = 'time';
        $r_data['next_status'] = $next_status;
        $r_data['content'] = $response['content'];
        $response['r_data'] = $r_data;
        $result['response'] = $response;
        return $result;
    }

    private function getRenewDateTime($r_data) {
        // 가능 시간대 문의 체크
        $checkTimeRange = $this->getCheckTimeRange($r_data);
        if($checkTimeRange['result'] == true) {
            $response = $checkTimeRange['response'];
        } else {
            $checkDateTime = $this->getCheckIsPrevDateTime($r_data);
            if($checkDateTime['result'] == true) {
                $response = $checkDateTime['response'];
            } else {
                if($r_data['sys_date'] && $r_data['temp_sys_date'] && $r_data['sys_date'] != $r_data['temp_sys_date']) {
                    $r_data['sys_date'] = $r_data['temp_sys_date'];
                    $r_data['sys_week'] = $r_data['temp_sys_week'];
                    $r_data['step'] = "date";
                    $response = $this->getReserveScheduleDate($r_data);
                } else if($r_data['intentName'] == "시스템-날짜시간변경") {
                    preg_match("/날|날짜|요일|예약일|시간/iu", $this->msg, $_match);

                    if($_match[0] != "시간") {
                        if(count($r_data['dates']) > 2) {
                            // 주어진 날짜를 제외한 다른 예약 가능 날짜 제시
                            $idx = array_search($r_data['sys_date'], $r_data['dates']);
                            $_dates = $r_data['dates'];
                            array_splice($_dates, $idx, 1);
                            $ableDates = $this->getNearestTimes($r_data['sys_date'], $_dates, 'date');

                            $r_data['able_weeks'] = array();
                            $_datesKorean = "";
                            foreach($ableDates as $date) {
                                $_week = $this->getWeekKorean($date)."요일";
                                $r_data['able_weeks'][$_week] = $date;
                                $_datesKorean .=$this->getDateKorean($date, 'no_year', 'with_week').", ";
                            }
                            $_datesKorean = rtrim($_datesKorean, ", ");
                            $response['next_status'] = $r_data['next_status'];
                            $response['content'] = $_datesKorean."에 예약 가능하십니다. 원하시는 예약 날짜를 말씀해주세요.";
                            $r_data['content'] = $response['content'];
                            $r_data['step'] = 'date';
                            $response['r_data'] = $r_data;
                        } else {
                            // 다른 예약 가능날짜 없을 경우
                            $response['next_status'] = $r_data['next_status'];
                            $_datesKorean = $this->getDateKorean($r_data['sys_date'], 'no_year', 'with_week').", ";
                            $response['content'] = "죄송합니다. 예약 가능한 날짜는 ".$_datesKorean." 밖에 없습니다. 예약 시간을 말씀해주세요.";
                            $r_data['content'] = $response['content'];
                            $r_data['step'] = 'time';
                            $response['r_data'] = $r_data;
                        }
                    } else {
                        $response['next_status'] = $r_data['next_status'];
                        $response['content'] = "원하시는 예약 시간을 말씀해주세요.";
                        $r_data['content'] = $response['content'];
                        $response['r_data'] = $r_data;
                    }
                } else {
                    $response['next_status'] = $r_data['next_status'];
                    $response['content'] = $r_data['content'];
                    $response['r_data'] = $r_data;
                }
            }
        }
        return $response;
    }

    private function getRenewRequest($r_data) {
        // 가능 시간대 문의 체크
        $checkTimeRange = $this->getCheckTimeRange($r_data);
        if($checkTimeRange['result'] == true) {
            $response = $checkTimeRange['response'];
        } else {
            if($r_data['temp_sys_date'] || $r_data['temp_sys_time']) {
                $checkDateTime = $this->getCheckIsPrevDateTime($r_data);
                if($checkDateTime['result'] == true) {
                    $response = $checkDateTime['response'];
                } else {
                    preg_match("/날|날짜|요일|예약일|시간/iu", $this->msg, $_match);

                    if($_match[0] != "시간" || ($r_data['temp_sys_date'] && $r_data['sys_date'] != $r_data['temp_sys_date'])) {
                        // 날짜 파싱
                        $r_data['sys_date'] = $r_data['temp_sys_date'] ? $r_data['temp_sys_date'] : "";
                        $r_data['sys_week'] = $r_data['temp_sys_week'] ? $r_data['temp_sys_week'] : "";
                        $r_data['sys_time'] = $r_data['temp_sys_time'] ? $r_data['temp_sys_time'] : "";

                        if($r_data['sys_date']) {
                            // 날짜 정보가 있다면
                            $r_data['step'] = 'date';
                            $response = $this->getReserveScheduleDate($r_data);
                        } else {
                            // 예약 날짜 물어보기
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "몇일로 바꾸시겠어요?";

                            $r_data['step'] = 'date';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        }
                    } else if($_match[0] == "시간" || ($r_data['temp_sys_time'] && $r_data['sys_time'] != $r_data['temp_sys_time'])) {
                        $aDT = explode(":", $r_data['temp_sys_time']);
                        if ($aDT[0] == "" || (int)$aDT[0] == 0) {
                            $r_data['sys_time'] = "";
                            $next_status = array('action'=>'recognize');
                            $response['next_status'] = $next_status;
                            $response['content'] = "몇 시로 바꾸시겠어요? ";

                            $r_data['step'] = 'time';
                            $r_data['next_status'] = $next_status;
                            $r_data['content'] = $response['content'];
                            $response['r_data'] = $r_data;
                        } else {
                            $r_data['step'] = 'time';
                            $r_data['sys_time'] = $r_data['temp_sys_time'];
                            $response = $this->getReserveScheduleDate($r_data);
                        }
                    }
                }

            } else {
                // 예약 날짜 물어보기
                $next_status = array('action'=>'recognize');
                $response['next_status'] = $next_status;
                $response['content'] = "변경하실 날짜와 시간을 말씀해주세요.";

                $r_data['step'] = 'date';
                $r_data['next_status'] = $next_status;
                $r_data['content'] = $response['content'];
                $response['r_data'] = $r_data;
            }
        }
        return $response;
    }

    private function getReserveSend($r_data) {
        global $m, $table, $chatbot;

        $result = array();

        // 예약 정보 전송
        $this->api_data['postParam']['bot_id'] = $this->botid;

        if($r_data['reserve_idx'] && ($r_data['action'] == "modify" || $r_data['action'] == "cancel")) {
            $this->api_data['postParam']['mode'] = "get_reserve_".$r_data['action'];
            $this->api_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
        } else if($r_data['action'] == "search") {
            $this->api_data['postParam']['mode'] = "get_reserve_search";
        } else {
            $this->api_data['postParam']['mode'] = "get_reserve_submit";
        }

        $this->api_data['postParam']['name'] = $r_data['uname'];
        $this->api_data['postParam']['phone'] = $r_data['uphone'];

        if($r_data['action'] != "search" && $r_data['action'] != "cancel") {
            $this->api_data['postParam']['branch_idx'] = $r_data['branch_idx'];
            $this->api_data['postParam']['branch_name'] = $r_data['branch_name'];
            $this->api_data['postParam']['department_idx'] = $r_data['department_idx'];
            $this->api_data['postParam']['department_name'] = $r_data['department_name'];
            $this->api_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
            $this->api_data['postParam']['doctor_name'] = $r_data['doctor_name'];
            $this->api_data['postParam']['date'] = $r_data['sys_date'];
            $this->api_data['postParam']['time'] = $r_data['sys_time'];
        }

        $apiResult = $chatbot->getReserveAPI($this->api_data);
        return $apiResult;
    }
}
?>