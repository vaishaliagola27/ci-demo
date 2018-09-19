# ci-demo

This demo repo will include basic circle ci for below.

- phpcs
- deploy

**PHPCS**

phpcs will run on diff of latest commit and master branch. For phpcs below files are required.

- phpcs.xml
- setup_phpcs.sh
- parse-diff-ranges.php
- filter-report-for-patch-ranges.php

**DEPLOY**

This demo deploys to server using deployer and circle-ci. Follow below steps to setup SSH for deployer.

- Visit https://circleci.com/ and login with GitHub.
- Go to user settings and link your github account.
- Click on Add Projects, then click on Set up Project for which directory you want setup
- Click on Start building
- GO TO Settings->Projects
- Click on project settings from followed projects
- Create a SSH Key without passphrase
- In _SSH Permissions_ menu, click on `Add SSH Key` and add private key created in above step
- Add public key to knownhosts on server to run deployer

**ENVIRONMENT Variables**

Circle-ci allows to add important and private variables into environment variable and access them while running scripts on server. In project settings->`Environment Variables`, we have option to Add new variable. We can access them in two ways

- PHP - `$server_name = getenv( 'SERVER_NAME' );`
- shell - `$PROJECT_ROOT`

Below is the list of variable used in this project.

- SERVER_NAME - Server name to deploy
- SERVER_USER - User to ssh login( ex. www-data )
- SSH_FINGERPRINT - SSH fingerprint (private key) to deploy
- DEP_PATH - directory path to deploy on server
