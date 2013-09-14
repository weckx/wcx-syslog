##php-syslog-sender
=================

Simple classes to send messages to arbitrary syslog servers according to RFC5424 or RFC3164.
There are two classes: \SyslogMessage\Message, which the preferred and creates messages according
to the RFC5424 (http://tools.ietf.org/html/rfc5424) and \SyslogMessage\BsdMessage which creates messages
according to RFC3164 (http://tools.ietf.org/html/rfc3164).

The two classes are completely independent, so you can use just the one you want (or both).

The messages can be sent through UDP or TCP, just use the sendUdp or sendTcp methods.

Sample usage:

```php
//Create the message
$message = new \SyslogMessage\Message();

//Set basic message values
$message->setPriority(Message::PRIORITY_NOTICE)
        ->setFacility(Message::FACILITY_USER)
        ->setAppName('php-syslog-sender')
        ->setMsg('Test message');

//Send to host 192.168.0.1 using UDP
$message->sendUdp('192.168.0.1');
```
