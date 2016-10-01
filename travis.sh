#!/bin/bash

set -e

. ./utils/functions.sh

setup()
{
  # Install Python.
  run mkdir -p "$downloads"
  installer_url='https://www.python.org/ftp/python/3.5.3/python-3.5.3-macosx10.6.pkg'
  installer_md5='6f9ee2ad1fceb1a7c66c9ec565e57102'
  installer_pkg="$downloads/python35.pkg"
  for retry in $(seq 3)
  do
    if ! [ -r "$dst" ] && ! run curl --show-error --silent -o "$installer_pkg" "$installer_url"
    then
      err "python download failed"
      run rm -f "$installer_pkg"
      continue
    fi
    if [ $opt_dry_run -eq 0 ] && [ "$(md5 -q "$installer_pkg")" != "$installer_md5" ]
    then
      err "python md5 did not match"
      run rm -f "$installer_pkg"
      continue
    fi
    break
  done
  run sudo installer -pkg "$installer_pkg" -target /
  # Install/upgrade pip/wheel.
  get_pip --upgrade "$@"
}

build_component()
{
  run_eval "(cd $1 && $python setup.py --quiet sdist --dist-dir=$2 bdist_wheel --dist-dir=$2)"
  run du -hsc dist/*
}

build()
{
  build_component pyobjc-core ../dist
  run "$python" -m pip install dist/pyobjc_core-*.whl
  for c in pyobjc-framework-Cocoa pyobjc-framework-Quartz
  do
    build_component "$c" ../dist
  done
  run du -hsc dist/*
}

[ "$TRAVIS_OS_NAME" == 'osx' ]
[ $# -eq 1 ]

python='python3'

"$1"
