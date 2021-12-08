# Async PHP 8.1

Using PHP 8.1 Fiber to create HTTP async client
- [x] Support async
- [ ] Support SSL
- [ ] Support POST/PUT
- [ ] Support query in URL

## Run
Install PHP 8.1 and run
```shell
$ php index.php
```
Output should be:
```
Start http://httpbin.org/delay/8
Start http://httpbin.org/delay/7
Start http://httpbin.org/delay/5
Start http://httpbin.org/delay/2
Start http://httpbin.org/delay/4
Start http://httpbin.org/delay/6
Start http://httpbin.org/delay/1
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/1
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/2
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/4
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/5
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/6
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/7
Done http://a0207c42-pmhttpbin-pmhttpb-c018-592832243.us-east-1.elb.amazonaws.com/delay/8
Done in 9.150323s
```
