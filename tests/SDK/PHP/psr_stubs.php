<?php

declare(strict_types=1);
// Minimal PSR-7/17/18 implementations for tests (no external deps)

namespace Psr\Http\Message {
    interface StreamInterface
    {
        public function __toString();

        public function close();

        public function detach();

        public function getSize();

        public function tell();

        public function eof();

        public function isSeekable();

        public function seek($offset, $whence = SEEK_SET);

        public function rewind();

        public function isWritable();

        public function write($string);

        public function isReadable();

        public function read($length);

        public function getContents();

        public function getMetadata($key = null);
    }

    interface RequestInterface
    {
        public function getMethod();

        public function getUri();

        public function getHeaderLine($nameEntity);

        public function getHeaders();

        public function withHeader($nameEntity, $value);

        public function withBody(StreamInterface $b);

        public function getBody();
    }

    interface ResponseInterface
    {
        public function getStatusCode();

        public function getHeaders();

        public function getBody();
    }

    interface RequestFactoryInterface
    {
        public function createRequest(string $method, $uri): RequestInterface;
    }

    interface StreamFactoryInterface
    {
        public function createStream(string $content = ''): StreamInterface;
    }
}

namespace Psr\Http\Client {
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    interface ClientInterface
    {
        public function sendRequest(RequestInterface $request): ResponseInterface;
    }
}

namespace Tests\Support {
    use Psr\Http\Client\ClientInterface;
    use Psr\Http\Message\RequestFactoryInterface;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\StreamFactoryInterface;
    use Psr\Http\Message\StreamInterface;

    final class MemoryStream implements StreamInterface
    {
        private string $buf;

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
        public function close(): void
        {
        }

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
         * @return void
         */
        /**
         * @return void
         */
        public function seek($o, $w = SEEK_SET): void
        {
        }

        /**
         * @return void
         */
        public function rewind(): void
        {
        }

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
         * @return int
         */
        /**
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
         * @return string
         */
        /**
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
         * @return null
         */
        /**
         * @return null
         */
        public function getMetadata($k = null)
        {
            return null;
        }
    }

    final class MemoryResponse implements ResponseInterface
    {
        public function __construct(private readonly int $code, private readonly array $headers, private readonly string $body)
        {
        }

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
         * @return MemoryStream
         */
        /**
         * @return MemoryStream
         */
        public function getBody()
        {
            return new MemoryStream($this->body);
        }
    }

    final class MemoryRequest implements RequestInterface
    {
        private array $headers = [];
        private StreamInterface $body;

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
         * @return string
         */
        /**
         * @return string
         */
        public function getHeaderLine($nameEntity)
        {
            $n = strtolower($nameEntity);
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
         * @return $this|MemoryRequest
         */
        /**
         * @return $this|MemoryRequest
         */
        public function withHeader($nameEntity, $value)
        {
            $c = clone $this;
            $c->headers[$nameEntity] = (array) $value;

            return $c;
        }

        /**
         * @return $this|MemoryRequest
         */
        /**
         * @return $this|MemoryRequest
         */
        public function withBody(StreamInterface $b)
        {
            $c = clone $this;
            $c->body = $b;

            return $c;
        }

        /**
         * @return StreamInterface|MemoryStream
         */
        /**
         * @return StreamInterface|MemoryStream
         */
        public function getBody()
        {
            return $this->body;
        }
    }

    final class MemoryRequestFactory implements RequestFactoryInterface
    {
        public function createRequest(string $method, $uri): RequestInterface
        {
            return new MemoryRequest($method, (string) $uri);
        }
    }

    final class MemoryStreamFactory implements StreamFactoryInterface
    {
        public function createStream(string $content = ''): StreamInterface
        {
            return new MemoryStream($content);
        }
    }

    final class DummyHttpClient implements ClientInterface
    {
        public ?RequestInterface $last = null;
        /** @var callable(RequestInterface):ResponseInterface */
        public $responder;

        public function __construct(callable $responder)
        {
            $this->responder = $responder;
        }

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            $this->last = $request;
            $fn = $this->responder;

            return $fn($request);
        }
    }
}
