#!/usr/bin/env bash

echo "Cloning mu-plugins"
git clone -q --recursive "$mu_plugins_url" "$mu_plugins_dir"
