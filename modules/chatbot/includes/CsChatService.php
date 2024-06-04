<?php

use NexusDTOSpace\NexusDto;
use NexusSpace\NexusService;

class CsChatService {

    /**
     * @var NexusService
     */
    private $nexusService;

    public function __construct(NexusService $nexusService) {
        $this->nexusService = $nexusService;
    }

    public function nexusAssignAPI($data): array {
        $_log = "[".date("Y-m-d H:i:s")."] request ".getRemoteIP()." url: /".$data['endPoint'].'/'.$data['path']." data: ".json_encode($data);
        $this->setLog($_log);

        try {
            $response = $this->nexusService->nexusInvokeAPI(
                (new NexusDto())->of(
                    $data['endPoint'],
                    $data['path'],
                    $data['secretKey'],
                    $data['mode'],
                    $_SESSION['tenant'],
                    $data['botId'] ?? '0',
                    $data['roomToken'] ?? '',
                    $data['userInfo'] ?? '',
                    $data['messages'] ?? [],
                    $data['time'] ?? '',
                    $data['messageId'] ?? '',
                    $data['csId'] ?? ''
                )
            );
            $responseCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody(), true);

            $_log = "[".date("Y-m-d H:i:s")."] response ".getRemoteIP()." url: /".$data['endPoint'].'/'.$data['path']." code: ".$responseCode." response: ".json_encode($responseBody);
            $this->setLog($_log);

            if($responseCode === 200 && $responseBody) {
                if($responseBody['code'] == "200") {
                    $result = ['result'=>true];
                } else {
                    $result = ['result'=>false, 'msg'=>$responseBody['message']];
                }
            } else {
                $result = ['result'=>false, 'msg'=>'chat server not connected.'];
            }

        }catch (Exception $e) {
            $result = ['result'=>false, 'msg'=>$e->getMessage()];
        }

        return $result;
    }

    private function setLog($log) {
        $logFile = $_SERVER['DOCUMENT_ROOT']."/_tmp/log/cschat_log_".date("Y-m-d").".txt";
        file_put_contents($logFile, $log."\n", FILE_APPEND);
    }
}