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
 * @copyright  Copyright (c) 2013 Felipe Weckx
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author Felipe Weckx <fweckx@mt4.com.br>
 */

namespace Weckx\Syslog;

/**
 * BSD Syslog Message acording to RFC3164. Note that this is and old format and
 * should only be used to send messages to legacy syslog servers that do not support the
 * new RFC5424 format.
 *
 * Messages can be sent through UDP or TCP. Use:
 *
 * <code>
 * <?php
 * //Create the object
 * $message = new \SyslogMessage\BsdMessage();
 *
 * //Set basic message values
 * $message->setPriority(Message::PRIORITY_NOTICE)
 *         ->setFacility(Message::FACILITY_USER)
 *         ->setAppName('php-syslog-sender')
 *         ->setMsg('Test message');
 *
 * //Send to host 192.168.0.1 using UDP
 * $message->sendUdp('192.168.0.1');
 * ?>
 * </code>
 *
 * @author Felipe Weckx <fweckx@mt4.com.br>
 * @version 1.0
 */
class BsdMessage
{
    /**
     * RFC3164 specified facilities
     */
    const FACILITY_KERNEL       = 0;
    const FACILITY_USER         = 1;
    const FACILITY_MAIL         = 2;
    const FACILITY_SYSTEM       = 3;
    const FACILITY_SECURITY     = 4;
    const FACILITY_SYSLOG       = 5;
    const FACILITY_PRINTER      = 6;
    const FACILITY_NETWORK_NEWS = 7;
    const FACILITY_UUCP         = 8;
    const FACILITY_CLOCK        = 9;
    const FACILITY_AUTH         = 10;
    const FACILITY_FTP          = 11;
    const FACILITY_NTP          = 12;
    const FACILITY_AUDIT        = 13;
    const FACILITY_ALERT        = 14;
    const FACILITY_CLOCK2       = 15;
    const FACILITY_LOCAL0       = 16;
    const FACILITY_LOCAL1       = 17;
    const FACILITY_LOCAL2       = 18;
    const FACILITY_LOCAL3       = 19;
    const FACILITY_LOCAL4       = 20;
    const FACILITY_LOCAL5       = 21;
    const FACILITY_LOCAL6       = 22;
    const FACILITY_LOCAL7       = 23;

    /**
     * RFC3164 specified priorities
     */
    const PRIORITY_EMERGENCY = 0;
    const PRIORITY_ALERT     = 1;
    const PRIORITY_CRITICAL  = 2;
    const PRIORITY_ERROR     = 3;
    const PRIORITY_WARNING   = 4;
    const PRIORITY_NOTICE    = 5;
    const PRIORITY_INFO      = 6;
    const PRIORITY_DEBUG     = 7;

    /**
     * Message facility
     * @var int
     */
    protected $_facility = self::FACILITY_LOCAL4;

    /**
     * Message Priority
     * @var int
     */
    protected $_priority = self::PRIORITY_DEBUG;

    /**
     * PRI part of the message
     * @var int
     */
    protected $_pri = null;

    /**
     * Parts of the message header
     * @var array
     */
    protected $_header = array(
        'TIMESTAMP' => '', 'HOSTNAME' => ''
    );

    protected $_message = array(
        'APP-NAME' => '', 'MSG' => ''
    );

    /**
     * Constructor. Sets the current hostname and timestamp
     */
    public function __construct()
    {
        $this->setHostname(php_uname('n'));
        $this->setTimestamp(new \DateTime());
        $this->_calculatePri();
    }

    /**
     * Returns the message facility
     * @return int
     */
    public function getFacility()
    {
        return $this->_facility;
    }

    /**
     * Set the message facility
     * @var int $facility
     * @return SyslogMessage
     */
    public function setFacility($facility)
    {
        $this->_facility = $facility;
        $this->_calculatePri();
        return $this;
    }

    /**
     * Returns the message priority
     * @return int
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Set the message priority
     * @var int $priority
     * @return SyslogMessage
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
        $this->_calculatePri();
        return $this;
    }

    /**
     * Returns the timestamp of the message generation
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_header['TIMESTAMP'];
    }

    /**
     * Set the timestamp of the message generation
     * @var int|\DateTime $timestamp UNIX Timestamp (as generated by time() or strtotime()) or a php DateTime object
     * @return SyslogMessage
     */
    public function setTimestamp($timestamp)
    {
        if (!$timestamp instanceof \DateTime) {
            $date = new \DateTime();
            $date->setTimestamp($timestamp);
        } else {
            $date = $timestamp;
        }
        $this->_header['TIMESTAMP'] = $date->format('M d H:i:s');
        return $this;
    }

    /**
     * Returns the hostname of the machine where the message was created
     * @return string
     */
    public function getHostname()
    {
        return $this->_header['HOSTNAME'];
    }

    /**
     * Set the hostname of the machine where the message was created
     * @var string $hostname
     * @return SyslogMessage
     */
    public function setHostname($hostname)
    {
        $this->_header['HOSTNAME'] = $hostname;
        return $this;
    }

    /**
     * Returns the name of the application that created the message
     * @return string
     */
    public function getAppName()
    {
        return $this->_message['APP-NAME'];
    }

    /**
     * Set the name of the application that created the message
     * @var string $appName
     * @return SyslogMessage
     */
    public function setAppName($appName)
    {
        $this->_message['APP-NAME'] = $appName;
        return $this;
    }

    /**
     * Return the free form message
     * @return string
     */
    public function getMsg()
    {
        return $this->_message['MSG'];
    }

    /**
     * Set the free form message
     * @param string $msg
     * @return  Message
     */
    public function setMsg($msg)
    {
        $this->_message['MSG'] = $msg;
        return $this;
    }

    /**
     * Return the message as a string
     * @return string
     */
    public function toString()
    {
        $str = $this->_pri;
        $str .= implode(' ', $this->_header);
        if ($this->_message['APP-NAME']) {
            $str .= ' ' . $this->_message['APP-NAME'] . ': ' . $this->_message['MSG'];
        } else {
            $str .= ' ' . $this->_message['MSG'];
        }
        return $str;
    }

    /**
     * Sends the message to the specified server using UDP. Proxies to sendUdp
     * @param  string  $server
     * @param  integer $port
     * @return Message
     */
    public function send($server, $port = 514)
    {
        return $this->sendUdp($server, $port);
    }

    /**
     * Send message to server using UDP
     * @param  string  $server Server hostname or IP
     * @param  integer $port   Port where the syslog server is listening. Default is 514
     * @return Message
     * @throws \Exception If there's an error
     */
    public function sendUdp($server, $port = 514)
    {
        $msg = $this->toString();

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock === false) {
            $errorCode = socket_last_error();
            $errorMsg = socket_strerror($errorCode);
            throw new \Exception("Error creating socket: [$errorCode] $errorMsg");
        }

        socket_sendto($sock, $msg, strlen($msg), 0, $server, $port);
        socket_close($sock);

        return $this;
    }

    /**
     * Send message to server using TCP
     * @param  string  $server Server hostname or IP
     * @param  integer $port   Port where the syslog server is listening. Default is 514
     * @return Message
     * @throws \Exception If there's an error
     */
    public function sendTcp($server, $port = 514, $timeout = 15)
    {
        $msg = $this->toString() . "\n";

        $sock = fsockopen($server, $port, $errorCode, $errorMsg, $timeout);
        if ($sock === false) {
            throw new \Exception("Error connecting to {$server}: [{$errorCode}] {$errorMsg}");
        }

        $written = fwrite($sock, $msg);
        fclose($sock);

        if ($written != strlen($msg)) {
            throw new \Exception("Error sending message to {$server} not all bytes sent.");
        }

        return $this;
    }

    /**
     * Calculate the PRI value for the header (facility*8 + priority)
     * @return void
     */
    protected function _calculatePri()
    {
        $pri = ($this->getFacility() * 8) + $this->getPriority();
        $this->_pri = '<' . $pri . '>';
    }

    /**
     * For auto-conversion to string. Just proxies to toString()
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
