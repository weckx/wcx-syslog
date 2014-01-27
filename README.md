##wcx-syslog
=================

Simple classes to send messages to arbitrary syslog servers according to RFC5424 or RFC3164. Messages can be sent using TCP or UDP. Extensible through creation of new Transporter classes.

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
