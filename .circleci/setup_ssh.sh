#!/usr/bin/env bash
printf "[\e[0;34mNOTICE\e[0m] Setting up SSH access to server.\n"

mkdir -p ~/.ssh
if [[ -f /.dockerenv ]]; then
  echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config
fi
echo "$SSH_FINGERPRINT" > ~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
eval `ssh-agent`
ssh-add ~/.ssh/id_rsa
