##wcx-syslog
=================

Simple classes to send messages to arbitrary syslog servers according to RFC5424 or RFC3164. Messages can be sent using TCP or UDP. Extensible through creation of new Transport classes.

Sample usage:

```php
//It's usually better to use the BSD format since it's widely supported
use Wcx\Syslog\Message\Bsd as SyslogMessage;

//Create the message
$message = new SyslogMessage();

//Set basic message values
$message->setPriority(SyslogMessage::PRIORITY_NOTICE)
        ->setFacility(SyslogMessage::FACILITY_USER)
        ->setAppName('wcx-syslog')
        ->setMsg('Test message');

//Send to host 192.168.0.1 using UDP
$message->send('192.168.0.1');

//Send to 10.0.0.1 on port 5140 using TCP
$message->send('10.0.0.1:5140', new Wcx\Syslog\Transport\Tcp());
```
