<?php
/**
 * php-syslog-sender
 *
 * LICENSE
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Felipe Weckx
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
class MessageSyslog extends MessageAbstract
{
    /**
     * Value for empty fields
     */
    const NILVALUE = '-';

    /**
     * Syslog message version
     */
    const VERSION = '1';

    /**
     * Parts of the message header
     * @var array
     */
    protected $header = array(
        'PRI' => null, 'TIMESTAMP' => self::NILVALUE, 'HOSTNAME' => self::NILVALUE,
        'APP-NAME' => self::NILVALUE, 'PROCID' => self::NILVALUE, 'MSGID' => self::NILVALUE
    );

    /**
     * Structured data for the message
     * @var array
     */
    protected $structuredData = array();

    /**
     * The free form message
     * @var string
     */
    protected $msg = self::NILVALUE;

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
    public function getMessageString()
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
}
