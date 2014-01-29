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
 * Transport to send messages through the TCP protocol
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Tcp implements TransportInterface
{
    const DEFAULT_TCP_PORT = 514;

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
        //Add EOL to message so the receiver knows it has ended
        $msg = $message->toString() . "\n";

        list($host, $port) = explode(':', $target);
        if (!$port) {
            $port = self::DEFAULT_UDP_PORT;
        }

        $sock = fsockopen($host, $port, $errorCode, $errorMsg, $timeout);
        if ($sock === false) {
            throw new \Exception\RuntimeException("Error connecting to {$server}: [{$errorCode}] {$errorMsg}");
        }

        $written = fwrite($sock, $msg);
        fclose($sock);

        if ($written != strlen($msg)) {
            throw new \Exception\RuntimeException("Error sending message to {$server} not all bytes sent.");
        }

        return $this;
    }
}
