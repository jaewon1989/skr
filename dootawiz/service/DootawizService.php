<?php
require_once 'modules/chatbot/lib/guzzle7/vendor/autoload.php';
require_once 'dootawiz/service/dto/TtsPreListeningDTO.php';
require_once 'dootawiz/repository/mapper/DootawizMapper.php';

use Guzzle7Http\Exception\GuzzleException;
use Guzzle7Http\Utils;
use Guzzle7Http\Client;

class DootawizService
{
    private $_isError = true;
    private $_successCode = 200;
    private $_dootawizMapper;
    private $_headerByJson = 'Content-Type: application/json; charset=utf-8';
    private $_defaultVolume = '1';
    private $_defaultTimeout = 2.0;

    public function __construct()
    {
        global $DB_CONNECT;
        $this->_dootawizMapper = new DootawizMapper($DB_CONNECT);
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function getTtsPreListeningUrl(DootawizParamsModel $dootawizParams)
    {
        $botSettingInfo = $this->_dootawizMapper->getBotSettingByBotUid($dootawizParams->botUid)->fetch_assoc();

        $endPoint = 'http://211.37.148.176:8091';
        $path = 'naver' === $botSettingInfo['tts_vendor'] ? '/tts/naver' : '';

        $requestResult = $this->_callRequest($endPoint . $path,
            $this->_makeTtsPreListeningRequest(TtsPreListeningDTO::of([
                'bot_id' => $dootawizParams->botUid,
                'speaker' => $botSettingInfo['tts_audio'],
                'ment' => $dootawizParams->ttsMsg,
                'volume' => $this->_defaultVolume,
                'speed' => $dootawizParams->ttsSpeed,
                'pitch' => $botSettingInfo['tts_pitch']
            ])));

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $requestResult : $endPoint . '/tts/file/' . $requestResult
        ];
    }

    private function _proxy($resource)
    {
        $guzzleClient = new Client([
            'timeout' => $this->_defaultTimeout,
            'verify' => false
        ]);

        try {
            $response = $guzzleClient->get($resource);
            $audioData = $response->getBody();
            $contentType = $response->getHeaderLine('Content-Type');

            header("Content-Type: $contentType");
            $apiResult = $audioData;
        } catch (GuzzleException $guzzleException) {
            $apiResult = $guzzleException->getMessage();
        }

        return $apiResult;
    }

    /**
     * @throws JsonException
     */
    private function _makeTtsPreListeningRequest(TtsPreListeningDTO $ttsPreListeningDTO): array
    {
        return [
            'headers' => Utils::headersFromLines([$this->_headerByJson]),
            'body' => json_encode($ttsPreListeningDTO->toArray(), JSON_THROW_ON_ERROR)
        ];
    }

    private function _callRequest($apiUrl, $request)
    {
        $apiResult = '';
        $guzzleClient = new Client([
            'timeout' => $this->_defaultTimeout,
            'verify' => false
        ]);

        try {
            $response = $guzzleClient->post($apiUrl, $request);
            if ($this->_successCode === $response->getStatusCode()) {
                $responseBody = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                if (empty($responseBody['msg'])) {
                    $this->_isError = false;
                    $apiResult = $responseBody['path'];
                } else {
                    $apiResult = $responseBody['msg'];
                }
            }
        } catch (GuzzleException $guzzleException) {
            $apiResult = $guzzleException->getMessage();
        }

        return $apiResult;
    }

}