mysqldump -h localhost -u lolplay -p9d041fa83 lolplay | gzip > /var/www/backup/mysql/day/`date '+%Y-%m-%d_%T'`.gzip 

keep=6
x=1
for i in `ls -t  /var/www/backup/mysql/day/*`
        do
          if [ $x -le $keep ]
                then
                ((x++))
                continue
          fi
        rm $i
done