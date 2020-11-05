<?php

namespace PrintNode\Handler;

use PrintNode\Api\CurlInterface;
use PrintNode\Api\HandlerInterface;
use PrintNode\Api\HandlerRequestInterface;
use PrintNode\Api\ResponseInterface;
use PrintNode\HandlerException;
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


    public function run(HandlerRequestInterface $request): ResponseInterface
    {
        $this->checkExtensionLoaded();

        $curl = \curl_init($request->getUri());

        \curl_setopt_array($curl, $this->buildOptions($request) + CurlInterface::DEFAULT_OPTIONS);

        $request->setTimestamp(\microtime(true));
        $result = \curl_exec($curl);
        $responseTimestamp = \microtime(true);

        $request->setActualHeaders((string)\curl_getinfo($curl, \CURLINFO_HEADER_OUT));

        if (false === $result) {
            $errorCode = \curl_errno($curl);

            if ($errorCode && isset(CurlInterface::EXCEPTIONS[$errorCode])) {
                $message = CurlInterface::EXCEPTIONS[$errorCode] . ' ';
            } else {
                $message = null;
            }

            $message .= \curl_error($curl);

            throw new HandlerException($message, $errorCode, $request);
            // if CURLOPT_RETURNTRANSFER = false or PUT has been sent (PUT has no response payload) then result=true
        } elseif (true === $result) {
            $result = '';
        }

        $code = \curl_getinfo($curl, \CURLINFO_RESPONSE_CODE);
        $responseHeaderSize = \curl_getinfo($curl, \CURLINFO_HEADER_SIZE);
        \curl_close($curl);

        $response = $this->getResponse($result, $code, $responseHeaderSize);
        $response->setTimestamp($responseTimestamp);

        return $response;
    }

    private function getResponse(string $result, int $code, int $headerSize): ResponseInterface
    {
        $headers = \substr($result, 0, $headerSize);
        $responseBody = \substr($result, $headerSize);
        $headersArr = \explode("\r\n", $this->filterResponseHeaders($headers));

        // @see https://tools.ietf.org/html/rfc7230#section-3.1.2
        if (0 === \strpos($headersArr[0], \strtoupper('http'))) {
            list($protocolVersion, $code, $reasonPhrase) = \explode(' ', \substr($headersArr[0], 5));
            unset($headersArr[0]);
        }

        $response = $this->createResponse((int)$code, $reasonPhrase ?? '');
        $response->setActualHeaders($headers);
        $response->setBody($responseBody);

        return $response;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, $reasonPhrase);
    }

    /**
     * Filter response headers by cutting off exceeded header parts.
     *
     * @param string $headers
     *
     * @return string
     */
    private function filterResponseHeaders(string $headers): string
    {
        // cURL automatically decodes chunked-messages
        $headers = \preg_replace("/Transfer-Encoding:\s*chunked\\r\\n/i", '', $headers);

        // cURL automatically handles Proxy rewrites, remove the "HTTP/v.v 200 Connection established" string
        $headers = \preg_replace(
            '/HTTP\/\d.\d\s*200\s*Connection\s*established\\r\\n\\r\\n/',
            '',
            $headers
        );

        // Eliminate multiple HTTP responses.
        $headers = \preg_split('/(?:\r?\n){2}/m', \trim($headers, "\r\n"));

        return \array_pop($headers);
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

        $headers = [];

        if (\in_array($request->getMethod(), [
            HandlerRequestInterface::METHOD_POST,
            HandlerRequestInterface::METHOD_PUT,
            HandlerRequestInterface::METHOD_PATCH,
            HandlerRequestInterface::METHOD_DELETE,
            HandlerRequestInterface::METHOD_OPTIONS
        ], true)
        ) {
            $headers[] = 'Content-Type: application/json';
            $options[\CURLOPT_POST] = true;
            $options[\CURLOPT_POSTFIELDS] = $request->getBody() ?? '';
        }

        $options[\CURLOPT_HTTPHEADER] = array_merge(
            $headers,
            $this->childAuth,
            $this->headers
        );

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
