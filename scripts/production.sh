rm -f vendor.zip
rm -rf vendor/
rm -rf wwwroot/static/components/
rm -rf wwwroot/static/scripts/plugins/
rm -rf wwwroot/static/fonts/
curl -o vendor.zip https://coding.net/u/shonenada/p/szu-calendar-vendor/git/archive/master
unzip -qo vendor.zip