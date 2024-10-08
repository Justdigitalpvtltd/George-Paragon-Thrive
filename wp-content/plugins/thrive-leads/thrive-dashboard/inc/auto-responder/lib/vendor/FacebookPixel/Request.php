<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

namespace TVD\Autoresponder\FacebookPixel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

if ( ! class_exists( 'RequestInterface', false ) ) {
	require_once __DIR__ . '/RequestInterface.php';
}

class Request implements RequestInterface {

	/**
	 * @var string
	 */
	const PROTOCOL_HTTP = 'http://';

	/**
	 * @var string
	 */
	const PROTOCOL_HTTPS = 'https://';

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var Headers
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $method = self::METHOD_GET;

	/**
	 * @var string
	 */
	protected $protocol = self::PROTOCOL_HTTPS;

	/**
	 * @var string
	 */
	protected $domain;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $graphVersion;

	/**
	 * @var Parameters
	 */
	protected $queryParams;

	/**
	 * @var Parameters
	 */
	protected $bodyParams;

	/**
	 * @var Parameters
	 */
	protected $fileParams;

	/**
	 * @param Client $client
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	public function __clone() {
		$this->queryParams && $this->queryParams = clone $this->queryParams;
		$this->bodyParams && $this->bodyParams = clone $this->bodyParams;
		$this->fileParams && $this->fileParams = clone $this->fileParams;
	}

	/**
	 * @return Client
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param string $protocol
	 */
	public function setProtocol( $protocol ) {
		$this->protocol = $protocol;
	}

	/**
	 * @return string
	 */
	public function getDomain() {
		if ( $this->domain === null ) {
			$this->domain = sprintf(
				"%s.%s",
				Client::DEFAULT_LAST_LEVEL_DOMAIN,
				$this->client->getDefaultGraphBaseDomain() );
		}

		return $this->domain;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain( $domain ) {
		$this->domain = $domain;
	}

	/**
	 * @param string $last_level_domain
	 */
	public function setLastLevelDomain( $last_level_domain ) {
		$this->domain = sprintf(
			"%s.%s",
			$last_level_domain,
			$this->client->getDefaultGraphBaseDomain() );
	}

	/**
	 * @return Headers
	 */
	public function getHeaders() {
		if ( $this->headers === null ) {
			$this->headers = clone $this->getClient()->getDefaultRequestHeaderds();
		}

		return $this->headers;
	}

	/**
	 * @param Headers $headers
	 */
	public function setHeaders( Headers $headers ) {
		$this->headers = $headers;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param string $method
	 */
	public function setMethod( $method ) {
		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath( $path ) {
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getGraphVersion() {
		return $this->graphVersion;
	}

	/**
	 * @param string $version
	 */
	public function setGraphVersion( $version ) {
		$this->graphVersion = $version;
	}

	/**
	 * @return Parameters
	 */
	public function getQueryParams() {
		if ( $this->queryParams === null ) {
			$this->queryParams = new Parameters();
		}

		return $this->queryParams;
	}

	/**
	 * @param Parameters $params
	 */
	public function setQueryParams( Parameters $params ) {
		$this->queryParams = $params;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		$delimiter = null;
		if ( $this->getQueryParams()->count() ) {
			$delimiter = strpos( $this->getPath(), '?' ) ? '&' : '?';
		}

		return $this->getProtocol() . $this->getDomain()
		       . '/v' . $this->getGraphVersion() . $this->getPath()
		       . $delimiter
		       . http_build_query( $this->getQueryParams()->export(), '', '&' );
	}

	/**
	 * @return Parameters
	 */
	public function getBodyParams() {
		if ( $this->bodyParams === null ) {
			$this->bodyParams = new Parameters();
		}

		return $this->bodyParams;
	}

	/**
	 * @param Parameters $params
	 */
	public function setBodyParams( Parameters $params ) {
		$this->bodyParams = $params;
	}

	/**
	 * @return Parameters
	 */
	public function getFileParams() {
		if ( $this->fileParams === null ) {
			$this->fileParams = new Parameters();
		}

		return $this->fileParams;
	}

	/**
	 * @param Parameters $params
	 */
	public function setFileParams( Parameters $params ) {
		$this->fileParams = $params;
	}

	/**
	 * @return ResponseInterface
	 */
	public function execute() {
		return $this->getClient()->sendRequest( $this );
	}

	/**
	 * @return Request
	 * @see RequestInterface::createClone()
	 */
	public function createClone() {
		return clone $this;
	}
}
