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
 * BSD Syslog Message acording to RFC3164. Note that this is and old format and
 * should only be used to send messages to legacy syslog servers that do not support the
 * new RFC5424 format.
 *
 * @author Felipe Weckx <felipe@weckx.net>
 */
class Bsd extends MessageAbstract
{
}
