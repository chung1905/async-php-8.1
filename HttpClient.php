<?php

require_once __DIR__ . "/Async.php";

const USEC = 20_000;
const LEN = 5000;
const STREAM_CHECK_READ = 1;
const STREAM_CHECK_WRITE = 2;

class HttpClient
{
    public function fetch(string $url): \Fiber
    {
        $parts = parse_url($url);
        $port = (int)($parts['port'] ?? "80");
        $host = $parts['host'];
        $path = $parts['path'] ?? "/";

        $socket = $this->createAsyncSocket($host, $port);
        unset($parts);

        #echo "HttpClient::fetch\n";
        return new \Fiber(function () use ($socket, $host, $path) {
            #echo "HttpClient::fetch Fiber\n";
            $body = sprintf("GET %s\r\n", $path);
            $body .= sprintf("Host: %s\r\n", $host);
            $body .= "Accept: */*\r\n";
            $body .= "\r\n";

            \Async::async($this->write($socket, $body));
            return \Async::async($this->read($socket,));
        });
    }

    private function write(mixed $socket, string $data): \Fiber
    {
        return new \Fiber(function () use ($socket, $data) {
            do {
                $needCheck = $this->checkStream($socket, STREAM_CHECK_WRITE);
                if ($needCheck === 1) {
                    $bytes = fwrite($socket, $data, LEN);
                    if ($bytes !== false) {
                        $data = substr($data, LEN);
                    } else {
                        break;
                    }
                }

                \Fiber::suspend();
            } while ($data !== "");
        });
    }

    private function read(mixed $socket): \Fiber
    {
        return new \Fiber(function () use ($socket) {
            $buffer = "";

            do {
                $needCheck = $this->checkStream($socket, STREAM_CHECK_READ);
                if ($needCheck === 1) {
                    $data = fread($socket, LEN);
                    if ($data !== false) {
                        if ($data === "") {
                            if ($buffer !== "") {
                                break;
                            }
                        } else {
                            $buffer .= $data;
                        }
                    }
                }
                \Fiber::suspend();
            } while (true);

            return $buffer;
        });
    }

    private function checkStream(mixed $stream, int $mode = STREAM_CHECK_READ): int|false
    {
        $reads = [];
        $writes = [];
        $excepts = [];

        if ($mode & STREAM_CHECK_READ) {
            $reads = [$stream];
        }
        if ($mode & STREAM_CHECK_WRITE) {
            $writes = [$stream];
        }

        return stream_select($reads, $writes, $excepts, 0, USEC);
    }

    private function createAsyncSocket(string $host, int $port = 80)
    {
        $socket = stream_socket_client(
            sprintf("tcp://%s:%s", $host, $port),
            $ec,
            $em,
            null,
            STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT,
        );

        stream_set_blocking($socket, false);

        return $socket;
    }
}
