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

/**
 * Interface for transport os syslog messages
 */
interface TransportInterface
{
    /**
     * Sends a message through the transport
     * @param  Weckx\Syslog\Message $message
     * @return void
     * @throws Exception\RuntimeException If an error occurs on sending the message
     */
    public function send($message);
}
