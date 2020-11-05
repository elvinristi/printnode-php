<?php

namespace PrintNode\Api;

interface ResponseInterface extends MessageInterface
{
    /**
     * Response Status Codes
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6
     */

    /**
     * 1xx (Informational): The request was received, continuing process
     * =============================================================================================
     */

    /**
     * 100 Continue
     *
     * The 100 (Continue) status code indicates that the initial part of a
     * request has been received and has not yet been rejected by the
     * server.  The server intends to send a final response after the
     * request has been fully received and acted upon.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.2.1
     * @since 1.0.0
     */
    const CODE_CONTINUE = 100;

    /**
     * 101 Switching Protocols
     *
     * The 101 (Switching Protocols) status code indicates that the server
     * understands and is willing to comply with the client's request, via
     * the Upgrade header field (Section 6.7 of [RFC7230]), for a change in
     * the application protocol being used on this connection.  The server
     * MUST generate an Upgrade header field in the response that indicates
     * which protocol(s) will be switched to immediately after the empty
     * line that terminates the 101 response.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.2.2
     * @since 1.0.0
     */
    const CODE_SWITCHING_PROTOCOLS = 101;

    /**
     * 2xx (Successful): The request was successfully received, understood, and accepted
     * =============================================================================================
     */

    /**
     * 200 OK
     *
     * Standard response for successful HTTP requests. The actual response will depend on the request method used.
     * In a GET request, the response will contain an entity corresponding to the requested resource.
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.1
     * @since 0.1.0
     */
    const CODE_OK = 200;

    /**
     * 201 Created
     *
     * The 201 (Created) status code indicates that the request has been
     * fulfilled and has resulted in one or more new resources being
     * created.  The primary resource created by the request is identified
     * by either a Location header field in the response or, if no Location
     * field is received, by the effective request URI.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.2
     * @since 1.0.0
     */
    const CODE_CREATED = 201;

    /**
     * 202 Accepted
     *
     * The 202 (Accepted) status code indicates that the request has been
     * accepted for processing, but the processing has not been completed.
     * The request might or might not eventually be acted upon, as it might
     * be disallowed when processing actually takes place.  There is no
     * facility in HTTP for re-sending a status code from an asynchronous
     * operation.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.3
     * @since 1.0.0
     */
    const CODE_ACCEPTED = 202;

    /**
     * 203 Non-Authoritative Information
     *
     * The 203 (Non-Authoritative Information) status code indicates that
     * the request was successful but the enclosed payload has been modified
     * from that of the origin server's 200 (OK) response by a transforming
     * proxy (Section 5.7.2 of [RFC7230]).  This status code allows the
     * proxy to notify recipients when a transformation has been applied,
     * since that knowledge might impact later decisions regarding the
     * content.  For example, future cache validation requests for the
     * content might only be applicable along the same request path (through
     * the same proxies).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.4
     * @since 1.0.0
     */
    const CODE_NA_INFORMATION = 203;

    /**
     * 204 No Content
     *
     * The 204 (No Content) status code indicates that the server has
     * successfully fulfilled the request and that there is no additional
     * content to send in the response payload body.  Metadata in the
     * response header fields refer to the target resource and its selected
     * representation after the requested action was applied.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.5
     * @since 1.0.0
     */
    const CODE_NO_CONTENT = 204;

    /**
     * 205 Reset Content
     *
     * The 205 (Reset Content) status code indicates that the server has
     * fulfilled the request and desires that the user agent reset the
     * "document view", which caused the request to be sent, to its original
     * state as received from the origin server.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.6
     * @since 1.0.0
     */
    const CODE_RESET_CONTENT = 205;

    /**
     * 206 Partial Content
     *
     * The 206 (Partial Content) status code indicates that the server is
     * successfully fulfilling a range request for the target resource by
     * transferring one or more parts of the selected representation that
     * correspond to the satisfiable ranges found in the request's Range
     * header field (Section 3.1 of [RFC7233]).
     *
     * @see RFC7233
     * @link https://tools.ietf.org/html/rfc7233#section-4.1
     * @since 1.0.0
     */
    const CODE_PARTIAL_CONTENT = 206;

    /**
     * 3xx (Redirection): Further action needs to be taken in order to complete the request
     * =============================================================================================
     */

    /**
     * 300 Multiple Choices
     *
     * The 300 (Multiple Choices) status code indicates that the target
     * resource has more than one representation, each with its own more
     * specific identifier, and information about the alternatives is being
     * provided so that the user (or user agent) can select a preferred
     * representation by redirecting its request to one or more of those
     * identifiers.  In other words, the server desires that the user agent
     * engage in reactive negotiation to select the most appropriate
     * representation(s) for its needs (Section 3.4).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.1
     * @since 1.0.0
     */
    const CODE_MULTIPLE_CHOICES = 300;

    /**
     * 301 Moved Permanently
     *
     * The 301 (Moved Permanently) status code indicates that the target
     * resource has been assigned a new permanent URI and any future
     * references to this resource ought to use one of the enclosed URIs.
     * Clients with link-editing capabilities ought to automatically re-link
     * references to the effective request URI to one or more of the new
     * references sent by the server, where possible.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.2
     * @since 1.0.0
     */
    const CODE_MOVED_PERMANENTLY = 301;

    /**
     * 302 Found
     *
     * The 302 (Found) status code indicates that the target resource
     * resides temporarily under a different URI.  Since the redirection
     * might be altered on occasion, the client ought to continue to use the
     * effective request URI for future requests.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.3
     * @since 1.0.0
     */
    const CODE_FOUND = 302;

    /**
     * 303 See Other
     *
     * The 303 (See Other) status code indicates that the server is
     * redirecting the user agent to a different resource, as indicated by a
     * URI in the Location header field, which is intended to provide an
     * indirect response to the original request.  A user agent can perform
     * a retrieval request targeting that URI (a GET or HEAD request if
     * using HTTP), which might also be redirected, and present the eventual
     * result as an answer to the original request.  Note that the new URI
     * in the Location header field is not considered equivalent to the
     * effective request URI.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.4
     * @since 1.0.0
     */
    const CODE_SEE_OTHER = 303;

    /**
     * 304 Not Modified
     *
     * The 304 (Not Modified) status code indicates that a conditional GET
     * or HEAD request has been received and would have resulted in a 200
     * (OK) response if it were not for the fact that the condition
     * evaluated to false.  In other words, there is no need for the server
     * to transfer a representation of the target resource because the
     * request indicates that the client, which made the request
     *
     * @see RFC7232
     * @link https://tools.ietf.org/html/rfc7232#section-4.1
     * @since 1.0.0
     */
    const CODE_NOT_MODIFIED = 304;

    /**
     * 305 Use Proxy
     *
     * The 305 (Use Proxy) status code was defined in a previous version of
     * this specification and is now deprecated (Appendix B).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.5
     * @since 1.0.0
     */
    const CODE_USE_PROXY = 305;

    /**
     * 307 Temporary Redirect
     *
     * The 307 (Temporary Redirect) status code indicates that the target
     * resource resides temporarily under a different URI and the user agent
     * MUST NOT change the request method if it performs an automatic
     * redirection to that URI.  Since the redirection can change over time,
     * the client ought to continue using the original effective request URI
     * for future requests.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.4.7
     * @since 1.0.0
     */
    const CODE_TEMPORARY_REDIRECT = 307;

    /**
     * 4xx (Client Error): The request contains bad syntax or cannot be fulfilled
     * =============================================================================================
     */

    /**
     * 400 Bad Request
     *
     * The 400 (Bad Request) status code indicates that the server cannot or
     * will not process the request due to something that is perceived to be
     * a client error (e.g., malformed request syntax, invalid request
     * message framing, or deceptive request routing).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.1
     * @since 0.1.0
     */
    const CODE_BAD_REQUEST = 400;

    /**
     * 401 Unauthorized
     *
     * The 401 (Unauthorized) status code indicates that the request has not
     * been applied because it lacks valid authentication credentials for
     * the target resource.  The server generating a 401 response MUST send
     * a WWW-Authenticate header field (Section 4.1) containing at least one
     * challenge applicable to the target resource.
     *
     * @see RFC7235
     * @link https://tools.ietf.org/html/rfc7235#section-3.1
     * @since 0.1.0
     */
    const CODE_UNAUTHORIZED = 401;

    /**
     * 402 Payment Required
     *
     * The 402 (Payment Required) status code is reserved for future use.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.2
     * @since 1.0.0
     */
    const CODE_PAYMENT_REQUIRED = 402;

    /**
     * 403 Forbidden
     *
     * The 403 (Forbidden) status code indicates that the server understood
     * the request but refuses to authorize it.  A server that wishes to
     * make public why the request has been forbidden can describe that
     * reason in the response payload (if any).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.3
     * @since 1.0.0
     */
    const CODE_FORBIDDEN = 403;

    /**
     * 404 Not Found
     *
     * The 404 (Not Found) status code indicates that the origin server did
     * not find a current representation for the target resource or is not
     * willing to disclose that one exists.  A 404 status code does not
     * indicate whether this lack of representation is temporary or
     * permanent; the 410 (Gone) status code is preferred over 404 if the
     * origin server knows, presumably through some configurable means, that
     * the condition is likely to be permanent.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.4
     * @since 1.0.0
     */
    const CODE_NOT_FOUND = 404;

    /**
     * 405 Method Not Allowed
     *
     * The 405 (Method Not Allowed) status code indicates that the method
     * received in the request-line is known by the origin server but not
     * supported by the target resource.  The origin server MUST generate an
     * Allow header field in a 405 response containing a list of the target
     * resource's currently supported methods.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.5
     * @since 1.0.0
     */
    const CODE_METHOD_NOT_ALLOWED = 405;

    /**
     * 406 Not Acceptable
     *
     * The 406 (Not Acceptable) status code indicates that the target
     * resource does not have a current representation that would be
     * acceptable to the user agent, according to the proactive negotiation
     * header fields received in the request (Section 5.3), and the server
     * is unwilling to supply a default representation.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.6
     * @since 1.0.0
     */
    const CODE_NOT_ACCEPTABLE = 406;

    /**
     * 407 Proxy Authentication Required
     *
     * The 407 (Proxy Authentication Required) status code is similar to 401
     * (Unauthorized), but it indicates that the client needs to
     * authenticate itself in order to use a proxy.  The proxy MUST send a
     * Proxy-Authenticate header field (Section 4.3 of [RFC7235]) containing a challenge
     * applicable to that proxy for the target resource.  The client MAY
     * repeat the request with a new or replaced Proxy-Authorization header
     * field (Section 4.4 of [RFC7235]).
     *
     * @see RFC7235
     * @link https://tools.ietf.org/html/rfc7235#section-3.2
     * @since 1.0.0
     */
    const CODE_PROXY_AUTH_REQUIRED = 407;

    /**
     * 408 Request Timeout
     *
     * The 408 (Request Timeout) status code indicates that the server did
     * not receive a complete request message within the time that it was
     * prepared to wait.  A server SHOULD send the "close" connection option
     * (Section 6.1 of [RFC7230]) in the response, since 408 implies that
     * the server has decided to close the connection rather than continue
     * waiting.  If the client has an outstanding request in transit, the
     * client MAY repeat that request on a new connection.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.7
     * @since 1.0.0
     */
    const CODE_REQUEST_TIMEOUT = 408;

    /**
     * 409 Conflict
     *
     * The 409 (Conflict) status code indicates that the request could not
     * be completed due to a conflict with the current state of the target
     * resource.  This code is used in situations where the user might be
     * able to resolve the conflict and resubmit the request.  The server
     * SHOULD generate a payload that includes enough information for a user
     * to recognize the source of the conflict.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.8
     * @since 1.0.0
     */
    const CODE_CONFLICT = 409;

    /**
     *410 Gone
     *
     * The 410 (Gone) status code indicates that access to the target
     * resource is no longer available at the origin server and that this
     * condition is likely to be permanent.  If the origin server does not
     * know, or has no facility to determine, whether or not the condition
     * is permanent, the status code 404 (Not Found) ought to be used
     * instead.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.9
     * @since 1.0.0
     */
    const CODE_GONE = 410;

    /**
     * 411 Length Required
     *
     * The 411 (Length Required) status code indicates that the server
     * refuses to accept the request without a defined Content-Length
     * (Section 3.3.2 of [RFC7230]).  The client MAY repeat the request if
     * it adds a valid Content-Length header field containing the length of
     * the message body in the request message.
     *
     * @see RFC723
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.10
     * @since 1.0.0
     */
    const CODE_LENGTH_REQUIRED = 411;

    /**
     * 412 Precondition Failed
     *
     * The 412 (Precondition Failed) status code indicates that one or more
     * conditions given in the request header fields evaluated to false when
     * tested on the server.  This response code allows the client to place
     * preconditions on the current resource state (its current
     * representations and metadata) and, thus, prevent the request method
     * from being applied if the target resource is in an unexpected state.
     *
     * @see RFC7232
     * @link https://tools.ietf.org/html/rfc7232#section-4.2
     * @since 1.0.0
     */
    const CODE_PRECONDITION_FAILED = 412;

    /**
     * 413 Payload Too Large
     *
     * The 413 (Payload Too Large) status code indicates that the server is
     * refusing to process a request because the request payload is larger
     * than the server is willing or able to process.  The server MAY close
     * the connection to prevent the client from continuing the request.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.11
     * @since 1.0.0
     */
    const CODE_PAYLOAD_TOO_LARGE = 413;

    /**
     * 414 URI Too Long
     *
     * The 414 (URI Too Long) status code indicates that the server is
     * refusing to service the request because the request-target (Section
     * 5.3 of [RFC7230]) is longer than the server is willing to interpret.
     * This rare condition is only likely to occur when a client has
     * improperly converted a POST request to a GET request with long query
     * information, when the client has descended into a "black hole" of
     * redirection (e.g., a redirected URI prefix that points to a suffix of
     * itself) or when the server is under attack by a client attempting to
     * exploit potential security holes.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.12
     * @since 1.0.0
     */
    const CODE_URI_TOO_LONG = 414;

    /**
     * 415 Unsupported Media Type
     *
     * The 415 (Unsupported Media Type) status code indicates that the
     * origin server is refusing to service the request because the payload
     * is in a format not supported by this method on the target resource.
     * The format problem might be due to the request's indicated
     * Content-Type or Content-Encoding, or as a result of inspecting the
     * data directly.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.13
     * @since 1.0.0
     */
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * 416 Range Not Satisfiable
     *
     * The 416 (Range Not Satisfiable) status code indicates that none of
     * the ranges in the request's Range header field (Section 3.1 of [RFC7233]) overlap
     * the current extent of the selected resource or that the set of ranges
     * requested has been rejected due to invalid ranges or an excessive
     * request of small or overlapping ranges.
     *
     * @see RFC7233
     * @link https://tools.ietf.org/html/rfc7233#section-4.4
     * @since 1.0.0
     */
    const CODE_RANGE_NOT_SATISFIABLE = 416;

    /**
     * 417 Expectation Failed
     *
     * The 417 (Expectation Failed) status code indicates that the
     * expectation given in the request's Expect header field
     * (Section 5.1.1 of [RFC7231]) could not be met by at least one of the inbound
     * servers.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.14
     * @since 1.0.0
     */
    const CODE_EXPECTATION_FAILED = 417;

    /**
     * 426 Upgrade Required
     *
     * The 426 (Upgrade Required) status code indicates that the server
     * refuses to perform the request using the current protocol but might
     * be willing to do so after the client upgrades to a different
     * protocol.  The server MUST send an Upgrade header field in a 426
     * response to indicate the required protocol(s) (Section 6.7 of
     * [RFC7230]).
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.5.15
     * @since 1.0.0
     */
    const CODE_UPGRADE_REQUIRED = 426;

    /**
     * 5xx (Server Error): The server failed to fulfill an apparently valid request
     * =============================================================================================
     */

    /**
     * 500 Internal Server Error
     *
     * The 500 (Internal Server Error) status code indicates that the server
     * encountered an unexpected condition that prevented it from fulfilling
     * the request.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.1
     * @since 0.1.0
     */
    const CODE_INTERNAL_SERVER_ERROR = 500;

    /**
     * 501 Not Implemented
     *
     * The 501 (Not Implemented) status code indicates that the server does
     * not support the functionality required to fulfill the request.  This
     * is the appropriate response when the server does not recognize the
     * request method and is not capable of supporting it for any resource.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.2
     * @since 1.0.0
     */
    const CODE_NOT_IMPLEMENTED = 501;

    /**
     * 502 Bad Gateway
     *
     * The 502 (Bad Gateway) status code indicates that the server, while
     * acting as a gateway or proxy, received an invalid response from an
     * inbound server it accessed while attempting to fulfill the request.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.3
     * @since 1.0.0
     */
    const CODE_BAD_GATEWAY = 502;

    /**
     * 503 Service Unavailable
     *
     * The 503 (Service Unavailable) status code indicates that the server
     * is currently unable to handle the request due to a temporary overload
     * or scheduled maintenance, which will likely be alleviated after some
     * delay.  The server MAY send a Retry-After header field
     * (Section 7.1.3) to suggest an appropriate amount of time for the
     * client to wait before retrying the request.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.4
     * @since 1.0.0
     */
    const CODE_SERVICE_UNAVAILABLE = 503;

    /**
     * 504 Gateway Timeout
     *
     * The 504 (Gateway Timeout) status code indicates that the server,
     * while acting as a gateway or proxy, did not receive a timely response
     * from an upstream server it needed to access in order to complete the
     * request.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.5
     * @since 1.0.0
     */
    const CODE_GATEWAY_TIMEOUT = 504;

    /**
     * 505 HTTP Version Not Supported
     *
     * The 505 (HTTP Version Not Supported) status code indicates that the
     * server does not support, or refuses to support, the major version of
     * HTTP that was used in the request message.  The server is indicating
     * that it is unable or unwilling to complete the request using the same
     * major version as the client, as described in Section 2.6 of
     * [RFC7230], other than with this error message.  The server SHOULD
     * generate a representation for the 505 response that describes why
     * that version is not supported and what other protocols are supported
     * by that server.
     *
     * @see RFC7231
     * @link https://tools.ietf.org/html/rfc7231#section-6.6.6
     * @since 1.0.0
     */
    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * Further extensions to the request are required for the server to fulfil it.
     *
     * @see RFC2774
     * @link https://tools.ietf.org/html/rfc2774#section-7
     * @since 0.1.0
     */
    const CODE_NOT_EXTENDED = 510;

    /**
     * Unknown Error
     * The 520 error is used as a "catch-all response for when the origin server returns something unexpected",
     * listing connection resets, large headers, and empty or invalid responses as common triggers.
     *
     * @link https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     * @since 0.1.0
     */
    const CODE_UNKNOWN_ERROR = 520;

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode();

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * @return string
     */
    public function getReasonPhrase(): string;
}
