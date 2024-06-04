<?php

namespace NexusSpace;
global $g;
require_once $g['path_module'].'chatbot/lib/guzzle7/vendor/autoload.php';
//require_once $g['path_module'].'chatbot/lib/monolog/vendor/autoload.php';

use Exception;
use NexusDTOSpace\NexusDto;
use Guzzle7Http\Exception\GuzzleException;
use Guzzle7Http\Utils;
use Guzzle7Http\Client;
/*use Guzzle7Http\HandlerStack;
use Guzzle7Http\Middleware;
use Guzzle7Http\MessageFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;*/

class NexusService {

    public $chatbot;
    public $chatAPI;
    public $client;
    //public $stack;

    public function __construct() {

        /*$logger = new Logger('guzzle_logger');
        $logger->pushHandler(new StreamHandler($_SERVER['DOCUMENT_ROOT']."/_tmp/log/guzzle.log", Logger::DEBUG)); // 로그 파일 경로 설정
        $stack = HandlerStack::create();
        $this->stack = $stack->push(Middleware::log($logger, new MessageFormatter()));*/
        $this->chatbot = $GLOBALS['chatbot'];
        $this->chatAPI = $this->chatbot->csChatAPIs;
    }

    public function nexusInvokeAPI(NexusDto $dto)
    {
        $this->client = new Client([
            //'handler' => $this->stack,
            'base_uri' => $dto->getEndPoint(),
            'timeout'  => 2.0,
            'verify'   => false
        ]);

        $request['headers'] = $this->getHeader($dto);
        $request['body'] = json_encode($dto->getAllProperties());

        try {
            return $this->client->request('POST', $dto->getEndPoint().'/'.$dto->getPath(), $request);
        } catch (GuzzleException $e) {
            //error_log($e->getMessage());
            throw new Exception($e);
        }
    }

    private function getHeader(NexusDto $dto): array
    {
        $header = ["Content-Type: application/json; charset=utf-8"];
        $header[] = "secretKey: " . $dto->getKey();

        if("reference" !== $dto->getPath()){
            $header[] = "X-Bottalks-Key: " . $dto->getKey();
        }

        return Utils::headersFromLines($header);
    }
}

namespace NexusDTOSpace;

use Guzzle7Http\Utils;

class NexusDto {
    private $endPoint;
    private $path;
    private $secretKey;
    private $mode;
    private $tenant;
    private $botid;
    private $roomToken;
    private $userInfo;
    private $messages = [];
    private $time;
    private $messageId;
    private $cs_id;

    public function getKey() {
        return $this->secretKey;
    }

    public function getEndPoint() {
        return $this->endPoint;
    }

    public function getPath() {
        return $this->path;
    }

    public function getAllProperties(): array {
        $allProperties = get_object_vars($this);
        unset($allProperties['endPoint']);
        return $allProperties;
    }

    public function of($endPoint, $path, $secretKey, $mode, $tenant, $botId, $roomToken, $userInfo, array $messages, $time, $messageId, $csId): NexusDto
    {
        $this->endPoint = $endPoint;
        $this->path = $path;
        $this->secretKey = $secretKey;
        $this->mode = $mode;
        $this->tenant = $tenant;
        $this->botid = $botId;
        $this->roomToken = $roomToken;
        $this->userInfo = $userInfo;
        $this->messages = $messages;
        $this->time = $time;
        $this->messageId = $messageId;
        $this->cs_id = $csId;

        return $this;
    }
}

?>