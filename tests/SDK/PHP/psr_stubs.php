<?php

declare(strict_types=1);
// Minimal PSR-7/17/18 implementations for tests (no external deps)

namespace Psr\Http\Message {
    /**
     *
     */

    /**
     *
     */
    interface StreamInterface
    {
        /**
         * @return mixed
         */
        public function __toString();

        /**
         * @return mixed
         */
        public function close();

        /**
         * @return mixed
         */
        public function detach();

        /**
         * @return mixed
         */
        public function getSize();

        /**
         * @return mixed
         */
        public function tell();

        /**
         * @return mixed
         */
        public function eof();

        /**
         * @return mixed
         */
        public function isSeekable();

        /**
         * @param $offset
         * @param $whence
         * @return mixed
         */
        /**
         * @param $offset
         * @param $whence
         * @return mixed
         */
        public function seek($offset, $whence = SEEK_SET);

        /**
         * @return mixed
         */
        public function rewind();

        /**
         * @return mixed
         */
        public function isWritable();

        /**
         * @param $string
         * @return mixed
         */
        /**
         * @param $string
         * @return mixed
         */
        public function write($string);

        /**
         * @return mixed
         */
        public function isReadable();

        /**
         * @param $length
         * @return mixed
         */
        /**
         * @param $length
         * @return mixed
         */
        public function read($length);

        /**
         * @return mixed
         */
        public function getContents();

        /**
         * @param $key
         * @return mixed
         */
        /**
         * @param $key
         * @return mixed
         */
        public function getMetadata($key = null);
    }

    /**
     *
     */

    /**
     *
     */
    interface RequestInterface
    {
        /**
         * @return mixed
         */
        public function getMethod();

        /**
         * @return mixed
         */
        public function getUri();

        /**
         * @param $name
         * @return mixed
         */
        /**
         * @param $name
         * @return mixed
         */
        public function getHeaderLine($name);

        /**
         * @return mixed
         */
        public function getHeaders();

        /**
         * @param $name
         * @param $value
         * @return mixed
         */
        /**
         * @param $name
         * @param $value
         * @return mixed
         */
        public function withHeader($name, $value);

        /**
         * @param \Psr\Http\Message\StreamInterface $b
         * @return mixed
         */
        public function withBody(StreamInterface $b);

        /**
         * @return mixed
         */
        public function getBody();
    }

    /**
     *
     */

    /**
     *
     */
    interface ResponseInterface
    {
        /**
         * @return mixed
         */
        public function getStatusCode();

        /**
         * @return mixed
         */
        public function getHeaders();

        /**
         * @return mixed
         */
        public function getBody();
    }

    /**
     *
     */

    /**
     *
     */
    interface RequestFactoryInterface
    {
        /**
         * @param string $method
         * @param $uri
         * @return \Psr\Http\Message\RequestInterface
         */
        /**
         * @param string $method
         * @param $uri
         * @return \Psr\Http\Message\RequestInterface
         */
        public function createRequest(string $method, $uri): RequestInterface;
    }

    /**
     *
     */

    /**
     *
     */
    interface StreamFactoryInterface
    {
        /**
         * @param string $content
         * @return \Psr\Http\Message\StreamInterface
         */
        public function createStream(string $content = ''): StreamInterface;
    }
}

namespace Psr\Http\Client {

    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    /**
     *
     */

    /**
     *
     */
    interface ClientInterface
    {
        /**
         * @param \Psr\Http\Message\RequestInterface $request
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function sendRequest(RequestInterface $request): ResponseInterface;
    }
}

namespace Tests\Support {

    use Psr\Http\Client\ClientInterface;
    use Psr\Http\Message\{RequestInterface,
        StreamInterface,
        ResponseInterface,
        RequestFactoryInterface,
        StreamFactoryInterface
    };

    /**
     *
     */

    /**
     *
     */
    final class MemoryStream implements StreamInterface
    {
        private string $buf;

        /**
         * @param string $c
         */
        public function __construct(string $c = '')
        {
            $this->buf = $c;
        }

        /**
         * @return string
         */
        /**
         * @return string
         */
        public function __toString()
        {
            return $this->buf;
        }

        /**
         * @return void
         */
        public function close() {}

        /**
         * @return null
         */
        /**
         * @return null
         */
        public function detach()
        {
            return null;
        }

        /**
         * @return int
         */
        /**
         * @return int
         */
        public function getSize()
        {
            return strlen($this->buf);
        }

        /**
         * @return int
         */
        /**
         * @return int
         */
        public function tell()
        {
            return 0;
        }

        /**
         * @return true
         */
        /**
         * @return true
         */
        public function eof()
        {
            return true;
        }

        /**
         * @return false
         */
        /**
         * @return false
         */
        public function isSeekable()
        {
            return false;
        }

        /**
         * @param $o
         * @param $w
         * @return void
         */
        /**
         * @param $o
         * @param $w
         * @return void
         */
        public function seek($o, $w = SEEK_SET) {}

        /**
         * @return void
         */
        public function rewind() {}

        /**
         * @return true
         */
        /**
         * @return true
         */
        public function isWritable()
        {
            return true;
        }

        /**
         * @param $s
         * @return int
         */
        /**
         * @param $s
         * @return int
         */
        public function write($s)
        {
            $this->buf .= $s;
            return strlen($s);
        }

        /**
         * @return true
         */
        /**
         * @return true
         */
        public function isReadable()
        {
            return true;
        }

        /**
         * @param $l
         * @return string
         */
        /**
         * @param $l
         * @return string
         */
        public function read($l)
        {
            return '';
        }

        /**
         * @return string
         */
        /**
         * @return string
         */
        public function getContents()
        {
            return $this->buf;
        }

        /**
         * @param $k
         * @return null
         */
        /**
         * @param $k
         * @return null
         */
        public function getMetadata($k = null)
        {
            return null;
        }
    }

    /**
     *
     */

    /**
     *
     */
    final class MemoryResponse implements ResponseInterface
    {
        /**
         * @param int $code
         * @param array $headers
         * @param string $body
         */
        public function __construct(private readonly int $code, private readonly array $headers, private readonly string $body) {}

        /**
         * @return int
         */
        /**
         * @return int
         */
        public function getStatusCode()
        {
            return $this->code;
        }

        /**
         * @return array
         */
        /**
         * @return array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * @return \Tests\Support\MemoryStream
         */
        /**
         * @return \Tests\Support\MemoryStream
         */
        public function getBody()
        {
            return new MemoryStream($this->body);
        }
    }

    /**
     *
     */

    /**
     *
     */
    final class MemoryRequest implements RequestInterface
    {
        private array $headers = [];
        private StreamInterface $body;

        /**
         * @param string $method
         * @param string $url
         */
        public function __construct(private readonly string $method, private readonly string $url)
        {
            $this->body = new MemoryStream('');
        }

        /**
         * @return string
         */
        /**
         * @return string
         */
        public function getMethod()
        {
            return $this->method;
        }

        /**
         * @return string
         */
        /**
         * @return string
         */
        public function getUri()
        {
            return $this->url;
        }

        /**
         * @param $name
         * @return string
         */
        /**
         * @param $name
         * @return string
         */
        public function getHeaderLine($name)
        {
            $n = strtolower($name);
            foreach ($this->headers as $k => $v) {
                if (strtolower($k) === $n) {
                    return implode(', ', (array) $v);
                }
            }
            return '';
        }

        /**
         * @return array
         */
        /**
         * @return array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * @param $name
         * @param $value
         * @return $this|\Tests\Support\MemoryRequest
         */
        /**
         * @param $name
         * @param $value
         * @return $this|\Tests\Support\MemoryRequest
         */
        public function withHeader($name, $value)
        {
            $c = clone $this;
            $c->headers[$name] = (array) $value;
            return $c;
        }

        /**
         * @param \Psr\Http\Message\StreamInterface $b
         * @return $this|\Tests\Support\MemoryRequest
         */
        /**
         * @param \Psr\Http\Message\StreamInterface $b
         * @return $this|\Tests\Support\MemoryRequest
         */
        public function withBody(StreamInterface $b)
        {
            $c = clone $this;
            $c->body = $b;
            return $c;
        }

        /**
         * @return \Psr\Http\Message\StreamInterface|\Tests\Support\MemoryStream
         */
        /**
         * @return \Psr\Http\Message\StreamInterface|\Tests\Support\MemoryStream
         */
        public function getBody()
        {
            return $this->body;
        }
    }

    /**
     *
     */

    /**
     *
     */
    final class MemoryRequestFactory implements RequestFactoryInterface
    {
        /**
         * @param string $method
         * @param $uri
         * @return \Psr\Http\Message\RequestInterface
         */
        /**
         * @param string $method
         * @param $uri
         * @return \Psr\Http\Message\RequestInterface
         */
        public function createRequest(string $method, $uri): RequestInterface
        {
            return new MemoryRequest($method, (string) $uri);
        }
    }

    /**
     *
     */

    /**
     *
     */
    final class MemoryStreamFactory implements StreamFactoryInterface
    {
        /**
         * @param string $content
         * @return \Psr\Http\Message\StreamInterface
         */
        public function createStream(string $content = ''): StreamInterface
        {
            return new MemoryStream($content);
        }
    }

    /**
     *
     */

    /**
     *
     */
    final class DummyHttpClient implements ClientInterface
    {
        public ?RequestInterface $last = null;
        /** @var callable(RequestInterface):ResponseInterface */
        public $responder;

        /**
         * @param callable $responder
         */
        public function __construct(callable $responder)
        {
            $this->responder = $responder;
        }

        /**
         * @param \Psr\Http\Message\RequestInterface $request
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            $this->last = $request;
            $fn = $this->responder;
            return $fn($request);
        }
    }
}
