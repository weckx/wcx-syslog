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
 * @author Felipe Weckx <felipe@weckx.net>
 */

namespace Wcx\Syslog\Message;

/**
 * Syslog Message acording to RFC5424 suports all fields and structured data.
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Message
{
    /**
     * RFC5424 specified facilities
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
     * RFC5424 specified priorities
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
     * Value for empty fields
     */
    const NILVALUE = '-';

    /**
     * Syslog message version
     */
    const VERSION = '1';

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
     * Parts of the message header
     * @var array
     */
    protected $_header = array(
        'PRI' => null, 'TIMESTAMP' => self::NILVALUE, 'HOSTNAME' => self::NILVALUE,
        'APP-NAME' => self::NILVALUE, 'PROCID' => self::NILVALUE, 'MSGID' => self::NILVALUE
    );

    /**
     * Structured data for the message
     * @var array
     */
    protected $_structuredData = array();

    /**
     * The free form message
     * @var string
     */
    protected $_msg = self::NILVALUE;

    /**
     * Constructor. Initializes the message with the current hostname and timestamp
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
        $this->_header['TIMESTAMP'] = $date->format(\DateTime::RFC3339);
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
        return $this->_header['APP-NAME'];
    }

    /**
     * Set the name of the application that created the message
     * @var string $appName
     * @return SyslogMessage
     */
    public function setAppName($appName)
    {
        $this->_header['APP-NAME'] = $appName;
        return $this;
    }

    /**
     * Returns the message procid
     * @return string
     */
    public function getProcId()
    {
        return $this->_header['PROCID'];
    }

    /**
     * Set the message procid
     * @var string $procid
     * @return SyslogMessage
     */
    public function setProcId($procid)
    {
        $this->_header['PROCID'] = $procid;
        return $this;
    }

    /**
     * Returns the message id
     * @return string
     */
    public function getMsgId()
    {
        return $this->_header['MSGID'];
    }

    /**
     * Set the message id
     * @var string $msgId
     * @return SyslogMessage
     */
    public function setMsgId($msgId)
    {
        $this->_header['MSGID'] = $msgId;
        return $this;
    }

    /**
     * Add a structured data block
     * @param string $name   The name of the block. Must be in ASCII and in the format name@number
     *                       or a default IANA name (see section 7 of RFC5424)
     *
     * @param array  $values Array of key-value pairs
     * @return  Message
     */
    public function addStructuredData($name, array $values)
    {
        $params = array();
        foreach ($values as $key => $value) {
            $params[] = $key . '="' . $value . '"';
        }
        $this->_structuredData[] = '[' . $name . ' ' . implode(' ', $params) . ']';
        return $this;
    }

    /**
     * Return the free form message
     * @return string
     */
    public function getMsg()
    {
        return $this->_msg;
    }

    /**
     * Set the free form message
     * @param string $msg
     * @return  Message
     */
    public function setMsg($msg)
    {
        $this->_msg = $msg;
        return $this;
    }

    /**
     * Return the message as a string
     * @return string
     */
    public function toString()
    {
        $str = implode(' ', $this->_header);
        if (count($this->_structuredData)) {
            $str .= ' ' . implode('', $this->_structuredData);
        } else {
            $str .= ' ' . self::NILVALUE;
        }
        $str .= ' ' . $this->_msg;
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
        $this->_header['PRI'] = '<' . $pri . '>' . self::VERSION;
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
