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
    public function send(MessageInterface $message, $target);
}
