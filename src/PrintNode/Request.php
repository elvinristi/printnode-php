<?php

namespace PrintNode;

use BadMethodCallException;
use InvalidArgumentException;
use PrintNode\Api\CredentialsInterface;
use PrintNode\Api\HandlerInterface;
use PrintNode\Api\HandlerRequestInterface;
use PrintNode\Api\ResponseInterface;
use PrintNode\Entities\Account;
use PrintNode\Entities\ApiKey;
use PrintNode\Entities\Client;
use PrintNode\Entities\Computer;
use PrintNode\Entities\Download;
use PrintNode\Entities\Printer;
use PrintNode\Entities\PrintJob;
use PrintNode\Entities\Tag;
use PrintNode\Entities\Whoami;
use PrintNode\Message\ServerRequest;
use RuntimeException;

use function array_shift;
use function count;
use function func_get_args;
use function get_class;
use function gettype;
use function http_build_query;
use function is_string;
use function json_decode;
use function method_exists;
use function max;
use function min;
use function parse_str;
use function parse_url;
use function sprintf;

/**
 * HTTP request object.
 *
 * @method Computer[] getComputers() getComputers(int $computerId)
 * @method Printer[] getPrinters() getPrinters(int $printerId)
 * @method PrintJob[] getPrintJobs() getPrintJobs(int $printJobId)
 */
class Request
{
    /**
     * Credentials to use when communicating with API
     *
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     * API url to use with the client
     *
     * @var string
     * */
    private $apiUrl = 'https://api.printnode.com';

    /**
     * Header for child authentication
     *
     * @var string[]
     * */
    private $childauth = [];

    /**
     * Offset query argument on GET requests
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Limit query argument on GET requests
     *
     * @var int
     */
    private $limit = 10;

    /**
     * Map entity names to API URLs
     *
     * @var string[]
     */
    private $endPointUrls = [
        Client::class => '/download/clients',
        Download::class => '/download/client',
        ApiKey::class => '/account/apikey',
        Account::class => '/account',
        Tag::class => '/account/tag',
        Whoami::class => '/whoami',
        Computer::class => '/computers',
        Printer::class => '/printers',
        PrintJob::class => '/printjobs',
    ];

    /**
     * Map method names used by __call to entity names
     *
     * @var string[]
     */
    private $methodNameEntityMap = [
        'Clients' => Client::class,
        'Downloads' => Download::class,
        'ApiKeys' => ApiKey::class,
        'Account' => Account::class,
        'Tags' => Tag::class,
        'Whoami' => Whoami::class,
        'Computers' => Computer::class,
        'Printers' => Printer::class,
        'PrintJobs' => PrintJob::class,
    ];

    /**
     * @var string[]
     */
    private $endPointFullUrls = [];

    /**
     * @var string
     */
    private $handlerClassname = \PrintNode\Handler\Curl::class;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * Request constructor.
     *
     * @param CredentialsInterface $credentials
     * @param array                  $endPointUrls
     * @param array                  $methodNameEntityMap
     * @param int                    $offset
     * @param int                    $limit
     */
    public function __construct(
        CredentialsInterface $credentials,
        array $endPointUrls = [],
        array $methodNameEntityMap = [],
        int $offset = 0,
        int $limit = 10
    ) {
        $this->credentials = $credentials;

        if ($endPointUrls) {
            $this->endPointUrls = $endPointUrls;
        }

        if ($methodNameEntityMap) {
            $this->methodNameEntityMap = $methodNameEntityMap;
        }

        $this->makeEndPointUrls();
        $this->setOffset($offset);
        $this->setLimit($limit);
    }

    /**
     * Set the offset for GET requests
     *
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        if (!is_numeric($offset)) {
            throw new InvalidArgumentException('offset should be a number');
        }
        $this->offset = $offset;
    }

    /**
     * Set the limit for GET requests
     *
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        if (!is_numeric($limit)) {
            throw new InvalidArgumentException('limit should be a number');
        }
        $this->limit = $limit;
    }

    public function setHeaders(array $headers)
    {
        $this->getHandler()->setHeaders($headers);
    }

    public function setTimeout(int $timeout)
    {
        $this->getHandler()->setTimeout($timeout);
    }

    public function setChildAccountById($id)
    {
        $this->childauth = ['X-Child-Account-By-Id: ' . $id];
    }

    public function setChildAccountByEmail($email)
    {
        $this->childauth = ['X-Child-Account-By-Email: ' . $email];
    }

    public function setChildAccountByCreatorRef($creatorRef)
    {
        $this->childauth = ['X-Child-Account-By-CreatorRef: ' . $creatorRef];
    }

    /**
     * Delete an ApiKey for a child account
     *
     * @param string $apikey
     *
     * @return ResponseInterface
     * */
    public function deleteApiKey(string $apikey): ResponseInterface
    {
        $endPointUrl = "{$this->apiUrl}/account/apikey/{$apikey}";

        return $this->callDelete($endPointUrl);
    }

    /**
     * Delete a tag for a child account
     *
     * @param string $tag
     *
     * @return ResponseInterface
     * */
    public function deleteTag(string $tag): ResponseInterface
    {
        $endPointUrl = "{$this->apiUrl}/account/tag/{$tag}";

        return $this->callDelete($endPointUrl);
    }

    /**
     * Delete a child account
     * MUST have $this->childauth set to run.
     *
     * @return ResponseInterface
     */
    public function deleteAccount(): ResponseInterface
    {
        $endPointUrl = "{$this->apiUrl}/account/";

        return $this->callDelete($endPointUrl);
    }

    /**
     * Returns a client key.
     *
     * @param string $uuid
     * @param string $edition
     * @param string $version
     *
     * @return ResponseInterface
     * */
    public function getClientKey(string $uuid, string $edition, string  $version): ResponseInterface
    {
        $endPointUrl = "{$this->apiUrl}/client/key/{$uuid}?edition={$edition}&version={$version}";

        return $this->callGet($endPointUrl);
    }

    /**
     * Gets print job states.
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getPrintJobStates()
    {
        $arguments = func_get_args();

        if (count($arguments) > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintJobsStates.'
                )
            );
        }

        $endPointUrl = "{$this->apiUrl}/printjobs/";

        if (count($arguments) === 0) {
            $endPointUrl .= 'states/';
        } else {
            $printJobId = array_shift($arguments);
            $endPointUrl .= $printJobId . '/states/';
        }

        $response = $this->callGet($endPointUrl);
        $this->validateResponse($response);

        return $this->responseToEntity($response, \PrintNode\Entities\State::class);
    }

    /**
     * Gets PrintJobs relative to a printer.
     *
     * @param string $printerIdSet set of printer ids to find PrintJobs relative to
     * @param string $printJobId   OPTIONAL: set of PrintJob ids relative to the printer.
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getPrintJobsByPrinters(string $printerIdSet, ...$args)
    {
        if (count($args) > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintJobsByPrinters.'
                )
            );
        }

        $endPointUrl = "{$this->apiUrl}/printers/{$printerIdSet}/printjobs/";
        $endPointUrl .= implode('', $args);

        $response = $this->callGet($endPointUrl);
        $this->validateResponse($response);

        return $this->responseToEntity($response, \PrintNode\Entities\PrintJob::class);
    }

    /**
     * Gets scales relative to a computer.
     *
     * @param string $computerId id of computer to find scales
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getScales($computerId)
    {
        $endPointUrl = "{$this->apiUrl}/computer/{$computerId}/scales";
        $response = $this->callGet($endPointUrl);
        $this->validateResponse($response);

        return $this->responseToEntity($response, \PrintNode\Entities\Scale::class);
    }

    /**
     * Get printers relative to a computer.
     *
     * @param string $computerIdSet set of computer ids to find printers relative to
     * @param string $printerIdSet  OPTIONAL: set of printer ids only found in the set of computers.
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getPrintersByComputers(string $computerIdSet, ...$args)
    {
        if (count($args) > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintersByComputers.'
                )
            );
        }

        $endPointUrl = "{$this->apiUrl}/computers/{$computerIdSet}/printers/";
        $endPointUrl .= implode('', $args);

        $response = $this->callGet($endPointUrl);
        $this->validateResponse($response);

        return $this->responseToEntity($response, \PrintNode\Entities\Printer::class);
    }

    /**
     * PATCH (update) the specified entity
     *
     * @param Entity|\PrintNode\Entities\ApiKey|\PrintNode\Entities\Tag|\PrintNode\Entities\Account $entity
     *
     * @return ResponseInterface
     * */
    public function patch(Entity $entity): ResponseInterface
    {
        return $this->sendEntityData($entity, HandlerRequestInterface::METHOD_PATCH);
    }

    /**
     * POST (create) the specified entity
     *
     * @param Entity $entity
     *
     * @return ResponseInterface
     */
    public function post(Entity $entity): ResponseInterface
    {
        return $this->sendEntityData($entity, HandlerRequestInterface::METHOD_POST);
    }

    /**
     * PUT (update) the specified entity
     *
     * @param Entity $entity
     * @param array  $args
     *
     * @return ResponseInterface
     */
    public function put(Entity $entity, ...$args): ResponseInterface
    {
        $endPointUrl = $this->getEndPointUrl(get_class($entity));
        $endPointUrl .= implode('/', $args);

        $request = $this->createRequest($endPointUrl, HandlerRequestInterface::METHOD_PUT);
        $request->setBody($entity->__toString());

        return $this->getHandler()->run($request);
    }

    /**
     * DELETE (delete) the specified entity
     *
     * @param Entity $entity
     *
     * @return ResponseInterface
     */
    public function delete(Entity $entity): ResponseInterface
    {
        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        if (method_exists($entity, 'endPointUrlArg')) {
            $endPointUrl .= '/' . $entity->endPointUrlArg();
        }

        return $this->callDelete($endPointUrl);
    }

    /**
     * Map method names getComputers, getPrinters and getPrintJobs to entities
     *
     * @param string $methodName
     * @param array  $arguments
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function __call($methodName, $arguments)
    {
        $entityName = $this->getEntityName($methodName);
        $endPointUrl = $this->getEndPointUrl($entityName);

        if (count($arguments) > 0) {
            $argument = array_shift($arguments);

            if (!is_string($argument)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid argument type passed to %s. Expecting a string got %s',
                        $methodName,
                        gettype($argument)
                    )
                );
            }

            $endPointUrl = sprintf('%s/%s', $endPointUrl, $argument);
        } else {
            $endPointUrl = sprintf('%s', $endPointUrl);
        }

        $response = $this->callGet($endPointUrl);
        $this->validateResponse($response);

        return $this->responseToEntity($response, $entityName);
    }

    private function createRequest(string $apiUrl, string $method = null): HandlerRequestInterface
    {
        return new ServerRequest($apiUrl, $method);
    }

    private function callGet($endPointUrl): ResponseInterface
    {
        $request = $this->createRequest(
            $this->applyOffsetLimit($endPointUrl)
        );

        return $this->getHandler()->run($request);
    }

    private function callDelete($endPointUrl): ResponseInterface
    {
        if (!isset($this->childauth)) {
            throw new RuntimeException(
                sprintf(
                    'No child authentication set - cannot call DELETE'
                )
            );
        }

        $request = $this->createRequest(
            $endPointUrl,
            HandlerRequestInterface::METHOD_DELETE
        );


        $this->getHandler()->setChildAuth($this->childauth);

        return $this->getHandler()->run($request);
    }

    private function sendEntityData(Entity $entity, string $method): ResponseInterface
    {
        $postData = null;
        $postDataMethod = null;
        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        switch ($method) {
            case HandlerRequestInterface::METHOD_PATCH:
                $postDataMethod = 'formatForPatch';

                break;

            case HandlerRequestInterface::METHOD_POST:
                $postDataMethod = 'formatForPost';
                break;
        }

        if (method_exists($entity, 'endPointUrlArg')) {
            $endPointUrl .= '/' . $entity->endPointUrlArg();
        }

        if ($postDataMethod !== null && method_exists($entity, $postDataMethod)) {
            $postData = $entity->$postDataMethod();
        }


        $request = $this->createRequest($endPointUrl, $method);
        $request->setBody(!$postData ? $entity->__toString() : $postData);

        return $this->getHandler()->run($request);
    }

    /**
     * @param ResponseInterface $response
     * @param string              $class
     *
     * @return \PrintNode\Entity[]
     * @throws \Exception
     */
    private function responseToEntity(ResponseInterface $response, string $class)
    {
        return Entity::makeFromResponse($class, json_decode($response->getBody(), false));
    }

    /**
     * @param ResponseInterface $response
     * @throws RuntimeException
     */
    private function validateResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== ResponseInterface::CODE_OK) {
            throw new RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }
    }

    /**
     * @return \PrintNode\Api\HandlerInterface
     */
    private function getHandler()
    {
        if ($this->handler === null) {
            $this->handler = new $this->handlerClassname();
            $this->handler->setCredentials($this->credentials);
        }

        return $this->handler;
    }

    /**
     * Assign API EndPoint URL from an entity name
     *
     */
    private function makeEndPointUrls()
    {
        $endPointFullUrls = [];

        foreach ($this->methodNameEntityMap as $classes) {
            $endPointFullUrls[$classes] = $this->apiUrl . $this->endPointUrls[$classes];
        }

        $this->endPointFullUrls = $endPointFullUrls;
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getEndPointUrl(string $entityName): string
    {
        if (!isset($this->endPointFullUrls[$entityName])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing endPointUrl for entityName "%s"',
                    $entityName
                )
            );
        }

        return $this->endPointFullUrls[$entityName];
    }

    /**
     * Get entity name from __call method name
     *
     * @param string $methodName
     *
     * @return string
     */
    private function getEntityName(string $methodName): string
    {
        if (!preg_match('/^get(.+)$/', $methodName, $matchesArray)) {
            throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist',
                    get_class($this),
                    $methodName
                )
            );
        }
        if (!isset($this->methodNameEntityMap[$matchesArray[1]])) {
            throw new BadMethodCallException(
                sprintf(
                    '%s is missing an methodNameMap entry for %s',
                    get_class($this),
                    $methodName
                )
            );
        }

        return $this->methodNameEntityMap[$matchesArray[1]];
    }

    /**
     * Apply offset and limit to a end point URL.
     *
     * @param mixed $endPointUrl
     *
     * @return string
     */
    private function applyOffsetLimit($endPointUrl)
    {
        $endPointUrlArray = parse_url($endPointUrl);

        if (!isset($endPointUrlArray['query'])) {
            $endPointUrlArray['query'] = null;
        }

        parse_str($endPointUrlArray['query'], $queryStringArray);

        $queryStringArray['offset'] = $this->offset;
        $queryStringArray['limit'] = min(max(1, $this->limit), 500);
        $endPointUrlArray['query'] = http_build_query($queryStringArray, null, '&');

        $endPointUrl = (isset($endPointUrlArray['scheme'])) ? $endPointUrlArray['scheme'] . "://" : '';
        $endPointUrl .= (isset($endPointUrlArray['host'])) ? (string)($endPointUrlArray['host']) : '';
        $endPointUrl .= (isset($endPointUrlArray['port'])) ? ":{$endPointUrlArray['port']}" : '';
        $endPointUrl .= (isset($endPointUrlArray['path'])) ? (string)($endPointUrlArray['path']) : '';
        $endPointUrl .= (isset($endPointUrlArray['query'])) ? "?{$endPointUrlArray['query']}" : '';

        return $endPointUrl;
    }
}
