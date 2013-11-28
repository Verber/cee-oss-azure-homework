cee-oss-azure-homework
======================

Homework for Azure training in Warsaw

To run this you need install azure command line tool (see http://www.windowsazure.com/en-us/manage/install-and-configure-cli/#install)
Then you need to get publishing sertificate for Windows Azure (see http://www.windowsazure.com/en-us/manage/install-and-configure-cli/#Configure)

Also you will need pem and key files to connect to linuz machine. To generate it use opensssl

openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout myPrivateKey.key -out myCert.pem
