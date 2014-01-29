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

namespace Wcx\Syslog\Message;

/**
 * Syslog Message acording to RFC5424 suports all fields and structured data.
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Syslog extends MessageAbstract
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
        return $this->header['PROCID'];
    }

    /**
     * Set the message procid
     * @var string $procid
     * @return SyslogMessage
     */
    public function setProcId($procid)
    {
        $this->header['PROCID'] = $procid;
        return $this;
    }

    /**
     * Returns the message id
     * @return string
     */
    public function getMsgId()
    {
        return $this->header['MSGID'];
    }

    /**
     * Set the message id
     * @var string $msgId
     * @return SyslogMessage
     */
    public function setMsgId($msgId)
    {
        $this->header['MSGID'] = $msgId;
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
        $this->structuredData[] = '[' . $name . ' ' . implode(' ', $params) . ']';
        return $this;
    }

    /**
     * Return the free form message
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set the free form message
     * @param string $msg
     * @return  Message
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    /**
     * Return the message as a string
     * @return string
     */
    public function getMessageString()
    {
        $str = implode(' ', $this->header);
        if (count($this->structuredData)) {
            $str .= ' ' . implode('', $this->structuredData);
        } else {
            $str .= ' ' . self::NILVALUE;
        }
        $str .= ' ' . $this->msg;
        return $str;
    }
}
