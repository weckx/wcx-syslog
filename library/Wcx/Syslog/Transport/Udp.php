<?php
/**
 * php-syslog-sender
 *
 * LICENSE
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Felipe Weckx
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   System
 * @package    Syslog
 * @copyright  Copyright (c) 2014 Felipe Weckx
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author Felipe Weckx <felipe@weckx.net>
 */
namespace Wcx\Syslog\Transport;

use Weckx\Syslog\Message\MessageInterface;

/**
 * Transport to send messages through UDP protocol
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Udp implements TransportInterface
{
    const DEFAULT_UDP_PORT = 514;

    /**
     * Send the syslog to target host using the UDP protocol. Note that the UDP protocol
     * is stateless, which means we can't confirm that the message was received by the
     * other end
     * @param  MessageInterface $message
     * @param  string           $target  Host:port, if port not specified uses default 514
     * @return void
     * @throws \Exception\RuntimeException If there's an error creating the socket
     */
    public function send(MessageInterface $message, $target)
    {
        $msg = $message->toString();
        list($host, $port) = explode(':', $target);
        if (!$port) {
            $port = self::DEFAULT_UDP_PORT;
        }

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock === false) {
            $errorCode = socket_last_error();
            $errorMsg = socket_strerror($errorCode);
            throw new \Exception\RuntimeException("Error creating socket: [$errorCode] $errorMsg");
        }

        socket_sendto($sock, $msg, strlen($msg), 0, $host, $port);
        socket_close($sock);
    }
}
