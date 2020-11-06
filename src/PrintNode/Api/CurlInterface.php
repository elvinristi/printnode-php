<?php

namespace PrintNode\Api;

if (false === \defined('FILEINFO_MIME')) {
    \define('FILEINFO_MIME', 1040);
}

if (false === \defined('FILEINFO_MIME_TYPE')) {
    \define('FILEINFO_MIME_TYPE', 16);
}

if (false === \defined('FILEINFO_MIME_ENCODING')) {
    \define('FILEINFO_MIME_ENCODING', 1024);
}

interface CurlInterface
{
    /**
     * Default options for cURL
     * All descriptions is taken from http://www.php.net/manual/en/function.curl-setopt.php
     * @since 0.1.0
     */
    const DEFAULT_OPTIONS = [
        /**
         * TRUE to return the raw output when \CURLOPT_RETURNTRANSFER is used.
         * From PHP 5.1.3, this option has no effect: the raw output will always be returned
         * when \CURLOPT_RETURNTRANSFER is used.
         */
        //\CURLOPT_BINARYTRANSFER          => false,
        /**
         * TRUE to mark this as a new cookie "session".
         * It will force libcurl to ignore all cookies it is about to load that are "session cookies"
         * from the previous session.
         * By default, libcurl always stores and loads all cookies, independent if they are session cookies or not.
         * Session cookies are cookies without expiry date and they are meant to be alive and
         * existing for this "session" only.
         */
        \CURLOPT_COOKIESESSION           => false,
        /**
         * TRUE tells the library to perform all the required proxy authentication and connection setup,
         * but no data transfer.
         * This option is implemented for HTTP, SMTP and POP3.
         */
        \CURLOPT_CONNECT_ONLY            => false,
        /**
         * TRUE to convert Unix newlines to CRLF newlines on transfers.
         */
        \CURLOPT_CRLF                    => false,
        /**
         * TRUE to use a global DNS cache. This option is not thread-safe and is enabled by default.
         */
        \CURLOPT_DNS_USE_GLOBAL_CACHE    => true,
        /**
         * TRUE to fail verbosely if the HTTP code returned is greater than or equal to 400.
         * The default behavior is to return the page normally, ignoring the code.
         */
        \CURLOPT_FAILONERROR             => false,
        /**
         * TRUE to enable TLS false start.
         * Setting it to any value in array will reset lot of other options like return transfer and headers
         */
        //\CURLOPT_SSL_FALSESTART          => false,
        /**
         * TRUE to attempt to retrieve the modification date of the remote document.
         * This value can be retrieved using the CURLINFO_FILETIME option with curl_getinfo().
         */
        \CURLOPT_FILETIME                => false,
        /**
         * TRUE to force the connection to explicitly close when it has finished processing,
         * and not be pooled for reuse.
         */
        \CURLOPT_FORBID_REUSE            => false,
        /**
         * TRUE to force the use of a new connection instead of a cached one.
         */
        \CURLOPT_FRESH_CONNECT           => false,
        /**
         * TRUE to use EPRT (and LPRT) when doing active FTP downloads.
         * Use FALSE to disable EPRT and LPRT and use PORT only.
         */
        \CURLOPT_FTP_USE_EPRT            => false,
        /**
         * TRUE to first try an EPSV command for FTP transfers before reverting back to PASV.
         * Set to FALSE to disable EPSV.
         */
        \CURLOPT_FTP_USE_EPSV            => true,
        /**
         * TRUE to create missing directories
         * when an FTP operation encounters a path that currently doesn't exist.
         */
        \CURLOPT_FTP_CREATE_MISSING_DIRS => true,
        /**
         * TRUE to append to the remote file instead of overwriting it.
         */
        \CURLOPT_FTPAPPEND               => false,
        /**
         * TRUE to disable TCP's Nagle algorithm,
         * which tries to minimize the number of small packets on the network.
         */
        \CURLOPT_TCP_NODELAY             => true,
        /**
         * An alias of \CURLOPT_TRANSFERTEXT. Use that instead.
         */
        //\CURLOPT_FTPASCII
        /**
         * TRUE to only list the names of an FTP directory.
         */
        \CURLOPT_FTPLISTONLY             => false,
        /**
         * TRUE to include the header in the output.
         */
        \CURLOPT_HEADER                  => true,
        /**
         * TRUE to track the handle's request string.
         */
        \CURLINFO_HEADER_OUT             => true,
        /**
         * TRUE to reset the HTTP request method to GET.
         * Since GET is the default, this is only necessary if the request method has been changed.
         */
        \CURLOPT_HTTPGET                 => false,
        /**
         * TRUE to tunnel through a given HTTP proxy.
         */
        \CURLOPT_HTTPPROXYTUNNEL         => false,
        /**
         * TRUE to be completely silent with regards to the cURL functions.
         * Removed in cURL 7.15.5 (You can use \CURLOPT_RETURNTRANSFER instead)
         */
        //\CURLOPT_MUTE
        /**
         * TRUE to scan the ~/.netrc file to find a username and password for the remote site
         * that a connection is being established with.
         */
        \CURLOPT_NETRC                   => false,
        /**
         * TRUE to exclude the body from the output.
         * Request method is then set to HEAD. Changing this to FALSE does not change it to GET.
         */
        \CURLOPT_NOBODY                  => false,
        /**
         * TRUE to disable the progress meter for cURL transfers.
         * PHP automatically sets this option to TRUE, this should only be changed for debugging purposes.
         */
        \CURLOPT_NOPROGRESS              => true,
        /**
         * TRUE to ignore any cURL function that causes a signal to be sent to the PHP process.
         * This is turned on by default in multi-threaded SAPIs so timeout options can still be used.
         */
        \CURLOPT_NOSIGNAL                => true,
        /**
         * TRUE to not handle dot dot sequences.
         */
        \CURLOPT_PATH_AS_IS              => true,
        /**
         * TRUE to wait for pipelining/multiplexing.
         */
        \CURLOPT_PIPEWAIT                => false,
        /**
         * TRUE to do a regular HTTP POST.
         * This POST is the normal application/x-www-form-urlencoded kind, most commonly used by HTML forms.
         * Setting it to false will break CURLOPT_INFILESIZE and Content-length will not be sent
         */
        //\CURLOPT_POST                    => false,
        /**
         * TRUE to HTTP PUT a file. The file to PUT must be set with \CURLOPT_INFILE and \CURLOPT_INFILESIZE.
         * Setting it to false will automatically changes method to GET
         */
        //\CURLOPT_PUT                     => false,
        /**
         * The file that the transfer should be read from when uploading.
         */
        //\CURLOPT_INFILE                  => ??,
        /**
         * The expected size, in bytes, of the file when uploading a file to a remote site.
         * Note that using this option will not stop libcurl from sending more data,
         * as exactly what is sent depends on \CURLOPT_READFUNCTION.
         */
        //\CURLOPT_INFILESIZE              => ??,
        /**
         * TRUE to return the transfer as a string of the return value of curl_exec()
         * instead of outputting it out directly.
         */
        \CURLOPT_RETURNTRANSFER          => true,
        /**
         * PHP 7 removes this option; the CURLFile interface must be used to upload files.
         */
        //\CURLOPT_SAFE_UPLOAD
        /**
         * TRUE to enable sending the initial response in the first packet.
         */
        \CURLOPT_SASL_IR                 => false,
        /**
         * FALSE to disable ALPN in the SSL handshake
         * (if the SSL backend libcurl is built to use supports it), which can be used to negotiate http2.
         */
        \CURLOPT_SSL_ENABLE_ALPN         => false,
        /**
         * FALSE to disable NPN in the SSL handshake
         * (if the SSL backend libcurl is built to use supports it), which can be used to negotiate http2.
         */
        \CURLOPT_SSL_ENABLE_NPN          => false,
        /**
         * TRUE to output SSL certification information to STDERR on secure transfers.
         * Added in cURL 7.19.1. Available since PHP 5.3.2. Requires \CURLOPT_VERBOSE to be on to have an effect.
         */
        \CURLOPT_CERTINFO                => false,
        /**
         * FALSE to stop cURL from verifying the peer's certificate.
         * Alternate certificates to verify against can be specified with the \CURLOPT_CAINFO option
         * or a certificate directory can be specified with the \CURLOPT_CAPATH option.
         */
        \CURLOPT_SSL_VERIFYPEER          => false,
        /**
         * The name of a file holding one or more certificates to verify the peer with.
         * This only makes sense when used in combination with \CURLOPT_SSL_VERIFYPEER.
         */
        //\CURLOPT_CAINFO                  => ??,
        /**
         * A directory that holds multiple CA certificates. Use this option alongside \CURLOPT_SSL_VERIFYPEER.
         */
        //\CURLOPT_CAPATH                  => ??,
        /**
         * TRUE to verify the certificate's status
         */
        \CURLOPT_SSL_VERIFYSTATUS        => false,
        /**
         * TRUE to enable TCP Fast Open
         */
        //\CURLOPT_TCP_FASTOPEN            => false,
        /**
         * TRUE to not send TFTP options requests.
         */
        //\CURLOPT_TFTP_NO_OPTIONS         => false,
        /**
         * TRUE to use ASCII mode for FTP transfers.
         * For LDAP, it retrieves data in plain text instead of HTML.
         * On Windows systems, it will not set STDOUT to binary mode.
         */
        \CURLOPT_TRANSFERTEXT            => false,
        /**
         * TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
         */
        \CURLOPT_AUTOREFERER             => false,
        /**
         * TRUE to keep sending the username and password when
         * following locations (using \CURLOPT_FOLLOWLOCATION), even when the hostname has changed.
         */
        \CURLOPT_UNRESTRICTED_AUTH       => false,
        /**
         * TRUE to follow any "Location: " header that the server sends as part of the HTTP header
         * (note this is recursive, PHP will follow as many "Location: " headers that it is sent,
         * unless \CURLOPT_MAXREDIRS is set).
         */
        \CURLOPT_FOLLOWLOCATION          => false,
        /**
         * The maximum amount of HTTP redirections to follow. Use this option alongside \CURLOPT_FOLLOWLOCATION.
         */
        \CURLOPT_MAXREDIRS               => 0,
        /**
         * A bitmask of 1 (301 Moved Permanently), 2 (302 Found) and 4 (303 See Other)
         * if the HTTP POST method should be maintained when \CURLOPT_FOLLOWLOCATION
         * is set and a specific type of redirect occurs.
         */
        //\CURLOPT_POSTREDIR               => ??,
        /**
         * TRUE to prepare for an upload.
         * Setting this option to false automatically sets method to GET
         */
        //\CURLOPT_UPLOAD                  => false,
        /**
         * TRUE to output verbose information.
         * Writes output to STDERR, or the file specified using \CURLOPT_STDERR.
         * Setting this option to false affects on curl_getinfo(curl, CURLINFO_HEADER_OUT)
         * @link https://bugs.php.net/bug.php?id=65348
         *
         * Seems it works with TRUE but still does not work with FALSE
         */
        //\CURLOPT_VERBOSE                 => false,
        /**
         * The size of the buffer to use for each read. There is no guarantee this request will be fulfilled, however.
         */
        //\CURLOPT_BUFFERSIZE            => ??,
        /**
         * One of the CURLCLOSEPOLICY_* values. Removed in PHP 5.6.0.
         */
        //\CURLOPT_CLOSEPOLICY
        /**
         * The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
         *
         * If CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS is lower than CURLOPT_CONNECTTIMEOUT|CURLOPT_CONNECTTIMEOUT_MS
         * then value of CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS will be applied instead for connection phase
         * @link https://stackoverflow.com/questions/27776129/php-curl-curlopt-connecttimeout-vs-curlopt-timeout/46032973#46032973
         */
        //\CURLOPT_CONNECTTIMEOUT          => 3,
        /**
         * The number of milliseconds to wait while trying to connect. Use 0 to wait indefinitely.
         * If libcurl is built to use the standard system name resolver,
         * that portion of the connect will still use full-second resolution for timeouts
         * with a minimum timeout allowed of one second.
         *
         * If CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS is lower than CURLOPT_CONNECTTIMEOUT|CURLOPT_CONNECTTIMEOUT_MS
         * then value of CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS will be applied instead for connection phase
         * @link https://stackoverflow.com/questions/27776129/php-curl-curlopt-connecttimeout-vs-curlopt-timeout/46032973#46032973
         */
        \CURLOPT_CONNECTTIMEOUT_MS       => 3000,
        /**
         * The number of seconds to keep DNS entries in memory.
         * This option is set to 120 (2 minutes) by default.
         */
        \CURLOPT_DNS_CACHE_TIMEOUT       => 120,
        /**
         * The timeout for Expect: 100-continue responses in milliseconds. Defaults to 1000 milliseconds.
         */
        \CURLOPT_EXPECT_100_TIMEOUT_MS   => 1000,
        /**
         * The FTP authentication method (when is activated):
         * CURLFTPAUTH_SSL (try SSL first), CURLFTPAUTH_TLS (try TLS first), or
         * CURLFTPAUTH_DEFAULT (let cURL decide).
         */
        \CURLOPT_FTPSSLAUTH              => CURLFTPAUTH_DEFAULT,
        /**
         * How to deal with headers. One of the following constants:
         * CURLHEADER_UNIFIED: the headers specified in \CURLOPT_HTTPHEADER
         * will be used in requests both to servers and proxies.
         * With this option enabled, \CURLOPT_PROXYHEADER will not have any effect.
         * CURLHEADER_SEPARATE: makes \CURLOPT_HTTPHEADER headers only get sent to a server and not to a proxy.
         * Proxy headers must be set with \CURLOPT_PROXYHEADER to get used.
         * Note that if a non-CONNECT request is sent to a proxy,
         * libcurl will send both server headers and proxy headers. When doing CONNECT, libcurl will send
         * \CURLOPT_PROXYHEADER headers only to the proxy and then \CURLOPT_HTTPHEADER headers only to the server.
         * Defaults to CURLHEADER_SEPARATE as of cURL 7.42.1, and CURLHEADER_UNIFIED before.
         */
        \CURLOPT_HEADEROPT               => CURLHEADER_SEPARATE,
        /**
         * CURL_HTTP_VERSION_NONE (default, lets CURL decide which version to use),
         * CURL_HTTP_VERSION_1_0 (forces HTTP/1.0), or CURL_HTTP_VERSION_1_1 (forces HTTP/1.1).
         */
        \CURLOPT_HTTP_VERSION            => CURL_HTTP_VERSION_NONE,
        /**
         * The HTTP authentication method(s) to use. The options are: CURLAUTH_BASIC, CURLAUTH_DIGEST,
         * CURLAUTH_GSSNEGOTIATE, CURLAUTH_NTLM, CURLAUTH_ANY, and CURLAUTH_ANYSAFE.
         * The bitwise | (or) operator can be used to combine more than one method. If this is done,
         * cURL will poll the server to see what methods it supports and pick the best one.
         * CURLAUTH_ANY is an alias for CURLAUTH_BASIC | CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
         * CURLAUTH_ANYSAFE is an alias for CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
         */
        \CURLOPT_HTTPAUTH                => CURLAUTH_ANY,
        /**
         * The transfer speed, in bytes per second, that the transfer should be below during the count of
         * \CURLOPT_LOW_SPEED_TIME seconds before PHP considers the transfer too slow and aborts.
         */
        //\CURLOPT_LOW_SPEED_LIMIT         => ??,
        /**
         * The number of seconds the transfer speed should be below
         * \CURLOPT_LOW_SPEED_LIMIT before PHP considers the transfer too slow and aborts.
         */
        //\CURLOPT_LOW_SPEED_TIME          => ??,
        /**
         * The maximum amount of persistent connections that are allowed.
         * When the limit is reached, \CURLOPT_CLOSEPOLICY is used to determine which connection to close.
         */
        //\CURLOPT_MAXCONNECTS             => ??,
        /**
         * An alternative port number to connect to.
         */
        //\CURLOPT_PORT                    => ??,
        /**
         * Bitmask of CURLPROTO_* values. If used, this bitmask limits what protocols libcurl may use in the transfer.
         * This allows you to have a libcurl built to support a wide range of protocols
         * but still limit specific transfers to only be allowed to use a subset of them.
         * By default libcurl will accept all protocols it supports.
         * See also \CURLOPT_REDIR_PROTOCOLS.
         * Valid protocol options are: CURLPROTO_HTTP, CURLPROTO_HTTPS, CURLPROTO_FTP, CURLPROTO_FTPS,
         * CURLPROTO_SCP, CURLPROTO_SFTP, CURLPROTO_TELNET, CURLPROTO_LDAP, CURLPROTO_LDAPS, CURLPROTO_DICT,
         * CURLPROTO_FILE, CURLPROTO_TFTP, CURLPROTO_ALL
         */
        //\CURLOPT_PROTOCOLS               => ??,
        /**
         * The HTTP authentication method(s) to use for the proxy connection.
         * Use the same bitmasks as described in \CURLOPT_HTTPAUTH.
         * For proxy authentication, only CURLAUTH_BASIC and CURLAUTH_NTLM are currently supported.
         */
        //\CURLOPT_PROXYAUTH               => ??,
        /**
         * The port number of the proxy to connect to. This port number can also be set in \CURLOPT_PROXY.
         */
        //\CURLOPT_PROXYPORT               => ??,
        /**
         * Either CURLPROXY_HTTP (default), CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, CURLPROXY_SOCKS4A or
         * CURLPROXY_SOCKS5_HOSTNAME.
         */
        //\CURLOPT_PROXYTYPE               => ??,
        /**
         * Bitmask of CURLPROTO_* values. If used, this bitmask limits what protocols libcurl may use in a
         * transfer that it follows to in a redirect when \CURLOPT_FOLLOWLOCATION is enabled.
         * This allows you to limit specific transfers to only be allowed to use a subset of protocols in
         * redirections. By default libcurl will allow all protocols except for FILE and SCP.
         * This is a difference compared to pre-7.19.4 versions which unconditionally would follow to
         * all protocols supported. See also \CURLOPT_PROTOCOLS for protocol constant values.
         */
        //\CURLOPT_REDIR_PROTOCOLS         => ??,
        /**
         * The offset, in bytes, to resume a transfer from.
         */
        //\CURLOPT_RESUME_FROM             => ??,
        /**
         * Set SSL behavior options, which is a bitmask of any of the following constants:
         * CURLSSLOPT_ALLOW_BEAST: do not attempt to use any workarounds for a security flaw in the SSL3 and TLS1.0
         * protocols. CURLSSLOPT_NO_REVOKE: disable certificate revocation checks for those SSL backends
         * where such behavior is present.
         */
        //\CURLOPT_SSL_OPTIONS             => ??,
        /**
         * 1 to check the existence of a common name in the SSL peer certificate.
         * 2 to check the existence of a common name and also verify that it matches the hostname provided.
         * 0 to not check the names.
         * In production environments the value of this option should be kept at 2 (default value).
         */
        //\CURLOPT_SSL_VERIFYHOST          => ??,
        /**
         * One of CURL_SSLVERSION_DEFAULT (0), CURL_SSLVERSION_TLSv1 (1), CURL_SSLVERSION_SSLv2 (2),
         * CURL_SSLVERSION_SSLv3 (3), CURL_SSLVERSION_TLSv1_0 (4), CURL_SSLVERSION_TLSv1_1 (5) or
         * CURL_SSLVERSION_TLSv1_2 (6).
         *
         * Note: Your best bet is to not set this and let it use the default.
         * Setting it to 2 or 3 is very dangerous given the known vulnerabilities in SSLv2 and SSLv3.
         */
        //\CURLOPT_SSLVERSION              => ??,
        /**
         * Set the numerical stream weight (a number between 1 and 256).
         */
        //\CURLOPT_STREAM_WEIGHT           => ??,
        /**
         * How \CURLOPT_TIMEVALUE is treated. Use CURL_TIMECOND_IFMODSINCE to return the page only
         * if it has been modified since the time specified in \CURLOPT_TIMEVALUE.
         * If it hasn't been modified, a "304 Not Modified" header will be returned assuming \CURLOPT_HEADER is
         * TRUE. Use CURL_TIMECOND_IFUNMODSINCE for the reverse effect. CURL_TIMECOND_IFMODSINCE is the default.
         */
        //\CURLOPT_TIMECONDITION           => ??,
        /**
         * The maximum number of seconds to allow cURL functions to execute.
         *
         * If CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS is lower than CURLOPT_CONNECTTIMEOUT|CURLOPT_CONNECTTIMEOUT_MS
         * then value of CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS will be applied instead for connection phase
         * @link https://stackoverflow.com/questions/27776129/php-curl-curlopt-connecttimeout-vs-curlopt-timeout/46032973#46032973
         */
        //\CURLOPT_TIMEOUT                 => 5,
        /**
         * The maximum number of milliseconds to allow cURL functions to execute.
         * If libcurl is built to use the standard system name resolver, that portion of the connect will still
         * use full-second resolution for timeouts with a minimum timeout allowed of one second.
         *
         * If CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS is lower than CURLOPT_CONNECTTIMEOUT|CURLOPT_CONNECTTIMEOUT_MS
         * then value of CURLOPT_TIMEOUT|CURLOPT_TIMEOUT_MS will be applied instead for connection phase
         * @link https://stackoverflow.com/questions/27776129/php-curl-curlopt-connecttimeout-vs-curlopt-timeout/46032973#46032973
         */
        \CURLOPT_TIMEOUT_MS              => 5000,
        /**
         * The time in seconds since January 1st, 1970.
         * The time will be used by \CURLOPT_TIMECONDITION. By default, CURL_TIMECOND_IFMODSINCE is used.
         */
        //\CURLOPT_TIMEVALUE               => ??,
        /**
         * If a download exceeds this speed (counted in bytes per second) on cumulative average during the transfer,
         * the transfer will pause to keep the average rate less than or equal to the parameter value.
         * Defaults to unlimited speed.
         */
        //\CURLOPT_MAX_RECV_SPEED_LARGE    => ??,
        /**
         * If an upload exceeds this speed (counted in bytes per second) on cumulative average during the
         * transfer, the transfer will pause to keep the average rate less than or equal to the parameter value.
         * Defaults to unlimited speed.
         */
        //\CURLOPT_MAX_SEND_SPEED_LARGE    => ??,
        /**
         * A bitmask consisting of one or more of CURLSSH_AUTH_PUBLICKEY, CURLSSH_AUTH_PASSWORD,
         * CURLSSH_AUTH_HOST, CURLSSH_AUTH_KEYBOARD. Set to CURLSSH_AUTH_ANY to let libcurl pick one.
         */
        //\CURLOPT_SSH_AUTH_TYPES          => CURLSSH_AUTH_ANY,
        /**
         * Allows an application to select what kind of IP addresses to use when resolving host names.
         * This is only interesting when using host names that resolve addresses using more than one version of IP,
         * possible values are CURL_IPRESOLVE_WHATEVER, CURL_IPRESOLVE_V4, CURL_IPRESOLVE_V6,
         * by default CURL_IPRESOLVE_WHATEVER.
         */
        //\CURLOPT_IPRESOLVE               => CURL_IPRESOLVE_WHATEVER,
        /**
         * Tell curl which method to use to reach a file on a FTP(S) server.
         * Possible values are CURLFTPMETHOD_MULTICWD, CURLFTPMETHOD_NOCWD and CURLFTPMETHOD_SINGLECWD.
         */
        //\CURLOPT_FTP_FILEMETHOD          => ??,
        /**
         * The contents of the "Cookie: " header to be used in the HTTP request.
         * Note that multiple cookies are separated with a semicolon followed by a space
         * (e.g., "fruit=apple; colour=red")
         */
        //\CURLOPT_COOKIE                  => ??,
        /**
         * The name of the file containing the cookie data.
         * The cookie file can be in Netscape format, or just plain HTTP-style headers dumped into a file.
         * If the name is an empty string, no cookies are loaded, but cookie handling is still enabled.
         */
        //\CURLOPT_COOKIEFILE              => ??,
        /**
         * The name of a file to save all internal cookies to when the handle is closed,
         * e.g. after a call to curl_close.
         */
        //\CURLOPT_COOKIEJAR               => ??,
        /**
         * A custom request method to use instead of "GET" or "HEAD" when doing a HTTP request.
         * This is useful for doing "DELETE" or other, more obscure HTTP requests.
         * Valid values are things like "GET", "POST", "CONNECT" and so on; i.e.
         * Do not enter a whole HTTP request line here. For instance,
         * entering "GET /index.html HTTP/1.0\r\n\r\n" would be incorrect.
         * Note: Don't do this without making sure the server supports the custom request method first.
         */
        //\CURLOPT_CUSTOMREQUEST           => ??,
        /**
         * The default protocol to use if the URL is missing a scheme name.
         */
        //\CURLOPT_DEFAULT_PROTOCOL        => ??,
        /**
         * Set the name of the network interface that the DNS resolver should bind to.
         * This must be an interface name (not an address).
         */
        //\CURLOPT_DNS_INTERFACE           => ??,
        /**
         * Set the local IPv4 address that the resolver should bind to.
         * The argument should contain a single numerical IPv4 address as a string.
         */
        //\CURLOPT_DNS_LOCAL_IP4           => ??,
        /**
         * Set the local IPv6 address that the resolver should bind to.
         * The argument should contain a single numerical IPv6 address as a string.
         */
        //\CURLOPT_DNS_LOCAL_IP6           => ??,
        /**
         * Like \CURLOPT_RANDOM_FILE, except a filename to an Entropy Gathering Daemon socket.
         */
        //\CURLOPT_EGDSOCKET               => ??,
        /**
         * The contents of the "Accept-Encoding: " header.
         * This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip".
         * If an empty string, "", is set, a header containing all supported encoding types is sent.
         */
        //\CURLOPT_ENCODING                => ??,
        /**
         * The value which will be used to get the IP address to use for the FTP "PORT" instruction.
         * The "PORT" instruction tells the remote server to connect to our specified IP address.
         * The string may be a plain IP address, a hostname, a network interface name (under Unix),
         * or just a plain '-' to use the systems default IP address.
         */
        //\CURLOPT_FTPPORT                 => ??,
        /**
         * The name of the outgoing network interface to use.
         * This can be an interface name, an IP address or a host name.
         */
        //\CURLOPT_INTERFACE               => ??,
        /**
         * The password required to use the \CURLOPT_SSLKEY or \CURLOPT_SSH_PRIVATE_KEYFILE private key.
         */
        //\CURLOPT_KEYPASSWD               => ??,
        /**
         * The KRB4 (Kerberos 4) security level.
         * Any of the following values (in order from least to most powerful) are valid: "clear", "safe",
         * "confidential", "private".. If the string does not match one of these, "private" is used.
         * Setting this option to NULL will disable KRB4 security.
         * Currently KRB4 security only works with FTP transactions.
         */
        //\CURLOPT_KRB4LEVEL               => ??,
        /**
         * Can be used to set protocol specific login options, such as the preferred authentication mechanism
         * via "AUTH=NTLM" or "AUTH=*", and should be used in conjunction with the \CURLOPT_USERNAME option.
         */
        //\CURLOPT_LOGIN_OPTIONS           => ??,
        /**
         * Set the pinned public key. The string can be the file name of your pinned public key.
         * The file format expected is "PEM" or "DER". The string can also be any number of base64
         * encoded sha256 hashes preceded by "sha256//" and separated by ";".
         */
        //\CURLOPT_PINNEDPUBLICKEY         => ??,
        /**
         * The full data to post in a HTTP "POST" operation. To post a file, prepend a filename with @ and use
         * the full path. The filetype can be explicitly specified by following the filename with the type in
         * the format ';type=mimetype'. This parameter can either be passed as a urlencoded string like
         * 'para1=val1&para2=val2&...' or as an array with the field name as key and field data as value.
         * If value is an array, the Content-Type header will be set to multipart/form-data. As of PHP 5.2.0,
         * value must be an array if files are passed to this option with the @ prefix. As of PHP 5.5.0,
         * the @ prefix is deprecated and files can be sent using CURLFile. The @ prefix can be disabled for
         * safe passing of values beginning with @ by setting the \CURLOPT_SAFE_UPLOAD option to TRUE.
         */
        //\CURLOPT_POSTFIELDS              => ??,
        /**
         * Any data that should be associated with this cURL handle. This data can subsequently be retrieved
         * with the CURLINFO_PRIVATE option of curl_getinfo(). cURL does nothing with this data. When using a
         * cURL multi handle, this private data is typically a unique key to identify a standard cURL handle.
         */
        //\CURLOPT_PRIVATE                 => ??,
        /**
         * The HTTP proxy to tunnel requests through.
         */
        //\CURLOPT_PROXY                   => ??,
        /**
         * The proxy authentication service name.
         */
        //\CURLOPT_PROXY_SERVICE_NAME      => ??,
        /**
         * A username and password formatted as "[username]:[password]" to use for the connection to the proxy.
         */
        //\CURLOPT_PROXYUSERPWD            => ??,
        /**
         * A filename to be used to seed the random number generator for SSL.
         */
        //\CURLOPT_RANDOM_FILE             => ??,
        /**
         * Range(s) of data to retrieve in the format "X-Y" where X or Y are optional.
         * HTTP transfers also support several intervals, separated with commas in the format "X-Y,N-M".
         */
        //\CURLOPT_RANGE                   => ??,
        /**
         * The contents of the "Referer: " header to be used in a HTTP request.
         */
        //\CURLOPT_REFERER                 => ??,
        /**
         * The authentication service name.
         */
        //\CURLOPT_SERVICE_NAME            => ??,
        /**
         * A string containing 32 hexadecimal digits. The string should be the MD5 checksum of the remote
         * host's public key, and libcurl will reject the connection to the host unless the md5sums match.
         * This option is only for SCP and SFTP transfers.
         */
        //\CURLOPT_SSH_HOST_PUBLIC_KEY_MD5 => ??,
        /**
         * The file name for your public key. If not used, libcurl defaults to $HOME/.ssh/id_dsa.pub if the
         * HOME environment variable is set, and just "id_dsa.pub" in the current directory if HOME is not set.
         */
        //\CURLOPT_SSH_PUBLIC_KEYFILE      => ??,
        /**
         * The file name for your private key. If not used, libcurl defaults to $HOME/.ssh/id_dsa if the HOME
         * environment variable is set, and just "id_dsa" in the current directory if HOME is not set.
         * If the file is password-protected, set the password with \CURLOPT_KEYPASSWD
         */
        //\CURLOPT_SSH_PRIVATE_KEYFILE     => ??,
        /**
         * A list of ciphers to use for SSL. For example, RC4-SHA and TLSv1 are valid cipher lists.
         */
        //\CURLOPT_SSL_CIPHER_LIST         => ??,
        /**
         * The name of a file containing a PEM formatted certificate.
         */
        //\CURLOPT_SSLCERT                 => ??,
        /**
         * The password required to use the \CURLOPT_SSLCERT certificate.
         */
        //\CURLOPT_SSLCERTPASSWD           => ??,
        /**
         * The format of the certificate. Supported formats are "PEM" (default), "DER", and "ENG".
         */
        //\CURLOPT_SSLCERTTYPE             => ??,
        /**
         * The identifier for the crypto engine of the private SSL key specified in \CURLOPT_SSLKEY.
         */
        //\CURLOPT_SSLENGINE               => ??,
        /**
         * The identifier for the crypto engine used for asymmetric crypto operations.
         */
        //\CURLOPT_SSLENGINE_DEFAULT       => ??,
        /**
         * The name of a file containing a private SSL key.
         */
        //\CURLOPT_SSLKEY                  => ??,
        /**
         * The secret password needed to use the private SSL key specified in \CURLOPT_SSLKEY.
         * Note: Since this option contains a sensitive password, remember to keep the PHP script it is
         * contained within safe.
         */
        //\CURLOPT_SSLKEYPASSWD            => ??,
        /**
         * The key type of the private SSL key specified in \CURLOPT_SSLKEY.
         * Supported key types are "PEM" (default), "DER", and "ENG".
         */
        //\CURLOPT_SSLKEYTYPE              => ??,
        /**
         * Enables the use of Unix domain sockets as connection endpoint and sets the path to the given string.
         */
        //\CURLOPT_UNIX_SOCKET_PATH        => ??,
        //\CURLOPT_URL                     => ??,
        /**
         * The contents of the "User-Agent: " header to be used in a HTTP request.
         */
        \CURLOPT_USERAGENT               => 'PHP-CURL',
        /**
         * The user name to use in authentication.
         */
        //\CURLOPT_USERNAME                => ??,
        /**
         * A username and password formatted as "[username]:[password]" to use for the connection.
         */
        //\CURLOPT_USERPWD                 => ??,
        /**
         * Specifies the OAuth 2.0 access token.
         */
        //\CURLOPT_XOAUTH2_BEARER          => ??,

        #ARRAYS

        /**
         * Connect to a specific host and port instead of the URL's host and port.
         * Accepts an array of strings with the format HOST:PORT:CONNECT-TO-HOST:CONNECT-TO-PORT.
         */
        //\CURLOPT_CONNECT_TO              => ??,
        /**
         * An array of HTTP 200 responses that will be treated as valid responses and not as errors.
         */
        //\CURLOPT_HTTP200ALIASES          => ??,
        /**
         * An array of HTTP header fields to set, in the format array('Content-type: text/plain', 'Content-length: 100')
         */
        //\CURLOPT_HTTPHEADER              => ??,
        /**
         * An array of FTP commands to execute on the server after the FTP request has been performed.
         */
        //\CURLOPT_POSTQUOTE               => ??,
        /**
         * An array of custom HTTP headers to pass to proxies.
         */
        //\CURLOPT_PROXYHEADER             => ??,
        /**
         * An array of FTP commands to execute on the server prior to the FTP request.
         */
        //\CURLOPT_QUOTE                   => ??,
        /**
         * Provide a custom address for a specific host and port pair. An array of hostname, port, and IP address
         * strings, each element separated by a colon. In the format: array("example.com:80:127.0.0.1")
         */
        //\CURLOPT_RESOLVE                 => ??,

        #RESOURCE

        /**
         * The file that the transfer should be written to. The default is STDOUT (the browser window).
         */
        //\CURLOPT_FILE                    => ??,
        /**
         * An alternative location to output errors to instead of STDERR.
         */
        //\CURLOPT_STDERR                  => ??,
        /**
         * The file that the header part of the transfer is written to.
         */
        //\CURLOPT_WRITEHEADER             => ??,
        /**
         * A callback accepting two parameters. The first is the cURL resource, the second is a string with the
         * header data to be written. The header data must be written by this callback. Return the number of
         * bytes written.
         */
        //\CURLOPT_HEADERFUNCTION          => ??,
        /**
         * A callback accepting three parameters. The first is the cURL resource, the second is a string
         * containing a password prompt, and the third is the maximum password length. Return the string
         * containing the password.
         */
        //\CURLOPT_PASSWDFUNCTION          => ??,
        /**
         * A callback accepting five parameters. The first is the cURL resource, the second is the total number
         * of bytes expected to be downloaded in this transfer, the third is the number of bytes downloaded so
         * far, the fourth is the total number of bytes expected to be uploaded in this transfer, and the fifth
         * is the number of bytes uploaded so far.
         * Note: The callback is only called when the \CURLOPT_NOPROGRESS option is set to FALSE.
         * Return a non-zero value to abort the transfer.
         * In which case, the transfer will set a CURLE_ABORTED_BY_CALLBACK error.
         */
        //\CURLOPT_PROGRESSFUNCTION        => ??,
        /**
         * A callback accepting three parameters. The first is the cURL resource, the second is a stream resource
         * provided to cURL through the option \CURLOPT_INFILE, and the third is the maximum amount of data to be
         * read. The callback must return a string with a length equal or smaller than the amount of data
         * requested, typically by reading it from the passed stream resource. It should return an empty string
         * to signal EOF.
         */
        //\CURLOPT_READFUNCTION            => ??,
        /**
         * A callback accepting two parameters. The first is the cURL resource, and the second is a string with
         * the data to be written. The data must be saved by this callback. It must return the exact number of
         * bytes written or the transfer will be aborted with an error.
         */
        //\CURLOPT_WRITEFUNCTION           => ??,
    ];

    /**
     * List of cURL errors
     * @since 1.0.0
     */
    const EXCEPTIONS = [
        CURLE_UNSUPPORTED_PROTOCOL          => 'CURLE_UNSUPPORTED_PROTOCOL',
        CURLE_FAILED_INIT                   => 'CURLE_FAILED_INIT',
        CURLE_URL_MALFORMAT                 => 'CURLE_URL_MALFORMAT',
        CURLE_URL_MALFORMAT_USER            => 'CURLE_URL_MALFORMAT_USER',
        CURLE_COULDNT_RESOLVE_PROXY         => 'CURLE_COULDNT_RESOLVE_PROXY',
        CURLE_COULDNT_RESOLVE_HOST          => 'CURLE_COULDNT_RESOLVE_HOST',
        CURLE_COULDNT_CONNECT               => 'CURLE_COULDNT_CONNECT',
        CURLE_FTP_WEIRD_SERVER_REPLY        => 'CURLE_FTP_WEIRD_SERVER_REPLY',
        CURLE_FTP_ACCESS_DENIED             => 'CURLE_FTP_ACCESS_DENIED',
        CURLE_FTP_USER_PASSWORD_INCORRECT   => 'CURLE_FTP_USER_PASSWORD_INCORRECT',
        CURLE_FTP_WEIRD_PASS_REPLY          => 'CURLE_FTP_WEIRD_PASS_REPLY',
        CURLE_FTP_WEIRD_USER_REPLY          => 'CURLE_FTP_WEIRD_USER_REPLY',
        CURLE_FTP_WEIRD_PASV_REPLY          => 'CURLE_FTP_WEIRD_PASV_REPLY',
        CURLE_FTP_WEIRD_227_FORMAT          => 'CURLE_FTP_WEIRD_227_FORMAT',
        CURLE_FTP_CANT_GET_HOST             => 'CURLE_FTP_CANT_GET_HOST',
        CURLE_FTP_CANT_RECONNECT            => 'CURLE_FTP_CANT_RECONNECT',
        CURLE_FTP_COULDNT_SET_BINARY        => 'CURLE_FTP_COULDNT_SET_BINARY',
        CURLE_FTP_PARTIAL_FILE              => 'CURLE_FTP_PARTIAL_FILE or CURLE_PARTIAL_FILE',
        CURLE_FTP_COULDNT_RETR_FILE         => 'CURLE_FTP_COULDNT_RETR_FILE',
        CURLE_FTP_WRITE_ERROR               => 'CURLE_FTP_WRITE_ERROR',
        CURLE_FTP_QUOTE_ERROR               => 'CURLE_FTP_QUOTE_ERROR',
        CURLE_HTTP_NOT_FOUND                => 'CURLE_HTTP_NOT_FOUND or CURLE_HTTP_RETURNED_ERROR',
        CURLE_WRITE_ERROR                   => 'CURLE_WRITE_ERROR',
        CURLE_MALFORMAT_USER                => 'CURLE_MALFORMAT_USER',
        CURLE_FTP_COULDNT_STOR_FILE         => 'CURLE_FTP_COULDNT_STOR_FILE',
        CURLE_READ_ERROR                    => 'CURLE_READ_ERROR',
        CURLE_OUT_OF_MEMORY                 => 'CURLE_OUT_OF_MEMORY',
        CURLE_OPERATION_TIMEDOUT            => 'CURLE_OPERATION_TIMEDOUT or CURLE_OPERATION_TIMEOUTED',
        CURLE_FTP_COULDNT_SET_ASCII         => 'CURLE_FTP_COULDNT_SET_ASCII',
        CURLE_FTP_PORT_FAILED               => 'CURLE_FTP_PORT_FAILED',
        CURLE_FTP_COULDNT_USE_REST          => 'CURLE_FTP_COULDNT_USE_REST',
        CURLE_FTP_COULDNT_GET_SIZE          => 'CURLE_FTP_COULDNT_GET_SIZE',
        CURLE_HTTP_RANGE_ERROR              => 'CURLE_HTTP_RANGE_ERROR',
        CURLE_HTTP_POST_ERROR               => 'CURLE_HTTP_POST_ERROR',
        CURLE_SSL_CONNECT_ERROR             => 'CURLE_SSL_CONNECT_ERROR',
        CURLE_BAD_DOWNLOAD_RESUME           => 'CURLE_BAD_DOWNLOAD_RESUME or CURLE_FTP_BAD_DOWNLOAD_RESUME',
        CURLE_FILE_COULDNT_READ_FILE        => 'CURLE_FILE_COULDNT_READ_FILE',
        CURLE_LDAP_CANNOT_BIND              => 'CURLE_LDAP_CANNOT_BIND',
        CURLE_LDAP_SEARCH_FAILED            => 'CURLE_LDAP_SEARCH_FAILED',
        CURLE_LIBRARY_NOT_FOUND             => 'CURLE_LIBRARY_NOT_FOUND',
        CURLE_FUNCTION_NOT_FOUND            => 'CURLE_FUNCTION_NOT_FOUND',
        CURLE_ABORTED_BY_CALLBACK           => 'CURLE_ABORTED_BY_CALLBACK',
        CURLE_BAD_FUNCTION_ARGUMENT         => 'CURLE_BAD_FUNCTION_ARGUMENT',
        CURLE_BAD_CALLING_ORDER             => 'CURLE_BAD_CALLING_ORDER',
        CURLE_HTTP_PORT_FAILED              => 'CURLE_HTTP_PORT_FAILED',
        CURLE_BAD_PASSWORD_ENTERED          => 'CURLE_BAD_PASSWORD_ENTERED',
        CURLE_TOO_MANY_REDIRECTS            => 'CURLE_TOO_MANY_REDIRECTS',
        CURLE_UNKNOWN_TELNET_OPTION         => 'CURLE_UNKNOWN_TELNET_OPTION',
        CURLE_TELNET_OPTION_SYNTAX          => 'CURLE_TELNET_OPTION_SYNTAX',
        CURLE_OBSOLETE                      => 'CURLE_OBSOLETE',
        CURLE_SSL_PEER_CERTIFICATE          => 'CURLE_SSL_PEER_CERTIFICATE',
        CURLE_GOT_NOTHING                   => 'CURLE_GOT_NOTHING',
        CURLE_SSL_ENGINE_NOTFOUND           => 'CURLE_SSL_ENGINE_NOTFOUND',
        CURLE_SSL_ENGINE_SETFAILED          => 'CURLE_SSL_ENGINE_SETFAILED',
        CURLE_SEND_ERROR                    => 'CURLE_SEND_ERROR',
        CURLE_RECV_ERROR                    => 'CURLE_RECV_ERROR',
        CURLE_SHARE_IN_USE                  => 'CURLE_SHARE_IN_USE',
        CURLE_SSL_CERTPROBLEM               => 'CURLE_SSL_CERTPROBLEM',
        CURLE_SSL_CIPHER                    => 'CURLE_SSL_CIPHER',
        CURLE_SSL_CACERT                    => 'CURLE_SSL_CACERT',
        CURLE_BAD_CONTENT_ENCODING          => 'CURLE_BAD_CONTENT_ENCODING',
        CURLE_LDAP_INVALID_URL              => 'CURLE_LDAP_INVALID_URL',
        CURLE_FILESIZE_EXCEEDED             => 'CURLE_FILESIZE_EXCEEDED',
        CURLE_FTP_SSL_FAILED                => 'CURLE_FTP_SSL_FAILED',
        CURLE_SSH                           => 'CURLE_SSH',
        CURLE_SSL_CACERT_BADFILE            => 'CURLE_SSL_CACERT_BADFILE',
        CURLE_SSL_PINNEDPUBKEYNOTMATCH      => 'CURLE_SSL_PINNEDPUBKEYNOTMATCH',
    ];
}
