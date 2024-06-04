<?php

class InterfaceUtil
{
    /**
     * @throws JsonException
     */
    public static function chkInterfaceParams($params, $interfaceModeRequiredParams, $errorMsgs): void
    {
        try {
            if (!array_key_exists($params->mode, $interfaceModeRequiredParams)) {
                (new InterfaceUtil)->returnJsonEncodeBuilder(['error' => true, 'result' => $errorMsgs['mode']]);
            }

            foreach ($interfaceModeRequiredParams[$params->mode] as $requiredParam) {
                if (is_null($params->$requiredParam)) {
                    (new InterfaceUtil)->returnJsonEncodeBuilder(['error' => true, 'result' => $errorMsgs[$requiredParam]]);
                }
            }
        } catch (Exception $exception) {
            (new InterfaceUtil)->returnJsonEncodeBuilder(['error' => true, 'result' => $exception->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public static function returnJsonEncodeBuilder($chkParamsResult): void
    {
        echo json_encode([
            'error' => $chkParamsResult['error'],
            'result' => $chkParamsResult['result']
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}