<?php

namespace PrintNode\Handler;

use PrintNode\Api\CurlInterface;
use PrintNode\Api\HandlerInterface;
use PrintNode\Api\HandlerRequestInterface;
use PrintNode\Response;
use RuntimeException;
use function array_pop;
use function curl_close;
use function curl_init;
use function curl_setopt;
use function explode;
use function extension_loaded;
use function max;
use function sprintf;

class Curl implements CurlInterface, HandlerInterface
{
    /**
     * Custom headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $childAuth = [];

    /**
     * @var int
     */
    private $timeout = 5;

    /**
     * @var string
     */
    private $credentials;

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function setCredentials(string $credentials)
    {
        $this->credentials = $credentials;
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    public function setChildAuth(array $childAuthHeader)
    {
        $this->childAuth = $childAuthHeader;
    }


    public function run(HandlerRequestInterface $request, string $data = null): Response
    {
        $this->checkExtensionLoaded();

        $curl = \curl_init($request->getUri());

        \curl_setopt_array($curl, $this->buildOptions($request) + CurlInterface::DEFAULT_OPTIONS);
    }

    /**
     * Execute cURL request using the specified API EndPoint
     *
     * @param mixed $curlHandle
     * @param mixed $endPointUrl
     *
     * @return Response
     */
    private function curlExec($curlHandle, $endPointUrl)
    {
        curl_setopt($curlHandle, CURLOPT_URL, $endPointUrl);
        $response = @curl_exec($curlHandle);

        if ($response === false) {
            throw new RuntimeException(
                sprintf(
                    'cURL Error (%d): %s',
                    curl_errno($curlHandle),
                    curl_error($curlHandle)
                )
            );
        }
        curl_close($curlHandle);
        $response_parts = explode("\r\n\r\n", $response);
        $content = array_pop($response_parts);
        $headers = explode("\r\n", array_pop($response_parts));

        return new Response($endPointUrl, $content, $headers);
    }

    /**
     * Make a GET request using cURL
     *
     * @param mixed $endPointUrl
     *
     * @return Response
     */
    private function curlGet($endPointUrl)
    {
        $curlHandle = $this->curlInit();
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_merge(
            $this->childAuth,
            $this->headers
        ));

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

    private function curlDelete($endPointUrl)
    {
        $curlHandle = $this->curlInit();
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->childauth);

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

    /**
     * Make a POST/PUT/DELETE request using cURL
     *
     * @return Response
     */
    private function curlSend($httpMethod, string $data, $endPointUrl, ...$arguments)
    {
        $curlHandle = $this->curlInit();

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_merge(
                ['Content-Type: application/json'],
                $this->childAuth,
                $this->headers)
        );

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

    /**
     * Initialise cURL with the options we need
     * to communicate successfully with API URL.
     *
     * @param void
     *
     * @return resource|false
     */
    private function curlInit()
    {
        $this->checkExtensionLoaded();

        $curlHandle = curl_init();

        \curl_setopt_array($curlHandle, $this->buildOptions());

        return $curlHandle;
    }

    private function buildOptions(HandlerRequestInterface $request): array
    {
        /** @noinspection CurlSslServerSpoofingInspection */
        $options = [
            $this->getMethodOption($request->getMethod()) => $request->getMethod(),
            \CURLOPT_ENCODING => 'gzip,deflate',
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_VERBOSE => false,
            \CURLOPT_FOLLOWLOCATION => true,
            \CURLOPT_HEADER => true,
            \CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_SSL_VERIFYHOST option 2 is to check the existence of a common name and also verify that it matches
            // the hostname provided.
            // 0 to not check the names.
            // Support for value 1 removed in cURL 7.28.1.
            \CURLOPT_SSL_VERIFYHOST => 2,
            \CURLOPT_TIMEOUT => max($this->timeout, 5),
            $this->getAuthorizationOptions($request)
        ];


        if (\in_array($request->getMethod(), [HandlerRequestInterface::METHOD_POST,
                                              HandlerRequestInterface::METHOD_PUT,
                                              HandlerRequestInterface::METHOD_PATCH,
                                              HandlerRequestInterface::METHOD_DELETE,
                                              HandlerRequestInterface::METHOD_OPTIONS])
        ) {
            $options[\CURLOPT_POST] = true;
            $body = $request->getBody();
        }

        return $options;
    }

    /**
     * Applies basic authorization in cURL way appropriate request's authorization header
     *
     * @param \PrintNode\Api\HandlerRequestInterface $request
     *
     * @return array
     */
    private function getAuthorizationOptions(HandlerRequestInterface $request): array
    {
        $options = [];

        if (!empty($this->credentials)) {
            $options[\CURLOPT_HTTPAUTH] = \CURLAUTH_BASIC;
            $options[\CURLOPT_USERPWD] = $this->credentials;
        }

        return $options;
    }

    /**
     * Returns cURL method type option appropriate request's method
     *
     * @param string $method
     *
     * @return int
     */
    private function getMethodOption(string $method): int
    {
        switch ($method) {
            case HandlerRequestInterface::METHOD_GET:
                $methodOption = \CURLOPT_HTTPGET;
                break;
            case HandlerRequestInterface::METHOD_POST:
                $methodOption = \CURLOPT_POST;
                break;
            default:
                $methodOption = \CURLOPT_CUSTOMREQUEST;
        }

        return $methodOption;
    }

    private function checkExtensionLoaded()
    {
        if (extension_loaded('curl') === false) {
            throw new RuntimeException('cURL extension is not loaded');
        }
    }
}
