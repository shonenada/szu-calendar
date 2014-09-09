rm -f /app/vendor.zip
wget https://coding.net/u/shonenada/p/szu-calendar-vendor/git/archive/master -O /app/vendor.zip -q
unzip -q /app/vendor.zip -d /app/ > /dev/null
/app/boot.sh