package config

import (
	"fmt"
	"github.com/spf13/viper"
	"log"
)

var (
	SQLDB string
)

func init() {
	//database string initialization
	//get global variables from config
	viper.SetConfigName("config")
	viper.SetConfigType("yaml")
	viper.AddConfigPath(".")
	err := viper.ReadInConfig()
	if err != nil {
		log.Fatal(fmt.Sprintf("Can not find config file. Err: %v", err.Error()))
	}

	db_user := viper.GetString("db.db_user")
	db_password := viper.GetString("db.db_password")
	db_host := viper.GetString("db.db_host")
	db_port := viper.GetString("db.db_port")
	db_name := viper.GetString("db.db_name")
	db_tz := viper.GetString("db.db_tz")
	if db_host == "" || db_name == "" {
		log.Fatal("db_host or db_name is missing. Most likely config.yml doesn't have these values")
	}

	SQLDB = fmt.Sprintf("%v:%v@tcp(%v:%v)/%v?charset=utf8&allowOldPasswords=1&parseTime=true&loc=%v", db_user, db_password, db_host, db_port, db_name, db_tz)
}
