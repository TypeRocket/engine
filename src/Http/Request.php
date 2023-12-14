<?php
namespace TypeRocket\Engine7\Http;

use TypeRocket\Engine7\Core\Container;
use TypeRocket\Engine7\Core\Hook;
use TypeRocket\Engine7\Models\AuthUser;
use TypeRocket\Engine7\Utility\Data;
use TypeRocket\Engine7\Utility\Str;

class Request
{
    protected ?string $method = null;
    protected ?string $host = null;
    protected ?string $uri = null;
    protected ?string $referer = null;
    protected string $protocol = 'http';
    protected mixed $path = null;
    protected mixed $fields = null;
    protected mixed $post = null;
    protected mixed $get = null;
    protected mixed $input = null;
    protected mixed $files = null;
    protected mixed $cookies = null;

    /**
     * Construct the request
     *
     * @internal param int $id the resource ID
     */
    public function __construct()
    {
        $this->method = $this->getFormMethod();
        $this->protocol = is_ssl() ? 'https' : 'http';
        $this->post = !empty($_POST) ? wp_unslash($_POST) : null;
        $this->get = !empty($_GET) ? wp_unslash($_GET) : null;
        $this->files = $_FILES ?? null;
        $this->uri = $_SERVER['REQUEST_URI'] ?? null;
        $this->referer = $_SERVER['HTTP_REFERER'] ?? null;
        $this->host = $_SERVER['HTTP_HOST'] ?? null;

        if( ! empty( $_SERVER['REQUEST_URI'] ) ) {
            $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
    }

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public static function new(...$args) : static
    {
        return new static(...$args);
    }

    /**
     * Get the HTTP protocol
     *
     * @return string
     */
    public function getProtocol() : string
    {
        return $this->protocol;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Is Get
     *
     */
    public function isGet() : bool
    {
        return $this->method === 'GET';
    }

    /**
     * Is Post
     *
     * @return bool
     */
    public function isPost() : bool
    {
        return $this->method === 'POST';
    }

    /**
     * Is Put
     *
     * @return bool
     */
    public function isPut() : bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Is Put
     *
     * @return bool
     */
    public function isDelete() : bool
    {
        return $this->method === 'DELETE';
    }

    /**
     * Is Marked AJAX
     *
     * @return bool
     */
    public function isMarkedAjax() : bool
    {
        return !empty($this->post['_tr_ajax_request']) || !empty($this->get['_tr_ajax_request']);
    }

    /**
     * Is Maybe Ajax
     *
     * The JavaScript sending the request needs to have applied
     * the custom header HTTP_X_REQUESTED_WITH.
     *
     * Maybe add: wp_doing_ajax()
     *
     * @return bool
     */
    public function isAjax() : bool
    {
        $ajax = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        if(strtolower($ajax) == 'xmlhttprequest' || $this->isMarkedAjax() ) {
            return true;
        }

        return false;
    }

    /**
     * Get Agents
     *
     * @return array
     */
    public function getAccepts() : array
    {
        return explode(',', $_SERVER['HTTP_ACCEPT'] ?? '');
    }

    /**
     * Accept Contains
     *
     * @param string $search
     * @return bool
     */
    public function acceptContains(string $search) : bool
    {
        return Str::contains($search, $_SERVER['HTTP_ACCEPT'] ?? '');
    }

    /**
     * Request Wants
     *
     * @param string $name
     * @return bool
     */
    public function wants(string $name) : bool
    {
        $types = [
            'json' => 'application/json',
            'html' => 'text/html',
            'xml' => 'application/xml',
            'plain' => 'text/pain',
            'any' => '*/*',
            'image' => 'image/',
        ];

        $search = $types[$name] ?? $name;
        return $search ? $this->acceptContains($search) : false;
    }

    /**
     * Get the form method
     *
     * @return string POST|DELETE|PUT|GET
     */
    public function getFormMethod() : string
    {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get Form Prefix
     *
     * @param string $default
     *
     * @return mixed|string
     */
    public function getFormPrefix(string $default = 'tr') : mixed
    {
        return $_POST['_tr_form_prefix'] ?? $default;
    }

    /**
     * Get Full URL
     *
     * @return string
     */
    public function getUriFull() : string
    {
        return $this->protocol.'://'.$this->host.$this->uri;
    }

    /**
     * Get Path Without Root
     *
     * @param null|string $root
     * @return string
     */
    public function getPathWithoutRoot(?string $root = null) : string
    {
        $root = $root ?? get_site_url();
        $site =  trim( (string) parse_url($root, PHP_URL_PATH), '/');
        return ltrim( Str::removeStart(ltrim((string) $this->path, '/'), $site), '/');
    }

    /**
     * Get the request URI
     */
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * Get Http Header
     *
     * @param string $header
     */
    public function getHeader(string $header) : mixed
    {
        $header = preg_replace( '/[^A-Z0-9_]/', '', strtoupper(str_replace('-', '_',$header)));

        return $_SERVER['HTTP_' . $header] ?? null;
    }

    /**
     * Get the request referer
     */
    public function getReferer(bool $fallback = true) : ?string
    {
        $fallback = $fallback ? $this->getUriFull() : null;

        return $this->referer ?? $fallback;
    }

    /**
     * Get the request path
     *
     * @return mixed|null
     */
    public function getPath() : mixed
    {
        return $this->path;
    }

    /**
     * Get the request path exploded into an array
     */
    public function getPathExploded() : array
    {
        return explode('/', trim($this->path, '/') );
    }

    /**
     * Get the host
     */
    public function getHost() : ?string
    {
        return $this->host;
    }

    /**
     * Get Input
     *
     * @param null|string $key
     * @param mixed $default
     */
    public function input(?string $key = null, mixed $default = null) : mixed
    {
        return $this->getInput($key, $default);
    }

    /**
     * Get Input
     *
     * @param null|string $key
     * @param null|string|array $default
     */
    public function getInput(?string $key = null, mixed $default = null) : mixed
    {
        return $this->getDataJson($key, $default) ?? $this->get[$key] ?? $default;
    }

    /**
     * Get Data JSON first or POST
     *
     * @param null|string $key
     * @param mixed $default
     */
    public function getDataJson(?string $key = null, mixed $default = null) : mixed
    {
        if(!$this->input) {
            $input = file_get_contents('php://input');
            if(Data::isJson($input)) { $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING); }
            else { $data = $this->post; /* parse_str($input, $data); */ }
            $this->input = $data;
        }

        return is_null($key) ? $this->input : ($this->input[$key] ?? $default);
    }

    /**
     * Get the $_POST data
     *
     * @param null|string $key
     * @param null|string|array $default
     *
     * @return null
     */
    public function getDataPost(?string $key = null, mixed $default = null) : mixed
    {
        return is_null($key) ? $this->post : ($this->post[$key] ?? $default);
    }

    /**
     * Get the $_GET data
     *
     * @param null|string $key
     * @param null|string|array $default
     *
     * @return null
     */
    public function getDataGet(?string $key = null, mixed $default = null) : mixed
    {
        return is_null($key) ? $this->get : ($this->get[$key] ?? $default);
    }

    /**
     * Get the $_FILES data
     */
    public function getDataFiles() : mixed
    {
        return $this->files;
    }

    /**
     * Get URI Query as Array
     */
    public function getQueryAsArray() : array
    {
        parse_str(parse_url($this->getUriFull(), PHP_URL_QUERY), $request_params);

        return $request_params;
    }

    /**
     * Get Full URL with Merged Query
     *
     * @param array $request_params
     */
    public function getModifiedUri(array $request_params = []) : string
    {
        $parts = parse_url($this->getUriFull());
        parse_str($parts['query'] ?? '', $query);
        $query = http_build_query(array_merge($query, $request_params));

        $map = [
            $parts['scheme'],
            '://',
            $parts['host'],
            !empty($parts['port']) ? ':'.$parts['port'] : null,
            $parts['path'],
            $query ? '?' : '',
            $query,
        ];

        return implode('', $map);
    }

    /**
     * Get the $_COOKIE data
     *
     * @param null|string $key
     * @param null|string|array $default
     */
    public function getDataCookies(?string $key = null, mixed $default = null) : mixed
    {
        if(!$this->cookies) {
            $this->cookies = !empty($_COOKIE) ? wp_unslash($_COOKIE) : null;
        }

        return is_null($key) ? $this->cookies : ($this->cookies[$key] ?? $default);
    }

    /**
     * Get the fields
     *
     * @param null|string $key
     * @param null|string|array $default
     * @param string $prefix
     */
    public function fields(?string $key = null, mixed $default = null, $prefix = 'tr') : mixed
    {
        return $this->getFields($key, $default, $prefix);
    }

    /**
     * Get the fields
     *
     * @param null|string $key
     * @param null|string|array $default
     * @param string $prefix
     */
    public function getFields(?string $key = null, mixed $default = null, $prefix = 'tr') : mixed
    {
        $fields = $this->getDataJson($prefix);
        return is_null($key) ? $fields : Data::walk($key, $fields, $default);
    }

    /**
     * Get Current User
     */
    public function getCurrentUser() : mixed
    {
        return Container::resolveAlias(AuthUser::CONTAINER_ALIAS);
    }

    /**
     * Get Auth Password
     *
     * Basic Auth server details for the password.
     *
     * @return null|string
     */
    public function getPassword() : ?string
    {
        return $_SERVER['PHP_AUTH_PW'] ?? null;
    }

    /**
     * Get Auth Username
     *
     * Basic Auth server details for the username.
     *
     * @return string|null
     */
    public function getUsername() : ?string
    {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    /**
     * Check Honey Pot
     *
     * @param array|null $fields
     */
    public function checkHoneypot(?array $fields = null) : bool
    {
        $honey = $fields ?? $_REQUEST['__hny'] ?? $this->input('__hny', []);
        $honey_taken = array_filter($honey);
        return Hook::applyFilters('honeypot_check', empty($honey_taken), $honey);
    }

    /**
     * Check Field Nonce
     *
     * Works the same as check_ajax_referer but also include
     * request header checks for: X-CSRF-TOKEN and X-WP-NONCE
     *
     * @param string $action
     * @param bool $die
     */
    function checkNonce(string $action = '', bool $die = false) : int|bool
    {
        $query_arg = '_tr_nonce_form'.$action;
        $action = 'form_' . $action . \TypeRocket\Core\Config::get('app.seed');
        $nonce = '';

        if ( isset( $_REQUEST[$query_arg] ) ) {
            $nonce = $_REQUEST[$query_arg];
        } elseif ( isset( $_REQUEST['_ajax_nonce'] ) ) {
            $nonce = $_REQUEST['_ajax_nonce'];
        } elseif ( isset( $_REQUEST['_wpnonce'] ) ) {
            $nonce = $_REQUEST['_wpnonce'];
        } elseif ( isset( $_SERVER['HTTP_X_CSRF_TOKEN'] ) ) {
            $nonce = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
            $nonce = $_SERVER['HTTP_X_WP_NONCE'];
        }

        $result = wp_verify_nonce( $nonce, $action );
        do_action( 'check_ajax_referer', $action, $result );

        if ( $die && false === $result ) {
            if ( wp_doing_ajax() ) {
                wp_die( -1, 403 );
            } else {
                die( '-1' );
            }
        }

        return $result;
    }

}
