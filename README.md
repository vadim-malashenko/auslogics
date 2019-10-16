Обращение по адресу /files/<имя файла>.exe должно возвратить файл (file.exe),
расположенный в корне сайта, а также установить cookie с параметром referrer равным
домену, с которого пришел данный пользователь для закачки этого файла. Имя отдаваемого
файла должно быть таким же, какое было в запросе.

Например, если на сайте www.cnet.com поместили прямую ссылку на
http://www.auslogics.com/files/myfile.exe и посетитель щелкает по ней, то он сможет скачать
файл /files/myfile.exe (а на самом деле /file.exe) и на его компьютере будет оставлена cookie с
referrer = cnet.com.

Напишите скрипт (PHP), реализующий этот функционал, приведите текст .htaccess, если
нужен.
