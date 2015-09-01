TermGate
============

Author: Arno0x0x - [@Arno0x0x](http://twitter.com/Arno0x0x)

TermGate is a web application that allows you to run console (*shell*) commands on your remote server, including getting an interactive shell, directly from a web browser.

The initial idea was to create a PHP wrapper around the wonderful job done on the [GoTTY project](https://github.com/yudai/gotty) writen by Iwasaki Yudai. But I decided to add a simple shell command call, much faster to execute for simple commands that don't need interaction or dynamic updates.

That means you can get a shell, or even an ssh client into your browser.

Check out this demo :
[![Demo](https://dl.dropboxusercontent.com/s/qglm4gvjjbritb6/termgate_video.png?dl=0)](https://vimeo.com/137886386)

The app is distributed under the terms of the [GPLv3 licence](http://www.gnu.org/copyleft/gpl.html).

Dependencies
----------------

On the server side, TermGate requires PHP5 and GoTTY:

- Go grab a [GoTTY](https://github.com/yudai/gotty/releases) binary release for your system. It's just one binary file, no dependencies or complex installation. Get the one file binary and drop it onto your system. It's available for almost every Unix flavor (including Raspberry Pi and MAC OSX).

TermGate also relies on one PHP5 library that you'll have to install on your own:

- The SQLite3 library (*on debian like systems*: sudo apt-get install php5-sqlite)


Security Aspects
-----------

TermGate doesn't handle any user authentication or authorization. So you should put it behind some kind of authentication at the web server level. It is advisable to use a two factor authentication portal such as [TwoFactorAuth](https://github.com/Arno0x/TwoFactorAuth) (*that I wrote :-)*).

TermGate can be configured to only accept HTTPS (HTTP over SSL) connections, see the configuration file.

TermGate can be configured to allow only commands that are already stored in the "command set" database. Although it's not really a security feature, this might help controlling which commands are made available in the interface.


Installation & Configuration
------------

* Unzip the TermGate package in your web server's directory and ensure all files and folders have appropriate *user:group* ownership, depending on your installation (*might be something like www-data:www-data*).

* **Edit the configuration file config.php** at the root path of TermGate directory and make it match your needs and your installation. Main parameters are :
  - GOTTY\_PATH : Set it to the full path of the GoTTY binary (*that you've previously installed*).
  - GOTTY\_TCP_PORT : Set the TCP listening port that will be used by GoTTY.
  - GOTTY\_BIND_INTERFACE : Set the IP address GoTTY should bind on. If you choose to make GoTTY reachable behing an Nginx reverse-proxy (*see section below*), it is safer to set it to `127.0.0.1`.
  - GOTTY\_TERM : Set the TERM environment GoTTY will use (*can be sometthing like 'vt100', 'xterm', etc.*).
  - GOTTY\_URL : Set the URL at which GoTTY will be reachable. Again this depends on whether or not you'll make it reachable behind Nginx or not, which GOTTY\_TCP_PORT you set etc.
  - HTTPS\_ONLY : If set to true, the application will only allow HTTPS connections.
  - RESTRICTED\_COMMAND\_SET : If set to `true`, only commands previously saved in the command set database can be executed. Also, no new command can be added, no command can be deleted. Initially, you must set it to `false` in order to add commands to you command set database.
  - **RUN\_AS_USER** : By default, your web server and PHP server runs as a dedicated user (*such as www-data*) which is probably not the one you want to run commands as. This parameter allows you to tell which user will be used to execute commands. TermGate will need to be able to `sudo` to another user to execute all commands **under a bash shell**. In order for this to work, it is required to modify the `/etc/sudoers` file. For example, if your web server is running as user `www-data` and you set RUN\_AS_USER to user `pi`:

```
sudo echo "www-data ALL=(pi) NOPASSWD:/bin/bash" >> /etc/sudoers
```

* Next, open a browser and **FIRST** navigate the TermGate **install.php** page (*exact path will vary depending on where you installed the TermGate application*):
https://www.example.com/termgate/install.php . This page will finalize the installation process by creating the SQLite3 command set database.

* Eventually, navigate to the home page, eg : https://www.example.com/termgate/index.php


[OPTIONNAL] NGINX integration
---------------------
Nginx can be used as a reverse-proxy to access GoTTY. It prevents us from opening another TCP port on the public facing interface of our web server.
You'll have to edit your Nginx configuration file.

Assuming the TermGate application was deployed in a location named /termgate/ on your webserver, and that you set GOTTY\_TCP\_PORT to 3850, add the following line under the "server" directive:

```
location /gotty/ {
    proxy_buffering off;
    proxy_pass_header Server;
    proxy_set_header Host $http_host;
    proxy_redirect off;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Scheme $scheme;
    proxy_pass http://127.0.0.1:3850/;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
}   
```

Credits
--------
Iwasaki Yudai for his fantastic [GoTTY project](https://github.com/yudai/gotty).

If you have a feature request, bug report, feel free to contact me on my twitter page.