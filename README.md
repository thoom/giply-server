Giply-server
============

This server script is included since I use the same server to host several small projects. This script allows me to
have a single domain (i.e. deploy.myserver.com) that I can use to manage POST deployments for all of my projects on the
server.

The server expects pretty URLs, in the format: __action/projectName/securityHash__. The only action supported at the moment
is _pull_. The other options: _projectName_ is the name of the working directory (in your /var/www folder). The security
hash is a simple md5 hash of the string _/var/www/projectName_. Other hashes may be supported in future revisions, and
it is really only there to add a little bit of simple security.

So an example of a POST url for Bitbucket or Github for my server:

    http://deploy.myserver.com/pull/mysite/ff56634640221a6b2716d276361162cd

The server script is built around projects that I have on Github and Bitbucket. Both of these providers POST to the server
with a json string to the _payload_ key. The server stores the JSON string in a file: **giply_payload.json**. This provides
any of the *post_exec* scripts access to the payload data for processing.
