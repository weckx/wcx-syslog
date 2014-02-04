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

use \Wcx\Syslog\Message\MessageInterface;

/**
 * Transport to send messages through the TCP protocol
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Tcp implements TransportInterface
{
    const DEFAULT_TCP_PORT = 514;

    /**
     * Connection timeout
     * @var int
     */
    protected $timeout = 15;

    /**
     * Retorna the connection timeout
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Define the connection timeout
     * @var int $timeout
     * @return Tcp
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Send the syslog to target host using the TCP protocol.Automatically adds a \n character to end
     * of the message string
     * @param  MessageInterface $message
     * @param  string           $target  Host:port, if port not specified uses default 514
     * @return void
     * @throws \RuntimeException If there's an error creating the socket
     */
    public function send(MessageInterface $message, $target)
    {
        //Add EOL to message so the receiver knows it has ended
        $msg = $message->getMessageString() . "\n";

        if (strpos($target, ':')) {
            list($host, $port) = explode(':', $target);
        } else {
            $host = $target;
            $port = self::DEFAULT_TCP_PORT;
        }

        $sock = fsockopen($host, $port, $errorCode, $errorMsg, $this->timeout);
        if ($sock === false) {
            throw new \RuntimeException("Error connecting to {$server}: [{$errorCode}] {$errorMsg}");
        }

        $written = fwrite($sock, $msg);
        fclose($sock);

        if ($written != strlen($msg)) {
            throw new \RuntimeException("Error sending message to {$server} not all bytes sent.");
        }

        return $this;
    }
}
