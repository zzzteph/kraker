<p align="center">
  <img src="https://github.com/zzzteph/Kraker/blob/main/docs/pics/cracker.png?raw=true"  height="350">
</p>

- [About](#about)
- [Setup](#setup)
  * [Server](#server)
    + [Setup](#setup-1)
  * [Agent](#agent)
    + [Setup](#setup-2)
    + [Build from source](#build-from-source)
    + [Using release](#using-release)
- [Contacts](#contacts)


# About

**Kraker** is a distributed password brute-force system that allows you to run and manage the hashcat on different servers and workstations, focused on easy of use. There were two main goals during the design and development: to create the most simple tool for distributed hash cracking and make it fault-tolerant.

**Kraker** consists of two main components - a server and an agent, which communicate through a REST API. You can read about their installation and configuration below.

**Kraker** continues to be in development, so the new functionality, documentation, and updates will be released as they become available. If you have suggestions for improvement or want to participate in the development or find bugs -  feel free to open issues, pull requests, or contact us: [@_w34kp455](https://twitter.com/w34kp455) and [@_asSheShouldBe](https://twitter.com/asSheShouldBe).





# Setup

Here you can find a manual on how to setup your own server, build an agent from the source and run it. You always can download a Release that is already built and run it.

## Server

Server is written on Laravel and provides a web interface for creating brute force tasks and also serves for managing agents. 

### Setup

```
sudo apt update
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/download/1.25.3/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
sudo systemctl enable docker

sudo docker-compose build app
sudo docker-compose up -d
sudo docker-compose exec app composer install
sudo docker-compose exec app php artisan key:generate
sudo docker-compose exec app php artisan migrate
sudo docker-compose exec app php artisan db:seed --class=HashtypeSeeder
sudo docker-compose exec app php artisan db:seed --class=UserSeeder

```

Navigate the server IP address and enter the next credentials:

- Login: admin
- Password: kraker



## Agent

It is written in .NET Core 5 and works on any OS where this framework is available - Linux, Windows, MacOS (not tested yet). The agent is responsible for performing brute-force tasks that it receives from the server.


### Setup

For the agent to work on the host, you need to install .NET Core 5, which can be downloaded from the following link:

https://dotnet.microsoft.com/download/dotnet/5.0

* Linux - https://docs.microsoft.com/ru-ru/dotnet/core/install/linux
* Windows -https://dotnet.microsoft.com/download/dotnet/thank-you/runtime-5.0.4-windows-x64-installer


### Build from source
1. Install .NET Core 5 SDK. Linux example:

```
wget https://packages.microsoft.com/config/debian/10/packages-microsoft-prod.deb -O packages-microsoft-prod.deb
sudo dpkg -i packages-microsoft-prod.deb
sudo apt-get update 
sudo apt-get install -y apt-transport-https
sudo apt-get update
sudo apt-get install -y dotnet-sdk-5.0
```

<p align="center">
  <img src="https://github.com/zzzteph/Kraker/blob/main/docs/pics/dotnet_install.gif?raw=true">
</p>


2. To compile agent from source code, go to agent folder and run the next command: ```dotnet build --configuration Release```. After that in ```Kracker.App/bin/Release/net5.0``` folder you will get the built project.
3. You need to download **hashcat** from the official page at  [https://hashcat.net/hashcat/](https://hashcat.net/hashcat/),  unpack it into the agent's folder.
4. Modify ```appsettings.json``` in ```Kracker.App/bin/Release/net5.0``` and put **ServerURL** and **Hashcat.Path** like:

```
{
        "HashCat":{
        "Path": "/home/admin/Kraker/agent/Kracker.App/bin/Release/net5.0/hashcat/hashcat.bin", //hashcat path 
                "SilencePeriodBeforeKill": 5, //default - 60 minutes
                "RepeatedStringsBeforeKill": 100, //defaut 1000 strings
                "NeedForce": true,
                "Options": "--quiet --status --status-timer=1 --machine-readable --logfile-disable --restore-disable --outfile-format=2"
        },
        "ServerUrl": "http://8.8.8.8/", //server url
        "InventoryCheckPeriod": 600,
        "HearbeatPeriod": 15
}

```
5. Create a folder **wordlist** and **rule** and put there your favorite wordlist and rules.
6. Run **Kraker.app.exe** or use command ```dotnet run Kraker.app.dll```
7. You can copy-paste the agent folder from server to server for easy setup. Happy cracking!


<p align="center">
  <img src="https://github.com/zzzteph/Kraker/blob/main/docs/pics/agent_setup.gif?raw=true">
</p>



### Using release

In release you can find:
- pre-installed hashcat [https://hashcat.net/hashcat/](https://hashcat.net/hashcat/)
- rules from  [https://github.com/ttmyst/tmyst_rules](https://github.com/ttmyst/tmyst_rules)

Running agent from release:

1. Install .NET Core 5 runtime.
2. Modify ```appsettings.json``` in ```Kracker.App/bin/Release/net5.0``` and put **ServerURL** and **Hashcat.Path**.
3. Put your favorite wordlist and rules in **wordlist** and **rule** folders. You can find lots of **wordlists** on [https://weakpass.com](https://weakpass.com).
4. Run **Kraker.app.exe** or use command ```dotnet Kraker.app.dll```





# Contacts

- [@_w34kp455](https://twitter.com/w34kp455)
- [@_asSheShouldBe](https://twitter.com/asSheShouldBe)
- [https://weakpass.com](https://weakpass.com)


