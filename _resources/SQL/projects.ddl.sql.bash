#!/bin/bash

database_server="localhost"
database_user="username"
database_password="p@55W0rd"
database_name="projects_dev"

drop_tables=false
include_ddl=true
include_sp=true
include_seed_data=false

drop_table_scripts=( \
  "projects.drop.sql"
)
ddl_files=( \
  "projects.ddl.sql"
)
sp_files=( \
  "projects.sp.sql"\
  "PR_Vote.sql"
)
seed_data_files=( \
  "projects.seed.sql"
)
exec_sql_files=()

if [[ $1 == "-a" ]]; then
  drop_tables=true
  include_ddl=true
  include_sp=true
  include_seed_data=true
fi

# move to working directory
cd $( dirname "${BASH_SOURCE[0]}" )

# trump credentials if external file exists
if [ -f credentials_local.bash ]; then
  source credentials_local.bash
fi

# backup data
# mysqldump --no-create-info --no-create-db --host=$database_server --user=$database_user --password=$database_password --databases $database_name

if $drop_tables; then
  for sql in "${drop_table_scripts[@]}"
  do
    exec_sql_files+=($sql)
  done
fi

if $include_ddl; then
  for sql in "${ddl_files[@]}"
  do
    exec_sql_files+=($sql)
  done
fi

if $include_sp; then
  for sql in "${sp_files[@]}"
  do
    exec_sql_files+=($sql)
  done
fi

if $include_seed_data; then
  for sql in "${seed_data_files[@]}"
  do
    exec_sql_files+=($sql)
  done
fi

for sql in "${exec_sql_files[@]}"
do
  mysql --host=$database_server --user=$database_user --password=$database_password --database=$database_name < $sql
done
