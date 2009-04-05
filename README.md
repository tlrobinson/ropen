ropen - Remote "open"
=====================

The Problem
-----------

Most Mac OS X power users know about the ["open"](http://tuvix.apple.com/documentation/Darwin/Reference/ManPages/man1/open.1.html) command line tool which opens the files specified as arguments in their default (or a specified) OS X application. Additionally, many OS X text editors, such as TextMate ("mate") and SubEthaEdit ("see"), come with command line tools which can be used to open files.

These are great when working locally, but obviously do no work remotely. Often when working on remote servers you end up using command line editors which you may not be as familiar with.

ropen's Solution
----------------

The ropen project solves this problem using two simple shell scripts, which make use of MacFuse's sshfs. You run the "ropen" program on your remote machine(s) when you want to open a remote file locally (this is equivalent to the OS X "open" command). The "ropend" daemon runs on your local OS X machine waiting for open requests, and the "ropen.php" PHP script proxies requests from ropen to ropend.

How it works
------------

1. When ropen is executed it makes an HTTP request to ropen.php with the paths to be opened and application to open them with, if any, as well as the SSH user, host, and port of the remote machine.
2. ropen.php stores this open request in a queue that is tied to ROPEN_SECRET via PHP's sessions.
3. ropend polls ropen.php every 1 second waiting for open requests. When it receives one it mounts the remote filesystem using sshfs (if it's not already mounted) and opens the files or directories specified.

Installation
------------

1. Install [MacFuse](http://code.google.com/p/macfuse/) and [sshfs](http://code.google.com/p/macfuse/wiki/MACFUSE_FS_SSHFS) on your local (Mac OS X) machine.
2. Copy "ropend" to your local machine, fill in ROPEN_SECRET if you don't want to specify the "-s" argument each time. If you are using the default ROPEN_URL (see below) you should use a large (> 50 characters) *random* string to prevent collisions.
3. Run "ropend". To run it in the background try "nohup ./ropend &"
4. Copy "ropen" to each remote machine you wish to use it from, and fill in ROPEN_SECRET
5. Copy your local machine's SSH public key (usually ~/.ssh/id_rsa.pub) the authorized_keys file of each remote (~/.ssh/authorized_keys). When connecting to the remote machine via ssh there should not be any prompts to the user.
6. (Optional) Install ropen.php on a webserver with PHP enabled, and edit ROPEN_URL in both ropen and ropend.
7. (Optional) Uncomment and set the SSH_CONNECT_STRING in ropen to be the username, host, and port combination the local machine needs to use to login. Normally this is detected automatically if you are logged in SSH.

Usage
-----

Once ropend is running on your local machine, and ropen is in your PATH variable on the remote machine(s), and both are pointing to the same ropen.php and have the same ROPEN_SECRET varialbe or "-s" argument, you can simply use ropen just as you would use "open" on OS X, for example:

    ropen /etc/hosts
    
This will open "/etc/hosts" from the remote machine on your local machine. You can then edit and save it, as long as ropend is running and your connection is open.

You can also specify the Mac OS X application to open the files and directories with (see next section)

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

Security
--------

ropen may have a number of security issues. If the ROPEN_SECRET and ROPEN_URL are known or guessed, anyone could issue arbitrary open commands on your local machine. If you don't see why this is a problem, you probably shouldn't use this tool.

Using a large random ROPEN_SECRET, and placing ropen.php under SSL and HTTP authentication helps improve the security, and is suggested.

There is no guarantee of security when using the default ROPEN_URL (currently http://tlrobinson.net/ropen/ropen.php), it is for testing non-sensitive systems only.

Bugs
----

* SSH ports other 22 are not yet supported.
* TextMate and other applications are very slow at opening large directory trees over sshfs.

License
-------

Copyright (c) 2009, Thomas Robinson
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of Thomas Robinson nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
