<?php

namespace WcxTest\Syslog;

use Wcx\Syslog\Message\Bsd as BsdMessage;

class MessageBsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Example 1 of RFC3164
     */
    public function testSecurityCritical()
    {
        $expected = "<34>Oct 11 22:14:15 mymachine su: 'su root' failed for lonvick on /dev/pts/8";
        $message = new BsdMessage();
        $message->setTimestamp(new \DateTime("2001-10-11 22:14:15"))
                ->setFacility(BsdMessage::FACILITY_SECURITY)
                ->setPriority(BsdMessage::PRIORITY_CRITICAL)
                ->setHostname("mymachine")
                ->setAppName("su")
                ->setMsg("'su root' failed for lonvick on /dev/pts/8");

        $this->assertEquals($expected, $message->getMessageString());
    }
}
