mysqldump -h localhost -u lolplay -p9d041fa83 lolplay | gzip > /var/www/backup/mysql/week/`date '+%Y-%m-%d_%T'`.gzip 

keep=4
x=1
for i in `ls -t  /var/www/backup/mysql/week/*`
        do
          if [ $x -le $keep ]
                then
                ((x++))
                continue
          fi
        rm $i
done