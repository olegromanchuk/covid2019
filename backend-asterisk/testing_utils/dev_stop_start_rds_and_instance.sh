#!/bin/bash
DB_INSTANCE_ID=covid2019databaseinstance
EC2_INSTANCE_ID=i-05bf930d8df6393fe

if [[ $# -ne 1 ]]; then
    echo "Use $0 on|off"
    exit 1
fi
if [[ "${1}" == "on" ]]; then
    aws rds start-db-instance --region=us-west-2 --db-instance-identifier ${DB_INSTANCE_ID}
    aws ec2 start-instances --region=us-west-2 --instance-ids ${EC2_INSTANCE_ID}
    exit 0
fi
aws rds stop-db-instance --region=us-west-2 --db-instance-identifier ${DB_INSTANCE_ID}
aws ec2 stop-instances --region=us-west-2 --instance-ids ${EC2_INSTANCE_ID}
