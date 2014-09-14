echo 'Removing vendor.zip'
rm -f vendor.zip
echo 'Removing vendor/'
rm -rf vendor/
echo 'Removing wwwroot/static/components'
rm -rf wwwroot/static/components/
echo 'Removing wwwroot/static/script/plugins'
rm -rf wwwroot/static/scripts/plugins/
echo 'Removing wwwroot/static/fonts'
rm -rf wwwroot/static/fonts/
echo 'Downloading vendor.zip'
curl -o vendor.zip https://coding.net/u/shonenada/p/szu-calendar-vendor/git/archive/master
echo 'unpacking'
unzip -qo vendor.zip
echo 'Done!'