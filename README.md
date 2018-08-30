# iCryptoNode - Monero Raspberry Pi Cryptocurrency Node Management Software

[iCryptoNode](https://icryptonode.com/) is an open source software project to manage blockchain daemons, specifically for single-board computers like Raspberry Pi. It aims to be blockchain agnostic by standardizing interfaces.

For now, we are only supporting [Monero](https://getmonero.org/).

Anyone can use or build this software. Development is sponsored by [iCryptoNode.com](https://icryptonode.com/) which sells hardware pre-installed and configured with iCryptoNode and blockchain software.

## Features

 - Blockchains:
   - Monero
   - *More coming soon*
 - Privacy & Security
   - Built-in support for VPN ([Private Internet Access](https://www.privateinternetaccess.com/pages/buy-vpn/easyvpnr))
   - Use the blockchain without exposing your IP
   - Stop relying on untrusted third-party remote nodes
   - Nothing is tracked by us or any service provider
   - All updates cryptographically signed to prevent tampering
 - Simplify Management
   - Easily update blockchain daemon
   - Easily update iCryptoNode software
   - GUI shows stats and enables quick configuration changes
   - Everything is automatically running on device boot
 - Optimized for Raspberry Pi
   - Minimal resource overhead
   - Swap with minimal use to preserve SD Card lifespan
   - All decisions made to squeeze performance from low-end devices
 - Fault Tolerant
   - Services are restarted automatically

## Software

Our web server is [lighttpd](http://www.lighttpd.net/) as it is optimized for low resource environments. It is similar to apache.

The front-end is written in [VueJS](https://vuejs.org/) and delivered as a single-page app.

The backend is written in PHP, as it doesn't require a constantly running process (like NodeJS) so we save system resources.

## Database

We do not use a mysql, sqlite, etc. in order to minimize system resources. We use [UCI standalone](https://openwrt.org/start?id=docs/guide-user/base-system/uci) from the OpenWRT project which is key-value config system written in C. OpenWRT devs built it from scratch to be used on tiny wireless routers, so it's perfect for our use case.

## Security

Updates are in configuration files hosted by iCryptoNode and signed by our PGP key. Please read [iCryptoNode Security](https://icryptonode.com/pages/security) for more information.

## Installation

Installation is a combination of automated and manual steps. You must be able to SSH into your Raspberry Pi. *Follow the steps below in order!*

### Flash Rasbian

Using disk of at least 128 GB, flash [Raspbian Stretch Lite](https://www.raspberrypi.org/downloads/raspbian/). We want Lite because it doesn't waste system resources on running a full desktop GUI environment. We want those resources for our blockchain node.

Full instructions for how to download and install can be found [here](https://hackernoon.com/raspberry-pi-headless-install-462ccabd75d0). Make sure you do the SSH step and add the `ssh` file to the root directory! Otherwise, you won't be able to SSH in.

Once you have your local IP, SSH in (user: `pi`, password: `raspberry`) and do some updates.

`sudo apt-get update`
`sudo apt-get upgrade`

Update raspberry pi and install kernel drivers:
`sudo rpi-update`

Run raspi-config to enable Wifi. You need to do this once to set a Wifi country, and later it can be changed from within iCryptoNode software:
`sudo raspi-config`

You must now reboot, which will close the SSH tunnel, and you'll have to SSH back in:
`sudo reboot`

Open port 22 for SSH:
`sudo ufw allow 22`
Enable UFW firewall:
`sudo ufw enable`

### Install UCI

Before doing anything else, we must install [UCI](https://openwrt.org/start?id=docs/guide-user/base-system/uci).

Install necessary packages:
`sudo apt-get install dh-autoreconf git lua5.1 liblua5.1-0-dev cmake`

You can build from scratch using [these instructions](https://wiki.openwrt.org/doc/techref/uci#make_uci_in_ubuntu_1604_raspbian_jessie_or_similar). Be aware you must build and install `json-c` and `libubox` per instructions as UCI requires them.

It is best to build statically to ensure no errors finding shared libs. When you clone UCI, edit `CMakeLists.txt` and change:
`OPTION(BUILD_STATIC "statically linking uci" OFF)`

To:
`OPTION(BUILD_STATIC "statically linking uci" ON)`

Then `cmake .`, `make`, `sudo make install`.

### Clone this repository

In your home folder on the raspberry pi:
`git clone git@github.com:seibelj/iCryptoNode.git`

### Run the iCryptoNode Config Script

This automatically configures as many things as possible. Unfortunately some things can't (easily) be automated, which is why there are more manual steps after this.

`cd iCryptoNode/setup`
`sudo ./icn_configure monero`

Let it run.

### Enable GPG for PHP

We use GPG for our PGP encryption implementation. It must be enabled in `php.ini`.

Edit `php.ini`:
`sudo nano /etc/php/7.0/cgi/php.ini`

Navigate to the `Dynamic Extensions` section and add this line:
`extension=gnupg.so`

Do the same for the PHP command-line interface if you'd like:
`sudo nano /etc/php/7.0/cli/php.ini`

Make sure there are no semi-colons (`;`) before it! That comments out the line.

### Enabling sudo www-data access for specific commands

We enable executing specific commands as `sudo` user for `www-data` (web server) to allow system management from the GUI.

The security model of iCryptoNode assumes that it only runs on a network safe from attack, meaning your primary security is keeping the router safe from physical attack and using a strong Wifi password.

However, we still try to make iCryptoNode as secure as possible, in case the first layer of security fails.

Therefore, we restrict the commands accessible to `www-data` running `sudo` to only what is needed. We also do argument sanitization (`escapeshellarg()`) to stop injection of shell commands.

Start visudo:
`sudo visudo`

Add to the bottom:
```
Cmnd_Alias WWW_COMMANDS = /usr/local/bin/uci, /var/www/html/icryptonode/system_commands/*, /var/www/html/icryptonode/vpn/commands/*, /var/www/html/icryptonode/node_commands/*
www-data ALL = (ALL) NOPASSWD: WWW_COMMANDS
```

This restricts `sudo` access for user `www-data` to specific commands.

### Add Swap

The current top-of-the-line Raspberry Pi has only 1 GB of ram. We add swap in order to allow ram to be extended by disk in cases where memory is exhausted. However, given that random write to SD cards can wear them out, we want to make the system prefer ram to disk whenever possible.

Remove old swap and make new, bigger swap (2GB). Some of these commands take a while to run, just be patient.
```
sudo /etc/init.d/dphys-swapfile stop
sudo rm /var/swap
sudo dd if=/dev/zero of=/var/swap count=2K bs=1M
sudo mkswap /var/swap
sudo chmod 600 /var/swap
sudo swapon /var/swap
```

Set swappiness value to 0 to make system use swap only when absolutely necessary:
`sudo sysctl vm.swappiness=0`

Make it permanent. Edit:
`sudo nano /etc/sysctl.conf`
Add to bottom:
`vm.swappiness = 0`
Save the file.

You also need to do this. Edit:
`sudo nano /etc/dphys-swapfile`
Replace existing `CONF_SWAPSIZE` with:
`CONF_SWAPSIZE=2048`
Save the file.

Restart system service:
```
sudo /etc/init.d/dphys-swapfile stop
sudo /etc/init.d/dphys-swapfile start
```

### DNS Leak Protection

Whether you use VPN or not, it is recommended to set your DNS servers to [Private Internet Access](https://www.privateinternetaccess.com/pages/buy-vpn/easyvpnr)' DNS servers to prevent DNS leaks and enhance privacy.

Edit network interfaces:
`sudo nano /etc/network/interfaces`

Add to the bottom of the file:
`dns-nameservers 209.222.18.222 209.222.18.218`

Edit dhcpcd conf:
`sudo nano /etc/dhcpcd.conf`

Add to the bottom of the file:
`static domain_name_servers=209.222.18.222 209.222.18.218`

Reboot machine and SSH back in:
`sudo reboot`

Sometimes you need to do a hard shutoff with a powercycle, if the reboot fails.

After reboot, you can verify PIA DNS servers are used (it should look similar to this):
```
$ nslookup google.com
Server:     209.222.18.222
Address: 209.222.18.222#53

Non-authoritative answer:
Name: google.com
Address: 172.217.12.174
```

### Disable IPv6
Disabling IPv6 ensures all traffic goes over IPv4 and is protected by the VPN.

Edit sysctl conf file:
`sudo nano /etc/sysctl.d/99-sysctl.conf`

Add these 3 lines to the bottom:
```
net.ipv6.conf.all.disable_ipv6 = 1
net.ipv6.conf.default.disable_ipv6 = 1
net.ipv6.conf.lo.disable_ipv6 = 1
```

Save the file.

Now enable it:
`sudo sysctl -p`

This will be preserved across reboots.

Verify IPv6 is disabled:
`cat /proc/sys/net/ipv6/conf/all/disable_ipv6`

Output should be `1`

### Access iCryptoNode and Install Blockchain

You should now be able to access iCryptoNode. Instructions on use are hosted here at iCryptoNode.com.

When you build your own iCryptoNode rather than pre-purchase one, you need to install the blockchain software and sync it.

Go to the Updates tab and download and install the latest version of Monero. Once installed, go to the Node tab and enable the daemon. Syncing will take about a week, [unless you pre-install the blockchain](https://getmonero.org/resources/user-guides/importing_blockchain.html).

### Conclusion

Congratulations! You have successfully built your own iCryptoNode.

If you find a bug, please file a ticket on this Github project. You can also post on the [iCryptoNode subreddit](https://reddit.com/r/iCryptoNode).

## License
GPLv3. Essentially if you modify this code, you must release your modifications open source with the same GPLv3 license.