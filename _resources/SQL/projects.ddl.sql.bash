#!/bin/bash

database_server="localhost"
database_user="username"
database_password="p@55W0rd"
database_name="projects_dev"

include_ddl=false
include_sp=true
include_fake_data=false

# must be in proper order for drop/add with key relationships
ddl_files=( \
  "_resources/SQL/projects.ddl.sql"\
  "_resources/SQL/projects.seed.sql"
)
sp_files=( \
  "_resources/SQL/projects.sp.sql"\
  "_resources/SQL/PR_Vote.sql"
)
fake_data_files=( \
  "_resources/SQL/createusers.sql"\
  "projects/_resources/SQL/projects.fakedata.sql"\
  "projects/_resources/SQL/projectslist.sql"
)

exec_sql_files=()

# move to working directory
cd $( dirname "${BASH_SOURCE[0]}" )

# trump credentials if external file exists
if [ -f credentials_local.bash ]; then
  source credentials_local.bash
fi

# backup data
# mysqldump --no-create-info --no-create-db --host=$database_server --user=$database_user --password=$database_password --databases $database_name

# move to site root directory
cd ../..


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

if $include_fake_data; then
  for sql in "${fake_data_files[@]}"
  do
    exec_sql_files+=($sql)
  done
fi

for sql in "${exec_sql_files[@]}"
do
  mysql --host=$database_server --user=$database_user --password=$database_password --database=$database_name < $sql
done
