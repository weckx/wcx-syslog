<?php

namespace WcxTest\Syslog;

use Wcx\Syslog\Message\Bsd as BsdMessage;

class MessageBsdTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleMessage()
    {
        $message = new BsdMessage();

    }
}
