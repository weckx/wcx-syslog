<?php
/**
 * wcx-syslog
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
