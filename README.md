Giply-server
============

Giply-server is a git-based deployment server written in PHP. It's very much early stage, being used on several
personal/small projects, so the options are pretty rudimentary at the moment. This script allows me to
have a single domain (i.e. deploy.myserver.com) that I can use to manage POST deployments for all of my projects on the
server.

There are some assumptions with the Giply-server:

 1. The server expects to be located in a the same parent ($parentDir) as the projects it manages (for instance, inside of __/var/www__).
 2. The server web root should be the included _web_ directory.
 3. The server expects pretty URLs, in the format: __action/projectName/securityHash__.
    * The only actions supported at the moment are _pull_.
    * _projectName_ is the name of the working directory (in your /var/www folder).
    * _securityHash_ is a simple md5 hash of the string _$parentDir/projectName_. Other hashes may be supported in future
      revisions, and it is really only there to add a little bit of simple security.

So an example of a POST url for Bitbucket or Github for my server:

    http://deploy.myserver.com/pull/mysite/ff56634640221a6b2716d276361162cd

The server script is built around projects that I have on Github and Bitbucket. Both of these providers POST to the server
with a json string to the _payload_ key. The server stores the JSON string in a file: **giply_payload.json**. This provides
any of the *post_exec* scripts access to the payload data for processing.

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

        git pull origin master
        php self-update.php
