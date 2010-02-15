rm -rf docu/*

phpdoc -c config.ini
for f in `grep -rl charset docu/*`; do sed -i -e 's/charset=iso-8859-1/charset=utf-8/g' $f; done;
