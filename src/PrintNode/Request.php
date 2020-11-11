<?php

namespace PrintNode;

use BadMethodCallException;
use PrintNode\Api\CredentialsInterface;
use PrintNode\Api\HandlerInterface;
use PrintNode\Api\HandlerRequestInterface;
use PrintNode\Api\ResponseInterface;
use PrintNode\Entity\Account;
use PrintNode\Entity\ApiKey;
use PrintNode\Entity\ChildAccount;
use PrintNode\Entity\ClientDownload;
use PrintNode\Entity\Computer;
use PrintNode\Entity\Download;
use PrintNode\Entity\Printer;
use PrintNode\Entity\PrintJob;
use PrintNode\Entity\PrintJobState;
use PrintNode\Entity\Tag;
use PrintNode\Entity\Whoami;
use PrintNode\Exception\HTTPException;
use PrintNode\Exception\InvalidArgumentException;
use PrintNode\Exception\RuntimeException;
use PrintNode\Message\ServerRequest;

use function array_merge;
use function array_shift;
use function base64_encode;
use function count;
use function get_class;
use function gettype;
use function http_build_query;
use function is_numeric;
use function is_string;
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
     * @var array
     */
    private $additionalHeaders = [];

    /**
     * API url to use with the client
     *
     * @var string
     * */
    private $apiUrl = 'https://api.printnode.com';

    /**
     * If set, requests that the responding JSON should be formatted for human
     * readability
     * @var bool
     */
    public $prettyJSON = false;

    /**
     * If set, requests that this API request should not be logged.
     * @var bool
     */
    public $dontLog = false;

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
        ClientDownload::class => '/download/clients',
        Download::class => '/download/client',
        ApiKey::class => '/account/apikey',
        ChildAccount::class => '/account',
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
        'Clients' => ClientDownload::class,
        'ClientDownloads' => ClientDownload::class,
        'Downloads' => Download::class,
        'ApiKeys' => ApiKey::class,
        'Account' => ChildAccount::class,
        'Tags' => Tag::class,
        'Whoami' => Whoami::class,
        'Computers' => Computer::class,
        'Printers' => Printer::class,
        'PrintJobs' => PrintJob::class,
    ];

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

        $this->setOffset($offset);
        $this->setLimit($limit);
    }

    /**
     * @param string $uri
     * @throws InvalidArgumentException
     */
    public function setApiUri(string $uri)
    {
        $uriComponents = \parse_url($uri);

        if (empty($uriComponents['scheme']) || empty($uriComponents['host'])) {
            throw new InvalidArgumentException('Invalid API uri provided. Uri must have scheme and host.');
        }

        $this->apiUrl = $uri;
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
        $this->additionalHeaders = $headers;
    }

    public function setTimeout(int $timeout)
    {
        $this->getHandler()->setTimeout($timeout);
    }

    /**
     * Delete an ApiKey for a child account
     *
     * @param string $apiKey
     *
     * @return ResponseInterface
     * */
    public function deleteApiKey(string $apiKey): ResponseInterface
    {
        $endPointUrl = $this->getEndPointUrl(ApiKey::class);

        return $this->callDelete("{$endPointUrl}/{$apiKey}");
    }

    /**
     * Makes a view apikey request to the PrintNode API, returning the api key
     * string.
     *
     * @param string $apiKeyName The label of the API Key to be returned
     *
     * @return string
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewApiKey(string $apiKeyName)
    {
        $endPointUrl = $this->getEndPointUrl(ApiKey::class);
        $url = "{$endPointUrl}/{$apiKeyName}";

        $response = $this->callGet($url);
        $this->validateResponse($response);

        return $response->getBody();

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
        $endPointUrl = $this->getEndPointUrl(Tag::class);

        return $this->callDelete("{$endPointUrl}/{$tag}");
    }

    /**
     * Delete a child account
     *
     * @return ResponseInterface
     */
    public function deleteAccount(): ResponseInterface
    {
        $endPointUrl = $this->getEndPointUrl(Account::class);

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
        $endPointUrl = "/client/key/{$uuid}?edition={$edition}&version={$version}";

        return $this->callGet($endPointUrl);
    }

    /**
     * Creates a new child account by making a POST to the PrintNode API,
     * returning a ChildAccount object.
     *
     * @param \PrintNode\Entity\Account $account A populated account object
     * @param array                     $apiKeys (Optional) An array of API keys that should be created on the new account
     * @param array                     $tags    (Optional) An array of Tags that should be created on the new account
     *
     * @return \PrintNode\Entity\ChildAccount|Entity
     */
    public function createChildAccount(\PrintNode\Entity\Account $account, array $apiKeys = [], array $tags = [])
    {
        $newChildAccount = new \PrintNode\Entity\ChildAccount($this);

        $newChildAccount->Account = $account;

        foreach ($apiKeys as $apiKey) {
            $newChildAccount->ApiKeys[] = $apiKey;
        }

        foreach ($tags as $tagName => $tag) {
            $newChildAccount->Tags[$tagName] = $tag;
        }

        $response = $this->post($newChildAccount);

        return $response->getDecodedAsEntity($this,ChildAccount::class);
    }

    /**
     * Creates a new printJob by making a POST to the PrintNode API, returning
     * the new print job id, or optionally a printjob object is argument 2 is
     * populated
     *
     * @param \PrintNode\Entity\PrintJob $printJob     A populated printjob object
     * @param bool                       $returnObject (Optional) If set to true, returns the full printjob data by making a second request
     *
     * @return \PrintNode\Api\ResponseInterface|\PrintNode\Entity\PrintJob|Entity
     * @throws \PrintNode\Exception\HTTPException
     */
    public function createPrintJob(PrintJob $printJob, bool $returnObject = false)
    {
        $response = $this->post($printJob);

        if ($response->getStatusCode() !== ResponseInterface::CODE_CREATED) {
            throw new HTTPException($response->getStatusCode(), $response->getReasonPhrase());
        }

        if ($returnObject) {
            $printJobId = trim($response->getBody());
            $jobs = $this->viewPrintJobs(0, 1, $printJobId);

            return $jobs[0] ?? null;
        }

        return $response;
    }

    /**
     * Makes a printjobState request to the PrintNode API, returning an array
     * of all the printjobs that have been processed on the active account.
     * If a 'set' string or array of print job ids is passed to the third
     * argument, the returned array will be filtered to just those print jobs.
     * Returned is an array of states in an array keyed by the print job id.
     *
     * @param int          $offset      (Optional) The start index for the records the API should return
     * @param int          $limit       (Optional) The number of records the API should return
     * @param string|array $printJobSet (Optional) 'set' string or array of print job ids to which the response should
     *                                  be limited
     *
     * @return Entity[]
     * @throws \PrintNode\Exception\HTTPException
     */
    public function getPrintJobStates($offset = 0, $limit = 500, $printJobSet = null)
    {
        $endPointUrl = $this->getEndPointUrl(PrintJob::class);
        $url = '/states';

        if (isset($printJobSet)) {
            $url = sprintf('/%s/states', $this->setImplode($printJobSet));
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        $response = $this->callGet($endPointUrl . $url);
        $this->validateResponse($response);

        return $response->getDecodedAsEntity($this, PrintJobState::class);
    }

    /**
     * Makes a whoami request to the PrintNode API, returning a Whoami entity
     * describing the active account.
     *
     * @return \PrintNode\Entity\Whoami|Entity
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewWhoAmI()
    {
        $response = $this->callGet($this->getEndPointUrl(Whoami::class));
        $this->validateResponse($response);

        return $response->getDecodedAsEntity($this, \PrintNode\Entity\Whoami::class);
    }

    /**
     * Makes a printers request to the PrintNode API, returning an array
     * of all the registered printers on the active account.
     * If a 'set' string or array of printer ids is passed to the third
     * argument, the returned array will be filtered to just those printers.
     * If a 'set' string or array of computer ids is passed to the fourth
     * argument, the returned array will be filtered to just those printers.
     * Both arguments can be combined to return only certain printers on certain
     * computers
     *
     * @param int          $offset      (Optional) The start index for the records the API should return
     * @param int          $limit       (Optional) The number of records the API should return
     * @param string|array $printerSet  (Optional) 'set' string or array of printer ids to which the response should be limited
     * @param string|array $computerSet (Optional) 'set' string or array of computer ids to which the response should be limited
     *
     * @return \PrintNode\Entity[]|Printer[]
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewPrinters($offset = 0, $limit = 500, $printerSet = null, $computerSet = null)
    {
        $endPointComputer = $this->getEndPointUrl(Computer::class);
        $endPointPrinters = $this->getEndPointUrl(Printer::class);
        $url = $endPointPrinters;

        if (isset($computerSet) && isset($printerSet)){
            $url = sprintf('%s/%s/printers/%s', $endPointComputer, $this->setImplode($computerSet), $this->setImplode($printerSet));
        } else if (isset($printerSet)) {
            $url = sprintf('%s/%s', $endPointPrinters, $this->setImplode($printerSet));
        } else if (isset($computerSet)) {
            $url = sprintf('%s/%s/printers', $endPointComputer, $this->setImplode($computerSet));
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        $response = $this->callGet($url);
        $this->validateResponse($response);

        return $response->getDecodedAsEntity($this, \PrintNode\Entity\Printer::class);
    }

    /**
     * Makes a viewScales request to the PrintNode API, returning an array
     * of all the scales filtered to the
     * If a 'set' string or array of print job ids is passed to the third
     * argument, the returned array will be filtered to just those print jobs.
     * Returned is an array of statuses in an array keyed by the print job id.
     *
     * @param string $computerId   The id of the computer on which to view scales
     * @param string $deviceName   (Optional) The name of the scale device
     * @param string $deviceNumber (Optional) The id of the scale device
     *
     * @return array
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewScales($computerId, $deviceName = null, $deviceNumber = null)
    {
        $endPointComputer = $this->getEndPointUrl(Computer::class);
        $url = sprintf('/%s/%s/scales', $endPointComputer, $computerId);

        if (isset($deviceName, $deviceNumber)) {
            $url .= sprintf('/%s/%s', $deviceName, $deviceNumber);
        } else if (isset($deviceName)) {
            $url .= sprintf('/%s', $deviceName);
        }

        $response = $this->callGet($url);
        $this->validateResponse($response);

        //TODO get []'deviceNum' ?
        return $response->getDecodedAsEntity($this, \PrintNode\Entity\Scale::class);
    }

    /**
     * Makes a printjobs request to the PrintNode API, returning an array
     * of all the printjobs that have been processed on the active account.
     * If a 'set' string or array of print job ids is passed to the third
     * argument, the returned array will be filtered to just those print jobs.
     * If a 'set' string or array of printer ids is passed to the fourth
     * argument, the returned array will be filtered to just those printers.
     * Both arguments can be combined to return only certain print jobs on
     * certain printers
     *
     * @param int          $offset      (Optional) The start index for the records the API should return
     * @param int          $limit       (Optional) The number of records the API should return
     * @param string|array $printJobSet (Optional) 'set' string or array of print job ids to which the response should be limited
     * @param string|array $printerSet  (Optional) 'set' string or array of printer ids to which the response should be limited
     *
     * @return \PrintNode\Entity[]
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewPrintJobs($offset = 0, $limit = 500, $printJobSet = null, $printerSet = null)
    {
        $endPointPrinters = $this->getEndPointUrl(Printer::class);
        $endPointPrintJobs = $this->getEndPointUrl(PrintJob::class);
        $url = $endPointPrintJobs;

        if (isset($printerSet, $printJobSet)){
            $url = sprintf('%s/%s/printjobs/%s', $endPointPrinters, $this->setImplode($printerSet), $this->setImplode($printJobSet));
        } else if (isset($printJobSet)) {
            $url = sprintf('%s/%s', $endPointPrintJobs, $this->setImplode($printJobSet));
        } else if (isset($printerSet)) {
            $url = sprintf('%s/%s/printjobs', $endPointPrinters, $this->setImplode($printerSet));
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        $response = $this->callGet($url);
        $this->validateResponse($response);

        return $response->getDecodedAsEntity($this, \PrintNode\Entity\PrintJob::class);

    }

    /**
     * @deprecated Use viewPrintJobs()
     * Gets PrintJobs relative to a printer.
     *
     * @param string $printerIdSet set of printer ids to find PrintJobs relative to
     * @param string $printJobId   OPTIONAL: set of PrintJob ids relative to the printer.
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getPrintJobsByPrinters(string $printerIdSet, $printJobId = null)
    {
        return $this->viewPrintJobs(0, 100, $printJobId, $printerIdSet);
    }

    /**
     * @deprecated Use viewScales($computerId, $deviceName = null, $deviceNumber = null)
     * Gets scales relative to a computer.
     *
     * @param string $computerId id of computer to find scales
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getScales($computerId)
    {
        return $this->viewScales($computerId);
    }

    /**
     * @deprecated Use viewPrinters($offset = 0, $limit = 500, $printerSet = null, $computerSet = null)
     * Get printers relative to a computer.
     *
     * @param string $computerIdSet set of computer ids to find printers relative to
     * @param string $printerIdSet  OPTIONAL: set of printer ids only found in the set of computers.
     *
     * @return Entity[]
     * @throws \Exception
     */
    public function getPrintersByComputers(string $computerIdSet, $printerIdSet = null)
    {
        return $this->viewPrinters($offset = 0, $limit = 500, $printerIdSet, $computerIdSet);
    }

    /**
     * PATCH (update) the specified entity
     *
     * @param Entity|\PrintNode\Entity\ApiKey|\PrintNode\Entity\Tag|\PrintNode\Entity\Account $entity
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
     * @throws \PrintNode\Exception\HandlerException
     */
    public function put(Entity $entity, ...$args): ResponseInterface
    {
        $endPointUrl = $this->getEndPointUrl(get_class($entity));
        $endPointUrl .= implode('/', $args);

        $request = $this->createRequest(
            $this->wrapWithApiUrl($endPointUrl),
            HandlerRequestInterface::METHOD_PUT
        );
        $request->setBody(json_encode($entity));

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

        return $response->getDecodedAsEntity($this, $entityName);
    }

    /**
     * Returns the authentication headers for a request
     *
     * @return array
     */
    public function getCredentialHeader()
    {
        $headers = [];
        $authHeader = $this->getAuthHeader();

        if ($authHeader) {
            $headers = [
                'Authorization: ' . $authHeader,
            ];

            if (!$this->credentials->apiKey && $this->credentials->username) {
                $headers[] = 'X-Auth-With-Account-Credentials: true';
            }
        }

        if ($childAccountHeaders = $this->getChildAccountHeaders()) {
            $headers = array_merge($headers, $childAccountHeaders);
        }

        return $headers;
    }

    public function getAuthHeader()
    {
        if (trim($this->credentials->apiKey)) {
            return 'Basic ' . base64_encode($this->credentials->apiKey . ':');
        }

        if (trim($this->credentials->username)) {
            return 'Basic ' . base64_encode($this->credentials->username . ':' . $this->credentials->password);
        }

        return null;
    }

    /**
     * Returns child account authentication request headers, if set
     *
     * @return array
     */
    public function getChildAccountHeaders()
    {

        if ($this->credentials->childAccountEmail) {

            return array(sprintf('X-Child-Account-By-Email: %s',
                $this->credentials->childAccountEmail));

        } else if ($this->credentials->childAccountCreatorRef) {

            return array(sprintf('X-Child-Account-By-CreatorRef: %s',
                $this->credentials->childAccountCreatorRef));

        } else if ($this->credentials->childAccountId) {

            return array(sprintf('X-Child-Account-By-Id: %s',
                $this->credentials->childAccountId));

        }

        return [];
    }

    /**
     * @return array
     */
    public function getAdditionalHeaders()
    {
        // The 'Expect:' header is required to prevent the server responding
        // with '100 Continue' headers
        $headers = [
            'Expect:',
        ];

        if ($this->prettyJSON) {
            $headers[] = 'X-Pretty: 1';
        }

        if ($this->dontLog) {
            $headers[] = 'X-Dont-Log: 1';
        }

        return array_merge($headers, $this->additionalHeaders);
    }

    /**
     * Appends the offset and limit arguments to a given api endpoint url
     *
     * @param string $url (Optional) The url to which any limits or offsets will be applied
     * @param int $offset (Optional) The offset to apply to the url
     * @param int $limit (Optional) The limit to apply to the url
     * @return string
     * @throws \PrintNode\Exception\InvalidArgumentException
     */
    private function applyLimitOffsetToUrl($url, $offset, $limit)
    {

        if (!is_numeric($offset)) {
            throw new \PrintNode\Exception\InvalidArgumentException('Offset must be a number');
        }

        if (!is_numeric($limit)) {
            throw new \PrintNode\Exception\InvalidArgumentException('Limit must be a number');
        }

        if ($offset < 0) {
            throw new \PrintNode\Exception\InvalidArgumentException('Offset cannot be negative');
        }

        if ($limit < 1) {
            throw new \PrintNode\Exception\InvalidArgumentException('Limit must be greater than zero');
        }

        return sprintf('%s?offset=%s&limit=%s', $url, $offset, $limit);

    }

    private function setImplode($set)
    {

        if (is_array($set)) {
            return implode(',', $set);
        }

        return $set;

    }

    private function createRequest(string $apiUrl, string $method = null): HandlerRequestInterface
    {
        return new ServerRequest($apiUrl, $method);
    }

    private function wrapWithApiUrl(string $requestUri): string
    {
        return $this->apiUrl . $requestUri;
    }

    private function callGet($endPointUrl): ResponseInterface
    {
        $request = $this->createRequest($this->wrapWithApiUrl($endPointUrl));

        return $this->getHandler()->run($request);
    }

    private function callDelete($endPointUrl): ResponseInterface
    {
        if (!empty($this->getChildAccountHeaders())) {
            throw new RuntimeException(
                sprintf(
                    'No child authentication set - cannot call DELETE'
                )
            );
        }

        $request = $this->createRequest(
            $this->wrapWithApiUrl($endPointUrl),
            HandlerRequestInterface::METHOD_DELETE
        );

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


        $request = $this->createRequest($this->wrapWithApiUrl($endPointUrl), $method);
        $request->setBody(!$postData ? json_encode($entity) : $postData);

        return $this->getHandler()->run($request);
    }

    /**
     * @param ResponseInterface $response
     * @throws HTTPException
     */
    private function validateResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== ResponseInterface::CODE_OK) {
            throw new HTTPException($response->getStatusCode(), $response->getReasonPhrase());
        }
    }

    /**
     * @return \PrintNode\Api\HandlerInterface
     */
    private function getHandler()
    {
        if ($this->handler === null) {
            $this->handler = new $this->handlerClassname($this);
        }

        return $this->handler;
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getEndPointUrl(string $entityName): string
    {
        if (!isset($this->endPointUrls[$entityName])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing endPointUrl for entityName "%s"',
                    $entityName
                )
            );
        }

        return $this->endPointUrls[$entityName];
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

        $entityClass = $this->methodNameEntityMap[$matchesArray[1]] ?? null;

        if (!$entityClass) {
            throw new BadMethodCallException(
                sprintf(
                    '%s is missing an methodNameMap entry for %s',
                    get_class($this),
                    $methodName
                )
            );
        }

        return $entityClass;
    }

    /**
     * @deprecated
     * Apply offset and limit to a end point URL.
     *
     * @param string $endPointUrl
     *
     * @return string
     */
    private function applyOffsetLimit(string $endPointUrl): string
    {
        $endPointUrlArray = parse_url($endPointUrl);

        if (!isset($endPointUrlArray['query'])) {
            $endPointUrlArray['query'] = null;
        }

        parse_str($endPointUrlArray['query'], $queryStringArray);

        $queryStringArray['offset'] = $this->offset;
        $queryStringArray['limit'] = min(max(1, $this->limit), 500);
        $endPointUrlArray['query'] = http_build_query($queryStringArray, null, '&');

        return $this->buildUrl($endPointUrlArray);
    }

    private function buildUrl(array $parts): string
    {
        $url = (isset($parts['scheme'])) ? $parts['scheme'] . '://' : '';
        $url .= (isset($parts['host'])) ? (string)($parts['host']) : '';

        $url .= (isset($parts['port']) ? ':' . $parts['port'] : '');
        $url .= ($parts['path'] ?? '');
        $url .= (isset($parts['query']) ? '?' . $parts['query'] : '');

        return $url;
    }
}
