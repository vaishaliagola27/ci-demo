#!/usr/bin/env bash

setup_environment() {
  echo "Setting up env variables"
  mu_plugins_url="https://github.com/Automattic/vip-mu-plugins-public"
  temp_directory=$(mktemp -d 2>/dev/null || mktemp -d -t 'build')
  project_root="$( git rev-parse --show-toplevel )"
  build_root="$temp_directory"
  mu_plugins_dir="$build_root/wp-content/mu-plugins"

}
