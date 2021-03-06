#!/bin/bash

INSTALLDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# From: http://tldp.org/LDP/abs/html/colorizing.html
# Colorizing the installation process.

black='\E[1;30;40m'
red='\E[1;31;40m'
green='\E[1;32;40m'
yellow='\E[1;33;40m'
blue='\E[1;34;40m'
magenta='\E[1;35;40m'
cyan='\E[1;36;40m'
white='\E[1;37;40m'

cecho ()                     # Color-echo.
                             # Argument $1 = message
                             # Argument $2 = color
{
  local default_msg="No message passed."
                             # Doesn't really need to be a local variable.

  message=${1:-$default_msg}   # Defaults to default message.
  color=${2:-$white}           # Defaults to white, if not specified.

  echo -e "$color"
  echo -e "$message"
  
  tput sgr0                     # Reset to normal.

  return
}

# Check where is installed structwsf
STRUCTWSFFOLDER="/usr/share/structwsf/"

cecho "Where is structWSF installed on your server (default: $STRUCTWSFFOLDER):" $magenta

read NEWSTRUCTWSFFOLDER

[ -n "$NEWSTRUCTWSFFOLDER" ] && STRUCTWSFFOLDER=$NEWSTRUCTWSFFOLDER

# Make sure there is no trailing slashes
STRUCTWSFFOLDER=$(echo "${STRUCTWSFFOLDER}" | sed -e "s/\/*$//")


STRUCTWSFPHPAPIDOWNLOADURL="https://github.com/structureddynamics/structWSF-PHP-API/zipball/master"

echo -e "\n\n"
cecho "----------------------------------"
cecho " Installing the structWSF PHP API "
cecho "----------------------------------"
echo -e "\n\n"

# Current location: /usr/share/structwsf/

sudo wget $STRUCTWSFPHPAPIDOWNLOADURL  

cecho "\n\n9.3) Decompressing structWSF PHP API...\n"

sudo unzip "master"  

cd `ls -d structureddynamics*/`

cd "StructuredDynamics/structwsf/"

sudo cp -a php $STRUCTWSFFOLDER"/StructuredDynamics/structwsf/"

cd ../../

sudo rm -rf `ls -d structureddynamics*/`

sudo rm master

echo -e "\n\n"
cecho "--------------------"
cecho " Installing PHPUnit "
cecho "--------------------"
echo -e "\n\n"

cecho "\n\nInstall PHPUnit...\n"

sudo apt-get install -y phpunit

# Download the tests suites, and move them into the structwsf folder.
sudo mkdir tests

cd tests

cecho "\n\nDownload the latest system integration tests for structWSF...\n"

sudo wget https://github.com/structureddynamics/structWSF-Tests-Suites/zipball/master

unzip master

cd `ls -d structureddynamics*/`

cd StructuredDynamics/structwsf/

# Move the tests suites to structWSF's folder structure
sudo mv * $STRUCTWSFFOLDER"/StructuredDynamics/structwsf/"

cd ../../../

sudo rm -rf `ls -d structureddynamics*/`

# Go to the tests' folder, and change the configuration files
cd $STRUCTWSFFOLDER"/StructuredDynamics/structwsf/tests/"

DOMAINNAME="localhost"

cecho "What is the domain name where the structWSF instance is accessible (default: $DOMAINNAME):" $magenta

read NEWDOMAINNAME

[ -n "$NEWDOMAINNAME" ] && DOMAINNAME=$NEWDOMAINNAME

cecho "\n\nConfigure tests...\n"

sudo sed -i "s>REPLACEME>"$STRUCTWSFFOLDER"/StructuredDynamics/structwsf>" phpunit.xml

sudo sed -i "s>$this-\>structwsfInstanceFolder = \"/usr/share/structwsf/\";>$this-\>structwsfInstanceFolder = \""$STRUCTWSFFOLDER"/\";>" Config.php

sudo sed -i "s>$this-\>endpointUrl = \"http://localhost/ws/\";>$this-\>endpointUrl = \"http://"$DOMAINNAME"/ws/\";>" Config.php

sudo sed -i "s>$this-\>endpointUri = \"http://localhost/wsf/ws/\";>$this-\>endpointUri = \"http://"$DOMAINNAME"/wsf/ws/\";>" Config.php


cecho "\n\nRun the system integration tests suites...\n"

sudo phpunit --configuration phpunit.xml --verbose --colors --log-junit log.xml

cecho "\n\n=============================\nIf errors are reported after these tests, please check the "$INSTALLDIR"/tests/log.xml file to see where the errors come from. If you have any question that you want to report on the mailing list, please do include that file in your email: http://groups.google.com/group/open-semantic-framework\n=============================\n\n"

