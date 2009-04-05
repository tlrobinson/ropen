ropen - Remote "open"
=====================

The Problem
-----------

Most Mac OS X power users know about the ["open"](http://tuvix.apple.com/documentation/Darwin/Reference/ManPages/man1/open.1.html) command line tool which opens the files specified as arguments in their default (or a specified) OS X application. Additionally, many OS X text editors, such as TextMate ("mate") and SubEthaEdit ("see"), come with command line tools which can be used to open files.

These are great when working locally, but obviously do no work remotely. Often when working on remote servers you end up using command line editors which you may not be as familiar with.

ropen's Solution
----------------

The ropen project solves this problem using two simple shell scripts, which make use of MacFuse's sshfs. You run "ropen" on your remote machines (this is equivalent to the OS X "open" command), "ropend" on your local OS X machine, and a PHP script on a mutually accessible webserver.

* "ropen" is the interface to ropen and is similar to the "open" command on OS X, but sends the open commands from your remote machines (running any Unix-like OS and an ssh server) to the local machine (running OS X and sshfs), which then connects to the remote machine via sshfs.
* "ropend" is a daemon that receives open commands and mounts the remote filesystem via "sshfs", then opens the specified files in the specified application.
* "ropen.php" is a simple PHP script that facilitates the communication between ropend running on your local machine, and ropen running on remote machines. This is necessary to simplify the design and eliminate the need to open additional ports.
    
Installation
------------

1. Install MacFuse and sshf on your local (Mac OS X) machine.
2. Copy ropend to your local machine, fill in ROPEN_SECRET if you don't want to specify the "-s" argument. If you are using the default ROPEN_URL (see below) you should use a large (> 50 characters) *random* string to prevent colisions.
3. Run ropend. To put it in the background you might try "nohup ./ropend &"
4. Copy ropen to each remote machine you wish to use it from, and fill in ROPEN_SECRET
5. Copy your local machine's SSH public key (usually ~/.ssh/id_rsa.pub) the authorized_keys file of each remote (~/.ssh/authorized_keys). When connecting to the remote machine via ssh there should not be any prompts to the user.
6. (Optional) Install ropen.php on a webserver with PHP enabled, and edit ROPEN_URL in both ropen and ropend.

"mate", "see", etc
------------------

To get "mate" and "see" like commands (which open all files in TextMate or SubEthaEdit, respectively), simple add some aliases to your remote machine's shell configuration file (.profile, etc):

    alias mate="ropen -a TextMate"
    alias see="ropen -a SubEthaEdit"
 
Multiple Local Machines
-----------------------

If you have multiple local machines, you can use different ROPEN_SECRET's for each ropend, then simply create an alias for each ropen, using the -s command line argument:

    alias ropen-imac="ropen -s 12345678-imac"
    alias ropen-macbook="ropen -s 12345678-macbook"
    
Notes
-----

TextMate and other applications are very slow at opening large directory trees over sshfs.
    
Security
--------

ropen may have a number of security issues. If the ROPEN_SECRET and ROPEN_URL are known or guessed, anyone could issue arbitrary open commands on your local machine. If you don't see why this is a problem, you probably shouldn't use this tool.

Using a large random ROPEN_SECRET and placing ropen.php under SSL and HTTP authentication helps improve the security and is suggested.

There is no guarantee of any kind when using the default ROPEN_URL (currently http://tlrobinson.net/ropen/ropen.php)
