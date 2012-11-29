Giply-server
============

Giply-server is a git-based deployment server written in PHP. This script allows me to
have a single domain (i.e. deploy.myserver.com) that I can use to manage POST deployments for all of my projects on the
server.

There are some assumptions with the Giply-server:

 1. The server expects to be located in a the same parent ($parentDir) as the projects it manages (for instance, inside of `/var/www`).
 2. The server web root should be the included `web`_ directory.
 3. The server expects pretty URLs, in the format: `action/projectName/securityHash`.
    * The only actions supported at the moment are _pull_.
    * _projectName_ is the name of the working directory (in your /var/www folder).
    * _securityHash_ is by default a simple md5 hash of the string `$parentDir/projectName`. To provide your own security hash,
      you can add a hash object with the projectname as a key in your giply_config.json file:

          { "hash": {"mysite": "abc123456"}}

    So an example of a POST url for Bitbucket or Github for my server:

        http://deploy.myserver.com/pull/mysite/ff56634640221a6b2716d276361162cd

    The server script is built around projects that I have on Github and Bitbucket. Both of these providers POST to the server
    with a json string to the _payload_ key. The server stores the JSON string in a file: **giply_payload.json**. This provides
    any of the *post_exec* scripts access to the payload data for processing.

 4. Any project that you want to have updated by Giply needs to have its Git repo initialized and origin added. Connecting to the
    repository using SSH means that you also need to make sure that the web user running Giply has the SSH key to connect
    to the server. As an example:

        sudo su www-data
        cd /var/www/mysite
        git init
        git remote add origin git@bitbucket.org:myacct/myacct.git
        git pull origin master

    If you get an error pulling the origin, it probably means that the SSH key is missing or not approved to access the repo.
    However, if you can successfully pull the origin using your web user (like `www-data`), Giply-server should work fine.

To create a server
------------------

Note: There is an assumption here that you know how to set up Apache or Nginx for pretty URLs. I personally use Nginx
for my projects, but even with Apache I like putting my rewrite rules in a vhost file over .htaccess. For that reason,
I'm not including an .htaccess file in the web directory.

 1. To install, first just check out the code to the directory of your choice. I use something like */var/www/deploy*.

        git init
        git remote add origin git://github.com/thoom/Giply-server.git
        git pull origin master

 2. Run the install script using __php self-update.php__. Make sure that the user running this file has permission to write to
    this directory. When this process is done, you should have a new file call *giply_config.json*.
 3. Now, anytime you want to update to the latest version, just run:

        php self-update.php

To run the server from the command line
---------------------------------------

You can run the server from the command line:

    php cli.php pull mysite

