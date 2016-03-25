#!/bin/bash

database_server="localhost"
database_user="username"
database_password="p@55W0rd"
database_name="projects_dev"

include_fake_data=true

# must be in proper order for drop/add with key relationships
ddl_files=( \
  "_resources/SQL/projects.ddl.sql"\
  "_resources/SQL/projects.seed.sql"\
  "_resources/SQL/createusers.sql"
)
fake_data_files=(\
  "projects/_resources/SQL/projects.fakedata.sql"\
  "projects/_resources/SQL/projectslist.sql"
)

# move to working directory
cd $( dirname "${BASH_SOURCE[0]}" )

# trump credentials if external file exists
if [ -f credentials_local.bash ]; then
  source credentials_local.bash
fi

# move to site root directory
cd ../..


if $include_fake_data; then
  for sql in "${fake_data_files[@]}"
  do
    ddl_files+=($sql)
  done
fi

for sql in "${ddl_files[@]}"
do
  mysql --host=$database_server --user=$database_user --password=$database_password --database=$database_name < $sql
done
