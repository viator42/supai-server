# 脚本备份

# Backup files
filedate=`date +%Y%m%d`
BACKUP_ROOT="/backup"

# Delete the backup files of 1 weeks ago
DELETEDATE=`date -d '1 week ago' +%Y%m%d`
rm -r $BACKUP_ROOT/$DELETEDATE

mkdir $BACKUP_ROOT/$filedate
filepath=$BACKUP_ROOT/$filedate

tar -czvf $filepath/supai.im_backup.tar.gz /var/www/supai-server/supai

echo "程序文件备份结束"

# database backup
/usr/bin/mysqldump -u root supai > $filepath/supai.sql

echo "数据库备份结束"